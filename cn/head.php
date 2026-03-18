<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

run_event('pre_head');

if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/head.php');
    return;
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/head.php');
    return;
}

include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');


// 팝업창 포함
// include_once(G5_PATH.'/popup.php');

// 문의폼은 메인/서브페이지에만 표시 (회원가입 등 제외)
if (!defined('_REGISTER_') && !preg_match('/(login|register|password|certify)/', $_SERVER['SCRIPT_NAME'])) {
    include_once(G5_PATH.'/simple_inquiry.php');
}
?>


<!-- 상단 시작 { -->
<?php include_once(G5_PATH.'/menu.json.php');
if (defined('_INDEX_'))
  $header_class = "main_head";
else
  $header_class = "sub_head";
?>
<header id="header" <?php if(!defined('_INDEX_')){echo "class=\"sub-hd\"";}?>>
  <div class="header_wrap">
    <div class="head_logo">
      <h1 class="logo_w"> <a href="<?php echo G5_URL?>"> <img src="<?php echo G5_IMG_URL?>/logo.png" alt="MEK"> </a> </h1>
      <h1 class="logo_b"> <a href="<?php echo G5_URL?>"> <img src="<?php echo G5_IMG_URL?>/logo_b.png" alt="MEK"> </a> </h1>
    </div>
    <div class="head_menu">
      <nav class="head_nav top_nav">
        <ul class="head_gnb">
          <?php
            foreach ($mainMenu as $k => $v) {
              echo "<li class=\"topmenu\">";
                echo "<a href=\"{$v->href}\">{$v->title}</a>";
                echo "<ul class=\"lnb\">";
                  foreach ($subMenu[$k] as $k2 => $v2) {
                    echo "<li><a href=\"{$v2->href}\" target=\"{$v2->target}\">{$v2->title}</a></li>";
                  }
                echo "</ul>";
              echo "</li>";
            }
          ?>

        </ul>
      </nav>
    </div>
    <div class="head_nav global_nav">
      <ul class="head_gnb">
        <li class="global"><a href="#"><img src="/public/img/icon/cn.png" alt="China"></a>
          <ul class="country">
            <li><a href="/home">한국어</a></li>
            <li><a href="/jp">日本語</a></li>
            <li><a href="#" class="active">中文</a></li>
            <li><a href="/en">English</a></li>
            <li><a href="/es">Español</a></li>
            <li><a href="/de">Deutsch</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>

  <div class="m-gnb">
    <nav class="m-nav">
      <div class="burger-menu">
        <div class="burger">
          <div class="burger-line"></div>
          <div class="burger-line"></div>
          <div class="burger-line"></div>
        </div>
      <!-- burger end -->
    </div>
    </nav>
    <div class="burger_back">
    </div>
  </div>
</header>

<!-- 상단 끝 -->

<div id="sitemap">
  <div class="sitemap">
    <div class="sitemap_left">
        <h1 class="logo_b container"> <a href="<?php echo G5_URL?>"> <img src="<?php echo G5_IMG_URL?>/logo_b.png" alt="MEK"> </a> </h1>
      <div class="site_left_txt">
        <h2 class="container">WEB质量管理体系专业公司,<br>MEK & MEK Engineering Corporation</h2>
        <p class="container">Your Technical Partner<br>For WEB Application</p>
      </div>
      <div class="site_left_add container">
        <p>仁川广域市富平区富平大路313街30<br>
          <small>Copyright(c) MEK Engineering Corp. & MEK Inc. All rights reserved.</small></p>
      </div>
    </div>
    <div class="sitemap-middle">
      <div class="head_nav global_nav container">
        <ul class="head_gnb">
          <li class="global"><a href="#"><i class="ri-settings-line" style="font-size: 1em; color: #bbb;"></i></a>
            <ul class="country">
              <li><a href="/home">KO</a></li>
              <li><a href="/jp">JP</a></li>
              <li><a href="#" class="active">CN</a></li>
              <li><a href="/en">EN</a></li>
              <li><a href="/es">ES</a></li>
              <li><a href="/de">DE</a></li>
            </ul>
          </li>
        </ul>
      </div>
      <nav id="map-nav" class="container">
        <ul id="map-gnb">
          <?php
            foreach ($mainMenu as $k => $v) {
              echo "<li class=\"map_menu\">";
                echo "<span class=\"map_wrap\">";
                echo "<a href=\"{$v->href}\">{$v->title}</a></span>";
                echo "<ul class=\"lnb_two\">";
                  foreach ($subMenu[$k] as $k2 => $v2) {
                  echo "<li><a href=\"{$v2->href}\" target=\"{$v2->target}\">{$v2->title}</a></li>";
                  }
                echo "</ul>";

              echo "</li>";
            }
          ?>
        </ul><!--m_gnb-->

      </nav>
    </div>
    <div class="sitemap-right">
      <p class="site_close"><i class="ri-close-line"></i></p>
      <p class="site_map_txt"><span>SITE MAP</span></p>
    </div>
  </div>
</div><!-- #sitemap  End -->

<?php if(!defined('_INDEX_')) { // index 아닐때만 실행 ?>
<?php include_once(G5_PATH.'/menu.json.php');?>
<?php if($bo_table == "history_cn") { $menuCodeParent = 0; $menuCodeChild = 1;}
else if($bo_table == "partner_cn") { $menuCodeParent = 0; $menuCodeChild = 2;}
else if($bo_table == "certif_cn") { $menuCodeParent = 0; $menuCodeChild = 3;}
else if($bo_table == "patent_cn") { $menuCodeParent = 0; $menuCodeChild = 3;}
else if($bo_table == "license_cn") { $menuCodeParent = 0; $menuCodeChild = 3;}
else if($bo_table == "pro_cn") { $menuCodeParent = 2; $menuCodeChild = 0;}
else if($bo_table == "pro02_cn") { $menuCodeParent = 2; $menuCodeChild = 1;}
else if($bo_table == "pro03_cn") { $menuCodeParent = 2; $menuCodeChild = 2;}
else if($bo_table == "pro04_cn") { $menuCodeParent = 2; $menuCodeChild = 3;}
else if($bo_table == "catalog_cn") { $menuCodeParent = 3; $menuCodeChild = 0;}
else if($bo_table == "news_cn") { $menuCodeParent = 4; $menuCodeChild = 3;}
else if($co_id == "privacy") { $menuCodeParent = 5; $menuCodeChild = 0;}

  ?>

  <section id="s_top" class="bg0<?php echo $menuCodeParent + 1?>">
    <div class="s_top_h2_wrap sub-container">
      <h2 class="tit"><?php echo $mainMenu[$menuCodeParent]->title?></h2>
      <h2 class="tit-p"><?php echo $mainMenu[$menuCodeParent]->p_title?></h2>
    </div>
  </section>
  <section id="snb_wrap"<?php if(defined('_content_')){echo "class=\"exception\"";}?>>
    <ul id="snb" class="sub-container">
      <li><a href="<?=G5_URL?>"><i class="ri-home-4-line"></i></a></li>
      <li>
        <?php echo $mainMenu[$menuCodeParent]->title?> <span class="arrow"><i class="ri-arrow-down-s-line"></i></span>
        <ul class="lnb">
          <?php
            foreach ($mainMenu as $k => $v) {
            echo "<li>";
              echo "<a href=\"{$v->href}\"  target=\"{$v->target}\">{$v->title}</a>";
            echo "</li>";
            }
          ?>
        </ul>
      </li>
      <li>
        <?php echo $subMenu[$menuCodeParent][$menuCodeChild]->title?> <span class="arrow"><i class="ri-arrow-down-s-line"></i></span>
        <ul class="lnb">
            <?php
              foreach ($subMenu[$menuCodeParent] as $k => $v) {
                $active = '';
                if ($k === $menuCodeChild) $active = ' class="active"';
                echo "<li><a href=\"{$v->href}\"{$active}>{$v->title}</a></li>";
              }
            ?>
        </ul>
      </li>
    </ul>
  </section>
  <div id="sub" class="sub0<?php echo $menuCodeParent + 1?>">
    <div class="subtitle_wrap sub-container" data-aos="fade-up"  data-aos-duration="2000">
      <h3 class="eng_title">
        <?php echo $subMenu[$menuCodeParent][$menuCodeChild]->p_title?>
      </h3>
      <div class="sub_title">
        <h3>
          <?php echo $subMenu[$menuCodeParent][$menuCodeChild]->title?>
        </h3>
        <p class="sub_description">
          <?php echo $subMenu[$menuCodeParent][$menuCodeChild]->text?>
        </p>
      </div>
    </div>

<?php }?>

<!-- <script>
$(document).ready(function() {
    $(".head_nav .global .country").on('click', 'li:not(:first-child) a', function(){
        alert("페이지 준비중입니다");
    });
});

</script> -->
