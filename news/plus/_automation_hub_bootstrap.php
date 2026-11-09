<?php
/**
 * automation_hub 연동 (helloart_kr/news 등 서브경로 그누보드 포함)
 */
if (defined('AH_HUB_BOOTSTRAP_LOADED')) {
    return;
}

if (!defined('AH_G5_ROOT')) {
    define('AH_G5_ROOT', dirname(__DIR__));
}

$ahHubCandidates = [
    dirname(__DIR__, 2) . '/automation_hub', // …/helloart_kr/automation_hub (보통 없음)
    dirname(__DIR__, 3) . '/automation_hub', // …/public/automation_hub
    '/home/www/automation_hub',
    '/home/www/public/automation_hub',
];
$ahHubRoot = '';
foreach ($ahHubCandidates as $candidate) {
    if (is_file($candidate . '/automation-core/plus_automation_hub_paths.inc.php')) {
        $ahHubRoot = $candidate;
        break;
    }
}
if ($ahHubRoot === '') {
    $ahHubRoot = '/home/www/automation_hub';
}
if (!defined('AH_PROJECT_ROOT')) {
    define('AH_PROJECT_ROOT', dirname($ahHubRoot));
}
if (!defined('AH_HUB_ROOT')) {
    define('AH_HUB_ROOT', $ahHubRoot);
}

require_once AH_HUB_ROOT . '/automation-core/plus_automation_hub_paths.inc.php';

define('AH_HUB_BOOTSTRAP_LOADED', true);
