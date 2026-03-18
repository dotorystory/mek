<?php
include_once('./_common.php');

$menuCodeParent = 0;
$menuCodeChild = 3;
include_once(G5_PATH.'/head.php');
?>
<div id="sub_wrap" class="sub01_04">
  <section class="sub-section section01 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="Patentes"></span>
      <h5>Patentes</h5>
    </div>
    <div class="sub_content">
      <?php echo latest("sub04_img_lat", "patent_en", 18, 32) ?>
    </div>
  </section>
  <section class="sub-section section02 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="Certificados"></span>
      <h5>Certificados</h5>
    </div>
    <div class="sub_content">
      <?php echo latest("sub04_img_lat", "certif_en", 18, 32) ?>
    </div>
  </section>
  <section class="sub-section section02 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="Licencia"></span>
      <h5>Licencia</h5>
    </div>
    <div class="sub_content">
      <?php echo latest("sub04_img_lat", "license_en", 18, 32) ?>
    </div>
  </section>
</div>

<?php
include_once(G5_PATH.'/tail.php');
?>
