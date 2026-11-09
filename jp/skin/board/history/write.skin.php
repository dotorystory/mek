<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<section id="bo_w" class="sub-container">
    <h2 id="container_title"><?php echo $g5['title'] ?></h2>

    <!-- 게시물 작성/수정 시작 { -->
    <form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off" style="width:<?php echo $width; ?>">
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
            $option .= "\n".'<input type="checkbox" id="notice" name="notice" value="1" '.$notice_checked.'>'."\n".'<label for="notice">공지</label>';
        }

        if ($is_html) {
            if ($is_dhtml_editor) {
                $option_hidden .= '<input type="hidden" value="html1" name="html">';
            } else {
                $option .= "\n".'<input type="checkbox" id="html" name="html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'."\n".'<label for="html">html</label>';
            }
        }

        if ($is_secret) {
            if ($is_admin || $is_secret==1) {
                $option .= "\n".'<input type="checkbox" id="secret" name="secret" value="secret" '.$secret_checked.'>'."\n".'<label for="secret">비밀글</label>';
            } else {
                $option_hidden .= '<input type="hidden" name="secret" value="secret">';
            }
        }

        if ($is_mail) {
            $option .= "\n".'<input type="checkbox" id="mail" name="mail" value="mail" '.$recv_email_checked.'>'."\n".'<label for="mail">답변메일받기</label>';
        }
    }

    echo $option_hidden;
    ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <tbody>

        <tr>
            <th scope="row"><label for="wr_subject_0">연도<strong class="sound_only">필수</strong></label></th>
            <td>
				<?php
				$index_year = "1982";
				$wr_year = date("Y");
				$len_year = $wr_year - $index_year + 1;
				for ($i=0; $i<$len_year; $i++){
				?>
        <span><input type="radio" name="wr_subject" id="wr_subject_<?php echo $i ?>" value="<?php echo $wr_year; ?>" <?php if ($subject==$wr_year){ echo 'checked'; } ?>><label for="wr_subject_<?php echo $i ?>" class="ctt_013_label"><?php echo $wr_year; ?>년</label></span>

				<?php
					$wr_year = $wr_year - 1;
				}
				?>
            </td>
        </tr>

		    <!-- <tr>
            <th scope="row"><label for="wr_1_0">월<strong class="sound_only">필수</strong></label></th>
            <td>
				<input type="radio" name="wr_1" id="wr_1_0" value="1" </?php if ($wr_1=='1'){ echo 'checked'; } ?>><label for="wr_1_0" class="ctt_013_label">1월</label>
				<input type="radio" name="wr_1" id="wr_1_1" value="2" </?php if ($wr_1=='2'){ echo 'checked'; } ?>><label for="wr_1_1" class="ctt_013_label">2월</label>
				<input type="radio" name="wr_1" id="wr_1_2" value="3" </?php if ($wr_1=='3'){ echo 'checked'; } ?>><label for="wr_1_2" class="ctt_013_label">3월</label>
				<input type="radio" name="wr_1" id="wr_1_3" value="4" </?php if ($wr_1=='4'){ echo 'checked'; } ?>><label for="wr_1_3" class="ctt_013_label">4월</label>
				<input type="radio" name="wr_1" id="wr_1_4" value="5" </?php if ($wr_1=='5'){ echo 'checked'; } ?>><label for="wr_1_4" class="ctt_013_label">5월</label>
				<input type="radio" name="wr_1" id="wr_1_5" value="6" </?php if ($wr_1=='6'){ echo 'checked'; } ?>><label for="wr_1_5" class="ctt_013_label">6월</label>
				<input type="radio" name="wr_1" id="wr_1_6" value="7" </?php if ($wr_1=='7'){ echo 'checked'; } ?>><label for="wr_1_6" class="ctt_013_label">7월</label>
				<input type="radio" name="wr_1" id="wr_1_7" value="8" </?php if ($wr_1=='8'){ echo 'checked'; } ?>><label for="wr_1_7" class="ctt_013_label">8월</label>
				<input type="radio" name="wr_1" id="wr_1_8" value="9" </?php if ($wr_1=='9'){ echo 'checked'; } ?>><label for="wr_1_8" class="ctt_013_label">9월</label>
				<input type="radio" name="wr_1" id="wr_1_9" value="10" </?php if ($wr_1=='10'){ echo 'checked'; } ?>><label for="wr_1_9" class="ctt_013_label">10월</label>
				<input type="radio" name="wr_1" id="wr_1_10" value="11" </?php if ($wr_1=='11'){ echo 'checked'; } ?>><label for="wr_1_10" class="ctt_013_label">11월</label>
				<input type="radio" name="wr_1" id="wr_1_11" value="12" </?php if ($wr_1=='12'){ echo 'checked'; } ?>><label for="wr_1_11" class="ctt_013_label">12월</label>
            </td>
        </tr> -->

        <tr>
            <th scope="row"><label for="wr_content">내용<strong class="sound_only">필수</strong></label></th>
            <td class="wr_content">
              <input type="hidden" name="wr_content" value="<?php echo $content ?>">
              <ul>
                <?php $spec_title = explode('//',$content);
                for($i=0; $i<count($spec_title); $i++) { ?>
                <li><input type="text" class="wr_content" value="<?=htmlspecialchars($spec_title[$i])?>" size="100" placeholder='입력'><a class="del">삭제</a></li>
                <?php } ?>
              </ul>
              <a class="add">행 추가</a>
            </td>
        </tr>


        </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="작성완료" id="btn_submit" accesskey="s" class="btn_submit btn">
        <a href="./board.php?bo_table=<?php echo $bo_table ?>" class="btn_cancel btn">취소</a>
    </div>
    </form>

    <script>
          cont_val();
          $(document).on("click", ".wr_content .add", function() {
              $(".wr_content ul").append("<li><input type='text' class='wr_content' size='100' placeholder='입력'><a class='del'>삭제</a></li>");
              cont_val();
          })

          $(document).on("click", ".wr_content li .del", function() {
              $(this).parent().remove();
              cont_val();
          })

          $(document).on("keyup", ".wr_content li input", function() {
              cont_val();
          })

          function cont_val() {
              var val1 = "";
              for(var i=1; i<=$(".wr_content ul li").length; i++) {
                  if(i>1) {
                      val1 += "//";
                  }
                  val1 += $(".wr_content ul li:nth-of-type("+i+") .wr_content").val();
              }
              $(".wr_content input[name=wr_content]").val(val1);
          }
      </script>

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
    function html_auto_br(obj)
    {
        if (obj.checked) {
            result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
            if (result)
                obj.value = "html2";
            else
                obj.value = "html1";
        }
        else
            obj.value = "";
    }

    function fwrite_submit(f)
    {
        <?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

        var subject = "";
        var content = "";
        $.ajax({
            url: g5_bbs_url+"/ajax.filter.php",
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
            alert("제목에 금지단어('"+subject+"')가 포함되어있습니다");
            f.wr_subject.focus();
            return false;
        }

        if (content) {
            alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
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
                    alert("내용은 "+char_min+"글자 이상 쓰셔야 합니다.");
                    return false;
                }
                else if (char_max > 0 && char_max < cnt) {
                    alert("내용은 "+char_max+"글자 이하로 쓰셔야 합니다.");
                    return false;
                }
            }
        }

        <?php echo $captcha_js; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사함  ?>

        document.getElementById("btn_submit").disabled = "disabled";

        return true;
    }
    </script>
</section>
<!-- } 게시물 작성/수정 끝 -->
