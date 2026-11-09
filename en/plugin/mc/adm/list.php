<?php
use mc\Category;


$sub_menu = "800100";
include_once("./_common.php");
auth_check($auth[$sub_menu], "r");

$g5['title'] = "카테고리 관리";

$category = null;
if (!empty($_GET['mc'])) {
    $category = Category::get($_GET['mc']);
    if ($category) {
        $g5['title'] .= " : ";
        $arr = array();
        foreach ($category->getParents() as $row) {
            $arr[] = '<a href="list.php?mc=' . $row->mc . '">' . $row->title . '</a>';
        }
        $g5['title'] .= join(' / ', $arr);
    }
}
include_once(G5_ADMIN_PATH . '/admin.head.php'); ?>
    <script src="./mc.admin.js"></script>
<?php if (!$category): ?>
    <div class="local_sch01 local_sch">
        <form class="mc-ajax-form" autocomplete="off">
            <input type="hidden" name="mc" value="<?php echo Category::root()->mc; ?>"/>
            <input type="hidden" name="mode" value="add"/>
            <label>카테고리 그룹명 <input type="text" name="title" required class="frm_input" style="width:300px;"/></label>
            <button type="submit" >카테고리그룹생성</button>
            구분자 , 를 포함하여 입력하시면 일괄입력됩니다.
        </form>
    </div>
    <div class="tbl_head01 tbl_wrap">
        <table>
            <colgroup>
                <col width="200"/>
                <col width="110"/>
                <col/>
                <col width="100"/>
                <col width="60"/>
            </colgroup>
            <thead>
            <tr>
                <th>카테고리명</th>
                <th></th>
                <th>출력예시</th>
                <th>카테고리추가</th>
                <th>삭제</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach (mc()->getChild() as $row): ?>
                <tr>
                    <td><a href="list.php?mc=<?php echo $row->mc; ?>"><?php echo $row->title; ?></a></td>
                    <td>
                        <button class="btn_frmline" type="button" onclick="mcApi.moveUp(<?php echo $row->mc; ?>)">위로</button>
                        <button class="btn_frmline" type="button" onclick="mcApi.moveDown(<?php echo $row->mc; ?>)">아래로</button>
                    </td>
                    <td>

                    </td>
                    <td>
                        <button type="button" onclick="location.href='list.php?mc=<?php echo $row->mc; ?>'" class="btn_frmline">카테고리추가</button>
                    </td>
                    <td>
                        <button type="button" onclick="mcApi.remove(<?php echo $row->mc; ?>)" class="btn_frmline">삭제</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="local_sch01 local_sch">
        <form class="mc-ajax-form" autocomplete="off">
            <input type="hidden" name="mode" value="add"/>
            <input type="hidden" name="mc" value="<?php echo $category->mc; ?>"/>
            <label>
                카테고리 추가
                <input type="text" style="width:300px" name="title" class="frm_input" maxlength="32"/>
                <textarea rows="5" name="titles" style="display:none;width:300px;"></textarea>
            </label>
            <button type="submit" class="btn_submit">카테고리등록</button>
            <label><input type="checkbox" id="extend-title" name="extend_title"/>확장</label>
            <script>
                (function ($) {
                    $("#extend-title").on("click", function () {
                        if (this.checked) {
                            $("input[name=title]").hide().val("");
                            $("textarea[name=titles]").show().focus();
                        } else {
                            $("input[name=title]").show().focus();
                            $("textarea[name=titles]").hide().val("");
                        }
                    });
                })(jQuery);
            </script>
        </form>
    </div>
    <div class="tbl_head01 tbl_wrap">
        <table>
            <colgroup>
                <col width="200"/>
                <col width="110"/>
                <col width="370"/>
                <col/>
            </colgroup>
            <thead>
            <tr>
                <th>카테고리명</th>
                <th></th>
                <th></th>
                <th>미리보기</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($category->getChild() as $row): ?>
                <tr>
                    <td><?php $depth = $row->depth - $category->depth - 1;
                        if ($depth > 0) {
                            str_repeat('-', $row->depth - $category->depth - 1);
                        } ?>
                        <a href="list.php?mc=<?php echo $row->mc; ?>"><?php echo $row->title; ?></a></td>
                    <td>
                        <button class="btn_frmline" type="button" onclick="mcApi.moveUp(<?php echo $row->mc; ?>)">위로</button>
                        <button class="btn_frmline" type="button" onclick="mcApi.moveDown(<?php echo $row->mc; ?>)">아래로</button>
                    </td>
                    <td>
                        <button type="button" onclick="location.href='list.php?mc=<?php echo $row->mc;?>'" class="btn_frmline">설정</button>
                        <form class="mc-ajax-form" style="display:inline" autocomplete="off">
                            <input type="hidden" name="mode" value="add"/>
                            <input type="hidden" name="mc" value="<?php echo $row->mc; ?>"/>
                            <input type="text" name="title" class="frm_input" maxlength="32" requi/>
                            <button type="submit" class="btn_frmline">하위 카테고리 추가</button>
                            <button type="button" onclick="mcApi.remove(<?php echo $row->mc; ?>)" class="btn_frmline">삭제</button>
                        </form>
                    </td>
                    <td>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>
<?php include_once(G5_ADMIN_PATH . '/admin.tail.php');
