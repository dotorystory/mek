<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
include_once(G5_PATH.'/KeyShotXR.php');


// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>

<!-- 게시물 읽기 시작 { -->


<article id="bo_v" style="width:<?php echo $width; ?>">
    <header>
        <h2 id="bo_v_title">
            <?php if ($category_name) { ?>
            <span class="bo_v_cate"><?php echo $view['ca_name']; // 분류 출력 끝 ?></span>
            <?php } ?>
            <span class="bo_v_tit">
            <?php
            echo cut_str(get_text($view['wr_subject']), 70); // 글제목 출력
            ?></span>
        </h2>
    </header>

    <section id="bo_v_atc">
        <h2 id="bo_v_atc_title">본문</h2>
        <div id="bo_v_share">
        	<?php include_once(G5_SNS_PATH."/view.sns.skin.php"); ?>
	    </div>
      <div id="pro_main_w">
        <section id="bo_v_info" class="sub-container">
            <h2>페이지 정보</h2>
            <div class="profile_info">
              <div class="pf_img"><?php echo get_member_profile_img($view['mb_id']) ?></div>
              <div class="profile_info_ct">
                <span class="sound_only">작성자</span> <strong><?php echo $view['name'] ?><?php if ($is_ip_view) { echo "&nbsp;($ip)"; } ?></strong><br>
                <span class="sound_only">댓글</span><strong><a href="#bo_vc"> <i class="fa fa-commenting-o" aria-hidden="true"></i> <?php echo number_format($view['wr_comment']) ?>건</a></strong>
                <span class="sound_only">조회</span><strong><i class="fa fa-eye" aria-hidden="true"></i> <?php echo number_format($view['wr_hit']) ?>회</strong>
                <strong class="if_date"><span class="sound_only">작성일</span><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo date("y-m-d H:i", strtotime($view['wr_datetime'])) ?></strong>
            </div>
          </div>

          <!-- 게시물 상단 버튼 시작 { -->
          <div id="bo_v_top">
              <?php ob_start(); ?>

              <ul class="btn_bo_user bo_v_com">
            <li><a href="<?php echo $list_href ?>" class="btn_b01 btn" title="목록"><i class="ri-menu-line"></i><span class="sound_only">목록</span></a></li>
                  <?php if ($reply_href) { ?><li><a href="<?php echo $reply_href ?>" class="btn_b01 btn" title="답변"><i class="ri-reply-line"></i><span class="sound_only">답변</span></a></li><?php } ?>
                  <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b01 btn" title="글쓰기"><i class="ri-pencil-line"></i><span class="sound_only">글쓰기</span></a></li><?php } ?>
                <?php if($update_href || $delete_href || $copy_href || $move_href || $search_href) { ?>
                <li>
                  <button type="button" class="btn_more_opt is_view_btn btn_b01 btn" title="게시판 리스트 옵션"><i class="ri-more-2-line"></i><span class="sound_only">게시판 리스트 옵션</span></button>
                  <ul class="more_opt is_view_btn">
    <?php if ($update_href) { ?><li><a href="<?php echo $update_href ?>">Edit <i class="ri-edit-line"></i></a></li><?php } ?>
    <?php if ($delete_href) { ?><li><a href="<?php echo $delete_href ?>" onclick="del(this.href); return false;">Delete <i class="ri-delete-bin-line"></i></a></li><?php } ?>
    <?php if ($copy_href) { ?><li><a href="<?php echo $copy_href ?>" onclick="board_move(this.href); return false;">Copy <i class="ri-file-copy-line"></i></a></li><?php } ?>
    <?php if ($move_href) { ?><li><a href="<?php echo $move_href ?>" onclick="board_move(this.href); return false;">Move <i class="ri-share-forward-box-line"></i></a></li><?php } ?>
    <?php if ($search_href) { ?><li><a href="<?php echo $search_href ?>">Search <i class="ri-search-line"></i></a></li><?php } ?>
</ul>

                </li>
                <?php } ?>
              </ul>
              <script>

                jQuery(function($){
                    // 게시판 보기 버튼 옵션
            $(".btn_more_opt.is_view_btn").on("click", function(e) {
                        e.stopPropagation();
                $(".more_opt.is_view_btn").toggle();
            })
    ;
                    $(document).on("click", function (e) {
                        if(!$(e.target).closest('.is_view_btn').length) {
                            $(".more_opt.is_view_btn").hide();
                        }
                    });
                });

                </script>
              <?php
              $link_buttons = ob_get_contents();
              ob_end_flush();
               ?>
          </div>
          <!-- } 게시물 상단 버튼 끝 -->
        </section>
      <div id="pro_text_w" class="sub-container">
        <div id="bo_v_text">
            <h2 id="bo_v_title">
            <p class="bo_v_tit">
            <?php
            echo cut_str(get_text($view['wr_subject']), 70); // 글제목 출력
            ?></p>
            <?php if ($category_name) { ?>
            <span class="bo_v_cate"><?php echo $view['ca_name']; // 분류 출력 끝 ?></span>
            <?php } ?>
          </h2>
          <div id="bo_v_con">
            <?php echo get_view_thumbnail($view['content']); ?>
          </div><!-- } #bo_v_con 끝 -->
        </div> <!-- } #bo_v_text 끝 -->


      <!-- Include Swiper CSS -->
      <link rel="stylesheet" href="./../css/swiper.css">
      <div class="pro-gallery-container">
        <div class="swiper-wrapper">
          <?php
          $v_img_count = count($view['file']);
          if ($v_img_count) {

              foreach ($view['file'] as $view_file) {
                $thumbnail = get_file_thumbnail($view_file);
                          // <a 태그에 swiper-slide 클래스 추가
                          $modifiedThumbnail = preg_replace('/<a /', '<a class="swiper-slide" ', $thumbnail);
                          echo $modifiedThumbnail;
                 }
          }
          ?>
        </div>
        <div class="pro-arrow">
          <div class="pro-pagination-wrap">
            <div class="pro-swiper-pagination"></div>
          </div>
            <div class="pro-arrow-wrap">
            <p class="pro-swiper-button-prev bx-prev"><i class="ri-arrow-left-s-line"></i></p>
            <span>|</span>
            <p class="pro-swiper-button-next bx-next"><i class="ri-arrow-right-s-line"></i></p>
          </div>
        </div>
      </div>
      <script src="./../js/swiper.js"></script>
      <script>
         document.addEventListener("DOMContentLoaded", function () {
      var mySwiper = new Swiper('.pro-gallery-container', {
          loop: true,               // Infinite looping
          slidesPerView: 1,        // Number of slides to show at once
          spaceBetween: 0,        // Space between slides
          autoplay: {
              delay: 2000,          // Auto-scrolling delay in milliseconds
              disableOnInteraction: false, // Enable autoplay even after user interaction
          },
          pagination: {
          el: ".pro-swiper-pagination",
          type: "progressbar",
          },
          navigation: {
              nextEl: '.pro-swiper-button-next',
              prevEl: '.pro-swiper-button-prev',
          },
      });

      // Add event listeners for user interaction
      mySwiper.on('slideChange', function () {
          // Slide changed, auto-scrolling is active
          // You can add code here to handle the slide change event
      });

      mySwiper.on('touchStart', function () {
          // User started a touch interaction, pause auto-scrolling
          mySwiper.autoplay.stop();
      });

      mySwiper.on('touchEnd', function () {
          // User ended a touch interaction, resume auto-scrolling
          mySwiper.autoplay.start();
      });
  });
  (function() {
      const targetNode = document.querySelector("#bo_v_text");

      // 옵션 설정
      const config = { childList: true, subtree: true };

      // 콜백 함수
      const callback = function(mutationsList, observer) {
          for(let mutation of mutationsList) {
              if (mutation.type === 'childList') {
                  let mcViewElement = document.querySelector(".mc-view-pro_en");
                  if(mcViewElement) {
                      targetNode.insertBefore(mcViewElement, targetNode.firstChild);
                      observer.disconnect(); // 필요한 동작을 완료한 후 감시 중지
                  }
              }
          }
      };

      const observer = new MutationObserver(callback);
      observer.observe(document.body, config); // body 전체를 감시
  })();
