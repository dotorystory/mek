<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
//add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);



//$thumb_width = 90;
//$thumb_height = 65;
?>





<!-- <ul class="p2_tit">
    <a href="<//?php echo G5_BBS_URL ?>/board.php?bo_table=<//?php echo $bo_table ?>"><//?php echo $bo_subject ?></a>
    <a href="<//?php echo G5_BBS_URL ?>/board.php?bo_table=<//?php echo $bo_table ?>" class="dot_r"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
</ul> -->

<link rel="stylesheet" href="./skin/latest/history/style.css">

<div class="bd1">

    <?php
    for ($i=0; $i<count($list); $i++) {
    $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height, false, true);

    if($thumb['src']) {
        $img = $thumb['src'];
    } else {
       // $img = G5_IMG_URL.'/no_img.png';
        $img = $latest_skin_url.'/img/no.png';
        //$thumb['alt'] = '이미지가 없습니다.';
    }
    $img_content = '<img src="'.$img.'" alt="'.$thumb['alt'].'" style="border:1px solid rgba(0,0,0,0.1); box-sizing: border-box;">';
    ?>

    <ul class="news_div">
      <?php if($is_admin){ ?>
        <li>
          <a href="<?php echo $list[$i]['href'] ?>" class="elss1">
            <dl>
              <div class="news_ul01">
                  <dt class="news_ll01"><?php echo $list[$i]['subject'] ?></dt>
                  <dd class="news_wrap">
                    <!-- <p class="news_ll02">
                        <strong class="month">
                          <//?php echo sprintf("%02d",$list[$i]['wr_1']); ?>월
                        </strong>　
                    </p> -->
                    <p class="news_ll03 desc">
                      <?php
                      $wr_content = $list[$i]['wr_content'];
                      $wr_content_lines = explode("\n", $wr_content);
                      foreach ($wr_content_lines as $line) {
                          echo '<span>' . trim($line) . '</span><br />';
                      }
                      ?>
                    </p>
                  </dd>
              </div>
            </dl>

            </a>
        </li>

        <?php } else {?>
          <li>
            <a href="javascript:void(0);" class="elss1">
              <dl>
                <div class="news_ul01">
                    <dt class="news_ll01"><?php echo $list[$i]['subject'] ?></dt>
                    <dd class="news_wrap">
                      <ul class="desc" <?php if(empty($list[$i]['wr_content'])=='NULL')  { echo "style=display:none"; }?>>
        								<?php $spec_title = explode('//',$list[$i]['wr_content']);
        											for($s=0; $s<count($spec_title); $s++) { ?>
        		            <li><?=$spec_title[$s]?></li>
        								<?php } ?>
        		          </ul>
                    </dd>
                  </div>
              </dl>
            </a>
          </li>
        <?php } ?>
    </ul>




    <?php } if (count($list) == 0) { //게시물이 없을 때  ?>
    <div class="news_div">
        <ul class="news_ul01">
            <li class="news_ll01">등록된 게시물이 없습니다.</li>
        </ul>
        <div style="clear:both"></div>
    </div>
    <?php }  ?>

</div>
