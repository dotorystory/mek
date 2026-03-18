<?php
/* copyright(c) WEBsiting.co.kr */
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/jquery.bxslider.css">', 0);
add_javascript('<script src="'.G5_JS_URL.'/jquery.bxslider.js"></script>', 10);
// $thumb_width = 955;
// $thumb_height = 415;
$thumb_width = 244;
$thumb_height = 95;
$list_count = (is_array($list) && $list) ? count($list) : 0;
?>

<div class="bannerImages bannerImages_<?php echo $board['bo_table']?>">
    <h2 class="sound_only"><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>"><?php echo $bo_subject ?></a></h2>
    <ul>
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
    ?>
        <li>
            <i class="lt_img"><?php echo $img_content; ?></i>
            <?php


            if($list[$i]['wr_link1']){
				echo "<a href=\"".$list[$i]['wr_link1']."\"> ";
			} else {
				echo '<a>';
			}
            if($list[$i]['subject'] == '제목없음') {
				echo '<!-- 제목없음 -->';
			} else {
				echo "<b>";
				echo $list[$i]['subject'];
				echo "</b>";
			}
            echo "</a>";

            ?>
        </li>
    <?php }  ?>
    </ul>
    <?php if (count($list) == 0) { //게시물이 없을 때  ?>
    <p class="empty_li">등록된 내용이 없습니다.</p>
    <?php }  ?>

</div>
<?php if (count($list)) { //게시물이 있다면 ?>
<script>
let tickerSpeed = 2;

let flickity = null;
let isPaused = false;
const slideshowEl = document.querySelector('.js-slideshow');


//
//   Functions
//
//////////////////////////////////////////////////////////////////////

const update = () => {
if (isPaused) return;
if (flickity.slides) {
  flickity.x = (flickity.x - tickerSpeed) % flickity.slideableWidth;
  flickity.selectedIndex = flickity.dragEndRestingSelect();
  flickity.updateSelectedSlide();
  flickity.settle(flickity.x);
}
window.requestAnimationFrame(update);
};

const pause = () => {
isPaused = true;
};

const play = () => {
if (isPaused) {
  isPaused = false;
  window.requestAnimationFrame(update);
}
};


//
//   Create Flickity
//
//////////////////////////////////////////////////////////////////////

flickity = new Flickity(slideshowEl, {
autoPlay: false,
prevNextButtons: true,
pageDots: false,
draggable: true,
wrapAround: true,
selectedAttraction: 0.015,
friction: 0.25
});
flickity.x = 0;


//
//   Add Event Listeners
//
//////////////////////////////////////////////////////////////////////

slideshowEl.addEventListener('mouseenter', pause, false);
slideshowEl.addEventListener('focusin', pause, false);
slideshowEl.addEventListener('mouseleave', play, false);
slideshowEl.addEventListener('focusout', play, false);

flickity.on('dragStart', () => {
isPaused = true;
});


//
//   Start Ticker
//
//////////////////////////////////////////////////////////////////////

update();
</script>
<?php } ?>
