<?php
include_once('./_common.php');

// CSV 파서 라이브러리 로드
require_once(G5_QUALITY_PATH . '/lib/quality_csv_parser.lib.php');

// 레벨 5 이상만 접근 가능
if ($member['mb_level'] < 5) {
    alert('템플릿 업로드는 회원 레벨 5 이상만 사용할 수 있습니다.', G5_URL);
}

$qt_id = isset($_GET['qt_id']) ? (int)$_GET['qt_id'] : 0;
$template = null;
if ($qt_id) {
    $template = get_quality_template($qt_id);
}

// 파일 업로드 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['template_file'])) {
    // TODO: 엑셀/CSV 파일 파싱 및 템플릿 저장 로직 구현
    // 현재는 기본 구조만 제공
    
    $file = $_FILES['template_file'];
    
    // 파일 검증
    $allowed_extensions = array('xlsx', 'xls', 'csv');
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_extensions)) {
        alert('엑셀(.xlsx, .xls) 또는 CSV 파일만 업로드 가능합니다.', './template_upload.php');
    }
    
    if ($file['size'] > 10 * 1024 * 1024) { // 10MB
        alert('파일 크기는 10MB 이하여야 합니다.', './template_upload.php');
    }
    
    // 템플릿 파일 저장 경로: /upload/quality/files/
    $files_dir = G5_QUALITY_UPLOAD_PATH . '/files/';
    if (!is_dir($files_dir)) {
        @mkdir($files_dir, 0755, true);
        @chown($files_dir, 'apache');
        @chgrp($files_dir, 'user');
    }
    
    // 파일명 생성 (템플릿 ID가 있으면 수정, 없으면 신규)
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($qt_id) {
        // 수정 시: 기존 파일명 유지 또는 덮어쓰기
        $saved_filename = 'template_' . $qt_id . '_' . time() . '.' . $file_extension;
    } else {
        // 신규 등록 시
        $saved_filename = 'template_' . date('YmdHis') . '_' . uniqid() . '.' . $file_extension;
    }
    
    $saved_file_path = $files_dir . $saved_filename;
    
    // 파일 저장
    if (!move_uploaded_file($file['tmp_name'], $saved_file_path)) {
        alert('파일 업로드에 실패했습니다.', './template_upload.php');
    }
    
    // 파일 권한 설정
    @chmod($saved_file_path, 0644);
    @chown($saved_file_path, 'apache');
    @chgrp($saved_file_path, 'user');
    
    // CSV 파일인 경우 파싱 및 DB 저장
    if ($file_extension == 'csv') {
        // CSV 파싱
        $parsed_data = parse_quality_csv($saved_file_path);
        
        if (isset($parsed_data['error'])) {
            alert('CSV 파일 파싱 중 오류가 발생했습니다: ' . $parsed_data['error'], './template_upload.php');
        }
        
        // 필수 데이터 검증 (doc_id는 자동 생성되므로 title만 확인)
        if (empty($parsed_data['title'])) {
            $debug_info = '파싱된 데이터 확인:\n';
            $debug_info .= '- doc_id: ' . (isset($parsed_data['doc_id']) ? $parsed_data['doc_id'] : '없음') . '\n';
            $debug_info .= '- title: ' . (isset($parsed_data['title']) ? $parsed_data['title'] : '없음') . '\n';
            $debug_info .= '- location: ' . (isset($parsed_data['location']) ? $parsed_data['location'] : '없음') . '\n';
            $debug_info .= '- items: ' . (isset($parsed_data['items']) ? count($parsed_data['items']) . '개' : '없음');
            alert('CSV 파일 형식이 올바르지 않습니다. 검사표제목이 필요합니다.\n\n' . $debug_info, './template_upload.php');
        }
        
        // 템플릿 등록 또는 수정
        $template_data = array(
            'qt_doc_id' => addslashes($parsed_data['doc_id']),
            'qt_title' => addslashes($parsed_data['title']),
            'qt_location' => addslashes($parsed_data['location']),
            'qt_memo' => addslashes($parsed_data['memo']),
            'qt_filepath' => $saved_file_path,
            'qt_filename' => $saved_filename,
            'qt_status' => 'active'
        );
        
        if ($qt_id) {
            // 수정
            update_quality_template($qt_id, $template_data);
            // 기존 항목 삭제 후 재등록
            delete_quality_items($qt_id);
            $new_qt_id = $qt_id;
        } else {
            // 신규 등록
            $new_qt_id = insert_quality_template($template_data);
        }
        
        // 검사 항목 등록
        if (isset($parsed_data['items']) && is_array($parsed_data['items'])) {
            foreach ($parsed_data['items'] as $item) {
                $item_data = array(
                    'qt_id' => $new_qt_id,
                    'qi_item_id' => addslashes($item['item_id']),
                    'qi_procedure' => addslashes($item['procedure']),
                    'qi_item' => addslashes($item['item']),
                    'qi_method' => addslashes($item['method']),
                    'qi_standard' => addslashes($item['standard']),
                    'qi_unit' => addslashes($item['unit']),
                    'qi_order' => $item['order']
                );
                insert_quality_item($item_data);
            }
        }
        
        alert('템플릿이 성공적으로 등록되었습니다.\n템플릿 ID: ' . $new_qt_id . '\n검사 항목: ' . count($parsed_data['items']) . '개', './template_list.php');
    } else {
        // 엑셀 파일인 경우 (PhpSpreadsheet 라이브러리 필요)
        // 템플릿 파일 정보를 세션에 저장 (파싱 후 DB 저장 시 사용)
        $_SESSION['quality_template_file'] = array(
            'filepath' => $saved_file_path,
            'filename' => $saved_filename,
            'original_name' => $file['name'],
            'size' => $file['size']
        );
        
        alert('템플릿 파일이 저장되었습니다. (' . $saved_filename . ')\n엑셀 파일 파싱 기능은 PhpSpreadsheet 라이브러리 설치 후 구현 예정입니다.\nCSV 파일은 자동으로 파싱되어 등록됩니다.', './template_list.php');
    }
}

