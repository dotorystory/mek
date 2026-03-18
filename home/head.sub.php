<?php
// 이 파일은 새로운 파일 생성시 반드시 포함되어야 함
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 테마 head.sub.php 파일
if(!defined('G5_IS_ADMIN') && defined('G5_THEME_PATH') && is_file(G5_THEME_PATH.'/head.sub.php')) {
    require_once(G5_THEME_PATH.'/head.sub.php');
    return;
}

$g5_debug['php']['begin_time'] = $begin_time = get_microtime();

if (!isset($g5['title'])) {
    $g5['title'] = $config['cf_title'];
    $g5_head_title = $g5['title'];
}
else {
    // 상태바에 표시될 제목
    $g5_head_title = implode(' | ', array_filter(array($g5['title'], $config['cf_title'])));
}

$g5['title'] = strip_tags($g5['title']);
$g5_head_title = strip_tags($g5_head_title);

// 현재 접속자
// 게시판 제목에 ' 포함되면 오류 발생
$g5['lo_location'] = addslashes($g5['title']);
if (!$g5['lo_location'])
    $g5['lo_location'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
$g5['lo_url'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
if (strstr($g5['lo_url'], '/'.G5_ADMIN_DIR.'/') || $is_admin == 'super') $g5['lo_url'] = '';

/*
// 만료된 페이지로 사용하시는 경우
header("Cache-Control: no-cache"); // HTTP/1.1
header("Expires: 0"); // rfc2616 - Section 14.21
header("Pragma: no-cache"); // HTTP/1.0
*/
?>
<!doctype html>
<html lang="ko">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-P6JDSWZJ');</script>
    <!-- End Google Tag Manager -->
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-PTMS6HLEJR"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-PTMS6HLEJR');
    </script>
<meta charset="utf-8">
<?php
if (G5_IS_MOBILE) {
    echo '<meta name="viewport" id="meta_viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=5">'.PHP_EOL;
    echo '<meta name="HandheldFriendly" content="true">'.PHP_EOL;
    echo '<meta name="format-detection" content="telephone=no">'.PHP_EOL;
} else {
    echo '<meta name="viewport" id="meta_viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=5">'.PHP_EOL;
    echo '<meta http-equiv="imagetoolbar" content="no">'.PHP_EOL;
    echo '<meta http-equiv="X-UA-Compatible" content="IE=Edge">'.PHP_EOL;
}

if($config['cf_add_meta'])
    echo $config['cf_add_meta'].PHP_EOL;
?>
<!-- <title><?php echo $config['cf_title']; ?></title> -->
<title>엠이케이(주) & 맥엔지니어링 | 두께측정기 글로벌 리더</title>
  <link rel="canonical" href="https://www.mekeng.com/">

  <!-- SEO -->
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
  <meta name="description" content="엠이케이(주) & 맥엔지니어링: 두께 질량 밀도 측정 및 제어 시스템의 글로벌 리더. 이차전지 및 각종 필름 품질 솔루션 전문.">
  <meta name="keywords" content="맥엔지니어링, 엠이케이, MEK, 두께측정기, 질량측정기, 밀도측정기, 두께측정, 웹프로세스제어, 이차전지, 리튬전지, 전고체, 박막,롤투롤, 스마트팩토리, APC, 품질관리">

  <!-- 회사 정보 -->
  <meta name="author" content="엠이케이(주) & 맥엔지니어링">
  <meta name="copyright" content="© 2025 엠이케이(주) & 맥엔지니어링. All rights reserved.">
  <meta name="content-language" content="ko">
  <meta http-equiv="Content-Language" content="ko">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:locale" content="ko_KR">
  <meta property="og:url" content="https://www.mekeng.com/">
  <meta property="og:title" content="엠이케이(주) & 맥엔지니어링 | 두께측정기 글로벌 리더">
  <meta property="og:site_name" content="엠이케이(주) & 맥엔지니어링">
  <meta property="og:description" content="두께·밀도·질량 측정의 혁신. 맥엔지니어링의 초정밀 측정 시스템은 이차전지, 박막, 필름, 금속 산업의 품질과 생산성을 극대화합니다.">
  <meta property="og:image" content="https://www.mekeng.com/mek-sns.png">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="엠이케이(주) & 맥엔지니어링 | 웹프로세스 측정제어시스템">
  <meta name="twitter:description" content="웹프로세스 측정 및 제어시스템의 글로벌 리더.">
  <meta name="twitter:image" content="https://www.mekeng.com/mek-sns.png">
  <meta name="twitter:site" content="@MEKofficial">

  <!-- Favicon / PWA -->
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">


    <!-- 웹앱 서비스워커 등록 -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('<?php echo G5_URL ?>/sw.js');
        }
    </script>


  <!-- Google Adsense -->
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5471485491705212"
      crossorigin="anonymous"></script>



<?php
if (defined('G5_IS_ADMIN')) {
    if(!defined('_THEME_PREVIEW_'))
        echo '<link rel="stylesheet" href="'.run_replace('head_css_url', G5_ADMIN_URL.'/css/admin.css?ver='.G5_CSS_VER, G5_URL).'">'.PHP_EOL;
} else {
    $shop_css = '';
    if (defined('_SHOP_')) $shop_css = '_shop';
    echo '<link rel="stylesheet" href="'.run_replace('head_css_url', G5_CSS_URL.'/'.(G5_IS_MOBILE?'mobile':'default').$shop_css.'.css?ver='.G5_CSS_VER, G5_URL).'">'.PHP_EOL;
}
?>
<!--[if lte IE 8]>
<script src="<?php echo G5_JS_URL ?>/html5.js"></script>
<![endif]-->
<script>
// 자바스크립트에서 사용하는 전역변수 선언
var g5_url       = "<?php echo G5_URL ?>";
var g5_bbs_url   = "<?php echo G5_BBS_URL ?>";
var g5_is_member = "<?php echo isset($is_member)?$is_member:''; ?>";
var g5_is_admin  = "<?php echo isset($is_admin)?$is_admin:''; ?>";
var g5_is_mobile = "<?php echo G5_IS_MOBILE ?>";
var g5_bo_table  = "<?php echo isset($bo_table)?$bo_table:''; ?>";
var g5_sca       = "<?php echo isset($sca)?$sca:''; ?>";
var g5_editor    = "<?php echo ($config['cf_editor'] && $board['bo_use_dhtml_editor'])?$config['cf_editor']:''; ?>";
var g5_cookie_domain = "<?php echo G5_COOKIE_DOMAIN ?>";
<?php if(defined('G5_USE_SHOP') && G5_USE_SHOP) { ?>
var g5_shop_url = "<?php echo G5_SHOP_URL; ?>";
<?php } ?>
<?php if(defined('G5_IS_ADMIN')) { ?>
var g5_admin_url = "<?php echo G5_ADMIN_URL; ?>";
<?php } ?>
</script>
<?php
add_javascript('<script src="'.G5_JS_URL.'/jquery-1.12.4.min.js"></script>', 0);
// add_javascript('<script src="'.G5_JS_URL.'/jquery-3.7.0.min.js"></script>', 0);
add_javascript('<script src="'.G5_JS_URL.'/jquery-migrate-1.4.1.min.js"></script>', 0);
if (defined('_SHOP_')) {
    if(!G5_IS_MOBILE) {
        add_javascript('<script src="'.G5_JS_URL.'/jquery.shop.menu.js?ver='.G5_JS_VER.'"></script>', 0);
    }
} else {
    add_javascript('<script src="'.G5_JS_URL.'/jquery.menu.js?ver='.G5_JS_VER.'"></script>', 0);
}
add_javascript('<script src="'.G5_JS_URL.'/common.js?ver='.G5_JS_VER.'"></script>', 0);
add_javascript('<script src="'.G5_JS_URL.'/wrest.js?ver='.G5_JS_VER.'"></script>', 0);
add_javascript('<script src="'.G5_JS_URL.'/placeholders.min.js"></script>', 0);
add_javascript('<script src="'.G5_JS_URL.'/dxee.common.js"></script>', 0);
add_javascript('<script src="'.G5_JS_URL.'/dxee.animation.js"></script>', 0);
add_javascript('<script src="'.G5_JS_URL.'/dxee.slide.js"></script>', 0);

add_javascript('<script src="'.G5_JS_URL.'/aos.js"></script>', 0);
add_javascript('<script src="'.G5_JS_URL.'/fullpage.min.js"></script>', 0);
add_javascript('<script src="'.G5_JS_URL.'/swiper.js"></script>', 0);
add_javascript('<script src="'.G5_JS_URL.'/parallax.js"></script>', 0);
add_javascript('<script src="'.G5_JS_URL.'/slick.js"></script>', 0);
add_javascript('<script src="'.G5_JS_URL.'/script.js"></script>', 0);
add_javascript('<script src="'.G5_JS_URL.'/KeyShotXR.js"></script>', 0);

// add_stylesheet('<link rel="stylesheet" href="'.G5_CSS_URL.'/fullpage_min.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_CSS_URL.'/swiper.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_CSS_URL.'/aos.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_CSS_URL.'/slick.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_JS_URL.'/font-awesome/css/font-awesome.min.css">', 0);
add_stylesheet('<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0">', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_CSS_URL.'/style.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_CSS_URL.'/mo_style.css">', 0);
if(G5_IS_MOBILE) {
    add_javascript('<script src="'.G5_JS_URL.'/modernizr.custom.70111.js"></script>', 1); // overflow scroll 감지
}
if(!defined('G5_IS_ADMIN'))
    echo $config['cf_add_script'];
?>
<script src="https://unpkg.com/pdfobject"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js"></script>
</head>
<body<?php echo isset($g5['body_script']) ? $g5['body_script'] : ''; ?>>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P6JDSWZJ"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
<?php
if ($is_member) { // 회원이라면 로그인 중이라는 메세지를 출력해준다.
    $sr_admin_msg = '';
    if ($is_admin == 'super') $sr_admin_msg = "최고관리자 ";
    else if ($is_admin == 'group') $sr_admin_msg = "그룹관리자 ";
    else if ($is_admin == 'board') $sr_admin_msg = "게시판관리자 ";

    echo '<div id="hd_login_msg">'.$sr_admin_msg.get_text($member['mb_nick']).'님 로그인 중 ';
    echo '<a href="'.G5_BBS_URL.'/logout.php">로그아웃</a></div>';
}
