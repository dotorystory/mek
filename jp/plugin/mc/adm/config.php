<?php

use mc\Column;

$sub_menu = "800100";
include_once("./_common.php");
if (!mc::isInstalled()) {
    include_once 'setup.php';
    return;
}
/** @var array $auth */
auth_check($auth[$sub_menu], "r");
$g5['title'] = "게시판 설정";
$sql = "SELECT * FROM {$g5['board_table']}";
$result = sql_query($sql);
$boards = array();
while ($row = sql_fetch_array($result)) {
    $boards[] = $row;
}
if (!empty($board['bo_table'])) {
    $h = '<form style="display:inline-block;padding-left:10px;"><select name="bo_table" style="font-weight: bold;vertical-align: middle;" onchange="this.form.submit()">';
    foreach ($boards as $row) {
        $selected = $row['bo_table'] === $board['bo_table'] ? ' selected' : '';
        $h .= '<option value="' . $row['bo_table'] . '"' . $selected . '>' . $row['bo_subject'] . '(' . $row['bo_table'] . ')</option>';
    }
    $h .= '</select></form>';
}
include_once(G5_ADMIN_PATH . '/admin.head.php'); ?>
    <script src="./mc.admin.js"></script>
<?php
if (empty($board['bo_table'])):
    $s = 0;
    ?>


    <form class="mc-ajax-form" autocomplete="off" id="mc-board-list-f">
        <div class="tbl_head01 tbl_wrap">
            <input type="hidden" name="mode" value="board_config"/>
            <table border="1">
                <col width="80">
                <col width="80">

                <col width="60">
                <col width="90">

                <col width="60">
                <col width="90">

                <col width="60">
                <col width="90">

                <col>
                <col width="80">
                <thead>
                <tr>
                    <th rowspan="3">bo_table</th>
                    <th rowspan="3">게시판명</th>
                    <th colspan="2">리스트</th>
                    <th colspan="2">글쓰기</th>
                    <th colspan="2">내용보기</th>
                    <th rowspan="3">설정된컬럼</th>
                    <th rowspan="3">설정</th>
                </tr>
                <tr>
                    <th colspan="2">스킨</th>
                    <th colspan="2">스킨</th>
                    <th colspan="2">스킨</th>
                </tr>
                <tr>
                    <th>자동출력</th>
                    <th>셀렉터</th>
                    <th>자동출력</th>
                    <th>셀렉터</th>
                    <th>자동출력</th>
                    <th>셀렉터</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($boards as $row): ?>
                    <?php
                    if ($_mc = mc_board($row['bo_table'])) {
                        $data = $_mc->getConfig();
                    } else {
                        continue;
                    } ?>
                    <tr>
                        <td rowspan="2">
                            <a href="config.php?bo_table=<?php
                            echo $row['bo_table']; ?>"><?php
                                echo $row['bo_table']; ?></a>
                        </td>
                        <td rowspan="2"><?php
                            echo $row['bo_subject']; ?></td>
                        <td colspan="2">
                            <input type="hidden" name="data[<?php
                            echo $row['bo_table']; ?>][bo_table]" value="<?php
                            echo $row['bo_table']; ?>"/>
                            <?php
                            echo mc_board($row['bo_table'])->getSelectbox('list', 'data[' . $row['bo_table'] . '][list_skin]', null, '<option value="">사용안함</option>'); ?>
                        </td>
                        <td colspan="2"><?php
                            echo mc_board($row['bo_table'])->getSelectbox('write', 'data[' . $row['bo_table'] . '][write_skin]', null, '<option value="">사용안함</option>'); ?></td>
                        <td colspan="2"><?php
                            echo mc_board($row['bo_table'])->getSelectbox('view', 'data[' . $row['bo_table'] . '][view_skin]', null, '<option value="">사용안함</option>'); ?></td>
                        <td rowspan="2">
                            <?php
                            if ($_mc = mc_board($row['bo_table'])) {
                                $_config = $_mc->getConfig();
                                echo join(', ', array_keys($_config['columns']));
                            } ?>
                        </td>
                        <td rowspan="2">
                            <button type="button" class="btn_frmline" onclick="location.href='config.php?bo_table=<?php
                            echo $row['bo_table']; ?>'">설정
                            </button>
                            <?php
                            $c = &mc_board($row['bo_table'])->getConfig();
                            if (empty($c['bo_table']) && $s === 0) {
                                // echo '<script>$(function(){$("#mc-board-list-f").trigger("submit");});</script>';
                                $s = 1;
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="data[<?php
                            echo $row['bo_table']; ?>][list_selector_use]" value="1" <?php
                            echo $data['list_selector_use'] ? ' checked' : ''; ?>></td>
                        <td>
                            <input type="text" style="width:100%" name="data[<?php
                            echo $row['bo_table']; ?>][list_selector]" value="<?php
                            echo $data['list_selector'] ? $data['list_selector'] : '#bo_list'; ?>">
                        </td>

                        <td><input type="checkbox" name="data[<?php
                            echo $row['bo_table']; ?>][write_selector_use]" value="1" <?php
                            echo $data['write_selector_use'] ? ' checked' : ''; ?>></td>
                        <td><input type="text" style="width:100%" name="data[<?php
                            echo $row['bo_table']; ?>][write_selector]" value="<?php
                            echo $data['write_selector'] ? $data['write_selector'] : '#fwrite'; ?>"></td>

                        <td><input type="checkbox" name="data[<?php
                            echo $row['bo_table']; ?>][view_selector_use]" value="1" <?php
                            echo $data['view_selector_use'] ? ' checked' : ''; ?>></td>
                        <td><input type="text" style="width:100%" name="data[<?php
                            echo $row['bo_table']; ?>][view_selector]" value="<?php
                            echo $data['view_selector'] ? $data['view_selector'] : '#bo_v_atc'; ?>"></td>

                    </tr>
                <?php
                endforeach; ?></tbody>
            </table>
        </div>
        <div class="btn_confirm01 btn_confirm">
            <input type="submit" value="저장" class="btn_submit">
        </div>
    </form>
    <p>자동출력 : 스킨수정없이 자동으로 셀렉터 위치에 prepend 합니다</p>
    <p>스킨내 코딩을 하실경우 자동출력 체크박스를 해제하시기 바랍니다</p>
    <p>스킨 : 폼 출력을 위한 스킨입니다</p>


