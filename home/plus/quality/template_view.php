<?php
include_once('./_common.php');

// 레벨 3 이상만 접근 가능
if ($member['mb_level'] < 3) {
    alert('템플릿 보기는 회원 레벨 3 이상만 사용할 수 있습니다.', G5_URL);
}

$qt_id = isset($_GET['qt_id']) ? (int)$_GET['qt_id'] : 0;

if (!$qt_id) {
    alert('템플릿 ID가 없습니다.', './template_list.php');
}

$template = get_quality_template($qt_id);
if (!$template) {
    alert('템플릿을 찾을 수 없습니다.', './template_list.php');
}

// 검사 항목 목록 조회
$items = get_quality_items($qt_id);

$g5['title'] = '템플릿 상세';
add_stylesheet('<link rel="stylesheet" href="'.G5_QUALITY_URL.'/common_style.css">', 0);
include_once(G5_PATH.'/head_simple.php');
?>

<style>
.template-view-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}
.template-info {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.template-info h3 {
    margin-top: 0;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 10px;
}
.info-row {
    display: flex;
    margin-bottom: 10px;
}
.info-label {
    width: 150px;
    font-weight: bold;
    color: #666;
}
.info-value {
    flex: 1;
}
.items-table {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.items-table table {
    width: 100%;
    border-collapse: collapse;
}
.items-table th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #dee2e6;
    font-weight: bold;
}
.items-table td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
}
.items-table tr:hover {
    background: #f8f9fa;
}
</style>

<div class="template-view-container">
    <h2>템플릿 상세</h2>
    
    <!-- 템플릿 정보 -->
    <div class="template-info">
        <h3>템플릿 정보</h3>
        <div class="info-row">
            <div class="info-label">템플릿 ID</div>
            <div class="info-value"><?php echo $template['qt_id']; ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">문서 ID</div>
            <div class="info-value"><?php echo htmlspecialchars($template['qt_doc_id']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">제목</div>
            <div class="info-value"><?php echo htmlspecialchars($template['qt_title']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">위치</div>
            <div class="info-value"><?php echo htmlspecialchars($template['qt_location']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">메모</div>
            <div class="info-value"><?php echo nl2br(htmlspecialchars($template['qt_memo'])); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">상태</div>
            <div class="info-value">
                <span style="color: <?php echo $template['qt_status'] == 'active' ? '#28a745' : '#dc3545'; ?>; font-weight: bold;">
                    <?php echo $template['qt_status'] == 'active' ? '활성' : '비활성'; ?>
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">생성일</div>
            <div class="info-value"><?php echo date('Y-m-d H:i:s', strtotime($template['qt_created_at'])); ?></div>
        </div>
        <?php if ($template['qt_updated_at']) { ?>
        <div class="info-row">
            <div class="info-label">수정일</div>
            <div class="info-value"><?php echo date('Y-m-d H:i:s', strtotime($template['qt_updated_at'])); ?></div>
        </div>
        <?php } ?>
    </div>
    
    <!-- 검사 항목 목록 -->
    <div class="items-table">
        <h3 style="padding: 20px 20px 10px; margin: 0;">검사 항목 (<?php echo sql_num_rows($items); ?>개)</h3>
        <table>
            <thead>
                <tr>
                    <th>순서</th>
                    <th>항목ID</th>
                    <th>검사절차명</th>
                    <th>검사항목</th>
                    <th>검사방법</th>
                    <th>품질기준</th>
                    <th>단위</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (sql_num_rows($items) > 0) {
                    while ($item = sql_fetch_array($items)) {
                ?>
                <tr>
                    <td><?php echo $item['qi_order']; ?></td>
                    <td><?php echo htmlspecialchars($item['qi_item_id']); ?></td>
                    <td><?php echo htmlspecialchars($item['qi_procedure']); ?></td>
                    <td><?php echo htmlspecialchars($item['qi_item']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($item['qi_method'])); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($item['qi_standard'])); ?></td>
                    <td><?php echo htmlspecialchars($item['qi_unit']); ?></td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">등록된 검사 항목이 없습니다.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px; text-align: right;">
        <a href="./template_export_csv.php?qt_id=<?php echo $qt_id; ?>" class="btn btn-success">CSV 다운로드</a>
        <?php if ($member['mb_level'] >= 5) { ?>
        <a href="./template_upload.php?qt_id=<?php echo $qt_id; ?>" class="btn btn-warning">수정</a>
        <?php } ?>
        <a href="./template_list.php" class="btn btn-secondary">목록</a>
    </div>
</div>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

