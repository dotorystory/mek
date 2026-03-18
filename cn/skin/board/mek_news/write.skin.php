<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>


<!-- 커스텀 { -->
<link rel="stylesheet" href="<?php echo $board_skin_url ?>/css/magic-check.css">
<!-- } -->



<form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off" style="width:<?php echo $width; ?>"class="sub-container">
    <input type="hidden" name="uid" value="<?php echo get_uniqid(); ?>">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">

    <?php
    $option = '';
    $option_hidden = '';
    if ($is_notice || $is_html || $is_secret || $is_mail) {
        $option = '';
        if ($is_notice) {
            $option .= PHP_EOL.'<li class=""><input type="checkbox" id="notice" name="notice"  class="magic-checkbox" value="1" '.$notice_checked.'>'.PHP_EOL.'<label for="notice"><span></span>공지</label></li>';
        }
        if ($is_html) {
            if ($is_dhtml_editor) {
                $option_hidden .= '<input type="hidden" value="html1" name="html">';
            } else {
                $option .= PHP_EOL.'<li class=""><input type="checkbox" id="html" name="html" onclick="html_auto_br(this);" class="magic-checkbox" value="'.$html_value.'" '.$html_checked.'>'.PHP_EOL.'<label for="html"><span></span>html</label></li>';
            }
        }
        if ($is_secret) {
            if ($is_admin || $is_secret==1) {
                $option .= PHP_EOL.'<li class=""><input type="checkbox" id="secret" name="secret"  class="magic-checkbox" value="secret" '.$secret_checked.'>'.PHP_EOL.'<label for="secret"><span></span>비밀글</label></li>';
            } else {
                $option_hidden .= '<input type="hidden" name="secret" value="secret">';
            }
        }
        if ($is_mail) {
            $option .= PHP_EOL.'<li class=""><input type="checkbox" id="mail" name="mail"  class="magic-checkbox" value="mail" '.$recv_email_checked.'>'.PHP_EOL.'<label for="mail"><span></span>답변메일받기</label></li>';
        }
    }
    echo $option_hidden;
    ?>

    <div id="bo_w">
        <ul class="bbs_write_div_l">
            <div>
                <ul>

                    <?php if ($is_category) { ?>
                    <li class="divmb-10">
                        <select name="ca_name" id="ca_name" required class="select_w w40">
                            <option value="">분류를 선택하세요</option>
                            <?php echo $category_option ?>
                        </select>
                    </li>
                    <?php } ?>
                    <li class="divmb-20">
                        <input type="text" name="wr_subject" value="<?php echo $subject ?>" id="wr_subject" required class="bbs_w_inp w70 required" maxlength="255" placeholder="제목을 입력하세요.">
                    </li>

                    <?php if ($option) { ?>
                    <li class="divmb-10">
                        <ul class="bo_v_option">
                            <?php echo $option ?>
                            <div class="cb"></div>
                        </ul>
                    </li>
                    <?php } ?>

                    <li class="divmb-10">
                        <?php echo $editor_html; // 에디터 사용시는 에디터로, 아니면 textarea 로 노출 ?>
                    </li>

                    <li class="divmb-10">
                        <input type="text" name="wr_10" value="<?php echo $write['wr_10']; ?>" id="wr_10" class="bbs_w_inp w50 video_inp" placeholder="유튜브동영상 ID">
                        <dd class="help_txts">
                            유튜브 동영상의 ID를 입력하시면 동영상이 출력 되며, 썸네일이 자동등록 됩니다.<br>
                            https://www.youtube.com/watch?v=<b>ID</b>
                        </dd>
                    </li>


                </ul>
            </div>
        </ul>

        <ul class="bbs_write_div_r">
            <div>
                <ul>
                    <?php if ($is_name) { ?>
                    <li class="divmb-10">
                        <input type="text" name="wr_name" id="wr_paswr_namesword" class="bbs_w_inp w70 required" value="<?php echo $name ?>" placeholder="작성자 성함" required>
                    </li>
                    <?php } ?>

                    <?php if ($is_password) { ?>
                    <li class="divmb-20">
                        <input type="password" name="wr_password" id="wr_password" <?php echo $password_required ?> class="bbs_w_inp w50 <?php echo $password_required ?>" placeholder="비밀번호">
                    </li>
                    <?php } ?>

                    <?php if($is_file) { ?>
                    <li class="bbs_w_tit divmb-10">
                        <dd class="fl">파일 첨부</dd>
                        <dd class="w_icos"><img src="<?php echo $board_skin_url ?>/img/file-plus.svg" class="fr"></dd>
                        <div class="cb"></div>
                    </li>
                    <dd class="help_txts divmb-10">이미지인 경우 썸네일 및 본문 상단에 출력되며, 파일은 다운로드 링크로 출력 됩니다.</dd>

                    <?php } ?>

                    <?php for ($i=0; $is_file && $i<$file_count; $i++) { ?>
                    <li class="divmb-05">
                        <input type="file" name="bf_file[]" id="bf_file_<?php echo $i+1 ?>" title="파일첨부 <?php echo $i+1 ?> : 용량 <?php echo $upload_max_filesize ?> 이하만 업로드 가능" class="bbs_w_inp_file w100">

                        <?php if($w == 'u' && $file[$i]['file']) { ?>
                        <div class="divmb-05" style="padding-bottom:5px;">
                            <input type="checkbox" class="magic-checkbox" id="bf_file_del<?php echo $i ?>" name="bf_file_del[<?php echo $i;  ?>]" value="1">
                            <label for="bf_file_del<?php echo $i ?>" style="font-size:12px; color:#999"><span style="color:#ff6666"><?php echo $file[$i]['source'].' ('.$file[$i]['size'].')';  ?></span> 파일 삭제</label>
                        </div>
                        <?php } ?>

                    </li>
                    <?php } ?>

                    <?php if($is_link) { ?>
                    <li class="bbs_w_tit divmt-20 divmb-10">
                        <dd class="fl">링크</dd>
                        <dd class="w_icos"><img src="<?php echo $board_skin_url ?>/img/link.svg" class="fr"></dd>
                        <div class="cb"></div>
                    </li>
                    <?php } ?>

                    <?php for ($i=1; $is_link && $i<=G5_LINK_COUNT; $i++) { ?>
                    <div class="bo_w_link divmb-05">
                        <input type="text" name="wr_link<?php echo $i ?>" value="<?php if($w=="u"){ echo $write['wr_link'.$i]; } ?>" id="wr_link<?php echo $i ?>" class="bbs_w_inp w100"  placeholder="http://포함">
                    </div>
                    <?php } ?>


                    <?php if ($is_use_captcha) { //자동등록방지  ?>
                    <li class="divmt-20 bbs_w_tit divmb-10">자동등록방지</li>
                    <li>
                        <?php echo $captcha_html ?>
                    </li>
                    <?php } ?>
                </ul>

                <ul class="divmt-20 bbs_w_btns_div">
                    <button type="submit" id="btn_submit" accesskey="s" class="bbs_w_btn1">등록완료</button>
                    <button type="button" class="bbs_w_btn2" onclick="location.href='<?php echo get_pretty_url($bo_table); ?>';">취소</button>
                </ul>
            </div>
        </ul>

        <div class="cb"></div>

    </div>
