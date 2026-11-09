<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if (!function_exists('get_youtube_video_id_for_thumb')) {
    function get_youtube_video_id_for_thumb($wr_content = '', $wr_link1 = '', $wr_link2 = '')
    {
        $haystack = trim((string) $wr_content . ' ' . (string) $wr_link1 . ' ' . (string) $wr_link2);
        if ($haystack === '') {
            return '';
        }
        if (function_exists('html_entity_decode')) {
            $haystack = html_entity_decode($haystack, ENT_QUOTES | (defined('ENT_HTML5') ? ENT_HTML5 : 0), 'UTF-8');
        }
        $patterns = array(
            '/youtube\.com\/shorts\/([A-Za-z0-9_-]{11})\b/',
            '/youtube\.com\/live\/([A-Za-z0-9_-]{11})\b/',
            '/youtube\.com\/embed\/([A-Za-z0-9_-]{11})\b/',
            '/(?:www\.)?music\.youtube\.com\/watch\?[^#]*\bv=([A-Za-z0-9_-]{11})\b/',
            '/m\.music\.youtube\.com\/watch\?[^#]*\bv=([A-Za-z0-9_-]{11})\b/',
            '/(?:www\.)?youtube\.com\/v\/([A-Za-z0-9_-]{11})\b/',
            '/youtu\.be\/([A-Za-z0-9_-]{11})\b/',
            '/(?:www\.)?youtube\.com\/watch\?[^#]*\bv=([A-Za-z0-9_-]{11})\b/',
            '/m\.youtube\.com\/watch\?[^#]*\bv=([A-Za-z0-9_-]{11})\b/',
        );
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $haystack, $m)) {
                return $m[1];
            }
        }
        return '';
    }
}

include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// 선택삭제로 인해 셀합치기가 가변적으로 변함
$colspan = 4;

if ($is_admin) $colspan++;

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$new_skin_url.'/style.css">', 0);
?>

