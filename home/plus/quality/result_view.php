<?php
include_once('./_common.php');

$qr_id = isset($_GET['qr_id']) ? (int)$_GET['qr_id'] : 0;

if (!$qr_id) {
    alert('결과 ID가 없습니다.', './result_list.php');
}

$result = get_quality_result($qr_id);
if (!$result) {
    alert('검사 결과를 찾을 수 없습니다.', './result_list.php');
}

// 권한 체크
if ($member['mb_level'] >= 5) {
    // 레벨 5 이상: 모든 검사/검수/결제 결과 조회 가능
    // 권한 체크 없음
} elseif ($member['mb_level'] >= 4) {
    // 레벨 4: 검사/검수 결과보기 리스트 조회 가능
    // 검사 완료(inspected) 또는 검수 완료(reviewed) 상태이거나 본인이 작성한 결과
    if (!in_array($result['qr_status'], array('inspected', 'reviewed')) && $result['qr_inspector'] != $member['mb_id']) {
        alert('검사 완료 또는 검수 완료된 결과만 조회할 수 있습니다.', './result_list.php');
    }
} else {
    // 레벨 3: 검사 결과보기 리스트 조회만 가능 (본인이 작성한 것만)
    if ($result['qr_inspector'] != $member['mb_id']) {
        alert('본인이 작성한 검사 결과만 조회할 수 있습니다.', './result_list.php');
    }
}

// 검사 결과 상세 조회
$details = get_quality_result_details($qr_id);

// 결제 이력 조회
$approvals = get_quality_approvals($qr_id);

$g5['title'] = '검사 결과 상세 - ' . $result['qr_title'];
add_stylesheet('<link rel="stylesheet" href="'.G5_QUALITY_URL.'/common_style.css">', 0);
include_once(G5_PATH.'/head_simple.php');
?>

