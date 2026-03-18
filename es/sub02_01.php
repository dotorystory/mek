<?php
include_once('./_common.php');

$menuCodeParent = 1;
$menuCodeChild = 0;
include_once(G5_PATH.'/head.php');
?>
<div id="sub_wrap" class="sub02_01">
  <section class="sub-section section01 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="Applications"></span>
      <h5>Applications</h5>
    </div>
    <div class="sub_content">
      <h2>Baterías</h2>
      <div class="img_list_box">
        <div class="sub_img">
          <img src="<?php echo G5_IMG_URL?>/sub02_img01.png" alt="Equipo de Fabricación de Productos de Batería">
          </div>
          <div class="list_wrap">
            <ul class="list">
                <li>
                  Lámina de Cobre
                </li>
                <li>
                  Separador
                </li>
                <li>
                  Recubrimiento de Electrodo
                </li>
                <li>
                  Prensado de Rodillo de Electrodo
                </li>
                <li>
                  Batería de estado sólido
                </li>
                <li>
                  Celda de combustible
                </li>
              </ul>
              <p class="list_icon"><img src="<?php echo G5_IMG_URL?>/sub02_icon01.png" alt="Battery Icon"></p>
          </div>
      </div>
    </div>
  </section>
</div>
<?php
include_once(G5_PATH.'/tail.php');
?>
