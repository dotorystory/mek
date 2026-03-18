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
      <span data-split="Campos de Investigación"></span>
      <h5>Campos de Investigación</h5>
    </div>
    <ul>
      <li>        <p><span class="overview_tit">Desarrollo de Nuevos Productos</span></p>
        <p><i class="ri-survey-line"></i></p></li>
      <li><p><span class="overview_tit">Desarrollo de Sistema de Control IA</span></p><p><i class="ri-settings-2-line"></i></p></li>
      <li><p><span class="overview_tit">Desarrollo de Nuevos Sensores</span></p><p><i class="ri-lightbulb-flash-line"></i></p></li>
    </ul>
  </section>

  <section class="sub-section section03 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="Organización de Investigación"></span>
      <h5>Organización de Investigación</h5>
    </div>
    <img src="<?php echo G5_IMG_URL?>/rnd_04.png">
  </section>

  <section class="sub-section section04 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="Equipamiento de Investigación"></span>
      <h5>Equipamiento de Investigación</h5>
    </div>
    <div class="flex_wrap">
      <ul>
        <li>Sheet Testing Manufacturing</li>
        <li>On-Line Testing Measurement Line 1</li>
        <li>On-Line Testing Measurement Line 2 -</li>
        <li>Web Cleaner Testing Equipment (Clean Room)</li>
        <li>Pinning Demo Line</li>
        <li>Web Inspection Test Equipment</li>
      </ul>
      <img src="<?php echo G5_IMG_URL?>/rnd_05.png">
    </div>
  </section>

</div>

<?php
include_once(G5_PATH.'/tail.php');
?>
