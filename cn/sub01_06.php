<?php
include_once('./_common.php');

$menuCodeParent = 0;
$menuCodeChild = 4;
include_once(G5_PATH.'/head.php');
?>
<div id="sub_wrap" class="sub01_06">
  <section class="sub-section section01 sub-container">
    <ul class="img_box">
      <li><img src="<?php echo G5_IMG_URL?>/rnd_01.png"></li>
      <li><img src="<?php echo G5_IMG_URL?>/rnd_02.png"></li>
      <li><img src="<?php echo G5_IMG_URL?>/rnd_03.png"></li>
    </ul>
  </section>
  <section class="sub-section section02 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="研究领域"></span>
      <h5>研究领域</h5>
    </div>
    <ul>
      <li><p><span class="overview_tit">新产品开发</span></p>
        <p><i class="ri-survey-line"></i></p></li>
      <li><p><span class="overview_tit">AI控制系统开发</span></p><p><i class="ri-settings-2-line"></i></p></li>
      <li><p><span class="overview_tit">新型传感器开发</span></p><p><i class="ri-lightbulb-flash-line"></i></p></li>
    </ul>
  </section>

  <section class="sub-section section03 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="研究组织"></span>
      <h5>研究组织</h5>
    </div>
    <img src="<?php echo G5_IMG_URL?>/rnd_04.png">
  </section>

  <section class="sub-section section04 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="研究设备"></span>
      <h5>研究设备</h5>
    </div>
    <div class="flex_wrap">
      <ul>
        <li>Sheet 试制制造</li>
        <li>On-Line 试验测量 Line 1</li>
        <li>On-Line 试验测量 Line 2 -</li>
        <li>Web Cleaner 试验设备 (Clean Room)</li>
        <li>Pinning Demo Line</li>
        <li>Web检测Test设备</li>
      </ul>
      <img src="<?php echo G5_IMG_URL?>/rnd_05.png">
    </div>
  </section>

</div>

<?php
include_once(G5_PATH.'/tail.php');
?>