</script>
      </div>
    </div>
              <?php if ($is_signature) { ?><p><?php echo $signature ?></p><?php } ?>

<!--3d 이미지 출력 -->
        <div id="img360">
        <div class="wrap_360">
        <h4 class="img_360_tit"><img src="<?=G5_URL?>/img/img_360.png" ></h4>
            <ul class="img_360">
            <?php if($view['wr_8']) { ?>
                <li>
                <div id="KeyShotXR"></div>
                </li>
            <?php } ?>

            </ul>
        </div>
    </div>


    <div class="spec_box sub-container">
      <h4 class="view_title spec_title"<?php if(empty($view['wr_3'])=='NULL') { echo "style=display:none"; }?>>SPECIFICATIONS</h4>
      <?php if($view['file'][6]['file']) { ?><a href="<?php echo $view['file'][6]['href'];  ?>" class="manual"><i class="fas fa-download"></i> Manual</a><?php } ?>
      <div class="table_wrap">
        <table class="spec_table"<?php if(empty($view['wr_3'])=='NULL') { echo "style=display:none"; }?>>
          <thead>
            <tr>
              <th></th>
              <th>Description</th>
            </tr>
          </thead>
      <?php $spec_title = explode('//',$view['wr_3']);
      $spec = explode('//',$view['wr_4']);
      for($i=0; $i<count($spec_title); $i++) { ?>
      <tr class="table_tr">
        <th><?=$spec_title[$i]?></th>
        <td><?=$spec[$i]?></td>
      </tr>
      <?php } ?>
     </table>
      </div>
    </div>

    <div id="softwear" class="sub-container">
      <h4>PROGRAM SOFTWARE</h4>
      <div class="view_list_wrap view_list_wrap01">
      <h5>Features of the MEK Program</h5>
      <ul>
          <li><img src="<?=G5_URL?>/img/vi_img02.png"><span>• User-friendly Configuration - Intuitive Interface</span></li>
          <li><img src="<?=G5_URL?>/img/vi_img03.png"><span>• Language Options - English, Chinese, Japanese</span></li>
          <li><img src="<?=G5_URL?>/img/vi_img04.png"><span>• User HMI and Measurement Data Communication (Optional)</span></li>
          <li><img src="<?=G5_URL?>/img/vi_img05.png"><span>• Customization to Suit User Requirements</span></li>
      </ul>
  </div>

      <div class="view_list_wrap view_list_wrap02">
        <h5>Default Screen</h5>
        <ul>
          <li><img src="<?=G5_URL?>/img/vi_img06.png"></li>
          <li><img src="<?=G5_URL?>/img/vi_img07.png"></li>
          <li><img src="<?=G5_URL?>/img/vi_img08.png"></li>
          <li><img src="<?=G5_URL?>/img/vi_img09.png"></li>
          <li><img src="<?=G5_URL?>/img/vi_img10.png"></li>
          <li><img src="<?=G5_URL?>/img/vi_img11.png"></li>
          <li><img src="<?=G5_URL?>/img/vi_img12.png"></li>
          <li><img src="<?=G5_URL?>/img/vi_img13.png"></li>
        </ul>
      </div>
      <div class="view_list_wrap view_list_wrap03 spec_box">
        <table class="spec_table">
  <thead>
    <tr>
      <th></th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <tr class="table_tr">
      <th>Run Environment</th>
      <td>LabVIEW-based proprietary software</td>
    </tr>
    <tr class="table_tr">
      <th>Data display</th>
      <td>gsm (basis weight) / um (thickness)<br>Spatial resolution: 1mm</td>
    </tr>
    <tr class="table_tr">
      <th>Data compatibility</th>
      <td>Microsoft Excel, CSV</td>
    </tr>
    <tr class="table_tr">
      <th>Functions</th>
      <td>Full automatic edge detection<br>Full mapping of entire LOT (2D & 3D reconstruction)<br>Real-time Profile & SPC (Statistical Process Control) Trend chart<br>FFT (Fast Fourier Transform) Analysis of MD Profile<br>LOT Statistics & History<br>4 & 20 Scan Sigma</td>
    </tr>
    <tr class="table_tr">
      <th>Language</th>
      <td>English, Korean, Chinese, Japanese<br>(Hungarian translation available upon request)</td>
    </tr>
  </tbody>

  </table>
      </div>
      <div id="apc" class="sub-container">
  <h4>APC (Automatic Profile Control)</h4>
  <div class="view_list_wrap view_list_wrap04">
    <h5>Features of the MEK Program</h5>
    <ul>
      <li>• Optimized thickness control algorithm</li>
      <li>• Fast thickness control</li>
      <li>• Auto Mapping: Center bolt reference</li>
      <li>• Smooth Mill Roll appearance</li>
      <li>• Automatic adjustment of Control Gain within a few microns</li>
      <li>• Die bolt temperature feedback control (optional)</li>
      <li>• MD (Machine Direction) control available (optional)</li>
    </ul>
  </div>
  <div class="view_list_wrap view_list_wrap05">
    <h5>Profile Changes After Applying APC</h5>
    <div class="apc_grid">
      <div class="apc_txt">
        <p>Before</p>APC control begins after the target thickness is changed from 60㎛ to 30㎛
      </div>
      <div class="apc_pic">
        <img src="<?=G5_URL?>/img/vi_img14.png">
      </div>
      <div class="apc_arrow">
        <p> <img src="<?=G5_URL?>/img/pro_arrow.png" alt="Down Arrow"> </p>
      </div>
      <div class="apc_void">
      </div>
      <div class="apc_txt">
        <p>After 5 minutes</p>Regulates Control Gain of the auto-die, reducing variation in thickness
      </div>
      <div class="apc_pic">
        <img src="<?=G5_URL?>/img/vi_img15.png">
      </div>
      <div class="apc_arrow">
        <p> <img src="<?=G5_URL?>/img/pro_arrow.png" alt="Down Arrow"> </p>
      </div>
      <div class="apc_void">
      </div>
      <div class="apc_txt">
        <p>After 10 minutes</p>Steadily analyzes and controls the thickness profiles
      </div>
      <div class="apc_pic">
        <img src="<?=G5_URL?>/img/vi_img16.png">
      </div>
      <div class="apc_arrow">
        <p> <img src="<?=G5_URL?>/img/pro_arrow.png" alt="Down Arrow"> </p>
      </div>
      <div class="apc_void">
      </div>
      <div class="apc_txt">
        <p>After 15 minutes</p>Within 15 minutes, produces the best result
      </div>
      <div class="apc_pic">
        <img src="<?=G5_URL?>/img/vi_img17.png">
      </div>
    </div>
  </div>
