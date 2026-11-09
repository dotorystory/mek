<?php
if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
include_once(G5_THEME_PATH.'/inc/youtube_bootstrap.php');

add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
$thumb_width = 320;
$thumb_height = 180;
$list_count = (is_array($list) && $list) ? count($list) : 0;
?>

<div class="pic_lt">
    <h2 class="lat_title"><a href="<?php echo get_pretty_url($bo_table); ?>"><?php echo $bo_subject ?></a></h2>
    <ul>
    <?php
    for ($i=0; $i<$list_count; $i++) {
        $youtube_id = www_yt_id_from_list_item($list[$i]);
        $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height, false, true);
        $wr_href = get_pretty_url($bo_table, $list[$i]['wr_id']);
        if ($youtube_id !== '') {
            $img = www_yt_thumb_url($youtube_id);
            $alt = get_text($list[$i]['subject']);
        } elseif (!empty($thumb['src'])) {
            $img = $thumb['src'];
            $alt = $thumb['alt'];
        } else {
            $img = G5_IMG_URL.'/no_img.png';
            $alt = '이미지가 없습니다.';
        }
        $img_content = '<img src="'.$img.'" alt="'.$alt.'" >';
    ?>
        <li class="galley_li">
            <?php if ($youtube_id !== '') { ?>
                <a href="javascript:void(0)" class="youtube-thumb" onclick="playYoutubeVideo('<?php echo $youtube_id; ?>')">
                    <img src="<?php echo www_yt_thumb_url($youtube_id); ?>" alt="<?php echo get_text($list[$i]['subject']); ?>" onerror="this.onerror=null;this.src='<?php echo www_yt_thumb_url($youtube_id, 'hqdefault'); ?>';">
                </a>
            <?php } else { ?>
                <a href="<?php echo $wr_href; ?>" class="lt_img"><?php echo run_replace('thumb_image_tag', $img_content, $thumb); ?></a>
            <?php } ?>
            <?php
            if ($list[$i]['icon_secret']) echo "<i class=\"fa fa-lock\" aria-hidden=\"true\"></i><span class=\"sound_only\">비밀글</span> ";
            echo "<a href=\"".$wr_href."\"> ";
            echo $list[$i]['is_notice'] ? "<strong>".$list[$i]['subject']."</strong>" : $list[$i]['subject'];
            echo "</a>";
            if ($list[$i]['comment_cnt']) echo "<span class=\"lt_cmt\">".$list[$i]['wr_comment']."</span>";
            ?>
        </li>
    <?php } ?>
    <?php if ($list_count == 0) { ?>
    <li class="empty_li">게시물이 없습니다.</li>
    <?php } ?>
    </ul>
    <a href="<?php echo get_pretty_url($bo_table); ?>" class="lt_more"><span class="sound_only"><?php echo $bo_subject ?></span><i class="fa fa-plus" aria-hidden="true"></i><span class="sound_only"> 더보기</span></a>
</div>

<?php
www_yt_enqueue_modal();
?>
