<?php
/**
 * Thin site entry — config.php + shared page bootstrap
 */
include_once dirname(__DIR__, 2) . '/common.php';

if (!defined('_GNUBOARD_')) {
    exit;
}

$cfgFile = __DIR__ . '/config.php';
$GLOBALS['net_kwr_site_config'] = is_readable($cfgFile) ? require $cfgFile : [];
if (!is_array($GLOBALS['net_kwr_site_config'])) {
    $GLOBALS['net_kwr_site_config'] = [];
}

// page.php expects config beside itself; set cwd-relative by redefining require path
// Use inline bootstrap instead of shared page.php path confusion:
require_once '/home/www/public/shared/keyword-rank/widget.php';
net_kwr_boot($GLOBALS['net_kwr_site_config']);

$pageTitle = (string) net_kwr_cfg('page_title', '실시간 이슈 키워드');
$g5['title'] = $pageTitle;

if (defined('G5_COMMUNITY_USE') && G5_COMMUNITY_USE === false) {
    include_once G5_THEME_SHOP_PATH . '/index.php';

    return;
}

include_once G5_THEME_PATH . '/head.php';

$cssLocal = __DIR__ . '/keyword-rank.css';
if (is_file($cssLocal)) {
    add_stylesheet('<link rel="stylesheet" href="' . G5_URL . '/public/keyword-rank/keyword-rank.css?ver=' . filemtime($cssLocal) . '">', 0);
}
?>
<div class="public-page tr-kwr-page">
	<div class="tr-kwr-page__inner">
		<?php echo net_kwr_render_page(); ?>
	</div>
</div>
<?php
include_once G5_THEME_PATH . '/tail.php';
