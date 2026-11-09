<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
if (!defined('WWW_YOUTUBE_MODULE_PATH')) {
    define('WWW_YOUTUBE_MODULE_PATH', '/home/www/assets/modules/youtube');
}
if (!defined('WWW_YOUTUBE_MODULE_URL')) {
    define('WWW_YOUTUBE_MODULE_URL', G5_URL.'/resources/modules/youtube');
}
include_once WWW_YOUTUBE_MODULE_PATH.'/youtube_thumb.lib.php';