</div>
    </div>
    </section>

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
    <section id="bo_v_file">
        <h2>첨부파일</h2>
        <ul>
        <?php
        // 가변 파일
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
         ?>
            <li>
               	<i class="fa fa-folder-open" aria-hidden="true"></i>
                <a href="<?php echo $view['file'][$i]['href'];  ?>" class="view_file_download">
                    <strong><?php echo $view['file'][$i]['source'] ?></strong> <?php echo $view['file'][$i]['content'] ?> (<?php echo $view['file'][$i]['size'] ?>)
                </a>
                <br>
                <span class="bo_v_file_cnt"><?php echo $view['file'][$i]['download'] ?>회 다운로드 | DATE : <?php echo $view['file'][$i]['datetime'] ?></span>
            </li>
        <?php
            }
        }
         ?>
        </ul>
    </section>
    <!-- } 첨부파일 끝 -->
    <?php } ?>

    <?php if(isset($view['link']) && array_filter($view['link'])) { ?>
    <!-- 관련링크 시작 { -->
    <section id="bo_v_link">
    <h2>Related Links</h2>
    <ul>
    <?php
    // Loop through links
    $cnt = 0;
    for ($i = 1; $i <= count($view['link']); $i++) {
        if ($view['link'][$i]) {
            $cnt++;
            $link = cut_str($view['link'][$i], 70);
        ?>
        <li>
            <i class="fa fa-link" aria-hidden="true"></i>
            <a href="<?php echo $view['link_href'][$i] ?>" target="_blank">
                <strong><?php echo $link ?></strong>
            </a>
            <br>
            <span class="bo_v_link_cnt"><?php echo $view['link_hit'][$i] ?> times connected</span>
        </li>
        <?php
        }
    }
    ?>
    </ul>
</section>

    <!-- } 관련링크 끝 -->
    <?php } ?>

    <div class="control sub-container">
    <div class="khwrap">
        <div class="button fl">
            <?php if ($update_href) { ?><a href="<?php echo $update_href ?>" class="bt bt_b02"><i class="ri-edit-line"></i> <?php echo _("Edit") ?></a><?php } ?>
            <?php if ($delete_href) { ?><a href="<?php echo $delete_href ?>" class="bt bt_adm" onclick="del(this.href); return false;"><i class="ri-delete-bin-line"></i> <?php echo _("Delete") ?></a><?php } ?>
            <?php if ($copy_href) { ?><a href="<?php echo $copy_href ?>" class="bt bt_adm" onclick="board_move(this.href); return false;"><i class="ri-file-copy-line"></i> <?php echo _("Copy") ?></a><?php } ?>
            <?php if ($move_href) { ?><a href="<?php echo $move_href ?>" class="bt bt_adm" onclick="board_move(this.href); return false;"><i class="ri-share-forward-box-line"></i> <?php echo _("Move") ?></a><?php } ?>
            <?php if ($search_href) { ?><a href="<?php echo $search_href ?>" class="bt bt_adm"><i class="ri-search-line"></i> <?php echo _("Search") ?></a><?php } ?>
        </div>
        <div class="button fr">
            <a href="<?php echo $list_href ?>" class="bt bt_list"><i class="ri-menu-line"></i> <?php echo _("List") ?></a>
            <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="bt bt_b02"><i class="ri-pencil-line"></i> <?php echo _("Register") ?></a><?php } ?>
        </div>
    </div>
