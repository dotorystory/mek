<?php
include_once('./_common.php');

define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/index.php');
    return;
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/index.php');
    return;
}

include_once(G5_PATH.'/head.php');
?>
<!-- 메인 시작 -->
<div class="page_anchor_wrap carousel">
  <nav class="main-page-anchor">
    <ul>
      <li data-menuanchor="section-page1" class="active"><a href="#section-page1"></a></li>
      <li data-menuanchor="section-page2"><a href="#section-page2"></a></li>
  	  <li data-menuanchor="section-page3"><a href="#section-page3"></a></li>
      <li data-menuanchor="section-page4"><a href="#section-page4"></a></li>
      <li data-menuanchor="section-page5"><a href="#section-page5"></a></li>
      <li data-menuanchor="section-page6"><a href="#section-page6"></a></li>
      <li data-menuanchor="section-page7"><a href="#section-page7"></a></li>
      <li data-menuanchor="section-page8"><a href="#section-page8"></a></li>
    </ul>
  </nav>
  <div class="scroll-down">
      <p class="scroll-txt">Scroll Down</p>
      <p class="scroll-img"><img src="<?php echo G5_IMG_URL?>/icon_arrow01.png" alt="scroll down arrow"></p>
  </div>
</div>
<div class="main" id="fullpage">
<main>
    <section id="main_01" class="main-section section fp-auto-height fp-auto-height-responsive">
      <div class="swiper-container">
            <div class="arrow">
              <div class="arrow-wrap">
                <p class="swiper-button-prev bx-prev"><span class="line"></span></p>
                <div class="swiper-pagination"></div>
                <p class="swiper-button-next bx-next"><span class="line"></span></p>
              </div>

            </div>

        <div class="swiper-wrapper">
          <div class="swiper-slide sl01">
            <div class="main-container">
              <div class="vi_txt_po">
                <div class="vi_txt_wrap">
                  <h2>Measurement<br><span class="vi_small">and</span> Control<br class="br_mo"> Solutions</h2>
                  <p class="vi_txt">MEK オンライン厚み計は、ウェブ形状の製品の厚さを測定する装置です。<br class="pc">製品の生産中に全幅の厚みをリアルタイムで測定し、制御することで、<br class="pc">品質向上、プロセスの最適化及び原価削減に貢献します。</p>
                  <p class="main_button button_white"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=pro_jp&wr_id=20" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
                </div>
              </div>
            </div>
          </div>
          <div class="swiper-slide sl02">
            <div class="main-container">
              <div class="vi_txt_po">
                <div class="vi_txt_wrap">
                  <h2>Measurement<br><span class="vi_small">and</span> Control Solutions</h2>
                  <p class="vi_txt">MEK オンライン厚み計は、ウェブ形状の製品の厚さを測定する装置です。<br class="pc">製品の生産中に全幅の厚みをリアルタイムで測定し、制御することで、<br class="pc">品質向上、プロセスの最適化及び原価削減に貢献します。</p>
                  <p class="main_button button_white"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=pro_jp&wr_id=10" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
                </div>
              </div>
            </div>
          </div>
          <div class="swiper-slide sl03">
            <div class="main-container">
              <div class="vi_txt_po">
                <div class="vi_txt_wrap">
                  <h2>Measurement<br><span class="vi_small">and</span> Control Solutions</h2>
                  <p class="vi_txt">MEK オンライン厚み計は、ウェブ形状の製品の厚さを測定する装置です。<br class="pc">製品の生産中に全幅の厚みをリアルタイムで測定し、制御することで、<br class="pc">品質向上、プロセスの最適化及び原価削減に貢献します。</p>
                  <p class="main_button button_white"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=pro_jp&wr_id=13" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
                </div>
              </div>
            </div>
          </div>
          <div class="swiper-slide sl04">
            <div class="main-container">
              <div class="vi_txt_po">
                <div class="vi_txt_wrap">
                  <h2>Measurement<br><span class="vi_small">and</span> Control Solutions</h2>
                  <p class="vi_txt">MEK オンライン厚み計は、ウェブ形状の製品の厚さを測定する装置です。<br class="pc">製品の生産中に全幅の厚みをリアルタイムで測定し、制御することで、<br class="pc">品質向上、プロセスの最適化及び原価削減に貢献します。</p>
                  <p class="main_button button_white"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=pro_jp&wr_id=14" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="arrow-bottom">

          <div class="swiper-pagination2"></div>
        </div>
      </div>
    </section>

    <section id="main_08" class="main-section section fp-auto-height fp-auto-height-responsive main_partner_section">
      <div class="main_partner">
        <div class="main_partner_wrap">
          <div class="main_partner_txt container">
            <h3>Benefits of System</h3>
          </div>
          <ul>
            <li><span>1</span><span>IN-LINE非接触リアルタイム測定＆偏差管理（厚さ、重量、密度）</span></li>
            <li><span>2</span><span>最高のデータプログラム。チョナノ偏差管理。 X、β、IR、Laserなどのカスタムセンサー</span></li>
            <li><span>3</span><span>リアルタイムモニタリングおよび制御システムによる生産プロセスの最適化</span></li>
            <li><span>4</span><span>製品のクオリティ向上により、ロス削減、利益増大、ブランド競争力の向上</span></li>
            <li><span>5</span><span>世界最高レベルの技術力、プロセスオーダーメイド制作、グローバル企業の選択！</span></li>
          </ul>
          <p>Providing the data analyzed in <span>Various ways and providing the statistical data</span><br>including SQC to support the requirements of user and optimized process control.</p>
        </div>
      </div>
    </section>

    <section id="main_02" class="main-section section fp-auto-height fp-auto-height-responsive main_pro_section">
      <div id="scene1" class="scene">
        <div class="layer sec02_bg01" data-depth="0.5"></div>
      </div>
      <div class="main_product main_product01 container">
        <div class="main_product_table">
          <h3>Featured Products</h3>
          <div class="main_product_wrap">
            <div class="main_pro_img">
              <img src="<?php echo G5_IMG_URL?>/pro_img01.png" alt="電池用スキャナ">
            </div>
            <div class="main_pro_txt" >
              <h4>1050H Thickness Gauge</h4>
              <p class="main_pro_info">同軸変位センサを利用し、陽極/陰極コーティング厚さを測定します。<br class="pc">表面および反射光の影響を受けずに、振動とテンション変化の影響を最小限に抑え、<br class="pc">ロールプレスプロセスにおける最高の厚さ管理が可能です。<br class="pc"><span class="info_small">* 非接触式厚さ測定装置 / 特許第10-1825081号</span></p>
              <p class="main_button"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=pro_jp&wr_id=10" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
            </div>
            <p class="main_mark"><img src="<?php echo G5_IMG_URL?>/pro_img00.png"></p>
          </div>
        </div>
      </div>
    </section>
    <section id="main_03" class="main-section section fp-auto-height fp-auto-height-responsive main_pro_section">
      <div id="scene2" class="scene">
        <div class="layer sec02_bg01" data-depth="0.5"></div>
      </div>
      <div class="main_product main_product01 container">
        <div class="main_product_table">
          <!-- <h3>Featured Products</h3> -->
          <div class="main_product_wrap">
            <div class="main_pro_img">
              <img src="<?php echo G5_IMG_URL?>/pro_img02.png" alt="電池用スキャナ">
            </div>
            <div class="main_pro_txt">
              <h4>6800X Thickness Gauge</h4>
              <p class="main_pro_info">MEK オンラインX線及びβ線厚さ測定装置は、フィルム製造または製箔プロセスで製品の厚さを測定する機器です。<br class="pc">より精密な測定により、品質向上、プロセスの最適化、原価削減などに貢献します。</p>
              <p class="main_button"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=pro_jp&wr_id=12" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
            </div>

          </div>
        </div>
      </div>
    </section>
    <section id="main_04" class="main-section section fp-auto-height fp-auto-height-responsive main_partner_section">
      <div class="main_partner">
        <div class="main_partner_wrap">
          <div class="main_partner_txt container">
            <h3>Partner Company</h3>
            <p>豊富な経験と最新の技術を活かし、世界トップクラスの製品を作り、お客様の要求を満たすために常に最善を尽くしています。</p>
          </div>
          <div class="main_partner_content">
            <div class="main_banner01 container main-partners-roller left"><?php echo latest('swiper_pic_block', 'partner_jp', 20, 23); ?></div>
            <div class="main_banner02 container main-partners-roller right"><?php echo latest('swiper_pic_block', 'partner_jp', 20, 23); ?></div>
          </div>
          <div class="container">
            <p class="main_button"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=partner_jp" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
          </div>
        </div>
      </div>
    </section>

    <section id="main_05" class="main-section section fp-auto-height fp-auto-height-responsive main_history_section">
      <div class="main_history">
        <div class="main_history_30">
          <div id="scene3" class="scene">
            <div class="layer" data-depth="0.2"> <img src="<?php echo G5_IMG_URL?>/hi_30_bg03.png" alt="小さな花粉"> </div>
            <div class="layer" data-depth="0.1"> <img src="<?php echo G5_IMG_URL?>/hi_31_bg02.png" alt="30anniversary"> </div>
            <div class="layer" data-depth="0.15"> <img src="<?php echo G5_IMG_URL?>/hi_30_bg01.png" alt="大きな花粉"> </div>
          </div>
        </div>
        <div class="main_history_content container">
          <div class="main_history_wrap">
            <div class="main_history_flex">
              <h3>History of MEK</h3>
              <div class="main_history_info">
                <?php echo latest("history", "history_jp", 99, 50) ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="main_06" class="main-section section fp-auto-height fp-auto-height-responsive main_cert_section">
      <div class="main_cert patent container">
        <div class="main_cert_wrap">
          <div class="main_cert_patent main_cert_content up">
            <div class="main_cert_bond">
              <?php echo latest("lat_pat", "patent_jp", 18, 32) ?>
            </div>
            <div class="main_cert_txt">
              <h3>特許証</h3>
              <p class="main_cert_txt_con">MEKのR&Dセンターは、<br class="pc">絶え間ない研究を通じて常に<br class="pc">業界の技術発展をリードし、<br class="pc">最高の製品を提供しています。</p>
              <p class="main_cert_button"><a href="<?php echo G5_URL?>/sub01_04.php" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
              <div class="arrow">
                <div class="arrow-wrap">
                  <p class="swiper-button-prev3 main_cert_arrow"><i class="ri-arrow-left-s-line"></i></p>
                  <p class="swiper-button-next3 main_cert_arrow">
                  <i class="ri-arrow-right-s-line"></i></p>
                </div>
              </div>
            </div>
          </div>
          <div class="main_cert_certification main_cert_content down">
            <div class="main_cert_bond">
              <?php echo latest("lat_pat", "certif_jp", 18, 32) ?>
            </div>
            <div class="main_cert_txt">
              <h3>認証書</h3>
              <p class="main_cert_txt_con">MEKのR&Dセンターは、<br class="pc">絶え間ない研究を通じて常に<br class="pc">業界の技術発展をリードし、<br class="pc">最高の製品を提供しています。</p>
              <p class="main_cert_button"><a href="<?php echo G5_URL?>/sub01_04.php" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
              <div class="arrow">
                <div class="arrow-wrap">
                  <p class="swiper-button-prev4 main_cert_arrow"><i class="ri-arrow-left-s-line"></i></p>
                  <p class="swiper-button-next4 main_cert_arrow">
                  <i class="ri-arrow-right-s-line"></i></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="main_07" class="main-section section fp-auto-height fp-auto-height-responsive main_contact_section">
      <div class="main_contact container">
        <div class="main_contact_wrap">
          <div class="main_contact_txt">
            <h3>Contact</h3>
            <ul class="main_contact_info">
              <li>
                <h4>住所</h4><address>
                <p class="i_color"><i class="ri-map-pin-2-fill"></i> (21315) 仁川広域市 扶平区 扶平大路 313番道路 30</p></address>
              </li>
              <li>  <h4>電話番号</h4>
                <p class="i_color"><i class="ri-phone-fill"></i> <span> <a href="tel"><?php echo $default['de_admin_company_tel']; ?></a> </span></p></li>
                <li><h4>地図</h4>
                <p class="main_map_link"><a href="https://goo.gl/maps/GZyXpGczz41wA8kp7" target="_blank"><img src="<?php echo G5_IMG_URL?>/icon_google.png" alt="map pin"> <span>Google マップへのリンク</span> <i class="ri-external-link-line"></i></a></p>
                <p class="main_map_link"><a href="https://naver.me/56DkMkfv" target="_blank"><img src="<?php echo G5_IMG_URL?>/icon_naver.png" alt="map pin"> <span>Naver マップへのリンク</span> <i class="ri-external-link-line"></i></a></p>
                <p class="main_map_link"><a href="https://kko.to/AJHNDQK_Wu" target="_blank"><img src="<?php echo G5_IMG_URL?>/icon_kakao.png" alt="map pin"> <span>Kakao マップへのリンク</span> <i class="ri-external-link-line"></i></a></p>
              </li>
            </ul>
            <!-- <div class="button_wrap">
              <p class="main_button"><a href="<?php echo G5_URL?>/sub04_01.php" class="button"><span>Inquire</span><i class="ri-arrow-right-up-line"></i></a></p>
            </div> -->
          </div>
          <div class="main_contact_map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3164.493040237311!2d126.7199459!3d37.51987340000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357b7dbfb4c86b07%3A0x79c388befcd985ac!2s%EF%BC%93%EF%BC%90%20Bupyeong-daero%20313beon-gil%2C%20Bupyeong-gu%2C%20Incheon!5e0!3m2!1sja!2skr!4v1712642809451!5m2!1sja!2skr" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>
      </div>
    </section>

<!-- 메인 끝 -->

<script type="text/javascript">

// 마우스 따라 움직이는 이미지
var scene = document.getElementById('scene1');
var parallaxInstance1 = new Parallax(scene1);
parallaxInstance1.friction(0.1, 0.1);
var scene2 = document.getElementById('scene2');
var parallaxInstance2 = new Parallax(scene2);
parallaxInstance2.friction(0.1, 0.1);
var scene3 = document.getElementById('scene3');
var parallaxInstance3 = new Parallax(scene3);
parallaxInstance3.friction(0.1, 0.1);


</script>

<?php
include_once(G5_PATH.'/tail.php');
?>

</main>
</div>
