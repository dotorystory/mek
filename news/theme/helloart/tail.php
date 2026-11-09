<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/tail.php');
    return;
}

if(G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_SHOP_PATH.'/shop.tail.php');
    return;
}
?>

    </div>
    <div id="aside">
        <?php echo outlogin('theme/basic'); // 외부 로그인, 테마의 스킨을 사용하려면 스킨을 theme/basic 과 같이 지정 ?>
        <?php
        ?>
        <?php echo poll('theme/basic'); // 설문조사, 테마의 스킨을 사용하려면 스킨을 theme/basic 과 같이 지정 ?>
    </div>
</div>

</div>
<!-- } 콘텐츠 끝 -->

<hr>


<!-- 하단 시작 { -->
<div id="ft">

    <div id="ft_wr">
        <div id="ft_link" class="ft_cnt">
            <a href="https://new.mekeng.com" target="_blank" rel="noopener noreferrer">MEK 홈</a>
            <a href="https://blog.naver.com/mekeng" target="_blank" rel="noopener noreferrer">MEK 블로그</a>
            <a href="<?php echo get_pretty_url('content', 'privacy'); ?>">개인정보처리방침</a>
            <a href="<?php echo get_pretty_url('notice'); ?>">웹진 공지</a>
        </div>
        <div id="ft_company" class="ft_cnt">
        	<h2>MEK News</h2>
	        <p class="ft_info">
	        	MEK News<br>
				MEK Engineering의 산업·기술 정보 미디어입니다. 웹공정·측정·품질검사·스마트팩토리·자동화 설비 관련 칼럼과 이슈, 기술 영상을 전합니다.<br>
				문의: <a href="mailto:msk@mekeng.com">msk@mekeng.com</a> · 평일 09:00 ~ 18:00
			</p>
	    </div>
        <?php
        // 푸터 새글: new.php(board_new) 기준 최신 원글 5개 — 제목 클릭 시 해당 글 보기
        include_once G5_THEME_PATH.'/inc/footer_latest_new.inc.php';
        ?>

		<div class="ft_aside_stack">
		<?php
		include_once G5_THEME_PATH.'/inc/footer_sns_public.php';
		if ($is_admin) {
			echo visit('theme/basic'); // 접속자집계 (관리자만, SNS 아래)
		}
		?>
		</div>
	</div>
        <!-- <div id="ft_catch"><img src="<?php echo G5_IMG_URL; ?>/ft_logo.png" alt="<?php echo G5_VERSION ?>"></div> -->
        <div id="ft_copy">
            <?php if ($is_admin) { ?>
            <nav class="ha-ft-admin-nav" aria-label="자동발행 관리">
                <a href="<?php echo G5_URL ?>/plus/blog_automation/generate.php" class="ha-admin-link" title="수동 글 생성">글 생성</a>
                <span class="ha-ft-admin-sep" aria-hidden="true">·</span>
                <a href="<?php echo G5_URL ?>/plus/blog_automation/" class="ha-admin-link" title="글·영상 자동발행 관리">Posting</a>
            </nav>
            <?php } ?>
            <div class="ha-ft-copy-main">
                Copyright &copy; <b>MEK</b> All rights reserved.
                <?php if ($is_admin) { ?>
                · <a href="<?php echo correct_goto_url(G5_ADMIN_URL); ?>" class="ha-admin-link">웹진 관리</a>
                <?php } ?>
            </div>
        </div>
    
    
    <button type="button" id="top_btn">
    	<i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sound_only">상단으로</span>
    </button>
    <script>
    $(function() {
        $("#top_btn").on("click", function() {
            $("html, body").animate({scrollTop:0}, '500');
            return false;
        });
    });
    </script>
</div>

<?php
if(G5_DEVICE_BUTTON_DISPLAY && !G5_IS_MOBILE) { ?>
<?php
}

if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}
?>

<!-- } 하단 끝 -->

<script>
$(function() {
    // 폰트 리사이즈 쿠키있으면 실행
    font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
});
</script>

<?php
if (is_file(G5_THEME_PATH.'/inc/youtube_bootstrap.php')) {
    include_once G5_THEME_PATH.'/inc/youtube_bootstrap.php';
    if (function_exists('www_yt_render_modals')) {
        // 메인은 일반 유튜브 모달만 (쇼츠 CSS 미로드 시 × 노출 방지)
        www_yt_render_modals(false);
    }
}
?>

<?php
include_once(G5_THEME_PATH."/tail.sub.php");