</div>

    <!-- <//?php if ($prev_href || $next_href) { ?>
    <ul class="bo_v_nb sub-container">
        <//?php if ($prev_href) { ?><li class="btn_prv"><span class="nb_tit"><i class="fa fa-chevron-up" aria-hidden="true"></i> 이전글</span><a href="<//?php echo $prev_href ?>"><//?php echo $prev_wr_subject;?></a> <span class="nb_date"><//?php echo str_replace('-', '.', substr($prev_wr_date, '2', '8')); ?></span></li><//?php } ?>
        <//?php if ($next_href) { ?><li class="btn_next"><span class="nb_tit"><i class="fa fa-chevron-down" aria-hidden="true"></i> 다음글</span><a href="<//?php echo $next_href ?>"><//?php echo $next_wr_subject;?></a>  <span class="nb_date"><//?php echo str_replace('-', '.', substr($next_wr_date, '2', '8')); ?></span></li><//?php } ?>
    </ul>
    <//?php } ?> -->


</article>

<button id="popupButton">SENSOR</button>

<div id="popupOverlay" class="overlay">
    <div id="popup" class="popup">
        <span id="closePopup" class="close">&times;</span>
        <img src="<?=G5_IMG_URL?>/img_propop.png">
    </div>
</div>
<!-- } 게시판 읽기 끝 -->

