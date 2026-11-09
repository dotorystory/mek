<?php

/**
 * 그누보드 common.php 하단에 include 되어야 합니다.
 */
if (version_compare('5.6', PHP_VERSION)) {
    if (version_compare(G5_GNUBOARD_VER, '5.4', '<')) {
        define('G53', true);
        define('G54', false);
    } else {
        define('G53', false);
        define('G54', true);
    }
    include_once G5_PLUGIN_PATH . '/mc/bootstrap.php';
} else {
    define('MC', false);
}