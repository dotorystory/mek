<?php

use mc\Category;
use mc\Column;

/** @var string $bo_table */
/** @var array $auth */

include_once("./_common.php");
auth_check($auth[$sub_menu], "r");
$result = array();
if (empty($_POST['mode'])) {
    exit;
}

$mode = $_POST['mode'];
$result['mode'] = $mode;
function parseTitle($title)
{
    $title = trim($title);
    $titles = array_map('trim', explode(',', $title));
    foreach ($titles as $k => $v) {
        if (!$v) {
            unset($titles[$k]);
        }
    }
    return $titles;
}

switch ($mode) {
    case 'install':
        if (!mc::isInstalled()) {
            if (!empty($_POST['with_test_data'])) {
                include __DIR__ . '/test.php';
                $result['reload'] = true;
            } else {
                if (mc::install()) {
                    $result['reload'] = true;
                }
            }
        }
        break;
    case 'uninstall':
        if (mc::isInstalled()) {
            if (mc::uninstall(empty($_POST['board_config_use']))) {
                $result['reload'] = true;
            }
        }
        break;
    case 'add':
        try {
            if ($category = Category::get($_POST['mc'])) {
                $result['a'] = $category;
                if (!empty($_POST['extend_title'])) {
                    $category->addMulti($_POST['titles']);
                    $result['reload'] = true;
                } else {
                    if ($titles = parseTitle($_POST['title'])) {
                        foreach ($titles as $title) {
                            $category->add($title);
                        }
                        $result['reload'] = true;
                    } else {
                        $result['alert'] = '카테고리명을 입력해주세요.';
                    }
                }
            } else {
                $result['alert'] = '부모카테고리가 존재하지 않습니다.';
            }
        } catch (Exception $e) {
            $result['alert'] = $e->getMessage();
        }
        break;
    case 'remove':
        if ($category = Category::get($_POST['mc'])) {
            if ($category->depth > 0) {
                $category->remove();
                $result['reload'] = true;
            }
        } else {
            $result['alert'] = '카테고리가 존재하지 않습니다.';
        }
        break;
    case 'create_table_column': // 게시판 데이타베이스 컬럼 추가

        $name = $_POST['name'];
        $type = $_POST['type'];
        $bo_table = $_POST['bo_table'];
        $sql = sprintf('ALTER TABLE `%s` ADD `%s` VARCHAR(255);', G5_TABLE_PREFIX . 'write_' . $bo_table, $name);
        sql_query($sql);
        $result['reload'] = true;
        break;
    case 'add_board_column':
        // 게시판 여분필트설정
        $data = $_POST['data'];


        mc_board($bo_table)->addColumn($data['name'], $data)->save();
        $result['reload'] = true;
        /*
        if (empty($data['name'])) {
            $result['alert'] = '저장 컬럼명을 선택해주세요.';
        } elseif (empty($data['root'])) {
            $result['alert'] = '출력할 카테고리 위치를 선택해 주세요.';
        } else {
            if ($data['type'] === 'select') { // @todo 셀렉트일경우 최종 카테고리는 선택할 수 없다.

            }
            mc_board($bo_table)->addColumn($data['name'], $data)->save();
            $result['reload'] = true;
        }
        */
        break;
    case 'removeConfigColumn':
        mc_board($bo_table)->removeColumn($_POST['column'])->save();
        $result['reload'] = true;
        break;
    case 'board_config':// 게시판 멀티 설정
        $data = $_POST['data'];
        foreach ($data as $_bo_table => $values) {
            if ($mc_board = mc_board($_bo_table)) {
                $_config = &$mc_board->getConfig();
                if(empty($values['list_selector_use'])) $values['list_selector_use'] = 0;
                if(empty($values['write_selector_use'])) $values['write_selector_use'] = 0;
                if(empty($values['view_selector_use'])) $values['view_selector_use'] = 0;

                foreach ($values as $k => $value) {
                    $_config[$k] = $value;
                }
                $mc_board->save();
            }
        }
        $result['reload'] = true;
        break;
    case 'modify_wr': // 확장컬럼 전체저장

        if ($mc_board = mc_board($bo_table)) {
            $allow_names = array(
                'title', 'caption', 'searchable', 'required', 'type', 'list_type',
                'placeholder', 'pattern',
                'op', 'search_ck_first', 'control', 'data_type', 'data', 'write_type', 'multiple', 'category_depth');
            $config = &$mc_board->getConfig();
            $data = $_POST['data'];
            foreach ($data as $name => $values) {
                if (!empty($config['columns'][$name])) {

                    if($values['data_type']===Column::DATASET_STRING_SPLIT){
                       if($values['write_type'] === 'checkbox' || $values['write_type'] === 'radio') {
                           $values['multiple'] = 0;
                       }
                    }

                    foreach ($values as $k => $v) {
                        if (in_array($k, $allow_names)) {
                            if ($k === 'search_ck_first') {
                                $v = mc_category_name_input_filter($v);
                            }
                            $config['columns'][$name][$k] = $v;
                        }
                    }
                }
            }
            $mc_board->save();
            $result['reload'] = true;
        }
        break;
    case 'moveUp':
        if ($mc = mc((int)$_POST['mc'])) {
            $mc->moveUp();
            $result['reload'] = true;
        }
        break;
    case 'moveDown':
        if ($mc = mc((int)$_POST['mc'])) {
            $mc->moveDown();
            $result['reload'] = true;
        }
        break;

    case 'boardColumnMove':
        if ($mc_board = mc_board($bo_table)) {
            $config = &$mc_board->getConfig();
            $columns = $config['columns'];
            $keys = array_keys($columns);
            if (in_array($_POST['column'], $keys)) {
                $move = 1 === (int)$_POST['move'] ? 1 : -1;
                $idx = array_search($_POST['column'], $keys);
                if (($move === -1 && $idx !== 0) || ($move === 1 && $idx !== (count($keys) - 1))) {
                    $b = null;
                    if ($move === 1) {
                        $b = array_slice($keys, 0, $idx, true);
                        array_push($b, $keys[$idx + 1]);
                        array_push($b, $keys[$idx]);
                        $b += array_slice($keys, $idx + 2, count($keys), true);
                    } elseif ($move === -1) {
                        $b = array_slice($keys, 0, ($idx - 1), true);
                        $b[] = $keys[$idx];
                        $b[] = $keys[$idx - 1];
                        $b += array_slice($keys, ($idx + 1), count($keys), true);
                    }
                    if($b) {
                        $_columns = array();
                        foreach ($b as $k) {
                            $_columns[$k] = $columns[$k];
                        }
                        $config['columns'] = $_columns;
                        $mc_board->save();
                        $result['reload'] = true;
                    }
                }

            }
        }

        break;
    case 'form_data_type':
        $form = '<div>';

        switch ($_POST['data_type']) {
            case Column::DATASET_CATEGORY:
                $category = \mc\Category::root();
                $form .= '<div class="mc-controls" data-name="data[data]" >';
                $form .= '<!--
                <input type="hidden" name="data[data]">
                <select required onchange="mc.handle(this)">
                                <option value="">선택하세요</option>';
                foreach ($category->getChild() as $row):
                    $total_child = count(\mc\Category::get($row->mc)->getChild(0));
                    $disabled = $total_child === 0 ? ' disabled' : '';

                    $form .= '<option ' . $disabled . ' value="' . $row->mc . '">' . $row->title . '(' . $total_child . ')</option>';
                endforeach;
                $form .= '</select>-->';

                ob_start();
                $category = \mc\Category::root();
                $selectbox = new \mc\CategorySelectboxAdmin($category, null);
                $selectbox->input_name = 'data[data]';
                $selectbox->data_column = 'mc';
                $selectbox->render();
                $form .= ob_get_contents();
                ob_end_clean();


                $form .= '<input type="hidden" name="data[category_column]" value="path">';
                $form .= '<input type="hidden" step="1" min="1" max="10" name="data[category_depth]"></div>';
                break;
            case Column::DATASET_STRING_SPLIT;
                $form .= '데이타 <input type="text" name="data[data]" placeholder="가|나|다">';
                break;
            default:
                if (Column::isInputTextType($_POST['data_type'])) {
                    switch($_POST['data_type']){
                        case "number":case "date":
                            $form .= '<input type="hidden" name="data[list_type]" value="range">';
                            break;
                        default:
                            $form .= '<input type="hidden" name="data[list_type]" value="input">';
                    }

                    $form .= '<input type="hidden" name="data[placeholder]" value="">';
                    $form .= '<input type="hidden" name="data[pattern]" value="">';
                }
        }


        $form .= '</div>';
        $result['form'] = $form;
        break;
}


echo json_encode($result);
