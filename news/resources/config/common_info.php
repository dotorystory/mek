<?php
/**
 * www.mekeng.com/news — API 키는 credentials 공통 로드
 */
if (!defined('_GNUBOARD_')) {
    exit;
}

require_once '/home/www/credentials/load_secrets.inc.php';
credentials_define_gnuboard_constants();
