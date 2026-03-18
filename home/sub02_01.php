<?php
include_once('./_common.php');

$menuCodeParent = 1;
$menuCodeChild = 0;
include_once(G5_PATH.'/head.php');
?>
<div id="sub_wrap" class="sub02_01">
  <section class="sub-section section01 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="적용분야"></span>
      <h5>적용분야</h5>
    </div>
    <div class="sub_content">
      <h2>전지 / Batteries</h2>
      <div class="img_list_box">
        <div class="sub_img">
          <img src="<?php echo G5_IMG_URL?>/sub02_img01.png" alt="전지제품생산기기">
          </div>
          <div class="list_wrap">
            <ul class="list">
                <li>
                  동박(Copper Foil)
                </li>
                <li>
                  분리막(Separator)
                </li>
                <li>
                  전극 코팅(Electrode Coating)
                </li>
                <li>전극롤프레싱(Electrode Roll Pressing)</li>
                <li>전고체배터리(Solid-state battery)</li>
                <li>연료전지(Fuel cell)</li>
              </ul>
              <p class="list_icon"><img src="<?php echo G5_IMG_URL?>/sub02_icon01.png" alt="배터리아이콘"></p>
          </div>
      </div>
    </div>
  </section>
</div>
<?php
include_once(G5_PATH.'/tail.php');
?>
