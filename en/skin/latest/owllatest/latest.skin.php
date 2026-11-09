<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
// $thumb_width = 210;
// $thumb_height = 150;
$thumb_width = 244;
$thumb_height = 95;
?>
<link rel="stylesheet" href="<?php echo $latest_skin_url;?>/owl.carousel.min.css">
<link rel="stylesheet" href="<?php echo $latest_skin_url;?>/owl.theme.default.min.css">
<script src="<?php echo $latest_skin_url;?>/owl.carousel.js"></script>
<div class="pic_lt">
    <h2 class="lat_title"><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>"><?php echo $bo_subject ?></a></h2>
    <div class="owl-carousel owl-theme">
    <?php
    for ($i=0; $i<count($list); $i++) {
    $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height, false, true);

    if($thumb['src']) {
        $img = $thumb['src'];
    } else {
        $img = G5_IMG_URL.'/no_img.png';
        $thumb['alt'] = '이미지가 없습니다.';
    }
    $img_content = '<img src="'.$img.'" alt="'.$thumb['alt'].'" >';
    ?>
        <div class="item">
          <?php if($is_admin){ ?>
            <a href="<?php echo $list[$i]['href'] ?>"><?php echo $img_content; ?></a>
            <?php } else {?>
            <a href="javascript:" style="cursor:default;"><?php echo $img_content; ?></a>
              <?php } ?>
        </div>
    <?php }  ?>
    <?php if (count($list) == 0) { //게시물이 없을 때  ?>
    <li class="empty_li">게시물이 없습니다.</li>
    <?php }  ?>
  </div>
    <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>" class="lt_more"><span class="sound_only"><?php echo $bo_subject ?></span><i class="fa fa-plus" aria-hidden="true"></i><span class="sound_only"> 더보기</span></a>

</div>
<script>
            $(document).ready(function() {
              var owl = $('.main_banner01 .owl-carousel');
              owl.owlCarousel({
                items: 6,
                loop: true,
                margin: 30,
                autoplay: true,
                slideTransition: 'linear',
                autoplayTimeout: 3000,
                autoplaySpeed: 3000,
                smartSpeed: 3000,
                fluidSpeed: true,
                autoplayHoverPause: false,
                touchDrag: false,
                mouseDrag: false,
                dots:false,
                responsive:{
                             0:{
                                 items:2
                             },
                             600:{
                                 items:4
                             },
                             1000:{
                                 items:5
                             },
                             1400:{
                                 items:6
                             }
                         }
              });
              // $('.play').on('click', function() {
              //   owl.trigger('play.owl.autoplay', [1000])
              // })
              // $('.stop').on('click', function() {
              //   owl.trigger('stop.owl.autoplay')
              // })
            })

            $(document).ready(function() {
              var owl = $('.main_banner02 .owl-carousel');
              owl.owlCarousel({
                rtl:true,
                items: 6,
                loop: true,
                margin: 30,
                autoplay: true,
                slideTransition: 'linear',
                autoplayTimeout: 3000,
                autoplaySpeed: 3000,
                smartSpeed: 3000,
                fluidSpeed: true,
                autoplayHoverPause: false,
                touchDrag: false,
                mouseDrag: false,
                dots:false,
                responsive:{
                             0:{
                                 items:2
                             },
                             600:{
                                 items:4
                             },
                             1000:{
                                 items:5
                             },
                             1400:{
                                 items:6
                             }
                         }
              });
              // $('.play').on('click', function() {
              //   owl.trigger('play.owl.autoplay', [1000])
              // })
              // $('.stop').on('click', function() {
              //   owl.trigger('stop.owl.autoplay')
              // })
            })
          </script>
