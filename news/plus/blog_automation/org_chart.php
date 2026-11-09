<?php
require_once dirname(__DIR__) . '/_automation_hub_bootstrap.php';
if (is_readable(AH_HUB_BLOG_SVC . '/org_chart.php')) {
    include_once AH_HUB_BLOG_SVC . '/org_chart.php';
    return;
}
header('Content-Type: text/plain; charset=utf-8');
echo 'org_chart not configured for this site';