<script>
document.getElementById('popupButton').addEventListener('click', function() {
    document.getElementById('popupOverlay').classList.add('show');
});

document.getElementById('closePopup').addEventListener('click', function() {
    document.getElementById('popupOverlay').classList.remove('show');
});

window.addEventListener('click', function(event) {
    if (event.target == document.getElementById('popupOverlay')) {
        document.getElementById('popupOverlay').classList.remove('show');
    }
});

    // 제품 이미지 폴더 설정
    let folderName = "../../img360/<?=$view['wr_8']?>";

    <?php if ($board['bo_download_point'] < 0) { ?>
    $(function() {
        $("a.view_file_download").click(function() {
            if(!g5_is_member) {
                alert("You do not have download permission. Please log in as a member to use this feature.");
                return false;
            }

            var msg = "Downloading a file will deduct points (<?php echo number_format($board['bo_download_point']) ?> points).\n\nPoints are deducted only once per post, and downloading the same file again will not deduct additional points.\n\nDo you still want to download?";

            if(confirm(msg)) {
                var href = $(this).attr("href")+"&js=on";
                $(this).attr("href", href);

                return true;
            } else {
                return false;
            }
        });
    });
    <?php } ?>

    function board_move(href)
    {
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
        if(this.id == "good_button")
            $tx = $("#bo_v_act_good");
        else
            $tx = $("#bo_v_act_nogood");

        excute_good(this.href, $(this), $tx);
        return false;
    });

    // 이미지 리사이즈
    $("#bo_v_atc").viewimageresize();
});

