<?php
include_once('./_common.php');

// 최종결제자 권한 체크 (레벨 5 이상)
if ($member['mb_level'] < 5) {
    alert('최종결제자는 회원 레벨 5 이상만 사용할 수 있습니다.', G5_URL);
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// 최종결제 대기 목록 조회
$approval_list = get_quality_approval_list($per_page, $offset);

// 전체 개수
$total_count = sql_fetch("SELECT COUNT(*) as cnt FROM {$g5['quality_result_table']} WHERE qr_status = 'reviewed'");
$total_count = $total_count['cnt'];
$total_page = ceil($total_count / $per_page);

$g5['title'] = '최종결제 대기 목록';
add_stylesheet('<link rel="stylesheet" href="'.G5_QUALITY_URL.'/common_style.css">', 0);
include_once(G5_PATH.'/head_simple.php');
?>

<style>
.approval-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}
.approval-list {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.approval-list table {
    width: 100%;
    border-collapse: collapse;
}
.approval-list th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #dee2e6;
    font-weight: bold;
}
.approval-list td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
}
.approval-list tr:hover {
    background: #f8f9fa;
}
.btn-approval {
    padding: 5px 10px;
    background: #6f42c1;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    font-size: 12px;
}
</style>

<div class="approval-container">
    <h2>최종결제 대기 목록</h2>
    <p style="margin-bottom: 20px;">검수 완료된 검사표를 최종 결제합니다.</p>
    
    <div class="approval-list">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>문서ID</th>
                    <th>제목</th>
                    <th>검사자</th>
                    <th>검수자</th>
                    <th>검사일시</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (sql_num_rows($approval_list) > 0) {
                    while ($row = sql_fetch_array($approval_list)) {
                ?>
                <tr>
                    <td><?php echo $row['qr_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['qr_doc_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['qr_title']); ?></td>
                    <td><?php echo htmlspecialchars($row['qr_inspector_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['qr_reviewer_name']); ?></td>
                    <td><?php echo $row['qr_inspection_date'] ? date('Y-m-d H:i', strtotime($row['qr_inspection_date'])) : '-'; ?></td>
                    <td>
                        <a href="./approval_save.php?qr_id=<?php echo $row['qr_id']; ?>" class="btn-approval">결제하기</a>
                    </td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">최종결제 대기 중인 검사표가 없습니다.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <!-- 페이징 -->
    <div class="pagination" style="margin-top: 20px; text-align: center;">
        <?php
        if ($total_page > 0) {
            if ($page > 1) {
                echo '<a href="?page='.($page-1).'">이전</a> ';
            }
            
            for ($i = 1; $i <= $total_page; $i++) {
                $active = $i == $page ? 'active' : '';
                echo '<a href="?page='.$i.'" class="'.$active.'">'.$i.'</a> ';
            }
            
            if ($page < $total_page) {
                echo '<a href="?page='.($page+1).'">다음</a>';
            }
        }
        ?>
    </div>
    
    <div style="margin-top: 20px; text-align: right;">
        <a href="./index.php" class="btn btn-secondary">목록</a>
    </div>
</div>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

