<?php
/**
 * 카테고리 json 출력 요청
 */

use mc\Category;
use mc\Input;

include_once('./_common.php');
header('Content-Type: application/json; charset=utf-8', true);

$result = array();
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode($result);
    exit;
}
if (!mc::isInstalled()) {
    $result['alert'] = 'MC 가 설치되어 있지 않습니다.';
    echo json_encode($result);
    exit;
}


$rows = array();
if(!empty($_GET['categoryColumn'])) {
    $category = Category::get(array($_GET['categoryColumn'] => $_GET['categoryValue']));
    $rows = $category->getChild(0, 'stdClass');
    if ($rows && !empty($_GET['with_total_child'])) {
        foreach ($rows as $k => $row) {
            $rows[$k]->total_child = count(Category::get($row->mc)->getChild());
        }
    }
}else {

    switch ($_GET['type']) {
        case \mc\Column::DATASET_CATEGORY:
            if (!$root = Category::get($_GET['root'])) {
                $result['alert'] = 'root 카테고리가 존재하지 않습니다.';
                echo json_encode($result);
                exit;
            } else {
                $params = array('path' => $root->path . MC_CATEGORY_DELIMITER . $_GET['path']);
            }

           // var_dump($params);


            $category = Category::get($params);
            if (!$category) {
                $result['alert'] = '요청하신 카테고리가 존재하지 않습니다.';
                echo json_encode($result);
                exit;
            }
            $rows = $category->getChild(0, 'stdClass');

            if ($root = mc((int)$_GET['root'])) {
                $result['root_path'] = $root->path;

                foreach ($rows as $k => $row) {
                    $rows[$k]->path = substr($row->path, strlen($root->path) + 1);
                }
            }

            if (!empty($_GET['with_total_child'])) {
                foreach ($rows as $k => $row) {
                    $rows[$k]->total_child = count(Category::get($row->mc)->getChild());
                }
            }
            break;
    }
}


$result['data'] = $rows;
if (defined('JSON_UNESCAPED_UNICODE')) {
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode($result);
}
