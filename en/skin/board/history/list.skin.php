<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<?php if ($is_checkbox || $write_href) { ?>
<div class="bo_fx">
	<?php if ($is_checkbox) { ?>
		<!-- <ul class="btn_bo_adm">
			<li><input type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value"></li>
		</ul> -->
	<?php } ?>

	<?php if ($write_href) { ?>
	<!-- <ul class="btn_bo_user">
		<?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02 btn">연혁등록</a></li><?php } ?>
	</ul> -->
	<?php } ?>
</div>
<?php } ?>

<form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="spt" value="<?php echo $spt ?>">
<input type="hidden" name="sca" value="<?php echo $sca ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="sw" value="">

<?php
$j = "0";
$l = "0";
$sql = " select * from {$write_table} where wr_is_comment = 0 order by wr_subject desc, wr_1*1 desc ";
$result = sql_query($sql); ?>

<ul class="history_h_w">
	<?php for($i=0; $row[$i]=sql_fetch_array($result); $i++) {
		$j++;
		$k = $i - 1;
		$m = $i - 2;
		if ($row[$k]['wr_subject']!=$row[$i]['wr_subject']){ $l = $l+1; }
	?>

	<?php
		if ($row[$k]['wr_subject']!=$row[$i]['wr_subject']){
	?>

		<li data-aos="fade-up">
			<dl>
				<dt class="history_h_h1"><?php echo $row[$i]['wr_subject']; ?></dt>
				<?php
				$thumb = get_list_thumbnail($bo_table, $row[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height']);
				if($thumb['src']) {
				?>

				<?php } ?>

	<?php } ?>

			<dd>
			<?php { ?>
				<p>
					<!-- <strong class="month">
						<//?php echo sprintf("%02d",$row[$i]['wr_1']); ?>월
					</strong> -->
					<!-- <span class="desc">
						</?php echo nl2br($row[$i]['wr_content']).'<br>'; ?>
					</span> -->

					<?php if($row[$i]['wr_content']) { ?>
							<ul class="desc" <?php if(empty($row[$i]['wr_content'])=='NULL')  { echo "style=display:none"; }?>>
								<?php $spec_title = explode('//',$row[$i]['wr_content']);
											for($s=0; $s<count($spec_title); $s++) { ?>
		            <li><?=$spec_title[$s]?></li>
								<?php } ?>
		          </ul>
					<?php } ?>
				</p>
			<?php } ?>
			<?php if ($is_checkbox) { ?>
				<div class="history_chk">
					<input type="checkbox" name="chk_wr_id[]" value="<?php echo $row[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
					<span><a href="<?php echo G5_BBS_URL ?>/write.php?w=u&bo_table=<?php echo $bo_table ?>&wr_id=<?php echo $row[$i]['wr_id'] ?>">[Edit]</a></span>
				</div>
			<?php } ?>
			</dd>

			<?php
			$row_his_num = sql_fetch(" select count(*) as cnt from {$write_table} where wr_is_comment = 0 and wr_subject = '{$row[$i]['wr_subject']}' ");
			if ($j-$row_his_num['cnt']==0){
			?>
			<dl>
		</li>

	<?php
	$j = "0";
	}
	?>

<?php } ?>
</ul>


<?php if ($is_checkbox || $write_href) { ?>
	<div class="bo_fx">
		<?php if ($is_checkbox) { ?>
		<ul class="btn_bo_adm">
			<li><input type="submit" name="btn_submit" value="Delete Selected" onclick="document.pressed=this.value"></li>
		</ul>
		<?php } ?>

		<?php if ($write_href) { ?>
		<ul class="btn_bo_user">
			<?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02 btn">Add History</a></li><?php } ?>
		</ul>
		<?php } ?>
	</div>

<?php } ?>

</form>

<?php if($is_checkbox) { ?>
<noscript>
<p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>
<?php } ?>

<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
            return false;

        f.removeAttribute("target");
        f.action = "./board_list_update.php";
    }

    return true;
}
</script>
<?php } ?>
<!-- } 게시판 목록 끝 -->
