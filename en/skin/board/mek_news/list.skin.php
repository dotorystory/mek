<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 선택옵션으로 인해 셀합치기가 가변적으로 변함
$colspan = 5;

if ($is_checkbox) $colspan++;
if ($is_good) $colspan++;
if ($is_nogood) $colspan++;

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<!-- 커스텀 { -->
<script src="<?php echo $board_skin_url ?>/swiper/swiper.js"></script>
<link rel="stylesheet" href="<?php echo $board_skin_url ?>/swiper/swiper.css">
<link rel="stylesheet" href="<?php echo $board_skin_url ?>/css/magic-check.css">
<!-- } -->


<div class="sub-container">
        <div class="cate_div">
            <?php if ($is_category) { ?>
            <style>
                .cate_div {
                    margin-bottom: 20px;
                }

                .cate_div ul li {
                    margin-right: 0px;
                    margin-right: 15px;
                }

            </style>
            <ul class="fl">
                <?php echo $category_option ?>
            </ul>
            <?php } ?>


            <?php if ($is_checkbox) { ?>
            <ul class="top_chks">
                <input class="magic-checkbox" type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
                <label for="chkall">전체선택</label>

                <!--
                    <span>Total <?php echo number_format($total_count) ?>건</span>
                    <?php echo $page ?> 페이지
                    -->
            </ul>
            <?php } ?>
            <div class="cb"></div>
        </div>
        <!-- } -->



        <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
            <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
            <input type="hidden" name="stx" value="<?php echo $stx ?>">
            <input type="hidden" name="spt" value="<?php echo $spt ?>">
            <input type="hidden" name="sst" value="<?php echo $sst ?>">
            <input type="hidden" name="sod" value="<?php echo $sod ?>">
            <input type="hidden" name="page" value="<?php echo $page ?>">
            <input type="hidden" name="sw" value="">

            <div class="swiper-container swiper-container-ga">

                <div class="swiper-wrapper swiper-wrapper-ga">
                    <table>
                        <?php
                        $now_row = '';
                        for ($i=0; $i<count($list); $i++) {
                            $now_row = $list[$i]['wr_id'];
                            $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height'], false, true);

                                if($list[$i]['icon_secret']) {
                                    $img_content = $board_skin_url.'/img/sec.png';
                                } else {
                                    if($thumb['src']) {
                                        $img_content = $thumb['src'];
                                    } else if ($list[$i]['wr_10']){
										$img_content = 'https://img.youtube.com/vi/'.$list[$i]['wr_10'].'/0.jpg';
                                    } else {
                                        $img_content = $board_skin_url.'/img/no_img.png';
                                    }
                                    //echo run_replace('thumb_image_tag', $img_content, $thumb);
                                }
                        ?>

                        <div class="swiper-slide swiper-slide-ga">

                            <ul class="img_link" style="height:<?php echo $board['bo_gallery_height']; ?>px; background-image:url('<?php echo $img_content ?>');" onclick="location.href='<?php echo $list[$i]['href'] ?>';">
                                <?php if ($list[$i]['icon_new']) { ?>
                                <li class="main_lists_new">New</li>
                                <?php } ?>
                                <?php if ($list[$i]['wr_10']) { ?>
                                <li class="mov_ico">
                                    <img src="<?php echo $board_skin_url ?>/img/favicon_144-vfliLAfaB.png">
                                </li>
                                <?php } ?>
                            </ul>
                            <ul>
                                <li class="main_lists_tit cut">
                                    <a href="<?php echo $list[$i]['href'] ?>" class="titles">
                                        <?php echo $list[$i]['wr_subject'] ?>
                                    </a>
                                </li>
                                <li class="main_lists_date">
                                    <?php echo $list[$i]['wr_name']?>　<?php echo $list[$i]['wr_datetime']?>
                                    <?php if ($list[$i]['comment_cnt']) { ?>　<span class="cnt_cmt">+<?php echo $list[$i]['wr_comment']; ?></span><?php } ?>
                                    <?php if ($list[$i]['ca_name']) { ?>
                                    <dd class="list_ca"><a href="<?php echo $list[$i]['ca_name_href'] ?>"><?php echo $list[$i]['ca_name'] ?></a></dd>
                                    <?php } ?>
                                </li>

                                <!-- { 본문내용 추출 (사용시 주석해제 하세요.)-->
                                <li class="main_lists_cont">
                                    <?php
                                    $wr_content = preg_replace("/<(.*?)\>/","",$list[$i]['wr_content']);
                                    $wr_content = preg_replace("/&nbsp;/","",$wr_content);
                                    $wr_content = cut_str(get_text($wr_content),80);
                                    echo $wr_content;
                                ?>
                                </li>

                            </ul>

                            <?php if ($is_checkbox) { ?>
                            <div class="chk_boxs">
                                <input class="magic-checkbox" name="chk_wr_id[]" type="checkbox" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                                <label for="chk_wr_id_<?php echo $i ?>"></label>
                            </div>
                            <?php } ?>

                        </div>

                        <?php } ?>
                        <?php if (count($list) == 0) { echo "<div style='width:100%; text-align:center; font-size:12px; padding-top:100px; padding-bottom:100px; color:#999;'>등록된 게시물이 없습니다.</div>"; } ?>


                    </table>


                </div>

            </div>

            <div class="pageing_div">



                <div class="list_btns">
                    <?php if ($write_href) { ?>
                    <button type="button" onclick="location.href='<?php echo $write_href ?>';" title="등록" class="bbs_adm_btn">
                        <img src="<?php echo $board_skin_url ?>/img/edit.svg">
                    </button>
                    <?php } ?>
                    <?php if ($is_checkbox) { ?>　
                    <button type="submit" name="btn_submit" onclick="document.pressed=this.value" value="선택삭제" title="선택삭제" class="bbs_adm_btn">
                        <img src="<?php echo $board_skin_url ?>/img/trash.svg">
                    </button>
                    <?php } ?>
                    <?php if ($is_checkbox) { ?>　
                    <button type="submit" name="btn_submit" onclick="document.pressed=this.value" value="선택복사" title="선택복사" class="bbs_adm_btn">
                        <img src="<?php echo $board_skin_url ?>/img/copy.svg">
                    </button>
                    <?php } ?>

                </div>

                <?php echo $write_pages;  ?>
            </div>


        </form>

        <script>
            var swiper_slide = new Swiper('.swiper-container-ga', {

                slidesPerView: <?php echo $board['bo_gallery_cols']; ?>,
                slidesPerColumn: <?php echo $board['bo_page_rows']; ?>,
                spaceBetween: 30,
                touchRatio: 0,
                slidesPerColumnFill: 'row',

                breakpoints: {

                    1024: {
                        slidesPerView: <?php echo $board['bo_gallery_cols']; ?>,
                        slidesPerColumn: <?php echo $board['bo_page_rows']; ?>,
                        spaceBetween: 30
                    },
                    768: {
                        slidesPerView: 2,
                        slidesPerColumn: <?php echo $board['bo_page_rows']; ?>,
                        spaceBetween: 30
                    },

                    10: {
                        slidesPerView: 1,
                        slidesPerColumn: <?php echo $board['bo_page_rows']; ?>,
                        spaceBetween: 20
                    }
                }

            });
        </script>





        <?php if ($is_checkbox) { ?>
        <script>
            function all_checked(sw) {
                var f = document.fboardlist;

                for (var i = 0; i < f.length; i++) {
                    if (f.elements[i].name == "chk_wr_id[]")
                        f.elements[i].checked = sw;
                }
            }

            function fboardlist_submit(f) {
                var chk_count = 0;

                for (var i = 0; i < f.length; i++) {
                    if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
                        chk_count++;
                }

                if (!chk_count) {
                    alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
                    return false;
                }

                if (document.pressed == "선택복사") {
                    select_copy("copy");
                    return;
                }

                if (document.pressed == "선택이동") {
                    select_copy("move");
                    return;
                }

                if (document.pressed == "선택삭제") {
                    if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
                        return false;

                    f.removeAttribute("target");
                    f.action = "./board_list_update.php";
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
        </script>
        <?php } ?>


    </div>
