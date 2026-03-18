<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
//add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/swiper.css">', 0);
//add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);

// $thumb_width = 210;
// $thumb_height = 150;
$thumb_width = 244;
$thumb_height = 95;
$list_count = (is_array($list) && $list) ? count($list) : 0;
?>


<link rel="stylesheet" href="./skin/latest/swiper_pic_block/slick.css">
<link rel="stylesheet" href="./skin/latest/swiper_pic_block/style.css">
<script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="./skin/latest/swiper_pic_block/slick.js"></script>


<div class="pic_lt main-partners-list">
    <h2 class="lat_title"><a href="<?php echo get_pretty_url($bo_table); ?>"><?php echo $bo_subject ?></a></h2>

    <ul class="slick-list" style="transition-timing-function: linear!important;">
    <?php
    for ($i=0; $i<$list_count; $i++) {
    $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height, false, true);

    if($thumb['src']) {
        $img = $thumb['src'];
    } else {
        $img = G5_IMG_URL.'/no_img.png';
        $thumb['alt'] = '이미지가 없습니다.';
    }
    $img_content = '<img src="'.$img.'" alt="'.$thumb['alt'].'" >';
    $wr_href = get_pretty_url($bo_table, $list[$i]['wr_id']);
    ?>
        <li class="gallery_li">
          <?php if($is_admin){ ?>
            <a href="<?php echo $wr_href; ?>" class="lt_img"><?php echo run_replace('thumb_image_tag', $img_content, $thumb); ?></a>
            <?php } else {?>
              <a href="javascript:" class="lt_img" style="cursor:default;"><?php echo run_replace('thumb_image_tag', $img_content, $thumb); ?></a>
            <?php } ?>
            <?php
            if ($list[$i]['icon_secret']) echo "<i class=\"fa fa-lock\" aria-hidden=\"true\"></i><span class=\"sound_only\">비밀글</span> ";

            echo "<a href=\"".$wr_href."\"> ";
            if ($list[$i]['is_notice'])
                echo "<strong>".$list[$i]['subject']."</strong>";
            else
                echo $list[$i]['subject'];
            echo "</a>";

			if ($list[$i]['icon_new']) echo "<span class=\"new_icon\">N<span class=\"sound_only\">새글</span></span>";
            if ($list[$i]['icon_hot']) echo "<span class=\"hot_icon\">H<span class=\"sound_only\">인기글</span></span>";

            // if ($list[$i]['link']['count']) { echo "[{$list[$i]['link']['count']}]"; }
            // if ($list[$i]['file']['count']) { echo "<{$list[$i]['file']['count']}>"; }

			// echo $list[$i]['icon_reply']." ";
			// if ($list[$i]['icon_file']) echo " <i class=\"fa fa-download\" aria-hidden=\"true\"></i>" ;
            // if ($list[$i]['icon_link']) echo " <i class=\"fa fa-link\" aria-hidden=\"true\"></i>" ;

            if ($list[$i]['comment_cnt'])  echo "
            <span class=\"lt_cmt\">".$list[$i]['wr_comment']."</span>";

            ?>

            <div class="lt_info">
				<span class="lt_nick"><?php echo $list[$i]['name'] ?></span>
            	<span class="lt_date"><?php echo $list[$i]['datetime2'] ?></span>
            </div>
        </li>
    <?php }  ?>
    <?php if ($list_count == 0) { //게시물이 없을 때  ?>
    <li class="empty_li">게시물이 없습니다.</li>
    <?php }  ?>
    </ul>

    <a href="<?php echo get_pretty_url($bo_table); ?>" class="lt_more"><span class="sound_only"><?php echo $bo_subject ?></span>더보기</a>
</div>
<script>

//파트너
$(document).ready(function() {
  $('#main_04 .main_partner_content .main-partners-roller.right .main-partners-list .slick-list').attr('dir', 'rtl').addClass('rtl');

  function rtl_slick() {
    if ($('#main_04 .main_partner_content .main-partners-roller.right .main-partners-list .slick-list').hasClass("rtl")) {
      return true;
    } else {
      return false;
    }
  }

  $('#main_04 .main_partner_content .main-partners-roller.left .main-partners-list .slick-list').slick({
    speed: 4000,
    autoplay: true,
    autoplaySpeed: 0,
    centerMode: false,
    cssEase: 'linear',
    slidesToShow: 1,
    draggable: false,
    focusOnSelect: false,
    pauseOnFocus: false,
    pauseOnHover: false,
    slidesToScroll: 1,
    variableWidth: true,
    infinite: true,
    initialSlide: 1,
    arrows: false,
    buttons: false,
    rtl: false,
    responsive: [{
        breakpoint: 960,
        settings: {
          slidesToShow: 4
        }
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 5
        }
      }
    ]
  });

  $('#main_04 .main_partner_content .main-partners-roller.right .main-partners-list .slick-list').slick({
    speed: 4000,
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: false,
    dots: false,
    autoplay: true,
    centerMode: true,
    cssEase: 'linear',
    infinite: true,
    initialSlide: 1,
    autoplaySpeed: 0,
    pauseOnHover: false,
    touchThreshold: 50,
    swipe: false,
    touchMove: false,
    variableWidth: true,
    rtl: rtl_slick(),
    responsive: [{
        breakpoint: 960,
        settings: {
          slidesToShow: 4
        }
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 5
        }
      }
    ]
  });
});

</script>
