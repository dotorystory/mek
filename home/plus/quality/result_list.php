<?php
include_once('./_common.php');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// 검색 조건
$where = " WHERE 1=1 ";
$search_keyword = isset($_GET['search_keyword']) ? trim($_GET['search_keyword']) : '';
$search_status = isset($_GET['search_status']) ? trim($_GET['search_status']) : '';
$search_date_from = isset($_GET['search_date_from']) ? trim($_GET['search_date_from']) : '';
$search_date_to = isset($_GET['search_date_to']) ? trim($_GET['search_date_to']) : '';

// 권한에 따른 필터링
if ($member['mb_level'] >= 5) {
    // 레벨 5 이상: 모든 검사/검수/결제 결과 조회 가능
    // 필터링 없음
} elseif ($member['mb_level'] >= 4) {
    // 레벨 4: 검사/검수 결과보기 리스트 조회 가능
    // 검사 완료(inspected) 또는 검수 완료(reviewed) 상태이거나 본인이 작성한 결과
    $where .= " AND (qr_status IN ('inspected', 'reviewed') OR qr_inspector = '".sql_escape_string($member['mb_id'])."') ";
} else {
    // 레벨 3: 검사 결과보기 리스트 조회만 가능 (본인이 작성한 것만)
    $where .= " AND qr_inspector = '".sql_escape_string($member['mb_id'])."' ";
}

if ($search_keyword) {
    $where .= " AND (qr_title LIKE '%".sql_escape_string($search_keyword)."%' OR qr_doc_id LIKE '%".sql_escape_string($search_keyword)."%') ";
}
if ($search_status) {
    $where .= " AND qr_status = '".sql_escape_string($search_status)."' ";
}
if ($search_date_from) {
    $where .= " AND qr_inspection_date >= '{$search_date_from} 00:00:00' ";
}
if ($search_date_to) {
    $where .= " AND qr_inspection_date <= '{$search_date_to} 23:59:59' ";
}

// 전체 개수
$total_count = sql_fetch("SELECT COUNT(*) as cnt FROM {$g5['quality_result_table']} {$where}");
$total_count = $total_count['cnt'];
$total_page = ceil($total_count / $per_page);

// 결과 목록 조회
$sql = "SELECT * FROM {$g5['quality_result_table']} {$where} ORDER BY qr_created_at DESC LIMIT {$offset}, {$per_page}";
$result = sql_query($sql);

$g5['title'] = '검사 결과 목록';
add_stylesheet('<link rel="stylesheet" href="'.G5_QUALITY_URL.'/common_style.css">', 0);
include_once(G5_PATH.'/head_simple.php');
?>

<style>
.result-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}
.result-search {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.result-search form {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}
.result-search .form-group {
    flex: 1;
    min-width: 200px;
}
.result-search label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.result-search input, .result-search select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.result-search button {
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
.result-list {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.result-list table {
    width: 100%;
    border-collapse: collapse;
}
.result-list th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #dee2e6;
    font-weight: bold;
}
.result-list td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
}
.result-list td .action-buttons {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
    align-items: center;
}
.result-list td .action-buttons a {
    white-space: nowrap;
    flex-shrink: 0;
}
/* 태블릿 모드 (768px 이하) */
@media (max-width: 768px) {
    .result-list td .action-buttons {
        flex-direction: column;
        align-items: stretch;
    }
    .result-list td .action-buttons a {
        width: 100%;
        text-align: center;
        margin-left: 0 !important;
    }
    .result-list th:nth-child(2),
    .result-list td:nth-child(2) {
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
}
.result-list tr:hover {
    background: #f8f9fa;
}
.status-draft { color: #6c757d; font-weight: bold; }
.status-inspected { color: #007bff; font-weight: bold; }
.status-reviewed { color: #ffc107; font-weight: bold; }
.status-approved { color: #28a745; font-weight: bold; }
.status-rejected { color: #dc3545; font-weight: bold; }
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

<div class="result-container">
    <h2>검사 결과 목록</h2>
    
    <!-- 검색 영역 -->
    <div class="result-search">
        <form method="get" action="">
            <div class="form-group">
                <label>검색어</label>
                <input type="text" name="search_keyword" value="<?php echo htmlspecialchars($search_keyword); ?>" placeholder="제목 또는 문서ID">
            </div>
            <div class="form-group">
                <label>상태</label>
                <select name="search_status">
                    <option value="">전체</option>
                    <option value="draft" <?php echo $search_status == 'draft' ? 'selected' : ''; ?>>임시저장</option>
                    <option value="inspected" <?php echo $search_status == 'inspected' ? 'selected' : ''; ?>>검사완료</option>
                    <option value="reviewed" <?php echo $search_status == 'reviewed' ? 'selected' : ''; ?>>검수완료</option>
                    <option value="approved" <?php echo $search_status == 'approved' ? 'selected' : ''; ?>>최종승인</option>
                    <option value="rejected" <?php echo $search_status == 'rejected' ? 'selected' : ''; ?>>반려</option>
                </select>
            </div>
            <div class="form-group">
                <label>검사일시 (시작)</label>
                <input type="date" name="search_date_from" value="<?php echo htmlspecialchars($search_date_from); ?>">
            </div>
            <div class="form-group">
                <label>검사일시 (종료)</label>
                <input type="date" name="search_date_to" value="<?php echo htmlspecialchars($search_date_to); ?>">
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                <button type="submit" class="btn btn-primary">검색</button>
                <a href="./result_list.php" class="btn btn-secondary">초기화</a>
            </div>
        </form>
    </div>
    
    <!-- 목록 영역 -->
    <div class="result-list">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>문서ID</th>
                    <th>제목</th>
                    <th>검사자</th>
                    <th>검사일시</th>
                    <th>상태</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (sql_num_rows($result) > 0) {
                    while ($row = sql_fetch_array($result)) {
                        $status_class = 'status-' . $row['qr_status'];
                        $status_text = array(
                            'draft' => '임시저장',
                            'inspected' => '검사완료',
                            'reviewed' => '검수완료',
                            'approved' => '최종승인',
                            'rejected' => '반려'
                        );
                ?>
                <tr>
                    <td><?php echo $row['qr_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['qr_doc_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['qr_title']); ?></td>
                    <td><?php echo htmlspecialchars($row['qr_inspector_name']); ?></td>
                    <td><?php echo $row['qr_inspection_date'] ? date('Y-m-d H:i', strtotime($row['qr_inspection_date'])) : '-'; ?></td>
                    <td class="<?php echo $status_class; ?>"><?php echo $status_text[$row['qr_status']]; ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="./result_view.php?qr_id=<?php echo $row['qr_id']; ?>" style="padding: 5px 10px; background: #17a2b8; color: #fff; text-decoration: none; border-radius: 4px; font-size: 12px; margin-right: 5px;">보기</a>
                            <a href="./result_export_csv.php?qr_id=<?php echo $row['qr_id']; ?>" style="padding: 5px 10px; background: #28a745; color: #fff; text-decoration: none; border-radius: 4px; font-size: 12px; margin-right: 5px;">받기</a>
                            <?php if ($row['qr_status'] == 'draft' && ($is_admin == 'super' || $row['qr_inspector'] == $member['mb_id'])) { ?>
                            <a href="./inspection.php?qt_id=<?php echo $row['qt_id']; ?>&qr_id=<?php echo $row['qr_id']; ?>" style="padding: 5px 10px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">수정</a>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">등록된 검사 결과가 없습니다.</td>
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
    
    <div style="margin-top: 20px; text-align: right;">
        <a href="./index.php" class="btn btn-secondary">목록</a>
    </div>
</div>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