function executeGood(href, $el, $tx) {
    // Send a POST request to the specified 'href' URL with the parameter 'js' set to "on".
    $.post(
        href,
        { js: "on" },
        function(data) {
            // Check if there is an error in the response data.
            if (data.error) {
                alert(data.error); // Display the error message in an alert.
                return false; // Exit the function.
            }

            // Check if there is a 'count' property in the response data.
            if (data.count) {
                // Update the content of the element with the count.
                $el.find("strong").text(number_format(String(data.count)));

                // Determine whether the action was "good" or "nogood" based on the element's ID.
                if ($tx.attr("id").search("nogood") > -1) {
                    $tx.text("You have disliked this."); // Display a message for "nogood" action.
                    $tx.fadeIn(200).delay(2500).fadeOut(200); // Show and hide the message.
                } else {
                    $tx.text("You have liked this."); // Display a message for "good" action.
                    $tx.fadeIn(200).delay(2500).fadeOut(200); // Show and hide the message.
                }
            }
        }, "json"
    );
}


document.addEventListener("DOMContentLoaded", function() {
  const subtitleWrap = document.querySelector(".subtitle_wrap");
  if (subtitleWrap) {
      subtitleWrap.style.display = "none";
  }
  const subElement = document.querySelector("#sub");
   if (subElement) {
       subElement.style.padding = "0";
   }

});

$(document).ready(function() {
  var oldPosition;
  var easingStyle;
  var floatTarget = $("#popupButton");
  var floatSpeed = 400;

  $(window).load(function() {
    oldPosition = floatTarget.position().top;
    floating();
  });

  $(window).scroll(function() {
    floating();
  });

  function floating() {
    var newPosition = oldPosition + $(document).scrollTop();
    floatTarget.stop().animate({
      top: newPosition
    }, floatSpeed);
  }

  floatTarget.click(function() {
    $("html, body").animate({
      scrollTop: 0
    }, 400);
    return false;
  });

});

</script>
<!-- } 게시글 읽기 끝 -->
