<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/tail.php');
    return;
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/tail.php');
    return;
}
?>

<?php 
// plus 폴더에서는 sub div 제거
if (!preg_match('#/plus/#', $_SERVER['SCRIPT_NAME'])) {
    if(!defined('_INDEX_')) { // index 아닐때만 실행 ?>
      </div><!-- } #sub 끝 -->
    <?php }
}
?>

<!-- 푸터시작 -->
<footer id="footer" class="section fp-auto-height fp-auto-height-responsive main_contact_section" style="display: none;">
  <div class="footer container">
    <div class="footer_wrap">
      <div class="footer_left">
        <h1 class="logo_w"><a href="<?php echo G5_URL?>"><img src="<?php echo G5_IMG_URL?>/logo.png" alt="MEK"></a></h1>
        <div class="footer_info">
          <!-- <address>
            <ul>
              <li><?php echo $default['de_admin_company_name'];?></li>
              <li>대표 : <?php echo $default['de_admin_company_owner'];?></li>
              <li>주소 : <?php echo $default['de_admin_company_addr']; ?></li>
              <li>TEL :  <a href="tel:<?php echo $default['de_admin_company_tel']; ?>"><?php echo $default['de_admin_company_tel']; ?> </a> / <a href="tel:010-3698-1111">010-3698-1111 </a><span class="spc"> | </span><br class="mobr">FAX : <?php echo $default['de_admin_company_fax']; ?></li>
              <li><a href="mailto:<?php echo $default['de_admin_info_email']; ?>" >E-mail : <?php echo $default['de_admin_info_email']; ?></a></li>
            </ul>
          </address> -->
          <small>
            Copyrightⓒ MEK Engineering Corp. & MEK Inc. <br class="br_mo">All rights reserved. 
            <!-- <span class="spc"> | </span> <br class="br_mo"><a href="https://designtalktalk.com/home/" target="_blank">Design by DesignTalkTalk</a> -->
          </small>
      </div>
      </div>
      <div class="footer_link">
        <ul class="footer_link_list">
          <li><a href="https://blog.naver.com/mekeng" target="_blank"><img src="/public/img/icon_naver_blog.png" alt="MEK 네이버 블로그 링크 아이콘"></a></li>
          <li><a href="https://www.youtube.com/@mekeng_com" target="_blank"><img src="/public/img/icon_youtube.png" alt="MEK 유튜브 링크 아이콘"></a></li>
          <li class="ft_global">
            <a href="javascript:void(0)" class="ft_global_btn"><i class="ri-global-line"></i></a>
          <ul class="ft_global_list">
            <li><a href="#" class="active">KO</a></li>
            <li><a href="/jp">JP</a></li>
            <li><a href="/cn">CN</a></li>
            <li><a href="/en">EN</a></li>
            <li><a href="/es">ES</a></li>
            <li><a href="/de">DE</a></li>
          </ul>
        </li>
        </ul>
        <!-- <a href="<?php echo G5_BBS_URL?>/board.php?bo_table=news_ko&wr_id=1" class="pt_pri">개인정보처리방침</a> -->
      </div>
    </div>
  </div>
</footer>
<!-- 푸터 끝 -->

<div style="height: 70px;"></div>

<div style="position: fixed; bottom: 0; right: 0; width: 100%; height: 50px; background-color: #00000011; color: #fff; text-align: center; line-height: 50px;">
  <a href="<?php echo G5_URL?>/plus/" style="color: #000; text-decoration: none; font-weight: bold; font-size: 15px;">
    <i class="ri-arrow-left-line"></i> Plus 홈으로 이동
  </a>
</div>



<?php
include_once(G5_PATH."/tail.sub.php");
