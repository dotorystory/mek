<?php
include_once('./_common.php');

// 레벨 3 이상만 접근 가능
if ($member['mb_level'] < 3) {
    alert('템플릿 목록은 회원 레벨 3 이상만 사용할 수 있습니다.', G5_URL);
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// 검색 조건
$where = " WHERE 1=1 ";
$search_keyword = isset($_GET['search_keyword']) ? trim($_GET['search_keyword']) : '';
$search_status = isset($_GET['search_status']) ? trim($_GET['search_status']) : '';

if ($search_keyword) {
    $where .= " AND (qt_title LIKE '%".sql_escape_string($search_keyword)."%' OR qt_doc_id LIKE '%".sql_escape_string($search_keyword)."%') ";
}
if ($search_status) {
    $where .= " AND qt_status = '".sql_escape_string($search_status)."' ";
}

// 전체 개수
$total_count = sql_fetch("SELECT COUNT(*) as cnt FROM {$g5['quality_template_table']} {$where}");
$total_count = $total_count['cnt'];
$total_page = ceil($total_count / $per_page);

// 템플릿 목록 조회
$sql = "SELECT * FROM {$g5['quality_template_table']} {$where} ORDER BY qt_created_at DESC LIMIT {$offset}, {$per_page}";
$result = sql_query($sql);

$g5['title'] = '템플릿 목록';
add_stylesheet('<link rel="stylesheet" href="'.G5_QUALITY_URL.'/common_style.css">', 0);
include_once(G5_PATH.'/head_simple.php');
?>

<style>
.template-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}
.template-search {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.template-search form {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}
.template-search .form-group {
    flex: 1;
    min-width: 200px;
}
.template-search label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.template-search input, .template-search select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.template-search button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 36px;
    padding: 8px 16px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    vertical-align: middle;
    line-height: 1.2;
    box-sizing: border-box;
}
.template-list {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.template-list table {
    width: 100%;
    border-collapse: collapse;
}
.template-list th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #dee2e6;
    font-weight: bold;
}
.template-list td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
}
.template-list tr:hover {
    background: #f8f9fa;
}
.status-active {
    color: #28a745;
    font-weight: bold;
}
.status-inactive {
    color: #dc3545;
    font-weight: bold;
}
.btn-group {
    display: flex;
    gap: 5px;
}
.btn {
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
}
.btn-view {
    background: #17a2b8;
    color: #fff;
}
.btn-edit {
    background: #ffc107;
    color: #333;
}
.btn-delete {
    background: #dc3545;
    color: #fff;
}
.pagination {
    margin-top: 20px;
    text-align: center;
}
.pagination a {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 2px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
}
.pagination a.active {
    background: #007bff;
    color: #fff;
    border-color: #007bff;
}
</style>

<div class="template-container">
    <h2>템플릿 목록</h2>
    
    <!-- 검색 영역 -->
    <div class="template-search">
        <form method="get" action="">
            <div class="form-group">
                <label>검색어</label>
                <input type="text" name="search_keyword" value="<?php echo htmlspecialchars($search_keyword); ?>" placeholder="제목 또는 문서ID">
            </div>
            <div class="form-group">
                <label>상태</label>
                <select name="search_status">
                    <option value="">전체</option>
                    <option value="active" <?php echo $search_status == 'active' ? 'selected' : ''; ?>>활성</option>
                    <option value="inactive" <?php echo $search_status == 'inactive' ? 'selected' : ''; ?>>비활성</option>
                </select>
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                <button type="submit" class="btn btn-primary">검색</button>
                <a href="./template_list.php" class="btn btn-secondary">초기화</a>
            </div>
        </form>
    </div>
    
    <!-- 목록 영역 -->
    <div class="template-list">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>문서ID</th>
                    <th>제목</th>
                    <th>위치</th>
                    <th>상태</th>
                    <th>생성일</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (sql_num_rows($result) > 0) {
                    while ($row = sql_fetch_array($result)) {
                        $status_class = $row['qt_status'] == 'active' ? 'status-active' : 'status-inactive';
                        $status_text = $row['qt_status'] == 'active' ? '활성' : '비활성';
                ?>
                <tr>
                    <td><?php echo $row['qt_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['qt_doc_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['qt_title']); ?></td>
                    <td><?php echo htmlspecialchars($row['qt_location']); ?></td>
                    <td class="<?php echo $status_class; ?>"><?php echo $status_text; ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($row['qt_created_at'])); ?></td>
                    <td>
                        <div class="btn-group">
                            <a href="./template_view.php?qt_id=<?php echo $row['qt_id']; ?>" class="btn btn-view">보기</a>
                            <?php if ($member['mb_level'] >= 5) { ?>
                            <a href="./template_upload.php?qt_id=<?php echo $row['qt_id']; ?>" class="btn btn-edit">수정</a>
                            <a href="javascript:if(confirm('정말 삭제하시겠습니까?')) location.href='./template_delete.php?qt_id=<?php echo $row['qt_id']; ?>';" class="btn btn-delete">삭제</a>
                            <?php } ?>
                            <a href="./template_export_csv.php?qt_id=<?php echo $row['qt_id']; ?>" class="btn" style="background: #28a745; color: #fff;">받기</a>
                        </div>
                    </td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">등록된 템플릿이 없습니다.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <!-- 페이징 -->
    <div class="pagination">
        <?php
        if ($total_page > 0) {
            $page_range = 10;
            $start_page = max(1, $page - floor($page_range / 2));
            $end_page = min($total_page, $start_page + $page_range - 1);
            
            $query_string = '';
            if ($search_keyword) $query_string .= '&search_keyword=' . urlencode($search_keyword);
            if ($search_status) $query_string .= '&search_status=' . urlencode($search_status);
            
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
    
    <div style="margin-top: 20px; text-align: right;">
        <a href="./template_upload.php" class="btn btn-success">템플릿 등록</a>
        <a href="./index.php" class="btn btn-secondary">목록</a>
    </div>
</div>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