</form>


<script>
    <?php if($write_min || $write_max) { ?>
    // 글자수 제한
    var char_min = parseInt(<?php echo $write_min; ?>); // 최소
    var char_max = parseInt(<?php echo $write_max; ?>); // 최대
    check_byte("wr_content", "char_count");

    $(function() {
        $("#wr_content").on("keyup", function() {
            check_byte("wr_content", "char_count");
        });
    });

    <?php } ?>

    function html_auto_br(obj) {
        if (obj.checked) {
            result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
            if (result)
                obj.value = "html2";
            else
                obj.value = "html1";
        } else
            obj.value = "";
    }


    function fwrite_submit(f) {

        <?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

        var subject = "";
        var content = "";
        $.ajax({
            url: g5_bbs_url + "/ajax.filter.php",
            type: "POST",
            data: {
                "subject": f.wr_subject.value,
                "content": f.wr_content.value
            },
            dataType: "json",
            async: false,
            cache: false,
            success: function(data, textStatus) {
                subject = data.subject;
                content = data.content;
            }
        });

        if (subject) {
            alert("제목에 금지단어('" + subject + "')가 포함되어있습니다");
            f.wr_subject.focus();
            return false;
        }

        if (content) {
            alert("내용에 금지단어('" + content + "')가 포함되어있습니다");
            if (typeof(ed_wr_content) != "undefined")
                ed_wr_content.returnFalse();
            else
                f.wr_content.focus();
            return false;
        }

        if (document.getElementById("char_count")) {
            if (char_min > 0 || char_max > 0) {
                var cnt = parseInt(check_byte("wr_content", "char_count"));
                if (char_min > 0 && char_min > cnt) {
                    alert("내용은 " + char_min + "글자 이상 쓰셔야 합니다.");
                    return false;
                } else if (char_max > 0 && char_max < cnt) {
                    alert("내용은 " + char_max + "글자 이하로 쓰셔야 합니다.");
                    return false;
                }
            }
        }

        <?php echo $captcha_js; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사함  ?>

        document.getElementById("btn_submit").disabled = "disabled";

        return true;
    }
</script>
