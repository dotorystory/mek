<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
include_once(G5_THEME_PATH.'/inc/youtube_bootstrap.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
$thumb_width = 300;
$thumb_height = 300;
$list_count = (is_array($list) && $list) ? count($list) : 0;
?>

<div class="pic_li_lt">
    <h2 class="lat_title"><a href="<?php echo get_pretty_url($bo_table); ?>"><?php echo $bo_subject ?></a></h2>
    <ul>
    <?php
    for ($i=0; $i<$list_count; $i++) {
        
        $img_link_html = '';
        $first_has_thumb = false;

        $wr_href = get_pretty_url($bo_table, $list[$i]['wr_id']);
        
        if( $i === 0 ) {
            // placeholder 없음: 첨부/유튜브 썸네일만 표시 (trole tr_top main과 동일)
            $lt_thumb = www_yt_list_or_youtube_thumb($bo_table, $list[$i]['wr_id'], 100, 100, $list[$i], false);
            $thumb = $lt_thumb['thumb'];
            if ($lt_thumb['src'] !== '') {
                $first_has_thumb = true;
                $img = $lt_thumb['src'];
                $alt = $lt_thumb['alt'] !== '' ? $lt_thumb['alt'] : get_text(strip_tags($list[$i]['subject']), 0);
                $fallback = $lt_thumb['youtube_id'] !== '' ? www_yt_thumb_url($lt_thumb['youtube_id'], 'hqdefault') : '';
                $onerror = $fallback !== '' ? ' onerror="this.onerror=null;this.src=\''.$fallback.'\';"' : '';
                $img_content = '<img src="'.$img.'" alt="'.$alt.'"'.$onerror.'>';
                if ($lt_thumb['youtube_id'] !== '') {
                    $img_link_html = '<a href="javascript:void(0)" class="lt_img youtube-thumb" onclick="playYoutubeVideo(\''.$lt_thumb['youtube_id'].'\')">'.run_replace('thumb_image_tag', $img_content, $thumb).'</a>';
                } else {
                    $img_link_html = '<a href="'.$wr_href.'" class="lt_img" >'.run_replace('thumb_image_tag', $img_content, $thumb).'</a>';
                }
            }
        }
        $li_class = '';
        if ($i === 0) {
            $li_class = 'first_item'.($first_has_thumb ? '' : ' no_img');
        }
    ?>
        <li class="<?php echo $li_class; ?>">
            <?php echo $img_link_html; ?>
            <div class="lt_content">
                <?php
                echo "<a href=\"".$wr_href."\" class=\"pic_li_tit\">";
                if ($list[$i]['icon_secret']) echo "<i class=\"fa fa-lock\" aria-hidden=\"true\"></i><span class=\"sound_only\">비밀글</span> ";
                echo $list[$i]['subject'];
                if ($list[$i]['icon_new']) echo "<span class=\"new_icon\">N<span class=\"sound_only\">새글</span></span>";
                if ($list[$i]['icon_hot']) echo "<span class=\"hot_icon\">H<span class=\"sound_only\">인기글</span></span>";
                if ($list[$i]['comment_cnt']) echo "<span class=\"lt_cmt\">".$list[$i]['wr_comment']."</span>";
                echo "</a>";

                // 내용 미리보기 추가
                $content = strip_tags($list[$i]['wr_content']);
                echo "<div class=\"lt_content_preview\">".mb_substr($content, 0, 100)."...</div>";
                ?>
            </div>
        </li>
    <?php }  ?>
    <?php if ($list_count == 0) { //게시물이 없을 때  ?>
    <li class="empty_li">게시물이 없습니다.</li>
    <?php }  ?>
    </ul>
    <a href="<?php echo get_pretty_url($bo_table); ?>" class="lt_more"><span class="sound_only"><?php echo $bo_subject ?></span><i class="fa fa-plus" aria-hidden="true"></i><span class="sound_only"> 더보기</span></a>

</div>
<?php www_yt_enqueue_modal(); ?>
