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
    <!-- 상단배너 -->
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
                  <p class="vi_txt">MEK 온라인 두께측정기는 웹(WEB) 형상의 제품의 두께를 측정하는 장비입니다.<br class="pc">제품 생산 중에 전폭의 두께를 실시간 측정하여 제어함으로써 품질향상과<br class="pc">공정최적화 및 원가절감에 도움을 줍니다.</p>
                  <p class="main_button button_white"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=pro_ko&wr_id=20" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
                </div>
              </div>
            </div>
          </div>
          <div class="swiper-slide sl02">
            <div class="main-container">
              <div class="vi_txt_po">
                <div class="vi_txt_wrap">
                  <h2>Measurement<br><span class="vi_small">and</span> Control Solutions</h2>
                  <p class="vi_txt">MEK 온라인 두께측정기는 웹(WEB) 형상의 제품의 두께를 측정하는 장비입니다.<br class="pc">제품 생산 중에 전폭의 두께를 실시간 측정하여 제어함으로써 품질향상과<br class="pc">공정최적화 및 원가절감에 도움을 줍니다.</p>
                  <p class="main_button button_white"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=pro_ko&wr_id=10" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
                </div>
              </div>
            </div>
          </div>
          <div class="swiper-slide sl03">
            <div class="main-container">
              <div class="vi_txt_po">
                <div class="vi_txt_wrap">
                  <h2>Measurement<br><span class="vi_small">and</span> Control Solutions</h2>
                  <p class="vi_txt">MEK 온라인 두께측정기는 웹(WEB) 형상의 제품의 두께를 측정하는 장비입니다.<br class="pc">제품 생산 중에 전폭의 두께를 실시간 측정하여 제어함으로써 품질향상과<br class="pc">공정최적화 및 원가절감에 도움을 줍니다.</p>
                  <p class="main_button button_white"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=pro_ko&wr_id=13" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
                </div>
              </div>
            </div>
          </div>
          <div class="swiper-slide sl04">
            <div class="main-container">
              <div class="vi_txt_po">
                <div class="vi_txt_wrap">
                  <h2>Measurement<br><span class="vi_small">and</span> Control Solutions</h2>
                  <p class="vi_txt">MEK 온라인 두께측정기는 웹(WEB) 형상의 제품의 두께를 측정하는 장비입니다.<br class="pc">제품 생산 중에 전폭의 두께를 실시간 측정하여 제어함으로써 품질향상과<br class="pc">공정최적화 및 원가절감에 도움을 줍니다.</p>
                  <p class="main_button button_white"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=pro_ko&wr_id=14" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
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
    <!-- 상단배너 끝 -->

    <!-- 시스템 이점 -->
    <section id="main_08" class="main-section section fp-auto-height fp-auto-height-responsive main_partner_section">
      <div class="main_partner">
        <div class="main_partner_wrap">
          <div class="main_partner_txt container">
            <h3>Benefits of System</h3>
          </div>
          <ul>
            <li><span>1</span><span>품질 향상 - IN-LINE 비접촉 실시간 측정 & 편차 관리 (두께, 중량, 밀도)</span></li>
            <li><span>2</span><span>제품 관리 - 최상의 데이터 분석 프로그램. 초나노 단위 편차 관리. X, β, IR, Laser 등 맞춤 센서</span></li>
            <li><span>3</span><span>공정 관리 - 실시간 모니터링 및 컨트롤 시스템으로 생산 공정 최적화</span></li>
            <li><span>4</span><span>원자재 감소 - 생산품의 퀄리티 향상으로 Loss 감소, 이윤 증대 및 브랜드 경쟁력 제고</span></li>
            <li><span>5</span><span>안정성 향상 - 세계 최고 수준의 기술력, 공정 맞춤형 제작/개발 및 A/S, 글로벌 기업의 선택!</span></li>
          </ul>
          <p>Providing the data analyzed in <span>Various ways and providing the statistical data</span><br>including SQC to support the requirements of user and optimized process control.</p>
        </div>
      </div>
    </section>
    <!-- 시스템 이점 끝 -->

    <!-- 제품 소개 -->
    <section id="main_02" class="main-section section fp-auto-height fp-auto-height-responsive main_pro_section">
      <div id="scene1" class="scene">
        <div class="layer sec02_bg01" data-depth="0.5"></div>
      </div>
      <div class="main_product main_product01 container">
        <div class="main_product_table">
          <h3>Featured Products</h3>
          <div class="main_product_wrap">
            <div class="main_pro_img">
              <img src="<?php echo G5_IMG_URL?>/pro_img01.png" alt="전지용 스캐너">
            </div>
            <div class="main_pro_txt" >
              <h4>1050H Thickness Gauge</h4>
              <p class="main_pro_info">동축변위센서를 이용, 양극/음극 코팅 두께를 측정합니다.<br class="pc">표면 및 반사광의 영향을 받지 않고 진동과 Tension 변화의 영향을<br class="pc">극소화시켜 Roll Press 공정 현존 최고의 두께관리가 가능합니다.<br class="pc"><span class="info_small">* 비접촉식 두께 측정장치 / 특허 제 10-1825081 호</span></p>
              <p class="main_button"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=pro_ko&wr_id=10" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
            </div>
            <p class="main_mark"><img src="<?php echo G5_IMG_URL?>/pro_img00.png"></p>
          </div>
        </div>
      </div>
    </section>
    <!-- 제품 소개 끝 -->

    <!-- 제품 소개 -->
    <section id="main_03" class="main-section section fp-auto-height fp-auto-height-responsive main_pro_section">
      <div id="scene2" class="scene">
        <div class="layer sec02_bg01" data-depth="0.5"></div>
      </div>
      <div class="main_product main_product01 container">
        <div class="main_product_table">
          <!-- <h3>Featured Products</h3> -->
          <div class="main_product_wrap">
            <div class="main_pro_img">
              <img src="<?php echo G5_IMG_URL?>/pro_img02.png" alt="전지용 스캐너">
            </div>
            <div class="main_pro_txt">
              <h4>6800X Thickness Gauge</h4>
              <p class="main_pro_info">MEK 온라인 X-Ray & β-RAY 두께 측정기는 Film 또는 Foil 제작<br class="pc">공정에서 제품의 두께를 측정하는 장비입니다. 보다 정교한 측정으로<br class="pc">품질향상, 공정최적화, 원가절감 등에 도움을 줍니다.</p>
              <p class="main_button"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=pro_ko&wr_id=12" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
            </div>

          </div>
        </div>
      </div>
    </section>
    <!-- 제품 소개 끝 -->

    <!-- 파트너 소개 -->
    <section id="main_04" class="main-section section fp-auto-height fp-auto-height-responsive main_partner_section">
      <div class="main_partner">
        <div class="main_partner_wrap">
          <div class="main_partner_txt container">
            <h3>Partner Company</h3>
            <p>풍부한 경험과 최신 기술로 세계 최고의 제품을 만들고 고객님의 요구를 충족시키기 위해 언제나 최선을 다하고 있습니다.</p>
          </div>
          <div class="main_partner_content">
            <div class="main_banner01 container main-partners-roller left"><?php echo latest('swiper_pic_block', 'partner_ko', 20, 23); ?></div>
            <div class="main_banner02 container main-partners-roller right"><?php echo latest('swiper_pic_block', 'partner_ko', 20, 23); ?></div>
          </div>
          <div class="container">
            <p class="main_button"><a href="<?php echo G5_BBS_URL?>/board.php?bo_table=partner_ko" class="button"><span>View More</span><i class="ri-arrow-right-up-line"></i></a></p>
          </div>
        </div>
      </div>
    </section>
    <!-- 파트너 소개 끝 -->   

    <!-- 역사 -->
    <section id="main_05" class="main-section section fp-auto-height fp-auto-height-responsive main_history_section">
      <div class="main_history">
        <div class="main_history_30">
          <div id="scene3" class="scene">
            <div class="layer" data-depth="0.2"> <img src="<?php echo G5_IMG_URL?>/hi_30_bg03.png" alt="작은꽃가루"> </div>
            <div class="layer" data-depth="0.1"> <img src="<?php echo G5_IMG_URL?>/hi_31_bg02.png" alt="30anniversary"> </div>
            <div class="layer" data-depth="0.15"> <img src="<?php echo G5_IMG_URL?>/hi_30_bg01.png" alt="큰꽃가루"> </div>
          </div>
        </div>
        <div class="main_history_content container">
          <div class="main_history_wrap">
            <div class="main_history_flex">
              <h3>History of MEK</h3>
              <div class="main_history_info">
                <?php echo latest("history", "history_ko", 99, 50) ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- 역사 끝 -->

    <!-- 특허증 -->
    <section id="main_06" class="main-section section fp-auto-height fp-auto-height-responsive main_cert_section">
      <div class="main_cert patent container">
        <div class="main_cert_wrap">
          <div class="main_cert_patent main_cert_content up">
            <div class="main_cert_bond">
              <?php echo latest("lat_pat", "patent_ko", 18, 32) ?>
            </div>
            <div class="main_cert_txt">
              <h3>특허증</h3>
              <p class="main_cert_txt_con">MEK의 R&D Center는 끊임 없는<br class="pc">연구를 통해 언제나 업계의 기술발전을<br class="pc">이끌고 최상의 제품을 공급합니다.</p>
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
              <?php echo latest("lat_pat", "certif_ko", 18, 32) ?>
            </div>
            <div class="main_cert_txt">
              <h3>인증서</h3>
              <p class="main_cert_txt_con">MEK의 R&D Center는 끊임 없는<br class="pc">연구를 통해 언제나 업계의 기술발전을<br class="pc">이끌고 최상의 제품을 공급합니다.</p>
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
    <!-- 특허증 끝 -->

    <!-- 연락처 -->
    <section id="main_07" class="main-section section fp-auto-height fp-auto-height-responsive main_contact_section">
      <div class="main_contact container">
        <div class="main_contact_wrap">
          <div class="main_contact_txt">
            <h3>Contact</h3>
            <ul class="main_contact_info">
              <li>
                <h4>Address</h4><address>
              <p class="i_color"><i class="ri-map-pin-2-fill"></i> <?php echo $default['de_admin_company_addr']; ?></p></address></li>
              <li>  <h4>Tel</h4>
                <p class="i_color"><i class="ri-phone-fill"></i> <span> <a href="tel"><?php echo $default['de_admin_company_tel']; ?></a> </span></p></li>
                <li><h4>Map</h4>
                <p class="main_map_link"><a href="https://goo.gl/maps/GZyXpGczz41wA8kp7" target="_blank"><img src="<?php echo G5_IMG_URL?>/icon_google.png" alt="map pin"> <span>구글지도 바로가기</span> <i class="ri-external-link-line"></i></a></p>
                <p class="main_map_link"><a href="https://naver.me/56DkMkfv" target="_blank"><img src="<?php echo G5_IMG_URL?>/icon_naver.png" alt="map pin"> <span>네이버지도 바로가기</span> <i class="ri-external-link-line"></i></a></p>
                <p class="main_map_link"><a href="https://kko.to/AJHNDQK_Wu" target="_blank"><img src="<?php echo G5_IMG_URL?>/icon_kakao.png" alt="map pin"> <span>카카오지도 바로가기</span> <i class="ri-external-link-line"></i></a></p>
              </li>
            </ul>
            <!-- <div class="button_wrap">
              <p class="main_button"><a href="<?php echo G5_URL?>/sub04_01.php" class="button"><span>Inquire</span><i class="ri-arrow-right-up-line"></i></a></p>
            </div> -->
          </div>
          <div class="main_contact_map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d332.6260334246901!2d126.7193882023073!3d37.51994591886374!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357b7dbfba74c2a5%3A0x4fc4a5da984f4e54!2zKOyjvCnsl6DsnbTsvIDsnbQ!5e0!3m2!1sko!2skr!4v1689832371037!5m2!1sko!2skr" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>
      </div>
    </section>
    <!-- 연락처 끝 -->

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
<!-- 메인 끝 -->