<!-- 전체게시물 목록 시작 { -->
<form name="fnewlist" id="fnewlist" method="post" action="#" onsubmit="return fnew_submit(this);">
<input type="hidden" name="sw"       value="move">
<input type="hidden" name="view"     value="<?php echo $view; ?>">
<input type="hidden" name="sfl"      value="<?php echo $sfl; ?>">
<input type="hidden" name="stx"      value="<?php echo $stx; ?>">
<input type="hidden" name="bo_table" value="<?php echo $bo_table; ?>">
<input type="hidden" name="page"     value="<?php echo $page; ?>">
<input type="hidden" name="pressed"  value="">

<?php if ($is_admin) { ?>
<div class="admin_new_btn">
    <button type="submit" onclick="document.pressed=this.title" title="선택삭제" class="btn_b01 btn"><i class="fa fa-trash-o" aria-hidden="true"></i><span class="sound_only">선택삭제</span></button>
</div>
<?php } ?>
<div class="tbl_head01 tbl_wrap">
    <table>
    <thead>
    <tr>
        <?php if ($is_admin) { ?>
        <th scope="col" class="chk_box">
        	<input type="checkbox" id="all_chk" class="selec_chk">
            <label for="all_chk">
            	<span></span>
				<b class="sound_only">목록 전체</b>
            </label>
        </th>
        <?php } ?>
        <th scope="col">제목</th>
        <th scope="col">이름</th>
        <th scope="col">일시</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $i<count($list); $i++)
    {
        $num = $total_count - ($page - 1) * $config['cf_page_rows'] - $i;
        $bo_subject = cut_str($list[$i]['bo_subject'], 20);
        $wr_subject = get_text(cut_str($list[$i]['wr_subject'], 80));
        
        // 썸네일 (첨부·에디터 이미지 또는 유튜브 링크/본문)
        $thumb = get_list_thumbnail($list[$i]['bo_table'], $list[$i]['wr_id'], 100, 100);
        $thumb_img = '';
        $yt_id = get_youtube_video_id_for_thumb(
            isset($list[$i]['wr_content']) ? $list[$i]['wr_content'] : '',
            isset($list[$i]['wr_link1']) ? $list[$i]['wr_link1'] : '',
            isset($list[$i]['wr_link2']) ? $list[$i]['wr_link2'] : ''
        );
        if ($thumb['src']) {
            $thumb_img = '<div class="thumb"><img src="'.$thumb['src'].'" alt="'.$wr_subject.'"></div>';
        } elseif ($yt_id) {
            $thumb_img = '<div class="thumb"><img src="https://img.youtube.com/vi/'.$yt_id.'/mqdefault.jpg" alt="'.$wr_subject.'"></div>';
        }
    ?>
    <tr>
        <?php if ($is_admin) { ?>
        <td class="td_chk chk_box">
            <input type="checkbox" name="chk_bn_id[]" value="<?php echo $i; ?>" id="chk_bn_id_<?php echo $i; ?>" class="selec_chk">
            <label for="chk_bn_id_<?php echo $i; ?>">
            	<span></span>
            	<b class="sound_only"><?php echo $num?>번</b>
            </label>
            <input type="hidden" name="bo_table[<?php echo $i; ?>]" value="<?php echo $list[$i]['bo_table']; ?>">
            <input type="hidden" name="wr_id[<?php echo $i; ?>]" value="<?php echo $list[$i]['wr_id']; ?>">
        </td>
        <?php } ?>
        <td class="td_subject">
            <div class="subject_wrap">
                <?php echo $thumb_img; ?>
                <div class="subject_content">
                    <a href="<?php echo $list[$i]['href'] ?>" class="new_tit"><?php echo $list[$i]['comment'] ?><?php echo $wr_subject ?></a>
                    <div class="bo_name"><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $list[$i]['bo_table'] ?>"><?php echo $bo_subject ?></a></div>
                </div>
            </div>
        </td>
        <td class="td_name"><?php echo $list[$i]['name'] ?></td>
        <td class="td_date" style="color: #aaa;"><?php echo $list[$i]['datetime2'] ?></td>
    </tr>
    <?php } ?>

    <?php if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">게시물이 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<?php echo $write_pages ?>

</form>
<!-- } 전체게시물 목록 끝 -->
<br>

<!-- 전체게시물 검색 시작 { -->
<fieldset id="new_sch">
    <legend>상세검색</legend>
    <form name="fnew" method="get">
    <?php echo $group_select ?>
    <label for="view" class="sound_only">검색대상</label>
    <select name="view" id="view">
        <option value="">전체게시물
        <option value="w">원글만
        <option value="c">코멘트만
    </select>
    <label for="mb_id" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="mb_id" value="<?php echo $mb_id ?>" id="mb_id" required class="frm_input" size="40">
    <button type="submit" class="btn_submit"><i class="fa fa-search" aria-hidden="true"></i> 검색</button>
    <p>작성자 기준으로 발행글 조회하기 <span class="new_sch_note">(회원 아이디만 검색 가능)</span></p>
    </form>
    <script>
    /* 셀렉트 박스에서 자동 이동 해제
    function select_change()
    {
        document.fnew.submit();
    }
    */
    document.getElementById("gr_id").value = "<?php echo $gr_id ?>";
    document.getElementById("view").value = "<?php echo $view ?>";
    </script>
</fieldset>
<!-- } 전체게시물 검색 끝 -->

<?php if ($is_admin) { ?>
<script>
$(function(){
    $('#all_chk').click(function(){
        $('[name="chk_bn_id[]"]').attr('checked', this.checked);
    });
});

function fnew_submit(f)
{
    f.pressed.value = document.pressed;

    var cnt = 0;
    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_bn_id[]" && f.elements[i].checked)
            cnt++;
    }

    if (!cnt) {
        alert(document.pressed+"할 게시물을 하나 이상 선택하세요.");
        return false;
    }

    if (!confirm("선택한 게시물을 정말 "+document.pressed+" 하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다")) {
        return false;
    }

    f.action = "./new_delete.php";

    return true;
}
</script>
<?php } ?>