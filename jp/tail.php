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

<?php if(!defined('_INDEX_')) { // index 아닐때만 실행 ?>
  </div><!-- } #sub 끝 -->
<?php }?>

<!-- 푸터시작 -->
<footer id="footer" class="section fp-auto-height fp-auto-height-responsive main_contact_section">
  <div class="footer container">
    <div class="footer_wrap">
      <div class="footer_left">
        <h1 class="logo_w"><a href="<?php echo G5_URL?>"><img src="<?php echo G5_IMG_URL?>/logo.png" alt="MEK"></a></h1>
        <div class="footer_info">
          <address>
            <ul>
              <li><?php echo $default['de_admin_company_name']; ?></li>
              <li>最高経営責任者 : <?php echo $default['de_admin_company_owner']; ?></li>
              <li>住所 : <?php echo $default['de_admin_company_addr']; ?></li>
              <li>電話番号 :  <a href="tel:<?php echo $default['de_admin_company_tel']; ?>"><?php echo $default['de_admin_company_tel']; ?></a> / <a href="tel:010-3698-1111">010-3698-1111<span class="spc"> | </span><br class="mobr">ファックス : <?php echo $default['de_admin_company_fax']; ?></li>
              <li><a href="mailto:<?php echo $default['de_admin_info_email']; ?>" >メール : <?php echo $default['de_admin_info_email']; ?></a></li>
            </ul>
          </address>
          <small>
            MEKエンジニアリングコーポレーション & 株式会社MEK。全著作権所有。 
            <!-- <span class="spc"> | </span> <br class="br_mo"><a href="https://designtalktalk.com/home/" target="_blank">Design by DesignTalkTalk</a> -->
          </small>
      </div>
      </div>
      <div class="footer_link">
        <ul class="footer_link_list">
          <li><a href="https://blog.naver.com/mekeng" target="_blank"><img src="/public/img/icon_naver_blog.png" alt="MEK ネイバー ブログ リンク アイコン"></a></li>
          <li><a href="https://www.youtube.com/@mekeng_com" target="_blank"><img src="/public/img/icon_youtube.png" alt="MEK YouTube リンク アイコン"></a></li>
          <li class="ft_global">
            <a href="javascript:void(0)" class="ft_global_btn"><i class="ri-global-line"></i></a>
          <ul class="ft_global_list">
          <li><a href="/home">KO</a></li>
            <li><a href="#" class="active">JP</a></li>
            <li><a href="/cn">CN</a></li>
            <li><a href="/en">EN</a></li>
            <li><a href="/es">ES</a></li>
            <li><a href="/de">DE</a></li>
          </ul>
        </li>
        </ul>
        <a href="<?php echo G5_BBS_URL?>/board.php?bo_table=news_jp&wr_id=1" class="pt_pri">プライバシーポリシー</a>
      </div>
    </div>
  </div>
</footer>
<!-- 푸터 끝 -->



<?php
include_once(G5_PATH."/tail.sub.php");
