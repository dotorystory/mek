<?php
/**
 * 파일 업로드/다운로드 로그 조회 (관리자 전용)
 * 
 * @author AI Assistant
 * @version 1.0
 * @date 2025-12-05
 */

include_once('./_common.php');

// 관리자만 접근 가능
if ($is_admin != 'super') {
    alert('관리자만 접근 가능합니다.', G5_URL);
}

include_once(G5_LIB_PATH.'/file_log.lib.php');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 50;
$offset = ($page - 1) * $per_page;

// 검색 조건
$where = " WHERE 1=1 ";
$search_mb_id = isset($_GET['search_mb_id']) ? trim($_GET['search_mb_id']) : '';
$search_type = isset($_GET['search_type']) ? trim($_GET['search_type']) : '';
$search_date_from = isset($_GET['search_date_from']) ? trim($_GET['search_date_from']) : '';
$search_date_to = isset($_GET['search_date_to']) ? trim($_GET['search_date_to']) : '';

if ($search_mb_id) {
    $where .= " AND fl_mb_id LIKE '%".sql_escape_string($search_mb_id)."%' ";
}
if ($search_type) {
    $where .= " AND fl_type = '".sql_escape_string($search_type)."' ";
}
if ($search_date_from) {
    $where .= " AND fl_datetime >= '{$search_date_from} 00:00:00' ";
}
if ($search_date_to) {
    $where .= " AND fl_datetime <= '{$search_date_to} 23:59:59' ";
}

// 전체 개수
$total_count = sql_fetch("SELECT COUNT(*) as cnt FROM g5_file_log {$where}");
$total_count = $total_count['cnt'];
$total_page = ceil($total_count / $per_page);

// 로그 조회
$sql = "SELECT l.*, m.mb_name 
        FROM g5_file_log l 
        LEFT JOIN {$g5['member_table']} m ON l.fl_mb_id = m.mb_id 
        {$where} 
        ORDER BY l.fl_datetime DESC 
        LIMIT {$offset}, {$per_page}";
$result = sql_query($sql);

$g5['title'] = '파일 업로드/다운로드 로그';
include_once(G5_PATH.'/head_simple.php');
?>

