<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>


<!-- 게시판 목록 시작 { -->
<div id="bo_gall" style="width:<?php echo $width; ?>" class="sub-container">


    <?php if ($is_category) { ?>
    <nav id="bo_cate">
        <h2><?php echo $board['bo_subject'] ?> 카테고리</h2>
        <ul id="bo_cate_ul">
            <?php echo $category_option ?>
        </ul>
    </nav>
    <?php } ?>

    <form name="fboardlist"  id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

    <!-- 게시판 페이지 정보 및 버튼 시작 { -->
    <div id="bo_btn_top">
      <div id="bo_list_total">
  <span>合計 <?php echo number_format($total_count) ?> 件</span>
  <?php echo $page ?> ページ
</div>

        <ul class="btn_bo_user">
            <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin btn" title="관리자"><i class="ri-user-settings-fill"></i><span class="sound_only">관리자</span></a></li><?php } ?>
            <?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b01 btn" title="RSS"><i class="ri-rss-line"></i><span class="sound_only">RSS</span></a></li><?php } ?>
            <li>
                <button type="button" class="btn_bo_sch btn_b01 btn" title="掲示板検索"><i class="ri-search-line"></i> <span class="sound_only">掲示板検索</span></button>
            </li>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b01 btn" title="글쓰기"><i class="ri-pencil-line"></i><span class="sound_only">글쓰기</span></a></li><?php } ?>
            <?php if ($is_admin == 'super' || $is_auth) {  ?>
            <li>
                <button type="button" class="btn_more_opt is_list_btn btn_b01 btn" title="掲示板リストオプション"><i class="ri-menu-line"></i><span class="sound_only">掲示板リストオプション</span></button>
                <?php if ($is_checkbox) { ?>
                <ul class="more_opt is_list_btn">
                    <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value"<i class="ri-delete-bin-line"></i> 선택삭제</button></li>
                    <li><button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value"<i class="ri-file-copy-line"></i> 선택복사</button></li>
                    <li><button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value"><i class="ri-share-forward-box-line"></i> 선택이동</button></li>
                </ul>
                <?php } ?>
            </li>
            <?php }  ?>
        </ul>
    </div>
    <script>

      jQuery(function($){
          // 게시판 보기 버튼 옵션
  $(".btn_more_opt.is_list_btn").on("click", function(e) {
              e.stopPropagation();
      $(".more_opt.is_list_btn").toggle();
  })
;
          $(document).on("click", function (e) {
              if(!$(e.target).closest('.is_list_btn').length) {
                  $(".more_opt.is_list_btn").hide();
              }
          });
      });

      </script>

    <!-- } 게시판 페이지 정보 및 버튼 끝 -->

    <?php if ($is_checkbox) { ?>
    <div id="gall_allchk" class="all_chk chk_box">
        <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);" class="selec_chk">
        <label for="chkall">
            <span></span>
            <b class="sound_only">현재 페이지 게시물 </b> 전체선택
        </label>
    </div>
    <?php } ?>

    <ul id="gall_ul" class="gall_row">
        <?php for ($i=0; $i<count($list); $i++) {

            $classes = array();

            $classes[] = 'gall_li';
            $classes[] = 'col-gn-'.$bo_gallery_cols;

            if( $i && ($i % $bo_gallery_cols == 0) ){
                $classes[] = 'box_clear';
            }

            if( $wr_id && $wr_id == $list[$i]['wr_id'] ){
                $classes[] = 'gall_now';
            }

            $line_height_style = ($board['bo_gallery_height'] > 0) ? 'line-height:'.$board['bo_gallery_height'].'px' : '';
         ?>
        <li class="<?php echo implode(' ', $classes); ?>">
            <div class="gall_box">
                <div class="gall_chk chk_box">
                    <?php if ($is_checkbox) { ?>
            					<label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject'] ?></label>
            					<input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>" class="checkbox">
            					<?php echo $i ?>
                    <?php } ?>
                    <span class="sound_only">
                        <?php
                        if ($wr_id == $list[$i]['wr_id'])
                            echo "<span class=\"bo_current\">열람중</span>";
                        else
                            echo $list[$i]['num'];
                         ?>
                    </span>
                </div>
                <div class="gall_con">
                    <div class="gall_img" style="<?php if ($board['bo_gallery_height'] > 0) echo 'height:'.$board['bo_gallery_height'].'px;max-height:'.$board['bo_gallery_height'].'px'; ?>">
                      <a href="<?php echo $list[$i]['href'] ?>">
                      <?php
                      $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height'], false, true);
                      if($thumb['src']) {
                        $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" >';
                        } else {
                        $img_content = '<i>製品画像がありません。</i>';
                        }
                      echo $img_content;
                      ?>
                      </a>
                      <?php
                      if ($list[$i]['is_notice']) // お知らせ
                        echo '<strong class="notice_icon">ⓘ<span class="sound_only">お知らせ</span></strong>';
                      else if ($wr_id == $list[$i]['wr_id'])
                        echo "<span class=\"bo_current\">열람중</span>";
                      else
                        //echo $list[$i]['num'];
                      ?>
                      <!-- <//?php if ($is_admin) { ?>
                          <div class="move">
                          <a href="javascript:select_move('next');"><i class="xi-long-arrow-left"></i> <span>뒤로 이동</span></a>
                          <a href="javascript:select_move('change');"><i class="xi-compare-arrows"></i> <span>교차 변경</span></a>
                          <a href="javascript:select_move('prev');"><i class="xi-long-arrow-right"></i> <span>앞으로 이동</span></a>
                          </div>
                      <//?php } ?> -->
                    </div>
                    <div class="gall_text_href">
                      <div class="tit">
                        <a href="<?php echo $list[$i]['href'] ?>">
                        <?php echo _($list[$i]['subject']) ?>
                        <?php echo $list[$i]['icon_reply'] ?>
                        <?php if (isset($list[$i]['icon_secret'])) echo rtrim($list[$i]['icon_secret']); ?>
                        <?php
                        // if ($list[$i]['link']['count']) { echo '['.$list[$i]['link']['count']}.']'; }
                        // if ($list[$i]['file']['count']) { echo '<'.$list[$i]['file']['count'].'>'; }
                        /*if (isset($list[$i]['icon_file'])) echo rtrim($list[$i]['icon_file']);
                        if (isset($list[$i]['icon_link'])) echo rtrim($list[$i]['icon_link']);
                        if (isset($list[$i]['icon_new'])) echo rtrim($list[$i]['icon_new']);*/
                        if (isset($list[$i]['icon_hot'])) echo rtrim($list[$i]['icon_hot']);
                        ?>
                        </a>
                        <?php if ($list[$i]['comment_cnt']) { ?>
                        <span class="sound_only">댓글</span><span class="cnt">+<?php echo $list[$i]['wr_comment']; ?></span><span class="sound_only">개</span>
                        <?php } ?>
                      </div>
                      <?php if ($is_category && $list[$i]['ca_name']) { ?>
                      <div class="info">
                        <?php //echo $list[$i]['wr_4'] ?>
                      </div>
                      <?php } ?>
                </div>
            </div>
        </li>
        <?php } ?>
        <?php if (count($list) == 0) { echo "<li class=\"empty_list\">登録された製品がありません。</li>"; } ?>
    </ul>


    <?php if ($list_href || $is_checkbox || $write_href) { ?>
    <div class="control">
      <div class="khwrap">
      <?php if ($list_href || $write_href) { ?>
            <div class="button fl">
                <?php if ($is_checkbox) { ?>
                <span class="bt bt_adm">
                    <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
                    <label for="chkall"><?php echo _("全選択") ?></label>
                </span>
                <button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value" class="bt bt_adm"><i class="ri-delete-bin-line"></i> <?php echo _("선택삭제") ?></button>
                <button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value" class="bt bt_adm"><i class="ri-file-copy-line"></i> <?php echo _("선택복사") ?></button>
                <button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value" class="bt bt_adm"><i class="ri-share-forward-box-line"></i> <?php echo _("선택이동") ?></button>
                <?php } ?>
            </div>
            <div class="button fr">
                <?php if ($admin_href) { ?><a href="<?php echo $admin_href ?>" class="bt bt_adm"><i class="ri-user-settings-fill"></i> <?php echo _("관리자") ?></a><?php } ?>
                <?php if ($rss_href) { ?><a href="<?php echo $rss_href ?>" class="bt bt_b01"><?php echo _("RSS") ?></a><?php } ?>
                <?php if ($list_href) { ?><a href="<?php echo $list_href ?>" class="bt bt_b01"><i class="ri-menu-line"></i> <?php echo _("목록") ?></a><?php } ?>
                <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="bt bt_b02"><i class="ri-pencil-line"></i> <?php echo _("등록") ?></a><?php } ?>
            </div>
            <?php } ?>
    </div>
    </div>
    <!-- } 게시판 검색 끝 -->

    <?php } ?>
    <?php echo $write_pages; ?>
    </form>

    <!-- 게시판 검색 시작 { -->
    <div class="bo_sch_wrap">
    <fieldset class="bo_sch">
        <h3>検索</h3>
        <form name="fsearch" method="get">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
            <input type="hidden" name="sca" value="<?php echo $sca ?>">
            <input type="hidden" name="sop" value="and">
            <label for="sfl" class="sound_only">検索対象</label>
            <select name="sfl" id="sfl">
                <?php echo get_board_sfl_select_options($sfl); ?>
            </select>
            <label for="stx" class="sound_only">検索ワード<strong class="sound_only"> 必須</strong></label>
            <div class="sch_bar">
                <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder="検索ワードを入力してください">
                <button type="submit" value="検索" class="sch_btn"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only">検索</span></button>
            </div>
            <button type="button" class="bo_sch_cls"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">閉じる</span></button>
        </form>
    </fieldset>
    <div class="bo_sch_bg"></div>
</div>

    <script>
        // 게시판 검색
        $(".btn_bo_sch").on("click", function() {
            $(".bo_sch_wrap").toggle();
        })
        $('.bo_sch_bg, .bo_sch_cls').click(function(){
            $('.bo_sch_wrap').hide();
        });
    </script>

</div>

</div>
</div>

<?php if($is_checkbox) { ?>
<noscript>
<p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>
<?php } ?>

<?php if ($is_checkbox) { ?>
<script type="text/javascript">
	//<![CDATA[
	function select_move(sw)
	{
		/*
		var chk_count = 0;

		for (var i=0; i<f.length; i++) {
			if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
				chk_count++;
		}

		if (!chk_count) {
			alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
			return false;
		}
		*/

		var f = document.fboardlist;

		var sub_win = window.open("", "move", "width=0, height=0, scrollbars=1");

		f.sw.value = sw;
		f.target = "move";
		f.action = g5_bbs_url+"/move_update2.php";
		f.submit();
	}
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
		if(document.pressed == "선택복사") {
			select_copy("copy");
			return;
		}
		if(document.pressed == "선택이동") {
			select_copy("move");
			return;
		}
		if(document.pressed == "선택삭제") {
			if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
				return false;

			f.removeAttribute("target");
			f.action = g5_bbs_url+"/board_list_update.php";
		}
		return true;
	}

	// 선택한 게시물 복사 및 이동
	function select_copy(sw) {
		var f = document.fboardlist;

		if (sw == "copy")
			str = "복사";
		else
			str = "이동";

		var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

		f.sw.value = sw;
		f.target = "move";
		f.action = "./move.php";
		f.submit();
	}
  	//]]>
</script>
<?php } ?>
<!-- } 게시판 목록 끝 -->
