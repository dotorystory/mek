<?php
include_once('./_common.php');

$menuCodeParent = 0;
$menuCodeChild = 0;
include_once(G5_PATH.'/head.php');
?>

<div id="sub_wrap" class="sub01_01">
  <section class="sub-section section01 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub-content-img">
      <img src="<?php echo G5_IMG_URL; ?>/sub01_img.png" alt="MEK 회사 전경">
    </div>
    <div class="sub-content-txt">
      <h4>인간의 삶을 풍요롭게 하는 기술,<br class="pc">
        <span>MEK(맥엔지니어링)가 시작합니다.</span></h4>
      <div class="sub-content-intro">
        지난 삼십여 년간 부단한 노력을 통해 저희 기업은 고객님의 제품 품질과 함께 성장하였습니다.
        <br><br>
        산업 내 품질 경쟁이 점점 더 격화 되고 있는 상황에서도 고객님을 먼저 생각하는 기술을 바탕으로 세계 최고의 제품을 만들고 고객님의 요구를 충족시키기 위해 언제나 최선을 다하고 있습니다.
        <br><br>
        풍부한 경험과 최신 기술로 WEB과 관련하여 세계 최고 수준의 WEB Application 장비와 핵심 설비들을 공급하고 있습니다.
        <br><br>
        IT와 포장용 외에도 Display, 전지 등의 전자 부분과 Copper Foil, Al Foil, Glass, Steel Sheet 등 각종 첨단 소재 분야에서도 최고, 최적의 해결책으로 글로벌 WEB 온라인 품질 관리 시스템을 공급하겠습니다.
        <br><br>
        최고를 지향하는 저희 주식회사 엠이케이 / 맥엔지니어링 코퍼래이션이 항상 연구하여 더 좋은 제품을 보다 빠르게, 보다 경제적으로 공급하기 위해 최선을 다하겠습니다.
        <br><br>
        감사합니다.
        <br><br>
        <div class="ceo">
          <p class="txt_right"><span class="sub_corp"><?php echo $default['de_admin_company_name'];?></span>
          <br>
          <span class="sub_ceo">대표 <?php echo $default['de_admin_company_owner'];?></span></p>
          <p class="ceo_sign"><img src="<?php echo G5_IMG_URL; ?>/ceo_sign.png" alt="ceo sign"></p>
        </div>

      </div>
    </div>
  </section>
  <section class="sub-section section05 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <!-- <div class="sub_content_tit">
      <span data-split="Branch Section"></span>
      <h5>Branch Section</h5>
    </div> -->
    <!-- 기존 영상 주소: jyGHxG3Xgow -->
    <iframe width="100%" height="695" src="https://www.youtube.com/embed/kPqperLqlao" title="[엠이케이/MEK Inc.] 기업홍보영상" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
  </section>
  <section class="sub-section section02 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="기업개요"></span>
      <h5>기업개요</h5>
    </div>
    <ul>
      <li><p><span class="overview_tit">기업명</span><span class="overview_txt"><?php echo $default['de_admin_company_name'];?></span></p>
        <p><i class="ri-building-line"></i></p></li>
      <li><p><span class="overview_tit">대표</span><span class="overview_txt"><?php echo $default['de_admin_company_owner'];?></span></p><p><i class="ri-user-2-line"></i></p></li>
      <li><p><span class="overview_tit">소재지</span><span class="overview_txt"><?php echo $default['de_admin_company_addr']; ?></span></p><p><i class="ri-map-pin-line"></i></p></li>
      <li><p><span class="overview_tit">설립일</span><span class="overview_txt">1993년 8월</span></p><p><i class="ri-calendar-2-line"></i></p></li>
      <li><p><span class="overview_tit">주요 사업</span><span class="overview_txt">WEB 품질관리 시스템</span></p><p><i class="ri-briefcase-5-line"></i></p></li>
      <li><a href="tel:<?php echo $default['de_admin_company_tel']; ?>"><p><span class="overview_tit">전화</span><span class="overview_txt"><?php echo $default['de_admin_company_tel']; ?></span></p><p><i class="ri-phone-line"></i></p></a></li>
      <li><p><span class="overview_tit">팩스</span><span class="overview_txt"> <?php echo $default['de_admin_company_fax']; ?></span></p><p><i class="ri-file-paper-line"></i></p></li>
      <li><a href="mailto:msk@mekeng.com" ><p><span class="overview_tit">이메일</span><span class="overview_txt"><?php echo $default['de_admin_info_email']; ?></span></p><p><i class="ri-mail-line"></i></p></a></li>
    </ul>
  </section>
  <section class="sub-section section04 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit">
      <span data-split="Branch Section"></span>
      <h5>Branch Section</h5>
    </div>
    <img src="<?php echo G5_IMG_URL?>/branch_section.png">
  </section>
  <section class="sub-section section03" data-aos="fade-up"  data-aos-duration="2000">
    <div class="sub_content_tit sub-container">
      <span data-split="오시는 길"></span>
      <h5>오시는 길</h5>
    </div>
    <div class="sub_map">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d332.6260334246901!2d126.7193882023073!3d37.51994591886374!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357b7dbfba74c2a5%3A0x4fc4a5da984f4e54!2zKOyjvCnsl6DsnbTsvIDsnbQ!5e0!3m2!1sko!2skr!4v1689832371037!5m2!1sko!2skr" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
    <div class="sub_directions sub-container">
      <ul>
        <li><h6>지하철</h6>
          <ul>
            <li>
              <p>인천지하철 <span class="curcle">1</span></p>
              <p>갈산역 4번출구</p>
            </li>
          </ul>
          <span><i class="ri-train-line"></i></span>
        </li>
        <li><h6>버스</h6>
          <ul>
          <li>
            <p><span class="curcle">일반</span></p><p>88, 90</p>
          </li>
          <li>
            <p><span class="curcle">간선</span></p><p>1, 12, 30, 34, 67-1</p>
          </li>
        <li>
          <p><span class="curcle">지선</span></p><p>526, 555, 582 583, 584-1</p>
        </li>
        <li>
          <p><span class="curcle">광역</span></p><p>1400, 5000, 9500</p>
        </li>
        <li>
          <p><span class="curcle">시외</span></p><p>3000, 3030</p>
        </li>
        </ul>
        <span><i class="ri-bus-line"></i></span>
      </li>
      <li>
        <h6>지도</h6>
        <ul>
          <li><a href="https://goo.gl/maps/GZyXpGczz41wA8kp7" target="_blank"><img src="<?php echo G5_IMG_URL?>/icon_google.png" alt="map pin"> <p>구글 지도 바로가기</p><i class="ri-external-link-line"></i></a></li>
          <li><a href="https://naver.me/56DkMkfv" target="_blank"><img src="<?php echo G5_IMG_URL?>/icon_naver.png" alt="map pin"> <p>네이버 지도 바로가기</p><i class="ri-external-link-line"></i></a></li>
        <li><a href="https://kko.to/AJHNDQK_Wu" target="_blank"><img src="<?php echo G5_IMG_URL?>/icon_kakao.png" alt="map pin"> <p>카카오 지도 바로가기</p><i class="ri-external-link-line"></i></a></li>
      </ul>
      <span><i class="ri-map-2-line"></i></span>
    </li>
      </ul>
    </div>
  </section>
</div>


<?php
include_once(G5_PATH.'/tail.php');
?>
