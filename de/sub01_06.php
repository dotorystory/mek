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
      <span data-split="Forschungsfelder"></span>
      <h5>Forschungsfelder</h5>
    </div>
    <ul>
      <li><p><span class="overview_tit">Neue Produktentwicklung</span></p>
        <p><i class="ri-survey-line"></i></p></li>
      <li><p><span class="overview_tit">KI-Steuerungssystementwicklung</span></p><p><i class="ri-settings-2-line"></i></p></li>
      <li><p><span class="overview_tit">Neue Sensorentwicklung</span></p><p><i class="ri-lightbulb-flash-line"></i></p></li>
    </ul>
  </section>

  <section class="sub-section section03 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="Forschungsorganisation"></span>
      <h5>Forschungsorganisation</h5>
    </div>
    <img src="<?php echo G5_IMG_URL?>/rnd_04.png">
  </section>

  <section class="sub-section section04 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="Forschungsausrüstung"></span>
      <h5>Forschungsausrüstung</h5>
    </div>
    <div class="flex_wrap">
      <ul>
        <li>Blatt-Testherstellung</li>
        <li>Online-Testmesslinie 1</li>
        <li>Online-Testmesslinie 2</li>
        <li>Web-Reiniger-Testausrüstung (Reinraum)</li>
        <li>Pinning-Demolinie</li>
        <li>Web-Inspektionstestausrüstung</li>
      </ul>
      <img src="<?php echo G5_IMG_URL?>/rnd_05.png">
    </div>
  </section>

</div>

<?php
include_once(G5_PATH.'/tail.php');
?>