<?php
elseif (!empty($_GET['step'])): ?>
    <p>데이타 형식을 선택해 주세요</p>
    <?php
    foreach (Column::getDataTypes() as $k => $v): ?>
        <label><input type="radio" name="data[data_type]" value="<?php
            echo $k; ?>"><?php
            echo $v; ?></label>
    <?php
    endforeach; ?>


<?php
else: ?>
    <?php
    $category_empty = !count(mc()->getChild()) ? true : false; ?>
    <?php
    echo $category_empty ? '<h2 style="color:red">설정된 카테고리가 존재하지 않습니다. 카테고리를 등록해주세요.</h2>' : ''; ?>

    <?php
    if (!empty($_GET['column'])):
        include __DIR__.'/column.php';
    else: ?>

        <h2 class="h2_frm"><?php
            echo $board['bo_subject']; ?></h2>


        <style>
            .mc-group[data-type=category] select {
                width: 100px;
            }
        </style>

        <form class="mc-add-column" id="mc-add-column-f" autocomplete="off">
            <input type="hidden" name="mode" value="create_table_column"/>
            <input type="hidden" name="bo_table" value="<?php
            echo $bo_table; ?>"/>
            <input type="text" name="name" class="frm_input"/>

            <select name="type">
                <option value="varchar">문자열</option>
                <option value="integer">숫자</option>
                <option value="date">날짜</option>
            </select>
            <button type="submit" class="btn_frmline">컬럼추가</button>
        </form>

        <form class="mc-ajax-form-new" autocomplete="off">
            <fieldset <?php
            echo $category_empty ? 'disabled' : ''; ?>>
                <input type="hidden" name="mode" value="modify_wr"/>
                <input type="hidden" name="bo_table" value="<?php
                echo $bo_table; ?>"/>
                <div class="tbl_head01 tbl_wrap">
                    <table>
                        <colgroup>
                            <col width="60"/>
                            <col width="100"/>
                            <col width="120"/>
                            <col -width="220"/>
                            <col width="40"/>
                            <col width="40"/>
                            <col width="120"/>
                            <col width="40"/>
                            <col width="100"/>
                            <col width="50"/>
                            <col width="190"/>
                        </colgroup>
                        <thead>
                        <tr>
                            <th rowspan="2">컬럼명</th>
                            <th rowspan="2">제목</th>
                            <th rowspan="2">데이타형식</th>
                            <th colspan="4">글쓰기</th>
                            <th colspan="3">목록</th>
                            <th rowspan="2"></th>
                        </tr>
                        <tr>
                            <th>데이타</th>
                            <th>필수</th>
                            <th>멀티</th>
                            <th>폼</th>
                            <th>검색</th>
                            <th>폼</th>
                            <th>조건</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach (mc_board($bo_table)->getColumns() as $column):
                            $name = $column->name;
                            $types = $column::getDataTypes();
                            ?>
                            <tr>
                                <td><a href="config.php?bo_table=<?php
                                    echo $bo_table; ?>&column=<?php
                                    echo $name; ?>"><strong><?php
                                            echo $name; ?></strong></a></strong>
                                </td>
                                <td><input type="text" required name="data[<?php
                                    echo $name; ?>][title]" value="<?php
                                    echo $column->title; ?>" class="frm_input" maxlength="32"/></td>
                                <td>
                                    <?php
                                    if (!empty($_GET['adv'])): ?>

                                        <select name="data[<?php
                                        echo $name; ?>][data_type]">
                                            <?php
                                            foreach ($types as $k => $v): $selected = (string)$column->data_type === (string)$k ? ' selected' : ''; ?>
                                                <option value="<?php
                                                echo $k; ?>" <?php
                                                echo $selected; ?>><?php
                                                    echo $v; ?></option>
                                            <?php
                                            endforeach; ?>
                                        </select>
                                    <?php
                                    else: ?>
                                        <?php
                                        echo $types[$column->data_type]; ?>
                                        <input type="hidden" name="data[<?php
                                        echo $name; ?>][data_type]" value="<?php
                                        echo $column->data_type; ?>">
                                    <?php
                                    endif; ?>
                                </td>

                                <td>


                                    <?php
                                    if ($column->data_type === Column::DATASET_CATEGORY): ?>
                                        <?php
                                        echo \mc\Category::get($column->data)->path; ?>
                                        <input type="hidden" name="data[<?php
                                        echo $name; ?>][data]" value="<?php
                                        echo $column->data; ?>">
                                        <input type="hidden" name="data[<?php
                                        echo $name; ?>][category_depth]" value="<?php
                                        echo $column->category_depth; ?>">
                                    <?php
                                    elseif (Column::isInputTextType($column->data_type) || $column->data_type === Column::DATASET_DATE): ?>
                                        <input type="hidden" name="data[<?php
                                        echo $name; ?>][data]" value="">
                                        <input type="hidden" name="data[<?php
                                        echo $name; ?>][category_depth]" value="">

                                        <input class="frm_input" type="hidden" name="data[<?php
                                        echo $name; ?>][placeholder]" value="<?php
                                        echo $column->placeholder; ?>">
                                    <?php
                                    elseif ($column->data_type === Column::DATASET_STRING_SPLIT): ?>
                                        <input class="frm_input" type="text" name="data[<?php
                                        echo $name; ?>][data]" value="<?php
                                        echo $column->data; ?>" pattern="[^,]+">
                                        <input type="hidden" name="data[<?php
                                        echo $name; ?>][category_depth]" value="">
                                    <?php
                                    else: ?>
                                        <input type="text" name="data[<?php
                                        echo $name; ?>][data]" value="<?php
                                        echo $column->data; ?>">
                                        <input type="text" name="data[<?php
                                        echo $name; ?>][category_depth]" value="<?php
                                        echo $column->category_depth; ?>">
                                    <?php
                                    endif; ?>
                                </td>

                                <td>
                                    <input type="checkbox" name="data[<?php
                                    echo $name; ?>][required]" value="1" <?php
                                    echo $column->required ? 'checked' : ''; ?>/>
                                </td>
                                <td>
                                    <?php
                                    if ($column->data_type === Column::DATASET_CATEGORY || $column->data_type === Column::DATASET_STRING_SPLIT): ?>
                                        <input type="checkbox" name="data[<?php
                                        echo $name; ?>][multiple]" value="1" <?php
                                        echo $column->multiple ? 'checked' : ''; ?>/>
                                    <?php
                                    else: ?>
                                        <input type="hidden" name="data[<?php
                                        echo $name; ?>][multiple]" value="0"/>-
                                    <?php
                                    endif; ?>
                                </td>
                                <td>
                                    <?php
                                    if ($column->data_type === Column::DATASET_DATE || Column::isInputTextType($column->data_type)): ?>
                                        <input type="hidden" name="data[<?php
                                        echo $name; ?>][write_type]" value="input">input type="<?php
                                        echo $column->data_type; ?>"
                                    <?php
                                    else: ?>
                                        <select name="data[<?php
                                        echo $name; ?>][write_type]">
                                            <option value="select" <?php
                                            echo $column->write_type === 'select' ? ' selected' : ''; ?>>셀렉트박스
                                            </option>
                                            <option value="radio" <?php
                                            echo $column->write_type === 'radio' ? ' selected' : ''; ?>>라디오버튼
                                            </option>
                                            <option value="checkbox" <?php
                                            echo $column->write_type === 'checkbox' ? ' selected' : ''; ?>>체크박스
                                            </option>
                                        </select>
                                    <?php
                                    endif; ?>
                                </td>


                                <td>
                                    <?php
                                    if (!$column->supportSearchable()): ?>
                                        <input type="hidden" name="data[<?php
                                        echo $name; ?>][searchable]" value=""/>-
                                    <?php
                                    else: ?>
                                        <input type="checkbox" name="data[<?php
                                        echo $name; ?>][searchable]" value="1" <?php
                                        echo $column->searchable ? 'checked' : ''; ?>/>
                                    <?php
                                    endif; ?>
                                </td>

                                <td>
                                    <?php
                                    if (!$column->supportSearchable()): ?>
                                        <input type="hidden" name="data[<?php
                                        echo $name; ?>][list_type]" value="input">-
                                    <?php
                                    elseif ($column->data_type === Column::DATASET_DATE): ?>
                                        <select name="data[<?php
                                        echo $name; ?>][list_type]">
                                            <option value="between" <?php
                                            echo $column->list_type === 'between' ? ' selected' : ''; ?>>범위
                                            </option>
                                        </select>
                                    <?php
                                    elseif ($column->data_type === Column::DATASET_NUMBER): ?>
                                        <select name="data[<?php
                                        echo $name; ?>][list_type]">
                                            <option value="range">범위</option>
                                        </select>
                                        <input type="hidden" name="data[searchable]" value=""> -
                                    <?php
                                    else: ?>
                                        <select name="data[<?php
                                        echo $name; ?>][list_type]">
                                            <option value="select" <?php
                                            echo $column->list_type === 'select' ? ' selected' : ''; ?>>셀렉트박스
                                            </option>
                                            <option value="radio" <?php
                                            echo $column->list_type === 'radio' ? ' selected' : ''; ?>>라디오버튼
                                            </option>
                                            <option value="checkbox" <?php
                                            echo $column->list_type === 'checkbox' ? ' selected' : ''; ?>>체크박스
                                            </option>
                                            <option value="between" <?php
                                            echo $column->list_type === 'between' ? ' selected' : ''; ?>>범위
                                            </option>
                                        </select>
                                    <?php
                                    endif; ?>
                                </td>

                                <td>
                                    <?php
                                    if ($column->list_type === 'checkbox'): ?>
                                        <select name="data[<?php
                                        echo $name; ?>][op]">
                                            <option value="and" <?php
                                            echo $column->op === 'and' ? ' selected' : ''; ?>>AND
                                            </option>
                                            <option value="or" <?php
                                            echo $column->op === 'or' ? ' selected' : ''; ?>>OR
                                            </option>
                                        </select>
                                    <?php
                                    else: ?>
                                        <?php
                                        if ($column->supportSearchable()): ?>
                                            <input type="hidden" name="data[<?php
                                            echo $name; ?>][op]" value="and"> AND
                                        <?php
                                        else: ?>
                                            <input type="hidden" name="data[<?php
                                            echo $name; ?>][op]" value="">-

                                        <?php
                                        endif; ?>
                                    <?php
                                    endif; ?>
                                </td>
                                <!--<td>
                                <input type="text" name="data[<?php
                                echo $name; ?>][search_ck_first]" value="<?php
                                echo $column->search_ck_first; ?>" style="width:99%">
                            </td>-->
                                <td>
                                    <button type="button" style="background:red" class="btn_frmline" onclick="mcApi.removeConfigColumn('<?php
                                    echo $bo_table; ?>', '<?php
                                    echo $name; ?>')"> 삭제
                                    </button>

                                    <button type="button" class="btn_frmline" onclick="mcApi.boardColumnMove('<?php
                                    echo $bo_table; ?>', '<?php
                                    echo $name; ?>',-1);">위로
                                    </button>
                                    <button type="button" class="btn_frmline" onclick="mcApi.boardColumnMove('<?php
                                    echo $bo_table; ?>', '<?php
                                    echo $name; ?>',1);">아래로
                                    </button>
                                </td>
                            </tr>
                            <!--tr>
                            <td colspan="10">데이타

                                <input type="text" name="data[<?php
                            echo $name; ?>][data]" value="<?php
                            echo $column->data; ?>">
                                <input type="text" name="data[<?php
                            echo $name; ?>][category_depth]" value="<?php
                            echo $column->category_depth; ?>">


                            </td>
                        </tr-->

                        <?php
                        endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="btn_confirm01 btn_confirm">
                    <button type="submit" class="btn_submit">저장</button>
                </div>
            </fieldset>
        </form>
        <script>
            (function ($) {
                $(function () {
                    $(".mc-ajax-form-new").on('submit', function (evt) {
                        evt.preventDefault();
                        var f = $(evt.target);
                        var send_data = f.serializeArray();
                        f.find(":checkbox").not(':checked').each(function () {
                            send_data.push({name: this.name, value: ''});
                        });
                        $.ajax({
                            url: 'ajax.php',
                            data: send_data,
                            type: 'post',
                            dataType: 'json',
                            success: function (data) {
                                if (data && data.reload) {
                                    location.reload();
                                }
                            }
                        });
                    });
                });
            })(jQuery);
        </script>
        <h2 class="h2_frm"><?php
            echo $board['bo_subject']; ?> 컬럼 설정 추가</h2>
        <form class="mc-ajax-form" autocomplete="off">
            <fieldset <?php
            echo $category_empty ? 'disabled' : ''; ?>>
                <input type="hidden" name="mode" value="add_board_column"/>
                <input type="hidden" name="bo_table" value="<?php
                echo $bo_table; ?>"/>
                <div class="tbl_frm01 tbl_wrap">
                    <table>
                        <colgroup>
                            <col width="100"/>
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>사용컬럼</th>
                            <td>
                                <?php
                                $mc_board_config = mc_board($bo_table)->getConfig(); ?>
                                <?php
                                foreach (get_mc_board_ext_columns($bo_table) as $name): $disabled = !empty($mc_board_config['columns'][$name]) ? ' disabled' : ''; ?>
                                    <label class="<?php
                                    echo !$disabled ? 'mc-label-select' : ''; ?>"
                                           style="cursor: pointer;<?php
                                           echo $disabled ? 'color:#aaa' : ''; ?>">
                                        <input type="radio" name="data[name]" value="<?php
                                        echo $name; ?>" <?php
                                        echo $disabled; ?>/>
                                        <?php
                                        echo $name; ?>
                                    </label>
                                <?php
                                endforeach; ?>
                                <script>
                                    jQuery(function () {
                                        $('.mc-label-select').on('mouseover', function () {
                                            $(this).css('color', 'red');
                                        }).on('mouseleave', function () {
                                            $(this).css('color', 'inherit');
                                        });
                                    });
                                </script>
                            </td>
                        </tr>
                        <tr>
                            <th>데이타</th>
                            <td>
                                <script>
                                    function form_data_type(elm) {
                                        var el = $(elm);
                                        var send_data = {
                                            mode: 'form_data_type',
                                            data_type: el.val()
                                        };
                                        $.ajax({
                                            url: 'ajax.php',
                                            data: send_data,
                                            type: 'post',
                                            dataType: 'json',
                                            success: function (data) {
                                                if (data.form) {
                                                    $('#form_data_type').html(data.form);
                                                    if (send_data.data_type === 'number') {
                                                        console.log($("#searchable"));
                                                        $("#searchable").prop('checked', false);
                                                        $("#searchable").prop('disabled', true);
                                                    } else {
                                                        $("#searchable").prop('disabled', false);
                                                    }
                                                }
                                            }
                                        });
                                    }
                                </script>
                                <div>
                                    <?php
                                    foreach (Column::getDataTypes() as $k => $v): ?>
                                        <label><input type="radio" name="data[data_type]" onclick="form_data_type(this)" value="<?php
                                            echo $k; ?>"><?php
                                            echo $v; ?></label>
                                    <?php
                                    endforeach; ?>
                                </div>
                                <div id="form_data_type"></div><!--
                            <?php
                                $category = \mc\Category::root();
                                ?>
                            <input type="hidden" name="data[root]">
                            <select required onchange="mcApi.handleSelectboxAdmin(this)">
                                <option value="">선택하세요</option>
                                <?php
                                foreach ($category->getChild() as $row):
                                    $total_child = count(\mc\Category::get($row->mc)->getChild(0));
                                    $disabled = $total_child === 0 ? ' disabled' : '';
                                    ?>
                                    <option <?php
                                    echo $disabled; ?> value="<?php
                                    echo $row->mc; ?>"><?php
                                    echo $row->title; ?>(<?php
                                    echo $total_child; ?>)</option>
                                <?php
                                endforeach; ?>
                            </select>-->

                            </td>
                        </tr>
                        <!--tr>
                            <th>데이타 저장형식</th>
                            <td>
                                <label><input type="radio" name="data[column]" value="mc" disabled/>숫자형</label>
                                <label><input type="radio" name="data[column]" value="path" checked/>문자형</label>
                            </td>
                        </tr-->
                        <!--tr>
                            <th>폼출력형식</th>
                            <td>
                                <div>글작성 폼

                                    <label><input type="radio" name="data[type]" value="select" checked
                                                  class="mc-data-type"/>
                                        단일선택(트리형)select</label>

                                    <label><input type="radio" name="data[type]" value="radio" class="mc-data-type"/>
                                        단일선택:radio</label>

                                    <label><input type="radio" name="data[type]" value="checkbox" class="mc-data-type"/>
                                        다중선택:checkbox</label>
                                </div>
                                <div>목록 검색 폼 :
                                    <label><input type="radio" name="data[list_type]" value="select" checked
                                                  class="mc-data-type"/>
                                        단일선택(트리형)select</label>
                                    <label><input type="radio" name="data[list_type]" value="radio" class="mc-data-type"/>
                                        단일선택:radio</label>
                                    <label><input type="radio" name="data[list_type]" value="checkbox"
                                                  class="mc-data-type"/>
                                        다중선택:checkbox</label>
                                </div>
                            </td>
                        </tr-->

                        <tr>
                            <th>검색사용</th>
                            <td><label><input type="checkbox" name="data[searchable]" value="1" checked id="searchable"/>사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>제목</th>
                            <td><input type="text" name="data[title]" class="frm_input" maxlength="32" title="제목" required/></td>
                        </tr>
                        <!--tr>
                            <th>캡션</th>
                            <td><input type="text" name="data[caption]" value="선택하세요" class="frm_input" maxlength="32"
                                       required/>

                            </td>
                        </tr-->
                        <tr>
                            <th>필수입력</th>
                            <td><input type="checkbox" name="data[required]" value="1"/> * 글 작성시 반드시 입력을 요청합니다.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="btn_confirm01 btn_confirm">
                    <button type="submit" class="btn_submit">추가</button>
                </div>
            </fieldset>
        </form>
    <?php
    endif; ?>
<?php
endif; ?>
<?php
include_once(G5_ADMIN_PATH . '/admin.tail.php');