<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/css/style.css">', 0);
$thumb_width = 230;
$thumb_height = 300;
$list_count = (is_array($list) && $list) ? count($list) : 0;

//본문 추출시 아래코드 적절한 위치에 추가
//$wr_content = preg_replace("/<(.*?)\>/","",$list[$i]['wr_content']);
//$wr_content = preg_replace("/&nbsp;/","",$wr_content);
//$wr_content = cut_str(get_text($wr_content),120);
//echo $wr_content;

?>

<!-- Swiper 5.4.3 { -->
<script src="<?php echo $latest_skin_url ?>/js/swiper.js"></script>
<link rel="stylesheet" href="<?php echo $latest_skin_url ?>/css/swiper.css">
<!-- } -->

<div class="swiper-container swiper-container-slide" style="padding-bottom:50px;">
    <div class="swiper-wrapper">

        <?php
            for ($i=0; $i<$list_count; $i++) {
            $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height, false, true);

            if($list[$i]['icon_secret']) {
                $img = $latest_skin_url.'/img/sec.png';
            } else {
                if($thumb['src']) {
                    $img = $thumb['src'];
                } else {
                    $img = $latest_skin_url.'/img/noimg.png';
                    $thumb['alt'] = '이미지가 없습니다.';
                }
            }
            $img_content = '<img src="'.$img.'" alt="'.$thumb['alt'].'" >';
            $wr_href = get_pretty_url($bo_table, $list[$i]['wr_id']);
        ?>

        <div class="swiper-slide swiper-slide-slide">

            <a href="<?php echo $wr_href; ?>"><?php echo run_replace('thumb_image_tag', $img_content, $thumb); ?></a>

            <?php if ($list[$i]['icon_new']) { ?>
                <div class="slide_list_new">New</div>
            <?php } ?>
            <div class="slide_gap">
                <ul>
                    <li class="slide_title cut"><a href="<?php echo $wr_href; ?>"><?php echo $list[$i]['subject'] ?></a></li>
                    <li class="slide_date">
                        <?php
                            if($list[$i]['ca_name']) {
                                echo $list[$i]['ca_name']."　";
                            }
                            ?>
                        <?php echo $list[$i]['datetime'] ?>　
                        <?php
                            if ($list[$i]['comment_cnt']) {
					           echo "<span class='slide_comm'>+".$list[$i]['comment_cnt']."</span>";
                            }
                        ?>
                    </li>
                </ul>
            </div>
        </div>

        <?php } ?>

    </div>

    <div class="swiper-pagination swiper-pagination-slide"></div>


</div>
<script>
    var swiper = new Swiper('"#main_06 .up .swiper-container-slide', {
        //slidesPerColumnFill: 'row',
        slidesPerView: 5, // 가로갯수
        spaceBetween: 15, // 간격
        touchRatio: 1, // 드래그 가능여부(1, 0)

        autoplay: {
            delay: 4000 // 자동롤링 딜레이 (1000 = 1초)
        },

        pagination: { // 페이징
            el: '.swiper-pagination-slide',
            clickable: true,
        },

        breakpoints: { // 반응형 처리
            1280: {
                //slidesPerColumnFill: 'row',
                slidesPerView: 5,
                spaceBetween: 15
            },
            1024: {
                //slidesPerColumnFill: 'row',
                slidesPerView: 4,
                spaceBetween: 15
            },
            768: {
                //slidesPerColumnFill: 'row',
                slidesPerView: 3,
                spaceBetween: 15
            },
            480: {
                //slidesPerColumnFill: 'row',
                slidesPerView: 2,
                spaceBetween: 15
            },
            10: {
                //slidesPerColumnFill: 'row',
                slidesPerView: 1,
                spaceBetween: 15
            }
        }

    });
</script>
