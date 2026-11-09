<?php
/**
 * 파일 업로드 시스템 통계 대시보드 (관리자 전용)
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

// 전체 통계
$total_stats = get_total_file_stats();

// 최근 30일 활동
$recent_activity = sql_query("
    SELECT 
        DATE(fl_datetime) as date,
        fl_type,
        COUNT(*) as count,
        SUM(fl_filesize) as total_size
    FROM g5_file_log
    WHERE fl_datetime >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(fl_datetime), fl_type
    ORDER BY date DESC
");

// 회원별 통계 (TOP 10)
$top_users = get_top_file_users(10);

// 최근 활동 (최근 50개)
$recent_logs = sql_query("
    SELECT l.*, m.mb_name 
    FROM g5_file_log l 
    LEFT JOIN {$g5['member_table']} m ON l.fl_mb_id = m.mb_id 
    ORDER BY l.fl_datetime DESC 
    LIMIT 50
");

$g5['title'] = '파일 업로드 통계';
include_once(G5_PATH.'/head_simple.php');
?>

<style>
.stats-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.stat-card {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e8ba3 100%);
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
    text-align: center;
    color: #fff;
}
.stat-card-value {
    font-size: 36px;
    font-weight: bold;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.stat-card-label {
    margin-top: 10px;
    opacity: 0.9;
    font-size: 14px;
}
.chart-container {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}
.chart-container h3 {
    margin: 0 0 20px 0;
    color: #1e3c72;
    font-size: 18px;
    border-bottom: 3px solid #2a5298;
    padding-bottom: 10px;
}
.stats-table {
    width: 100%;
    border-collapse: collapse;
}
.stats-table th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
}
.stats-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}
.stats-table tr:hover {
    background: #f9f9f9;
}
.rank-badge {
    background: #ffd700;
    color: #333;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 12px;
}
.rank-badge.rank-1 { background: #ffd700; }
.rank-badge.rank-2 { background: #c0c0c0; }
.rank-badge.rank-3 { background: #cd7f32; }
.activity-list {
    max-height: 400px;
    overflow-y: auto;
}
.activity-item {
    padding: 12px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.activity-item:hover {
    background: #f9f9f9;
}
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .stat-card-value {
        font-size: 24px;
    }
}
</style>

<div class="stats-container">
    <h2>📊 파일 업로드 시스템 통계</h2>
    <p style="color: #666; margin-bottom: 30px;">전체 시스템 사용 현황 및 회원별 통계</p>

    <!-- 전체 통계 카드 -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-value"><?php echo number_format($total_stats['total_users']); ?></div>
            <div class="stat-card-label">👥 사용 회원 수</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-value"><?php echo number_format($total_stats['total_files']); ?></div>
            <div class="stat-card-label">📁 총 파일 개수</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-value"><?php echo $total_stats['total_size_gb']; ?> GB</div>
            <div class="stat-card-label">💾 총 용량</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-value"><?php echo number_format($total_stats['total_uploads']); ?></div>
            <div class="stat-card-label">⬆️ 총 업로드 횟수</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-value"><?php echo number_format($total_stats['total_downloads']); ?></div>
            <div class="stat-card-label">⬇️ 총 다운로드 횟수</div>
        </div>
    </div>

    <!-- 상위 10명 회원 -->
    <div class="chart-container">
        <h3>🏆 상위 10명 회원 (용량 기준)</h3>
        <table class="stats-table">
            <thead>
                <tr>
                    <th width="60">순위</th>
                    <th>회원</th>
                    <th width="60">레벨</th>
                    <th width="100">파일 개수</th>
                    <th width="100">총 용량</th>
                    <th width="100">업로드 횟수</th>
                    <th width="100">다운로드 횟수</th>
                    <th width="150">마지막 업로드</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($top_users) > 0) {
                    $rank = 1;
                    foreach ($top_users as $user) {
                        $rank_class = $rank <= 3 ? 'rank-' . $rank : '';
                ?>
                <tr>
                    <td style="text-align: center;">
                        <span class="rank-badge <?php echo $rank_class; ?>"><?php echo $rank; ?></span>
                    </td>
                    <td>
                        <strong><?php echo $user['mb_name'] ? htmlspecialchars($user['mb_name']) : '-'; ?></strong>
                        <br><small style="color:#999;"><?php echo htmlspecialchars($user['fs_mb_id']); ?></small>
                    </td>
                    <td style="text-align: center;"><?php echo $user['mb_level']; ?></td>
                    <td style="text-align: right;"><?php echo number_format($user['fs_total_files']); ?></td>
                    <td style="text-align: right;"><?php echo round($user['fs_total_size'] / 1024 / 1024, 2); ?> MB</td>
                    <td style="text-align: right;"><?php echo number_format($user['fs_upload_count']); ?></td>
                    <td style="text-align: right;"><?php echo number_format($user['fs_download_count']); ?></td>
                    <td><?php echo $user['fs_last_upload'] ? date('Y-m-d H:i', strtotime($user['fs_last_upload'])) : '-'; ?></td>
                </tr>
                <?php
                        $rank++;
                    }
                } else {
                ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 50px; color: #999;">데이터가 없습니다.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- 최근 활동 -->
    <div class="chart-container">
        <h3>🕐 최근 활동 (50개)</h3>
        <div class="activity-list">
            <?php
            if (sql_num_rows($recent_logs) > 0) {
                while ($log = sql_fetch_array($recent_logs)) {
                    $type_class = 'type-' . strtolower($log['fl_type']);
                    $type_icon = '';
                    switch ($log['fl_type']) {
                        case 'UPLOAD': $type_icon = '⬆️'; break;
                        case 'DOWNLOAD': $type_icon = '⬇️'; break;
                        case 'DELETE': $type_icon = '🗑️'; break;
                        case 'ACCESS': $type_icon = '🔑'; break;
                    }
            ?>
            <div class="activity-item">
                <div>
                    <span class="type-badge <?php echo $type_class; ?>"><?php echo $type_icon . ' ' . $log['fl_type']; ?></span>
                    <strong><?php echo htmlspecialchars($log['mb_name'] ? $log['mb_name'] : $log['fl_mb_id']); ?></strong>
                    님이 
                    <strong><?php echo htmlspecialchars($log['fl_filename']); ?></strong>
                    <?php 
                    if ($log['fl_filesize'] > 0) {
                        echo ' (' . round($log['fl_filesize'] / 1024 / 1024, 2) . ' MB)';
                    }
                    ?>
                </div>
                <div style="color: #999; font-size: 12px;">
                    <?php echo $log['fl_datetime']; ?>
                </div>
            </div>
            <?php
                }
            } else {
            ?>
            <div style="text-align: center; padding: 50px; color: #999;">활동 내역이 없습니다.</div>
            <?php } ?>
        </div>
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

