<?php
include_once('./_common.php');

// 최종결제자 권한 체크 (레벨 5 이상)
if ($member['mb_level'] < 5) {
    alert('최종결제자는 회원 레벨 5 이상만 사용할 수 있습니다.', G5_URL);
}

$qr_id = isset($_GET['qr_id']) ? (int)$_GET['qr_id'] : 0;
if (isset($_POST['qr_id'])) {
    $qr_id = (int)$_POST['qr_id'];
}

if (!$qr_id) {
    alert('결과 ID가 없습니다.', './approval.php');
}

$result = get_quality_result($qr_id);
if (!$result) {
    alert('검사 결과를 찾을 수 없습니다.', './approval.php');
}

if ($result['qr_status'] != 'reviewed') {
    alert('최종결제 대기 상태가 아닙니다.', './approval.php');
}

// 최종결제 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action == 'approve' || $action == 'reject') {
        // 결제 이력 등록
        $approval_data = array(
            'qr_id' => $qr_id,
            'qa_step' => 'final',
            'qa_status' => $action == 'approve' ? 'approved' : 'rejected',
            'qa_comment' => isset($_POST['qa_comment']) ? trim($_POST['qa_comment']) : ''
        );
        
        insert_quality_approval($approval_data);
        
        // 검사 결과 상태 업데이트
        $new_status = $action == 'approve' ? 'approved' : 'rejected';
        $approver_info = array(
            'final_approver' => $member['mb_id'],
            'final_approver_name' => $member['mb_name']
        );
        
        update_quality_result_status($qr_id, $new_status, $approver_info);
        
        if ($action == 'approve') {
            alert('최종 결제가 완료되었습니다.', './approval.php');
        } else {
            alert('검사 결과가 반려되었습니다.', './approval.php');
        }
    }
}

// 검사 결과 상세 조회
$details = get_quality_result_details($qr_id);

// 결제 이력 조회
$approvals = get_quality_approvals($qr_id);

$g5['title'] = '최종결제 처리 - ' . $result['qr_title'];
add_stylesheet('<link rel="stylesheet" href="'.G5_QUALITY_URL.'/common_style.css">', 0);
include_once(G5_PATH.'/head_simple.php');
?>

<style>
.approval-save-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}
.approval-form {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.result-summary {
    background: #e7f3ff;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}
.details-table {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
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
.btn-approve {
    background: #28a745;
    color: #fff;
}
.btn-reject {
    background: #dc3545;
    color: #fff;
}
.btn-cancel {
    background: #6c757d;
    color: #fff;
}
</style>

<div class="approval-save-container">
    <h2>최종결제 처리</h2>
    
    <div class="approval-form">
        <form method="post" action="">
            <input type="hidden" name="qr_id" value="<?php echo $qr_id; ?>">
            
            <div class="result-summary">
                <h4 style="margin-top: 0;">검사 정보</h4>
                <p><strong>제목:</strong> <?php echo htmlspecialchars($result['qr_title']); ?></p>
                <p><strong>검사자:</strong> <?php echo htmlspecialchars($result['qr_inspector_name']); ?></p>
                <p><strong>검수자:</strong> <?php echo htmlspecialchars($result['qr_reviewer_name']); ?></p>
                <p><strong>검사일시:</strong> <?php echo $result['qr_inspection_date'] ? date('Y-m-d H:i:s', strtotime($result['qr_inspection_date'])) : '-'; ?></p>
            </div>
            
            <h4>검사 항목별 결과</h4>
            <div class="details-table">
                <table>
                    <thead>
                        <tr>
                            <th>항목ID</th>
                            <th>검사항목</th>
                            <th>검사결과</th>
                            <th>검수자확인</th>
                            <th>비고</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (sql_num_rows($details) > 0) {
                            while ($detail = sql_fetch_array($details)) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detail['qi_item_id']); ?></td>
                            <td><?php echo htmlspecialchars($detail['qi_item']); ?></td>
                            <td><strong><?php echo htmlspecialchars($detail['qrd_result']); ?></strong></td>
                            <td><?php echo $detail['qrd_reviewer_check'] == 'Y' ? '✓' : ''; ?></td>
                            <td><?php echo nl2br(htmlspecialchars($detail['qrd_note'])); ?></td>
                        </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (sql_num_rows($approvals) > 0) { ?>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <h4>결제 이력</h4>
                <?php
                while ($approval = sql_fetch_array($approvals)) {
                    $step_text = array(
                        'inspector' => '검사자',
                        'reviewer' => '검수자',
                        'final' => '최종결제자'
                    );
                    $status_text = $approval['qa_status'] == 'approved' ? '승인' : '반려';
                ?>
                <p style="margin: 5px 0;">
                    <strong><?php echo $step_text[$approval['qa_step']]; ?>:</strong> 
                    <?php echo htmlspecialchars($approval['qa_approver_name']); ?> - 
                    <span style="color: <?php echo $approval['qa_status'] == 'approved' ? '#28a745' : '#dc3545'; ?>;">
                        <?php echo $status_text; ?>
                    </span>
                    (<?php echo date('Y-m-d H:i', strtotime($approval['qa_approved_at'])); ?>)
                </p>
                <?php } ?>
            </div>
            <?php } ?>
            
            <div style="margin-top: 20px;">
                <label>결제 의견</label>
                <textarea name="qa_comment" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="결제 의견을 입력하세요."></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="action" value="approve" class="btn btn-approve">최종 승인</button>
                <button type="submit" name="action" value="reject" class="btn btn-reject">반려</button>
                <a href="./approval.php" class="btn btn-cancel">취소</a>
            </div>
        </form>
    </div>
</div>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

