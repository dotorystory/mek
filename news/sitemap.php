<?php
/**
 * 그누보드 동적 sitemap (게시판 + 최근글)
 * 2026-08-31 공통 배치
 */
include_once __DIR__ . '/common.php';
if (!defined('_GNUBOARD_')) {
    exit;
}

header('Content-Type: application/xml; charset=utf-8');
$now = date('c');
$home = rtrim(G5_URL, '/') . '/';
$skip_bo = array('notice', 'qa', 'edit', 'shorts', 'online');
$skip_if_search_off = true;

$urls = array();
$urls[] = array('loc' => $home, 'lastmod' => $now, 'changefreq' => 'daily', 'priority' => '1.0');

$sql = "SELECT bo_table, bo_subject, bo_count_write, bo_use_search, bo_read_level, bo_list_level
        FROM {$g5['board_table']}
        ORDER BY bo_order, bo_table";
$res = sql_query($sql);
$boards = array();
while ($row = sql_fetch_array($res)) {
    $bo = preg_replace('/[^a-zA-Z0-9_]/', '', $row['bo_table']);
    if ($bo === '') {
        continue;
    }
    if ((int) $row['bo_read_level'] > 1 || (int) $row['bo_list_level'] > 1) {
        continue;
    }
    if ($skip_if_search_off && isset($row['bo_use_search']) && (string) $row['bo_use_search'] === '0') {
        continue;
    }
    $boards[] = $bo;
    $urls[] = array(
        'loc' => G5_BBS_URL . '/board.php?bo_table=' . rawurlencode($bo),
        'lastmod' => $now,
        'changefreq' => 'daily',
        'priority' => '0.8',
    );
}

foreach ($boards as $bo) {
    $write_table = $g5['write_prefix'] . $bo;
    $chk = sql_query("SHOW TABLES LIKE '" . sql_real_escape_string($write_table) . "'", false);
    if (!$chk || !sql_fetch_array($chk)) {
        continue;
    }
    $q = sql_query("SELECT wr_id, wr_datetime FROM {$write_table}
        WHERE wr_is_comment = 0
          AND wr_id = wr_parent
        ORDER BY wr_num LIMIT 300");
    while ($w = sql_fetch_array($q)) {
        $lm = !empty($w['wr_datetime']) ? date('c', strtotime($w['wr_datetime'])) : $now;
        $urls[] = array(
            'loc' => G5_BBS_URL . '/board.php?bo_table=' . rawurlencode($bo) . '&wr_id=' . (int) $w['wr_id'],
            'lastmod' => $lm,
            'changefreq' => 'weekly',
            'priority' => '0.6',
        );
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo "  <url>\n";
    echo '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
    echo '    <lastmod>' . htmlspecialchars($u['lastmod'], ENT_XML1, 'UTF-8') . "</lastmod>\n";
    echo '    <changefreq>' . htmlspecialchars($u['changefreq'], ENT_XML1, 'UTF-8') . "</changefreq>\n";
    echo '    <priority>' . htmlspecialchars($u['priority'], ENT_XML1, 'UTF-8') . "</priority>\n";
    echo "  </url>\n";
}
echo "</urlset>\n";
