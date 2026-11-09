<?php
if (!defined('_GNUBOARD_')) exit;

// wr_tags (comma-separated). Ensure column then save.
sql_query(" ALTER TABLE `{$write_table}` ADD `wr_tags` VARCHAR(255) NOT NULL DEFAULT '' AFTER `wr_10` ", false);
$wr_tags = isset($_POST['wr_tags']) ? trim((string) $_POST['wr_tags']) : '';
$wr_tags = preg_replace('/\s*,\s*/u', ',', $wr_tags);
$wr_tags = preg_replace('/^,+|,+$/u', '', $wr_tags);
if (function_exists('mb_substr')) {
    $wr_tags = mb_substr($wr_tags, 0, 255, 'UTF-8');
} else {
    $wr_tags = substr($wr_tags, 0, 255);
}
sql_query(" UPDATE {$write_table} SET wr_tags = '".sql_escape_string($wr_tags)."' WHERE wr_id = '".(int) $wr_id."' ");
