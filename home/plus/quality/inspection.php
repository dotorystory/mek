<?php
include_once('./_common.php');

// 템플릿 선택
$qt_id = isset($_GET['qt_id']) ? (int)$_GET['qt_id'] : 0;

if (!$qt_id) {
    // 템플릿 목록 표시
    $templates = get_quality_templates('active');
    
    $g5['title'] = '검사표 작성';
    add_stylesheet('<link rel="stylesheet" href="'.G5_QUALITY_URL.'/common_style.css">', 0);
    include_once(G5_PATH.'/head_simple.php');
    ?>
    
    <div style="max-width: 1200px; margin: 30px auto; padding: 20px;">
        <h2>검사표 작성</h2>
        <p style="margin-bottom: 20px;">검사할 템플릿을 선택하세요.</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php
            if (sql_num_rows($templates) > 0) {
                while ($template = sql_fetch_array($templates)) {
            ?>
            <div style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; background: #fff;">
                <h3 style="margin-top: 0;"><?php echo htmlspecialchars($template['qt_title']); ?></h3>
                <p><strong>문서ID:</strong> <?php echo htmlspecialchars($template['qt_doc_id']); ?></p>
                <p><strong>위치:</strong> <?php echo htmlspecialchars($template['qt_location']); ?></p>
                <?php if ($template['qt_memo']) { ?>
                <p><strong>메모:</strong> <?php echo nl2br(htmlspecialchars($template['qt_memo'])); ?></p>
                <?php } ?>
                <div style="margin-top: 10px; display: flex; gap: 5px;">
                    <a href="./inspection.php?qt_id=<?php echo $template['qt_id']; ?>" class="btn btn-info">검사 시작</a>
                    <?php if ($member['mb_level'] >= 3) { ?>
                    <a href="./template_export_csv.php?qt_id=<?php echo $template['qt_id']; ?>" class="btn btn-success">CSV 다운로드</a>
                    <?php } ?>
                </div>
            </div>
            <?php
                }
            } else {
            ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                등록된 템플릿이 없습니다. 관리자에게 문의하세요.
            </div>
            <?php } ?>
        </div>
        
        <div style="margin-top: 20px; text-align: right;">
            <a href="./index.php" class="btn btn-secondary">목록</a>
        </div>
    </div>
    
    <?php
    include_once(G5_PATH.'/tail_simple.php');
    exit;
}

// 기존 검사 결과 수정 모드 확인
$qr_id = isset($_GET['qr_id']) ? (int)$_GET['qr_id'] : 0;
$existing_result = null;
$existing_details = array();

if ($qr_id) {
    // 기존 검사 결과 조회
    $existing_result = get_quality_result($qr_id);
    if ($existing_result && $existing_result['qr_status'] == 'draft' && ($is_admin == 'super' || $existing_result['qr_inspector'] == $member['mb_id'])) {
        // 임시저장 상태이고 권한이 있는 경우 수정 모드
        $qt_id = $existing_result['qt_id']; // 템플릿 ID 재설정
        $existing_details_result = get_quality_result_details($qr_id);
        while ($detail = sql_fetch_array($existing_details_result)) {
            $existing_details[$detail['qi_id']] = $detail;
            // 기존 사진 로드
            $photos_result = get_quality_photos($detail['qrd_id']);
            $existing_details[$detail['qi_id']]['photos'] = array();
            while ($photo = sql_fetch_array($photos_result)) {
                $existing_details[$detail['qi_id']]['photos'][] = $photo;
            }
        }
    } else {
        alert('수정할 수 없는 검사 결과입니다.', './result_list.php');
    }
}

// 템플릿 정보 조회
$template = get_quality_template($qt_id);
if (!$template) {
    alert('템플릿을 찾을 수 없습니다.', './inspection.php');
}

// 검사 항목 목록 조회
$items = get_quality_items($qt_id);

$g5['title'] = '검사표 작성 - ' . $template['qt_title'];
add_stylesheet('<link rel="stylesheet" href="'.G5_QUALITY_URL.'/common_style.css">', 0);
include_once(G5_PATH.'/head_simple.php');
?>

