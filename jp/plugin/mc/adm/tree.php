<?php
use mc\Category;


$sub_menu = "800100";
include_once("./_common.php");
auth_check($auth[$sub_menu], "r");



$g5['title'] = "카테고리 관리(트리형) :  , 로 제목을 분류하시면 일괄 입력하실 수 있습니다.";
include_once(G5_ADMIN_PATH . '/admin.head.php');
if(!mc::isInstalled()){
    return;
}



?>
    <script src="./mc.admin.js"></script>
	<style>
		.mc-tree ul ul {padding-left:20px;}
	</style>
    <ul style="margin:0;padding:0;" class="mc-tree">
        <?php mc()->tree(function ($row) { ?>
            <li>
                <div style="padding:4px;">
                    <strong style="color:darkred"><?php echo $row->title; ?></strong>
                    <div style="display:inline-block;padding-left:10px;">
                        <a href="javascript:;" onclick="mcApi.moveUp(<?php echo $row->mc; ?>)">위로</a>
                        <a href="javascript:;" onclick="mcApi.moveDown(<?php echo $row->mc; ?>)">아래로</a>
                        <a href="javascript:;" onclick="mcApi.remove(<?php echo $row->mc; ?>)">삭제</a>
                        <form class="mc-ajax-form" style="display:inline-block">
                            <input type="hidden" name="mc" value="<?php echo $row->mc; ?>"/>
                            <input type="hidden" name="mode" value="add"/>
                            <input type="text" name="title">
                            <button type="submit">추가</button>
                        </form>
                    </div>
                </div>
            </li>
            <?php
        }, 'ul');
        ?>
    </ul>
<?php include_once(G5_ADMIN_PATH . '/admin.tail.php');



