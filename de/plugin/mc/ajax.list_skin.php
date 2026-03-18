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
/** @var string $bo_table */
if ($mc_board = mc_board($bo_table)) {
    $config = $mc_board->getConfig();
    foreach ($config['columns'] as $name => $column) {
        if(isset($params[$name]) || array_key_exists($name, $params)) {
            $params[$name] = (string)$params[$name];
        }
    }
    $mc_board->setMode('list')->setValues($params)->render();
}
ob_end_flush();