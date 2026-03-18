<?php
include_once('./_common.php');

$result = array();
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode($result);
    exit;
}
ob_start();
$params = array();
$urls = parse_url($_SERVER['HTTP_REFERER']);
if (!empty($urls['query'])) {
    parse_str($urls['query'], $params);
}
if ($mc_board = mc_board($bo_table)) {
    if(!empty($write)){
        $params = $write;
    }
    $mc_board->setMode('view')->setValues($params)->render();
}
ob_end_flush();