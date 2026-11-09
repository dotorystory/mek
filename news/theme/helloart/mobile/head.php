<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH.'/shop.head.php');
    return;
}

include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
?>

<header id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>

    <div class="to_content"><a href="#container">본문 바로가기</a></div>

    <?php
    if(defined('_INDEX_')) { // index에서만 실행
        include G5_MOBILE_PATH.'/newwin.inc.php'; // 팝업레이어
    } ?>

    <div id="hd_wrapper" class="ha-mobile-hd">
        <button type="button" id="gnb_open" class="hd_opener" aria-label="메뉴열기" aria-expanded="false" aria-controls="gnb"><i class="fa fa-bars" aria-hidden="true"></i><span class="sound_only"> 메뉴열기</span></button>

        <div id="logo">
            <a class="ha-brand" href="<?php echo G5_URL ?>">
                <span class="ha-brand__en">MEK News</span>
                <span class="ha-brand__ko">MEK News</span>
            </a>
        </div>

        <button type="button" id="user_btn" class="hd_opener" aria-label="검색열기" aria-expanded="false" aria-controls="user_menu"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only">검색</span></button>
    </div>
</header>

<div id="ha_mobile_dim"></div>

    <div id="gnb" class="hd_div">
        <button type="button" id="gnb_close" class="hd_closer"><span class="sound_only">메뉴 닫기</span><i class="fa fa-times" aria-hidden="true"></i></button>
        <?php echo outlogin('theme/basic'); // 외부 로그인 ?>
        <ul id="gnb_1dul">
        <?php
        $menu_datas = get_menu_db(1, true);
        $i = 0;
        foreach( $menu_datas as $row ){
            if( empty($row) ) continue;
        ?>
            <li class="gnb_1dli">
                <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>" class="gnb_1da"><?php echo $row['me_name'] ?></a>
                <?php
                $k = 0;
                foreach( (array) $row['sub'] as $row2 ){
                    if( empty($row2) ) continue;
                    if($k == 0)
                        echo '<button type="button" class="btn_gnb_op"><span class="sound_only">하위분류</span></button><ul class="gnb_2dul">'.PHP_EOL;
                ?>
                    <li class="gnb_2dli"><a href="<?php echo $row2['me_link']; ?>" target="_<?php echo $row2['me_target']; ?>" class="gnb_2da"><span></span><?php echo $row2['me_name'] ?></a></li>
                <?php
                $k++;
                }   //end foreach $row2

                if($k > 0)
                    echo '</ul>'.PHP_EOL;
                ?>
            </li>
        <?php
        $i++;
        }   //end foreach $row

        if ($i == 0) {  ?>
            <li class="gnb_1dli"><a href="<?php echo get_pretty_url('free'); ?>" class="gnb_1da">산업·기술 칼럼</a></li>
            <li class="gnb_1dli"><a href="<?php echo get_pretty_url('gallery'); ?>" class="gnb_1da">기술·공정 영상</a></li>
            <li class="gnb_1dli"><a href="<?php echo get_pretty_url('qa'); ?>" class="gnb_1da">이슈 Q&amp;A</a></li>
            <li class="gnb_1dli"><a href="<?php echo get_pretty_url('notice'); ?>" class="gnb_1da">웹진 공지</a></li>
        <?php } ?>
        <?php if (defined('G5_USE_SHOP') && G5_USE_SHOP) { ?>
            <li class="gnb_1dli"><a href="<?php echo G5_SHOP_URL ?>" class="gnb_1da"> 쇼핑몰</a></li>
        <?php } ?>
        </ul>

        <ul id="hd_nb">
            <li class="hd_nb1"><a href="<?php echo G5_URL ?>/public/new/" id="snb_new"><i class="fa fa-history" aria-hidden="true"></i>새글</a></li>
            <li class="hd_nb2"><a href="<?php echo get_pretty_url('notice'); ?>" id="snb_notice"><i class="fa fa-bullhorn" aria-hidden="true"></i>공지</a></li>
            <li class="hd_nb3"><a href="<?php echo G5_URL ?>/public/contact/" id="snb_contact"><i class="fa fa-envelope" aria-hidden="true"></i>문의</a></li>
            <li class="hd_nb4"><a href="<?php echo G5_URL ?>/public/faq/" id="snb_faq"><i class="fa fa-question" aria-hidden="true"></i>FAQ</a></li>
            <?php if ($is_admin) { ?>
            <li class="hd_nb7"><a href="<?php echo G5_URL ?>/plus/blog_automation/" title="글·영상 자동발행 관리"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Posting</a></li>
            <li class="hd_nb8"><a href="<?php echo G5_BBS_URL ?>/current_connect.php" class="visit" title="현재 접속자"><i class="fa fa-users" aria-hidden="true"></i>접속 <strong class="visit-num"><?php echo connect('theme/basic'); ?></strong></a></li>
            <?php } ?>
        </ul>
    </div>

    <div class="hd_div" id="user_menu">
        <button type="button" id="user_close" class="hd_closer"><span class="sound_only">검색 닫기</span><i class="fa fa-times" aria-hidden="true"></i></button>
        <div id="hd_sch">
            <h2>사이트 내 전체검색</h2>
            <form name="fsearchbox" action="<?php echo G5_URL ?>/public/search/" onsubmit="return fsearchbox_submit(this);" method="get">
            <input type="hidden" name="sfl" value="wr_subject||wr_content">
            <input type="hidden" name="sop" value="and">
            <input type="text" name="stx" id="sch_stx" placeholder="검색어를 입력해주세요" required maxlength="20">
            <button type="submit" value="검색" id="sch_submit"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only">검색</span></button>
            </form>
        </div>
        <?php echo popular('theme/basic'); // 인기검색어 ?>
    </div>

    <script>
    function fsearchbox_submit(f)
    {
        var stx = f.stx.value.trim();
        if (stx.length < 2) {
            alert("검색어는 두글자 이상 입력하십시오.");
            f.stx.select();
            f.stx.focus();
            return false;
        }

        var cnt = 0;
        for (var i = 0; i < stx.length; i++) {
            if (stx.charAt(i) == ' ')
                cnt++;
        }

        if (cnt > 1) {
            alert("빠른 검색을 위하여 검색어에 공백은 한개만 입력할 수 있습니다.");
            f.stx.select();
            f.stx.focus();
            return false;
        }
        f.stx.value = stx;

        return true;
    }

    $(function () {
        function closeMobilePanels() {
            $(".hd_div").removeClass("is-open").hide();
            $("body").removeClass("ha-panel-open");
            $("#ha_mobile_dim").removeClass("is-open");
            $("#hd").removeClass("is-menu-open is-search-open");
            $(".hd_opener").attr("aria-expanded", "false");
        }

        $("#gnb_open").on("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $gnb = $("#gnb");
            var willOpen = !$gnb.hasClass("is-open");

            closeMobilePanels();

            if (willOpen) {
                $gnb.addClass("is-open").show();
                $("body").addClass("ha-panel-open");
                $("#ha_mobile_dim").addClass("is-open");
                $("#hd").addClass("is-menu-open");
                $(this).attr("aria-expanded", "true");
            }
        });

        $("#user_btn").on("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $menu = $("#user_menu");
            var willOpen = !$menu.hasClass("is-open");

            closeMobilePanels();

            if (willOpen) {
                $menu.addClass("is-open").show();
                $("body").addClass("ha-panel-open");
                $("#ha_mobile_dim").addClass("is-open");
                $("#hd").addClass("is-search-open");
                $(this).attr("aria-expanded", "true");
                setTimeout(function() { $("#sch_stx").trigger("focus"); }, 120);
            }
        });

        $(".hd_closer, #ha_mobile_dim").on("click", function() {
            closeMobilePanels();
        });

        $("#container").on("click", function() {
            closeMobilePanels();
        });

        $(".btn_gnb_op").click(function(){
            $(this).toggleClass("btn_gnb_cl").next(".gnb_2dul").slideToggle(300);
        });
    });
    </script>



<div id="wrapper">

    <div id="container">
    <?php if (!defined("_INDEX_")) { ?>
        <h2 id="container_title" class="top" title="<?php echo get_text($g5['title']); ?>">
            <a href="javascript:history.back();"><i class="fa fa-chevron-left" aria-hidden="true"></i><span class="sound_only">뒤로가기</span></a> <?php echo get_head_title($g5['title']); ?>
        </h2>
    <?php } ?>
