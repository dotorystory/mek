<?php
/**
 * /public/faq/ — FAQ (bbs/faq.php 재사용)
 * CWD를 bbs로 맞춰야 faq.php 내부의 ./_common.php, ./_head.php 경로가 올바르게 동작합니다.
 */
include_once dirname(dirname(__DIR__)) . '/common.php';

if (!defined('_GNUBOARD_')) {
    exit;
}

if (defined('G5_COMMUNITY_USE') && G5_COMMUNITY_USE === false) {
    goto_url(G5_SHOP_URL . '/');
}

chdir(G5_BBS_PATH);
include_once G5_BBS_PATH . '/faq.php';
