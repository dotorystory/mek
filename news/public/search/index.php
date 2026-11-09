<?php
/**
 * /public/search/ — 전체검색 (bbs/search.php 재사용)
 * 페이징 등은 $_SERVER['SCRIPT_NAME']을 쓰므로 /public/search/ 경로가 유지됩니다.
 */
include_once dirname(dirname(__DIR__)) . '/common.php';

if (!defined('_GNUBOARD_')) {
    exit;
}

if (defined('G5_COMMUNITY_USE') && G5_COMMUNITY_USE === false) {
    goto_url(G5_SHOP_URL . '/');
}

chdir(G5_BBS_PATH);
include_once G5_BBS_PATH . '/search.php';
