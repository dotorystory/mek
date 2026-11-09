<?php
$sub_menu = "800400";
include_once("./_common.php");
auth_check($auth[$sub_menu], "r");
$g5['title'] = mc::isInstalled(true) ? "삭제" : "설치";
include_once(G5_ADMIN_PATH . '/admin.head.php'); ?>
    <script src="./mc.admin.js"></script>
<?php if (mc::isInstalled(true)): ?>
    <div class="tbl_head01 tbl_wrap">
        <p>MC Tools 설치정보를 삭제합니다.</p>
        <ul>
            <li>테이블명 : <?php echo mc\TABLE_NAME; ?>
            <li>데이타디렉토리 : <?php echo MC_DATA_PATH; ?>
        </ul>
    </div>
    <form class="mc-ajax-form">
        <input type="hidden" name="mode" value="uninstall"/>
        <div class="btn_confirm01 btn_confirm">
            <p><label><input type="checkbox" name="board_config_use" value="1" checked/>게시판 설정파일은 삭제하지 않습니다.</label> </p>
            <button type="submit" class="btn_submit">삭제</button>
        </div>
    </form>
<?php else: ?>
    <h2 class="h2_frm">MC Tools 설치</h2>
    <div class="tbl_head01 tbl_wrap">
        <p>MC Tools 사용을 위해 테이블과 디렉토리를 생성합니다.</p>
        <ul>
            <li>테이블명 : <?php echo mc\TABLE_NAME; ?>
            <li>데이타디렉토리 : <?php echo MC_DATA_PATH; ?>
        </ul>
    </div>
    <form class="mc-ajax-form">
        <input type="hidden" name="mode" value="install"/>
        <div class="btn_confirm01 btn_confirm">
            <p><label><input type="checkbox" name="with_test_data" value="1" checked/> 카테고리 샘플데이타를 함께 설치합니다.</label> </p>
            <button type="submit" class="btn_submit">설치</button>
        </div>
    </form>
<?php endif; ?>
<?php include_once(G5_ADMIN_PATH . '/admin.tail.php');
