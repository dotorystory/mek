<?php

use mc\Column;

define('G5_IS_ADMIN', true);
include_once('../../../common.php');
include_once(G5_ADMIN_PATH . '/admin.lib.php');
include_once('../lib.php');

add_stylesheet('<link rel="stylesheet" href="' . G5_PLUGIN_URL . '/mc/adm/css/mc.css">', 0);


function mc_input_options($data_type, array $item, $input_name)
{
    if ($data_type === Column::DATASET_CATEGORY) {
        $category = \mc\Category::get($item['data']);
        $selectbox = new \mc\CategorySelectboxAdmin($category->parent(), $category->mc);
        $selectbox->input_name = $input_name;
        $selectbox->renderAsButton();
    }
}