<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>

<!-- 커스텀 { -->
<link rel="stylesheet" href="<?php echo $board_skin_url ?>/css/magic-check.css">
<!-- } -->




            <div class="sub-container">
                <ul class="titles font-b font-18"><?php echo cut_str(get_text($view['wr_subject']), 999); ?></ul>
                <ul class="main_lists_date v_top_hd">

                    <li><img src="<?php echo $board_skin_url ?>/img/edit.svg"></li>
                    <li class="divmt-05"><?php echo $view['wr_name']?>　</li>

                    <li><img src="<?php echo $board_skin_url ?>/img/calendar.svg"></li>
                    <li class="divmt-05"><?php echo $view['datetime2']?>　</li>

                    <li><img src="<?php echo $board_skin_url ?>/img/eye.svg"></li>
                    <li class="divmt-05"><?php echo $view['wr_hit']?>　</li>

                    <?php if ($category_name) { ?>
                    <li class="divmt-05 v_cates"><?php echo $view['ca_name']; ?></li>
                    <li class="v_cates"><img src="<?php echo $board_skin_url ?>/img/tag.svg"></li>
                    <?php } ?>

                    <div class="cb"></div>
                </ul>
                <ul class="view_img_cont">

                                            <?php if ($view['wr_10']) { ?>
												<div class="video-container">
													<iframe width="100%" src="https://www.youtube.com/embed/<?php echo $view['wr_10']; ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
												</div>
												<br><br>
											<?php } ?>

                            <?php
                            // 파일 출력
                            $v_img_count = count($view['file']);
                            if($v_img_count) {
                                echo "<div id=\"bo_v_img\">\n";

                                foreach($view['file'] as $view_file) {
                                    echo get_file_thumbnail($view_file);
                                }

                                echo "</div>\n";
                            }
                             ?>
                    <?php echo get_view_thumbnail($view['content']); ?>
                    <br><br>
                </ul>
                <ul>
                                <?php
                                $cnt = 0;
                                if ($view['file']['count']) {
                                    for ($i=0; $i<count($view['file']); $i++) {
                                        if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view'])
                                            $cnt++;
                                    }
                                }
                                ?>

                    <?php if($cnt) { ?>
                    <!-- 첨부파일 시작 { -->
                    <div class="file_divs">
                        <?php
                            // 가변 파일
                            for ($i=0; $i<count($view['file']); $i++) {
                            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
                        ?>
                        <li class="fl" style="margin-right:20px;">
                            <a href="<?php echo $view['file'][$i]['href'];  ?>" class="view_file_download" style="font-size:12px;">
                                <b><?php echo $view['file'][$i]['source'] ?> <?php echo $view['file'][$i]['content'] ?></b> (<?php echo $view['file'][$i]['size'] ?>)
                            </a>
                        </li>
                        <?php
                            }
                        }
                        ?>
                        <div class="cb"></div>
                    </div>
                    <!-- } 첨부파일 끝 -->
                    <?php } ?>
                </ul>

                <?php if(isset($view['link'][1]) && $view['link'][1]) { ?>
                <ul>

                    <div class="file_divs">
                        <?php
                        // 링크
                        $cnt = 0;
                        for ($i=1; $i<=count($view['link']); $i++) {
                            if ($view['link'][$i]) {
                                $cnt++;
                                $link = cut_str($view['link'][$i], 70);
                        ?>

                        <li class="fl" style="margin-right:20px;">
                            <a href="<?php echo $view['link_href'][$i] ?>" class="view_file_download" target="_blank" style="font-size:12px;">
                                <b><?php echo $link ?></b> (<?php echo $view['link_hit'][$i] ?>회 연결)
                            </a>
                        </li>

                        <?php
                            }
                        }
                        ?>

                        <div class="cb"></div>
                    </div>

                </ul>
                <?php } ?>


                <ul class="view_img_btn" style="display:block !important;">
                    <a href="<?php echo $list_href ?>" title="목록" alt="목록" class="bbs_adm_btn">
                        <img src="<?php echo $board_skin_url ?>/img/menu.svg">
                    </a>

                    <?php if ($update_href) { ?>
                    　<a href="<?php echo $update_href ?>" title="수정" alt="수정" class="bbs_adm_btn">
                        <img src="<?php echo $board_skin_url ?>/img/settings.svg">
                    </a>
                    <?php } ?>

                    <?php if ($delete_href) { ?>
                    　<a href="<?php echo $delete_href ?>" onclick="del(this.href); return false;" title="삭제" alt="삭제" class="bbs_adm_btn">
                        <img src="<?php echo $board_skin_url ?>/img/trash.svg">
                    </a>
                    <?php } ?>

                    <?php if ($write_href) { ?>
                    　<a href="<?php echo $write_href ?>" title="등록" alt="등록" class="bbs_adm_btn">
                        <img src="<?php echo $board_skin_url ?>/img/edit.svg">
                    </a>
                    <?php } ?>


                </ul>
                <div class="cb"></div>
            </div>

<div class="comment_divs">
<?php
include_once(G5_BBS_PATH.'/view_comment.php');
?>
</div>

<script>
    <?php if ($board['bo_download_point'] < 0) { ?>
    $(function() {
        $("a.view_file_download").click(function() {
            if (!g5_is_member) {
                alert("다운로드 권한이 없습니다.\n회원이시라면 로그인 후 이용해 보십시오.");
                return false;
            }

            var msg = "파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board['bo_download_point']) ?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?";

            if (confirm(msg)) {
                var href = $(this).attr("href") + "&js=on";
                $(this).attr("href", href);

                return true;
            } else {
                return false;
            }
        });
    });
    <?php } ?>

    function board_move(href) {
        window.open(href, "boardmove", "left=50, top=50, width=500, height=550, scrollbars=1");
    }
</script>

<script>
    $(function() {
        $("a.view_image").click(function() {
            window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
            return false;
        });

        // 추천, 비추천
        $("#good_button, #nogood_button").click(function() {
            var $tx;
            if (this.id == "good_button")
                $tx = $("#bo_v_act_good");
            else
                $tx = $("#bo_v_act_nogood");

            excute_good(this.href, $(this), $tx);
            return false;
        });

        // 이미지 리사이즈
        $("#bo_v_atc").viewimageresize();
    });

    function excute_good(href, $el, $tx) {
        $.post(
            href, {
                js: "on"
            },
            function(data) {
                if (data.error) {
                    alert(data.error);
                    return false;
                }

                if (data.count) {
                    $el.find("strong").text(number_format(String(data.count)));
                    if ($tx.attr("id").search("nogood") > -1) {
                        $tx.text("이 글을 비추천하셨습니다.");
                        $tx.fadeIn(200).delay(2500).fadeOut(200);
                    } else {
                        $tx.text("이 글을 추천하셨습니다.");
                        $tx.fadeIn(200).delay(2500).fadeOut(200);
                    }
                }
            }, "json"
        );
    }
</script>
<!-- } 게시글 읽기 끝 -->
