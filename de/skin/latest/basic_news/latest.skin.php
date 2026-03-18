<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
//add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
//$thumb_width = 90;
//$thumb_height = 65;
?>


<link rel="stylesheet" href="./style.css">



<ul class="p2_tit">
    <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>"><?php echo $bo_subject ?></a>
    <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>" class="dot_r"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
</ul>
<div class="bd1">

    <?php
    for ($i=0; $i<count($list); $i++) {
    $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height, false, true);

    if($thumb['src']) {
        $img = $thumb['src'];
    } else {
       // $img = G5_IMG_URL.'/no_img.png';
        $img = $latest_skin_url.'/img/no.png';
        //$thumb['alt'] = '이미지가 없습니다.';
    }
    $img_content = '<img src="'.$img.'" alt="'.$thumb['alt'].'" style="border:1px solid rgba(0,0,0,0.1); box-sizing: border-box;">';
    ?>

    <div class="news_div">
        <div class="news_ul01">
            <li class="news_ll01"><a href="<?php echo $list[$i]['href'] ?>" class="elss3"><?php echo $list[$i]['subject'] ?></a></li>
            <li class="news_ll03">
                <strong class="month">
                  <?php echo sprintf("%02d",$row[$i]['wr_1']); ?>월
                </strong>　

            </li>
            <li class="news_ll02">
                <a href="<?php echo $list[$i]['href'] ?>" class="elss2" style="color:#999">
                    <?php echo strip_tags($list[$i]['wr_content'])?>
                </a>
            </li>

        </div>
        <ul class="news_ul02" onclick="location.href='<?php echo $list[$i]['href'] ?>';">
            <i class="fa fa-plus" aria-hidden="true"></i>
        </ul>
        <div style="clear:both"></div>
    </div>




    <?php } if (count($list) == 0) { //게시물이 없을 때  ?>
    <div class="news_div">
        <ul class="news_ul01">
            <li class="news_ll01">등록된 게시물이 없습니다.</li>
        </ul>
        <div style="clear:both"></div>
    </div>
    <?php }  ?>

</div>