<style>
.inspection-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}
.template-header {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.inspection-form {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.item-row {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background: #f8f9fa;
}
.item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 2px solid #dee2e6;
}
.item-id {
    font-weight: bold;
    font-size: 18px;
    color: #007bff;
}
.item-info {
    margin-bottom: 10px;
}
.item-info label {
    display: inline-block;
    width: 120px;
    font-weight: bold;
    color: #666;
}
.item-input {
    margin-top: 10px;
}
.item-input label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.item-input input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}
.photo-upload {
    margin-top: 10px;
}
.photo-preview {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 10px;
}
.photo-thumb {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.form-actions {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #dee2e6;
}
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 40px;
    padding: 10px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    text-decoration: none;
    vertical-align: middle;
    line-height: 1.2;
    box-sizing: border-box;
}
.btn-save {
    background: #ffc107;
    color: #333;
}
.btn-submit {
    background: #28a745;
    color: #fff;
}
.btn-cancel {
    background: #6c757d;
    color: #fff;
}
</style>

<div class="inspection-container">
    <h2>검사표 작성</h2>
    
    <!-- 검사 정보 입력 -->
    <div class="inspection-form">
        <form method="post" action="./inspection_save.php" id="inspectionForm" enctype="multipart/form-data">
            <input type="hidden" name="qt_id" value="<?php echo $qt_id; ?>">
            
            <?php if ($qr_id) { ?>
            <input type="hidden" name="qr_id" value="<?php echo $qr_id; ?>">
            <div style="background: #fff3cd; padding: 15px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #ffc107;">
                <strong>⚠️ 수정 모드:</strong> 임시저장된 검사 결과를 수정하고 있습니다.
            </div>
            <?php } ?>
            
            <!-- 템플릿 기본 정보 (수정 가능) -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #dee2e6;">
                <h4 style="margin-top: 0; margin-bottom: 15px;">템플릿 정보</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div>
                        <label>검사표제목 <span style="color: red;">*</span></label>
                        <input type="text" name="qr_title" value="<?php echo $existing_result ? htmlspecialchars($existing_result['qr_title']) : htmlspecialchars($template['qt_title']); ?>" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label>문서ID <span style="color: red;">*</span></label>
                        <input type="text" name="qr_doc_id" value="<?php echo $existing_result ? htmlspecialchars($existing_result['qr_doc_id']) : ($template['qt_doc_id'] . '_' . date('YmdHis')); ?>" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                        <small style="color: #666; font-size: 12px;">※ 템플릿의 문서ID에 날짜시간이 자동 추가됩니다. 필요시 수정 가능합니다.</small>
                    </div>
                    <div>
                        <label>위치</label>
                        <input type="text" name="qr_location" value="<?php echo $existing_result ? htmlspecialchars($existing_result['qr_location']) : htmlspecialchars($template['qt_location']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label>회사명</label>
                        <input type="text" name="qr_company" value="<?php echo $existing_result ? htmlspecialchars($existing_result['qr_company']) : ''; ?>" placeholder="회사명을 입력하세요." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label>제품구분</label>
                        <input type="text" name="qr_division" value="<?php echo $existing_result ? htmlspecialchars($existing_result['qr_division']) : ''; ?>" placeholder="제품구분을 입력하세요." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                    </div>
                </div>
                <div style="margin-top: 15px;">
                    <label>메모</label>
                    <input type="text" name="qr_memo" value="<?php echo $existing_result ? htmlspecialchars($existing_result['qr_memo']) : htmlspecialchars($template['qt_memo']); ?>" placeholder="메모를 입력하세요." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                </div>
            </div>
            
            <!-- 검사 정보 -->
            <div style="background: #e7f3ff; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <h4 style="margin-top: 0;">검사 정보</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div>
                        <label>검사일시 <span style="color: red;">*</span></label>
                        <input type="datetime-local" name="qr_inspection_date" value="<?php echo $existing_result ? date('Y-m-d\TH:i', strtotime($existing_result['qr_inspection_date'])) : date('Y-m-d\TH:i'); ?>" required>
                    </div>
                    <div>
                        <label>검사자</label>
                        <input type="text" value="<?php echo htmlspecialchars($member['mb_name']); ?>" readonly>
                        <input type="hidden" name="qr_inspector" value="<?php echo $member['mb_id']; ?>">
                    </div>
                </div>
            </div>
            
            <!-- 검사 항목 목록 -->
            <h4>검사 항목</h4>
            <?php
            if (sql_num_rows($items) > 0) {
                $order = 0;
                while ($item = sql_fetch_array($items)) {
                    $order++;
            ?>
            <div class="item-row" data-item-id="<?php echo $item['qi_id']; ?>">
                <div class="item-header">
                    <span class="item-id">항목 <?php echo $item['qi_item_id'] ?: $order; ?></span>
                    <label style="margin-left: 10px; display: inline-flex; align-items: center;">
                        <input type="checkbox" name="reviewer_check[<?php echo $item['qi_id']; ?>]" value="Y" <?php echo (isset($existing_details[$item['qi_id']]) && $existing_details[$item['qi_id']]['qrd_reviewer_check'] == 'Y') ? 'checked' : ''; ?>>
                        <span style="margin-left: 5px;">확인완료</span>
                    </label>
                </div>
                
                <div class="item-info">
                    <div><label>검사절차명:</label> <?php echo htmlspecialchars($item['qi_procedure']); ?></div>
                    <div><label>검사항목:</label> <?php echo htmlspecialchars($item['qi_item']); ?></div>
                    <div><label>검사방법:</label> <?php echo nl2br(htmlspecialchars($item['qi_method'])); ?></div>
                    <div><label>품질기준:</label> <?php echo nl2br(htmlspecialchars($item['qi_standard'])); ?></div>
                    <div><label>단위:</label> <?php echo htmlspecialchars($item['qi_unit']); ?></div>
                </div>
                
                <div class="item-input">
                    <label>검사결과</label>
                    <input type="text" name="result[<?php echo $item['qi_id']; ?>]" value="<?php echo isset($existing_details[$item['qi_id']]) ? htmlspecialchars($existing_details[$item['qi_id']]['qrd_result']) : ''; ?>" placeholder="검사 결과를 입력하세요." style="width: 100%;">
                </div>
                
                <div class="item-input">
                    <label>비고</label>
                    <input type="text" name="note[<?php echo $item['qi_id']; ?>]" value="<?php echo isset($existing_details[$item['qi_id']]) ? htmlspecialchars($existing_details[$item['qi_id']]['qrd_note']) : ''; ?>" placeholder="비고를 입력하세요." style="width: 100%;">
                </div>
                
                <div class="photo-upload">
                    <label>사진 첨부 (최대 5장)</label>
                    <input type="file" name="photo[<?php echo $item['qi_id']; ?>][]" multiple accept="image/*" data-item-id="<?php echo $item['qi_id']; ?>">
                    <div class="photo-preview" id="preview_<?php echo $item['qi_id']; ?>">
                        <?php
                        // 기존 사진 표시
                        if (isset($existing_details[$item['qi_id']]['photos']) && !empty($existing_details[$item['qi_id']]['photos'])) {
                            foreach ($existing_details[$item['qi_id']]['photos'] as $photo) {
                                $photo_url = '';
                                if (!empty($photo['qp_thumbnail']) && file_exists($photo['qp_thumbnail'])) {
                                    $photo_url = str_replace(G5_QUALITY_UPLOAD_PATH, G5_QUALITY_UPLOAD_URL, $photo['qp_thumbnail']);
                                } elseif (!empty($photo['qp_filepath']) && file_exists($photo['qp_filepath'])) {
                                    $photo_url = str_replace(G5_QUALITY_UPLOAD_PATH, G5_QUALITY_UPLOAD_URL, $photo['qp_filepath']);
                                }
                                if ($photo_url) {
                                    echo '<img src="' . htmlspecialchars($photo_url) . '" class="photo-thumb" alt="기존 사진">';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <input type="hidden" name="item_order[<?php echo $item['qi_id']; ?>]" value="<?php echo $order; ?>">
            </div>
            <?php
                }
            } else {
            ?>
            <div style="text-align: center; padding: 40px;">
                등록된 검사 항목이 없습니다.
            </div>
            <?php } ?>
            
            <div class="form-actions">
                <button type="submit" name="action" value="draft" class="btn btn-save">임시 저장</button>
                <button type="submit" name="action" value="submit" class="btn btn-submit">제출</button>
                <a href="./inspection.php" class="btn btn-cancel">취소</a>
            </div>
        </form>
    </div>
</div>

<script>
// 사진 미리보기
document.querySelectorAll('input[type="file"]').forEach(function(input) {
    input.addEventListener('change', function(e) {
        var itemId = this.getAttribute('data-item-id');
        var preview = document.getElementById('preview_' + itemId);
        preview.innerHTML = '';
        
        if (this.files.length > 5) {
            alert('최대 5장까지만 첨부할 수 있습니다.');
            this.value = '';
            return;
        }
        
        Array.from(this.files).forEach(function(file) {
            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'photo-thumb';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    });
});
</script>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