<style>
.log-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}
.log-search {
    background: #f5f5f5;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: flex-end;
}
.log-search input, .log-search select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 14px;
}
.log-search button {
    padding: 8px 20px;
    background: #2e6e84;
    color: #fff;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 14px;
}
.log-search button:hover {
    background: #28a745;
}
.log-search a {
    padding: 8px 20px;
    background: #6c757d;
    color: #fff;
    border-radius: 3px;
    text-decoration: none;
    font-size: 14px;
    display: inline-block;
    vertical-align: middle;
    line-height: 1.5;
}
.log-search a:hover {
    background: #5a6268;
}
.log-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.log-table th {
    background: linear-gradient(135deg, #1e7e34 0%, #28a745 100%);
    color: #fff;
    padding: 12px;
    text-align: left;
    font-weight: 500;
    white-space: nowrap;
}
.log-table td {
    padding: 10px;
    border-bottom: 1px solid #eee;
    font-size: 13px;
}
.log-table tr:hover {
    background: #f9f9f9;
}
.type-badge {
    padding: 4px 10px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
    display: inline-block;
}
.type-upload { background: #4CAF50; color: #fff; }
.type-download { background: #2196F3; color: #fff; }
.type-delete { background: #f44336; color: #fff; }
.type-access { background: #9E9E9E; color: #fff; }
.pagination {
    text-align: center;
    margin-top: 20px;
    padding: 20px;
}
.pagination a {
    padding: 8px 12px;
    margin: 0 2px;
    border: 1px solid #ddd;
    text-decoration: none;
    color: #333;
    border-radius: 3px;
    display: inline-block;
}
.pagination a.active {
    background: #2e6e84;
    color: #fff;
    border-color: #2e6e84;
}
.pagination a:hover:not(.active) {
    background: #f5f5f5;
}
.total-count {
    float: right;
    color: #666;
    font-size: 14px;
}
@media (max-width: 768px) {
    .log-table {
        font-size: 12px;
    }
    .log-table th, .log-table td {
        padding: 8px 5px;
    }
}
</style>

<div class="log-container">
    <h2>📋 파일 업로드/다운로드 로그</h2>

    <form method="get" class="log-search">
        <div>
            <label>회원 아이디</label>
            <input type="text" name="search_mb_id" placeholder="회원 아이디" value="<?php echo htmlspecialchars($search_mb_id); ?>">
        </div>
        <div>
            <label>작업 타입</label>
            <select name="search_type">
                <option value="">전체 타입</option>
                <option value="UPLOAD" <?php echo $search_type == 'UPLOAD' ? 'selected' : ''; ?>>업로드</option>
                <option value="DOWNLOAD" <?php echo $search_type == 'DOWNLOAD' ? 'selected' : ''; ?>>다운로드</option>
                <option value="DELETE" <?php echo $search_type == 'DELETE' ? 'selected' : ''; ?>>삭제</option>
                <option value="ACCESS" <?php echo $search_type == 'ACCESS' ? 'selected' : ''; ?>>접속</option>
            </select>
        </div>
        <div>
            <label>기간</label>
            <input type="date" name="search_date_from" value="<?php echo htmlspecialchars($search_date_from); ?>">
            ~
            <input type="date" name="search_date_to" value="<?php echo htmlspecialchars($search_date_to); ?>">
        </div>
        <div>
            <button type="submit">🔍 검색</button>
            <a href="?">초기화</a>
        </div>
        <div class="total-count">
            전체 <strong><?php echo number_format($total_count); ?></strong>건
        </div>
    </form>

    <table class="log-table">
        <thead>
            <tr>
                <th width="60">번호</th>
                <th width="120">회원</th>
                <th width="80">타입</th>
                <th>파일명</th>
                <th width="100">파일 크기</th>
                <th width="120">IP</th>
                <th width="150">일시</th>
                <th width="150">비고</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (sql_num_rows($result) > 0) {
                $num = $total_count - $offset;
                while ($row = sql_fetch_array($result)) {
                    $type_class = 'type-' . strtolower($row['fl_type']);
                    $filesize_mb = $row['fl_filesize'] > 0 ? round($row['fl_filesize'] / 1024 / 1024, 2) . ' MB' : '-';
                    
                    // 타입별 아이콘
                    $type_icon = '';
                    switch ($row['fl_type']) {
                        case 'UPLOAD': $type_icon = '⬆️'; break;
                        case 'DOWNLOAD': $type_icon = '⬇️'; break;
                        case 'DELETE': $type_icon = '🗑️'; break;
                        case 'ACCESS': $type_icon = '🔑'; break;
                    }
            ?>
            <tr>
                <td style="text-align: center;"><?php echo $num--; ?></td>
                <td>
                    <?php echo $row['mb_name'] ? htmlspecialchars($row['mb_name']) : htmlspecialchars($row['fl_mb_id']); ?>
                    <br><small style="color:#999;"><?php echo htmlspecialchars($row['fl_mb_id']); ?></small>
                </td>
                <td>
                    <span class="type-badge <?php echo $type_class; ?>">
                        <?php echo $type_icon . ' ' . $row['fl_type']; ?>
                    </span>
                </td>
                <td style="word-break: break-all;"><?php echo htmlspecialchars($row['fl_filename']); ?></td>
                <td style="text-align: right;"><?php echo $filesize_mb; ?></td>
                <td><?php echo htmlspecialchars($row['fl_ip']); ?></td>
                <td><?php echo $row['fl_datetime']; ?></td>
                <td><?php echo htmlspecialchars($row['fl_note']); ?></td>
            </tr>
            <?php
                }
            } else {
            ?>
            <tr>
                <td colspan="8" style="text-align: center; padding: 50px; color: #999;">로그가 없습니다.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- 페이징 -->
    <div class="pagination">
        <?php
        if ($total_page > 0) {
            $page_range = 10;
            $start_page = max(1, $page - floor($page_range / 2));
            $end_page = min($total_page, $start_page + $page_range - 1);
            
            $query_string = '';
            if ($search_mb_id) $query_string .= '&search_mb_id=' . urlencode($search_mb_id);
            if ($search_type) $query_string .= '&search_type=' . urlencode($search_type);
            if ($search_date_from) $query_string .= '&search_date_from=' . urlencode($search_date_from);
            if ($search_date_to) $query_string .= '&search_date_to=' . urlencode($search_date_to);
            
            if ($page > 1) {
                echo '<a href="?page=1'.$query_string.'">처음</a> ';
                echo '<a href="?page='.($page-1).$query_string.'">이전</a> ';
            }
            
            for ($i = $start_page; $i <= $end_page; $i++) {
                $active = $i == $page ? 'active' : '';
                echo '<a href="?page='.$i.$query_string.'" class="'.$active.'">'.$i.'</a> ';
            }
            
            if ($page < $total_page) {
                echo '<a href="?page='.($page+1).$query_string.'">다음</a> ';
                echo '<a href="?page='.$total_page.$query_string.'">마지막</a>';
            }
        }
        ?>
    </div>

    <?php if ($is_admin == 'super') { ?>
    <!-- 빠른 링크 -->
    <div style="text-align: center; margin-top: 30px; padding-bottom: 30px;">
        <a href="./upload_manager.php" style="padding: 12px 30px; background: #863a86; color: #fff; text-decoration: none; border-radius: 5px; margin: 0 5px;">📁 파일 관리자</a>
        <a href="./upload_stats.php" style="padding: 12px 30px; background: #2a5298; color: #fff; text-decoration: none; border-radius: 5px; margin: 0 5px;">� 통계 보기</a>
        <a href="./upload_log.php" style="padding: 12px 30px; background: #28a745; color: #fff; text-decoration: none; border-radius: 5px; margin: 0 5px;">📋 전체 로그 보기</a>
        <!-- <a href="<?php echo G5_URL; ?>/plus/" style="padding: 12px 30px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; margin: 0 5px;">⚙️ Plus 메인</a> -->
    </div>
    <?php } ?>
</div>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

