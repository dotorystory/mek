<?php
include_once('./_common.php');

// 검수자 권한 체크 (레벨 4 이상)
if ($member['mb_level'] < 4) {
    alert('검수자는 회원 레벨 4 이상만 사용할 수 있습니다.', G5_URL);
}

$qr_id = isset($_GET['qr_id']) ? (int)$_GET['qr_id'] : 0;
if (isset($_POST['qr_id'])) {
    $qr_id = (int)$_POST['qr_id'];
}

if (!$qr_id) {
    alert('결과 ID가 없습니다.', './review.php');
}

$result = get_quality_result($qr_id);
if (!$result) {
    alert('검사 결과를 찾을 수 없습니다.', './review.php');
}

if ($result['qr_status'] != 'inspected') {
    alert('검수 대기 상태가 아닙니다.', './review.php');
}

// 검사 결과 상세 조회
$details = get_quality_result_details($qr_id);

// 검수 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action == 'approve' || $action == 'reject') {
        // 각 항목별 검수자 확인 처리
        if (isset($_POST['reviewer_check'])) {
            foreach ($_POST['reviewer_check'] as $qrd_id => $check) {
                $check_value = $check == 'Y' ? 'Y' : 'N';
                $sql = "UPDATE {$g5['quality_result_detail_table']} SET qrd_reviewer_check = '{$check_value}' WHERE qrd_id = '{$qrd_id}'";
                sql_query($sql);
            }
        }
        
        // 결제 이력 등록
        $approval_data = array(
            'qr_id' => $qr_id,
            'qa_step' => 'reviewer',
            'qa_status' => $action == 'approve' ? 'approved' : 'rejected',
            'qa_comment' => isset($_POST['qa_comment']) ? trim($_POST['qa_comment']) : ''
        );
        
        insert_quality_approval($approval_data);
        
        // 검사 결과 상태 업데이트
        $new_status = $action == 'approve' ? 'reviewed' : 'rejected';
        $approver_info = array(
            'reviewer' => $member['mb_id'],
            'reviewer_name' => $member['mb_name']
        );
        
        update_quality_result_status($qr_id, $new_status, $approver_info);
        
        if ($action == 'approve') {
            alert('검수가 완료되었습니다.', './review.php');
        } else {
            alert('검사 결과가 반려되었습니다.', './review.php');
        }
    }
}

$g5['title'] = '검수 처리 - ' . $result['qr_title'];
add_stylesheet('<link rel="stylesheet" href="'.G5_QUALITY_URL.'/common_style.css">', 0);
include_once(G5_PATH.'/head_simple.php');
?>

<style>
.review-save-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}
.review-form {
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
.item-info {
    margin-bottom: 10px;
}
.item-info label {
    display: inline-block;
    width: 120px;
    font-weight: bold;
    color: #666;
}
.review-check {
    margin-top: 10px;
}
.review-check input[type="checkbox"] {
    width: 20px;
    height: 20px;
    margin-right: 5px;
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

<div class="review-save-container">
    <h2>검수 처리</h2>
    
    <div class="review-form">
        <form method="post" action="">
            <input type="hidden" name="qr_id" value="<?php echo $qr_id; ?>">
            
            <div style="background: #e7f3ff; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <h4 style="margin-top: 0;">검사 정보</h4>
                <p><strong>제목:</strong> <?php echo htmlspecialchars($result['qr_title']); ?></p>
                <p><strong>검사자:</strong> <?php echo htmlspecialchars($result['qr_inspector_name']); ?></p>
                <p><strong>검사일시:</strong> <?php echo $result['qr_inspection_date'] ? date('Y-m-d H:i:s', strtotime($result['qr_inspection_date'])) : '-'; ?></p>
            </div>
            
            <h4>검사 항목별 검수</h4>
            <?php
            if (sql_num_rows($details) > 0) {
                while ($detail = sql_fetch_array($details)) {
            ?>
            <div class="item-row">
                <div class="item-header">
                    <span style="font-weight: bold; font-size: 18px;">항목 <?php echo htmlspecialchars($detail['qi_item_id']); ?></span>
                </div>
                
                <div class="item-info">
                    <div><label>검사절차명:</label> <?php echo htmlspecialchars($detail['qi_procedure']); ?></div>
                    <div><label>검사항목:</label> <?php echo htmlspecialchars($detail['qi_item']); ?></div>
                    <div><label>검사방법:</label> <?php echo nl2br(htmlspecialchars($detail['qi_method'])); ?></div>
                    <div><label>품질기준:</label> <?php echo nl2br(htmlspecialchars($detail['qi_standard'])); ?></div>
                    <div><label>단위:</label> <?php echo htmlspecialchars($detail['qi_unit']); ?></div>
                    <div><label>검사결과:</label> <strong><?php echo htmlspecialchars($detail['qrd_result']); ?></strong></div>
                    <div><label>비고:</label> <?php echo nl2br(htmlspecialchars($detail['qrd_note'])); ?></div>
                </div>
                
                <div class="review-check">
                    <label>
                        <input type="checkbox" name="reviewer_check[<?php echo $detail['qrd_id']; ?>]" value="Y" <?php echo $detail['qrd_reviewer_check'] == 'Y' ? 'checked' : ''; ?>>
                        검수자 확인
                    </label>
                </div>
            </div>
            <?php
                }
            }
            ?>
            
            <div style="margin-top: 20px;">
                <label>검수 의견</label>
                <textarea name="qa_comment" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="검수 의견을 입력하세요."></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="action" value="approve" class="btn btn-approve">승인</button>
                <button type="submit" name="action" value="reject" class="btn btn-reject">반려</button>
                <a href="./review.php" class="btn btn-cancel">취소</a>
            </div>
        </form>
    </div>
</div>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