<style>
.result-view-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}
.result-info {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.result-info h3 {
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
.status-badge {
    display: inline-block;
    padding: 5px 15px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
}
.status-draft { background: #6c757d; color: #fff; }
.status-inspected { background: #007bff; color: #fff; }
.status-reviewed { background: #ffc107; color: #333; }
.status-approved { background: #28a745; color: #fff; }
.status-rejected { background: #dc3545; color: #fff; }
.details-table {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}
.details-table table {
    width: 100%;
    border-collapse: collapse;
}
.details-table th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #dee2e6;
    font-weight: bold;
}
.details-table td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: top;
}
.details-table tr:hover {
    background: #f8f9fa;
}
.photo-icon {
    font-size: 20px;
    cursor: pointer;
    color: #007bff;
}
.photo-icon:hover {
    color: #0056b3;
}
.approval-history {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.approval-item {
    padding: 15px;
    border-left: 4px solid #007bff;
    margin-bottom: 10px;
    background: #f8f9fa;
}
</style>

<div class="result-view-container">
    <h2>검사 결과 상세</h2>
    
    <!-- 검사 결과 정보 -->
    <div class="result-info">
        <h3>검사 정보</h3>
        <div class="info-row">
            <div class="info-label">결과 ID</div>
            <div class="info-value"><?php echo $result['qr_id']; ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">문서 ID</div>
            <div class="info-value"><?php echo htmlspecialchars($result['qr_doc_id']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">제목</div>
            <div class="info-value"><?php echo htmlspecialchars($result['qr_title']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">위치</div>
            <div class="info-value"><?php echo htmlspecialchars($result['qr_location']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">검사일시</div>
            <div class="info-value"><?php echo $result['qr_inspection_date'] ? date('Y-m-d H:i:s', strtotime($result['qr_inspection_date'])) : '-'; ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">검사자</div>
            <div class="info-value"><?php echo htmlspecialchars($result['qr_inspector_name']); ?></div>
        </div>
        <?php if ($result['qr_reviewer_name']) { ?>
        <div class="info-row">
            <div class="info-label">검수자</div>
            <div class="info-value"><?php echo htmlspecialchars($result['qr_reviewer_name']); ?></div>
        </div>
        <?php } ?>
        <?php if ($result['qr_final_approver_name']) { ?>
        <div class="info-row">
            <div class="info-label">최종결제자</div>
            <div class="info-value"><?php echo htmlspecialchars($result['qr_final_approver_name']); ?></div>
        </div>
        <?php } ?>
        <div class="info-row">
            <div class="info-label">상태</div>
            <div class="info-value">
                <span class="status-badge status-<?php echo $result['qr_status']; ?>">
                    <?php
                    $status_text = array(
                        'draft' => '임시저장',
                        'inspected' => '검사완료',
                        'reviewed' => '검수완료',
                        'approved' => '최종승인',
                        'rejected' => '반려'
                    );
                    echo $status_text[$result['qr_status']];
                    ?>
                </span>
            </div>
        </div>
        <?php if ($result['qr_memo']) { ?>
        <div class="info-row">
            <div class="info-label">메모</div>
            <div class="info-value"><?php echo nl2br(htmlspecialchars($result['qr_memo'])); ?></div>
        </div>
        <?php } ?>
    </div>
    
    <!-- 검사 항목별 결과 -->
    <div class="details-table">
        <h3 style="padding: 20px 20px 10px; margin: 0;">검사 항목별 결과</h3>
        <table>
            <thead>
                <tr>
                    <th>항목ID</th>
                    <th>검사절차명</th>
                    <th>검사항목</th>
                    <th>검사방법</th>
                    <th>품질기준</th>
                    <th>단위</th>
                    <th>검사결과</th>
                    <th>검수자확인</th>
                    <th>사진</th>
                    <th>비고</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (sql_num_rows($details) > 0) {
                    while ($detail = sql_fetch_array($details)) {
                        // 사진 조회
                        $photos = get_quality_photos($detail['qrd_id']);
                        $photo_count = sql_num_rows($photos);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($detail['qi_item_id']); ?></td>
                    <td><?php echo htmlspecialchars($detail['qi_procedure']); ?></td>
                    <td><?php echo htmlspecialchars($detail['qi_item']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($detail['qi_method'])); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($detail['qi_standard'])); ?></td>
                    <td><?php echo htmlspecialchars($detail['qi_unit']); ?></td>
                    <td><?php echo htmlspecialchars($detail['qrd_result']); ?></td>
                    <td><?php echo $detail['qrd_reviewer_check'] == 'Y' ? '✓' : ''; ?></td>
                    <td>
                        <?php if ($photo_count > 0) { ?>
                        <span class="photo-icon" onclick="viewPhotos(<?php echo $detail['qrd_id']; ?>)">📷 (<?php echo $photo_count; ?>)</span>
                        <?php } else { ?>
                        -
                        <?php } ?>
                    </td>
                    <td><?php echo nl2br(htmlspecialchars($detail['qrd_note'])); ?></td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="10" style="text-align: center; padding: 40px;">등록된 검사 항목이 없습니다.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <!-- 결제 이력 -->
    <?php if (sql_num_rows($approvals) > 0) { ?>
    <div class="approval-history">
        <h3>결제 이력</h3>
        <?php
        while ($approval = sql_fetch_array($approvals)) {
            $step_text = array(
                'inspector' => '검사자',
                'reviewer' => '검수자',
                'final' => '최종결제자'
            );
            $status_text = $approval['qa_status'] == 'approved' ? '승인' : '반려';
            $status_color = $approval['qa_status'] == 'approved' ? '#28a745' : '#dc3545';
        ?>
        <div class="approval-item">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <strong><?php echo $step_text[$approval['qa_step']]; ?>: <?php echo htmlspecialchars($approval['qa_approver_name']); ?></strong>
                <span style="color: <?php echo $status_color; ?>; font-weight: bold;"><?php echo $status_text; ?></span>
            </div>
            <div style="color: #666; font-size: 12px;">
                <?php echo date('Y-m-d H:i:s', strtotime($approval['qa_approved_at'])); ?>
            </div>
            <?php if ($approval['qa_comment']) { ?>
            <div style="margin-top: 10px; padding: 10px; background: #fff; border-radius: 4px;">
                <?php echo nl2br(htmlspecialchars($approval['qa_comment'])); ?>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
    
    <div style="margin-top: 20px; text-align: right;">
        <a href="./result_export_csv.php?qr_id=<?php echo $qr_id; ?>" class="btn btn-success">받기</a>
        <?php if ($result['qr_status'] == 'draft' && ($is_admin == 'super' || $result['qr_inspector'] == $member['mb_id'])) { ?>
        <a href="./inspection.php?qt_id=<?php echo $result['qt_id']; ?>&qr_id=<?php echo $qr_id; ?>" class="btn btn-warning">수정</a>
        <?php } ?>
        <a href="./result_list.php" class="btn btn-secondary">목록</a>
    </div>
</div>

<script>
function viewPhotos(qrd_id) {
    window.open('./photo_view.php?qrd_id=' + qrd_id, 'photoViewer', 'width=800,height=600,scrollbars=yes');
}
</script>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

