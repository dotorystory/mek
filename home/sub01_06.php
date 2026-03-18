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
      <span data-split="연구 분야"></span>
      <h5>연구 분야</h5>
    </div>
    <ul>
      <li><p><span class="overview_tit">신제품 개발</span></p>
        <p><i class="ri-survey-line"></i></p></li>
      <li><p><span class="overview_tit">AI제어시스템 개발</span></p><p><i class="ri-settings-2-line"></i></p></li>
      <li><p><span class="overview_tit">신규 센서 개발</span></p><p><i class="ri-lightbulb-flash-line"></i></p></li>
    </ul>
  </section>

  <section class="sub-section section03 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="연구 조직"></span>
      <h5>연구 조직</h5>
    </div>
    <img src="<?php echo G5_IMG_URL?>/rnd_04.png">
  </section>

  <section class="sub-section section04 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="연구 설비"></span>
      <h5>연구 설비</h5>
    </div>
    <div class="flex_wrap">
      <ul>
        <li>Sheet 시험 제조</li>
        <li>On-Line 시험 측정 Line 1</li>
        <li>On-Line 시험 측정 Line 2 -</li>
        <li>Web Cleaner 시험 설비 (Clean Room)</li>
        <li>Pinning Demo Line</li>
        <li>Web 검사 Test 설비</li>
      </ul>
      <img src="<?php echo G5_IMG_URL?>/rnd_05.png">
    </div>
  </section>

</div>

<?php
include_once(G5_PATH.'/tail.php');
?>
