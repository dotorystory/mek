<?php
if (!defined("_GNUBOARD_")) exit; // Individual page access is not allowed

// add_stylesheet('css statement', display order); The lower the number, the earlier it will be displayed
add_stylesheet('<link rel="stylesheet" href="'.$search_skin_url.'/style.css">', 0);
?>

<!-- Global Search Start { -->
<form name="fsearch" onsubmit="return fsearch_submit(this);" method="get">
<input type="hidden" name="srows" value="<?php echo $srows ?>">
<fieldset id="sch_res_detail">
    <legend>Advanced Search</legend>
    <?php echo $group_select ?>
    <script>document.getElementById("gr_id").value = "<?php echo $gr_id ?>";</script>

    <label for="sfl" class="sound_only">Search Condition</label>
    <select name="sfl" id="sfl">
        <option value="wr_subject||wr_content"<?php echo get_selected($sfl, "wr_subject||wr_content") ?>>Title+Content</option>
        <option value="wr_subject"<?php echo get_selected($sfl, "wr_subject") ?>>Title</option>
        <option value="wr_content"<?php echo get_selected($sfl, "wr_content") ?>>Content</option>
        <option value="mb_id"<?php echo get_selected($sfl, "mb_id") ?>>Member ID</option>
        <option value="wr_name"<?php echo get_selected($sfl, "wr_name") ?>>Name</option>
    </select>

    <label for="stx" class="sound_only">Search Keyword<strong class="sound_only"> Required</strong></label>
    <span class="sch_wr">
        <input type="text" name="stx" value="<?php echo $text_stx ?>" id="stx" required class="frm_input" size="40">
        <button type="submit" class="btn_submit"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
    </span>

    <script>
    function fsearch_submit(f)
    {
        if (f.stx.value.length < 2) {
            alert("Please enter a search term with at least two characters.");
            f.stx.select();
            f.stx.focus();
            return false;
        }

        // Remove this comment if the search is causing significant load.
        var cnt = 0;
        for (var i=0; i<f.stx.value.length; i++) {
            if (f.stx.value.charAt(i) == ' ')
                cnt++;
        }

        if (cnt > 1) {
            alert("You can only enter one space in the search term for quick search.");
            f.stx.select();
            f.stx.focus();
            return false;
        }

        f.action = "";
        return true;
    }
    </script>

	<div class="switch_field">
		<input type="radio" value="and" <?php echo ($sop == "and") ? "checked" : ""; ?> id="sop_and" name="sop">
    	<label for="sop_and">AND</label>
		<input type="radio" value="or" <?php echo ($sop == "or") ? "checked" : ""; ?> id="sop_or" name="sop" >
		<label for="sop_or">OR</label>
	</div>
</fieldset>
</form>

<div id="sch_result">
    <?php
    if ($stx) {
        if ($board_count) {
    ?>
    <section id="sch_res_ov">
        <h2>Search Results for <strong><?php echo $stx ?></strong></h2>
        <ul>
            <li>Boards: <?php echo $board_count ?> total</li>
            <li>Posts: <?php echo number_format($total_count) ?> total</li>
        	<li><?php echo number_format($page) ?>/<?php echo number_format($total_page) ?> pages viewed</li>
        </ul>
    </section>
    <?php
        }
    }
    ?>

    <?php
    if ($stx) {
        if ($board_count) {
     ?>
    <ul id="sch_res_board">
        <li><a href="?<?php echo $search_query ?>&amp;gr_id=<?php echo $gr_id ?>" <?php echo $sch_all ?>>All Boards</a></li>
        <?php echo $str_board_list; ?>
    </ul>
    <?php
        } else {
     ?>
    <div class="empty_list">No data found.</div>
    <?php } }  ?>

    <hr>

    <?php if ($stx && $board_count) { ?><section class="sch_res_list"><?php }  ?>
    <?php
    $k=0;
    for ($idx=$table_index, $k=0; $idx<count($search_table) && $k<$rows; $idx++) {
     ?>
		<div class="search_board_result">
        <h2><a href="<?php echo get_pretty_url($search_table[$idx], '', $search_query); ?>"><?php echo $bo_subject[$idx] ?> Board Results</a></h2>
		<a href="<?php echo get_pretty_url($search_table[$idx], '', $search_query); ?>" class="sch_more">View More</a>
        <ul>
        <?php
        for ($i=0; $i<count($list[$idx]) && $k<$rows; $i++, $k++) {
            if ($list[$idx][$i]['wr_is_comment'])
            {
                $comment_def = '<span class="cmt_def"><i class="fa fa-commenting-o" aria-hidden="true"></i><span class="sound_only">Comments</span></span> ';
                $comment_href = '#c_'.$list[$idx][$i]['wr_id'];
            }
            else
            {
                $comment_def = '';
                $comment_href = '';
            }
         ?>

            <li>
                <div class="sch_tit">
                    <a href="<?php echo $list[$idx][$i]['href'] ?><?php echo $comment_href ?>" class="sch_res_title"><?php echo $comment_def ?><?php echo $list[$idx][$i]['subject'] ?></a>
                    <a href="<?php echo $list[$idx][$i]['href'] ?><?php echo $comment_href ?>" target="_blank" class="pop_a"><i class="fa fa-window-restore" aria-hidden="true"></i><span class="sound_only">New Window</span></a>
                </div>
                <p><?php echo $list[$idx][$i]['content'] ?></p>
                <div class="sch_info">
                    <?php echo $list[$idx][$i]['name'] ?>
                    <span class="sch_datetime"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$idx][$i]['wr_datetime'] ?></span>
                </div>
            </li>
        <?php }  ?>
        </ul>
		</div>
    <?php }		//end for?>
    <?php if ($stx && $board_count) {  ?></section><?php }  ?>

    <?php echo $write_pages ?>

</div>
<!-- } Global Search End -->