$g5['title'] = $qt_id ? '템플릿 수정' : '템플릿 등록';
add_stylesheet('<link rel="stylesheet" href="'.G5_QUALITY_URL.'/common_style.css">', 0);
include_once(G5_PATH.'/head_simple.php');
?>

<style>
.upload-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}
.upload-form {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.form-group input, .form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}
.form-group textarea {
    min-height: 100px;
    resize: vertical;
}
.form-group .help-text {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}
.btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 40px;
    padding: 10px 24px;
    background: #28a745;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    vertical-align: middle;
    line-height: 1.2;
    box-sizing: border-box;
}
.btn-submit:hover {
    background: #218838;
}
.upload-notice {
    background: #fff3cd;
    border: 1px solid #ffc107;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}
.upload-notice h4 {
    margin-top: 0;
    color: #856404;
}
.upload-notice ul {
    margin: 10px 0;
    padding-left: 20px;
}
</style>

<div class="upload-container">
    <h2><?php echo $qt_id ? '템플릿 수정' : '템플릿 등록'; ?></h2>
    
    <div class="upload-notice">
        <h4>📋 파일 형식 안내</h4>
        <ul>
            <li>엑셀(.xlsx, .xls) 또는 CSV 파일만 업로드 가능합니다.</li>
            <li>파일 크기는 최대 10MB입니다.</li>
            <li><strong>첫 번째 행</strong>: 문서ID (1행 1열에 직접 입력) / 검사표제목 / (제목 내용) / 문서위치 / (문서 위치 경로) / 메모 / (메모 내용)</li>
            <li><strong>두 번째 행</strong>: 관리자 / 검사자 / (검사자 이름) / 검수자 / (검수자 이름) / 최종결제자 / (최종결제자 이름)</li>
            <li><strong>세 번째 행</strong>: 일시 / 검사일 / (검사일 직접입력 혹은 달력에서 선택) / 검수일 / (검수일 직접입력 혹은 달력에서 선택) / 최종결제일 / (최종결제일 직접입력 혹은 달력에서 선택)</li>
            <li><strong>네 번째 행</strong>: ID, 검사절차명, 검사항목, 검사방법, 품질기준, 단위, 검사결과, 첨부파일, 비고 (메뉴명 배치)
                <ul style="margin-top: 5px; padding-left: 20px;">
                    <li>검사결과: √ (체크박스 여부 - 합격/불합격)</li>
                    <li>첨부파일: '없음' 또는 '📷' (사진 첨부 여부)</li>
                </ul>
            </li>
            <li><strong>다섯 번째 행부터</strong>: 검사 항목 데이터</li>
        </ul>
        <p style="margin: 10px 0 0; color: #666; font-size: 13px;"><strong>※ 참고:</strong> 웹의 경우 입력일은 사용자의 입력을 받고, 수정일은 DB에 자동 기록됩니다.</p>
        <p style="margin: 10px 0 0; color: #28a745; font-size: 13px;"><strong>✅ CSV 파일:</strong> 자동으로 파싱되어 템플릿으로 등록됩니다.</p>
        <p style="margin: 10px 0 0; color: #856404; font-size: 13px;"><strong>⚠️ 엑셀 파일:</strong> PhpSpreadsheet 라이브러리 설치 후 지원 예정입니다.</p>
        <p style="margin: 10px 0 0; color: #28a745; font-size: 13px;"><strong>✅ CSV 파일:</strong> 자동으로 파싱되어 템플릿으로 등록됩니다.</p>
        <p style="margin: 10px 0 0; color: #856404; font-size: 13px;"><strong>⚠️ 엑셀 파일:</strong> PhpSpreadsheet 라이브러리 설치 후 지원 예정입니다.</p>
    </div>
    
    <div class="upload-form">
        <form method="post" enctype="multipart/form-data">
            <?php if ($template) { ?>
            <div class="form-group">
                <label>템플릿 ID</label>
                <input type="text" value="<?php echo $template['qt_id']; ?>" readonly>
                <input type="hidden" name="qt_id" value="<?php echo $template['qt_id']; ?>">
            </div>
            <?php } ?>
            
            <div class="form-group">
                <label>템플릿 파일 <span style="color: red;">*</span></label>
                <input type="file" name="template_file" accept=".xlsx,.xls,.csv" required>
                <div class="help-text">엑셀(.xlsx, .xls) 또는 CSV 파일을 선택하세요.</div>
            </div>
            
            <div style="text-align: center; margin-top: 30px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                <button type="submit" class="btn-submit"><?php echo $qt_id ? '수정' : '등록'; ?></button>
                <a href="./template_list.php" class="btn-submit" style="background: #6c757d; text-decoration: none;">취소</a>
            </div>
        </form>
    </div>
</div>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

