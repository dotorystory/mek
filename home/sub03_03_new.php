<?php
include_once('./_common.php');

$menuCodeParent = 2;
$menuCodeChild = 2;
include_once(G5_PATH.'/head.php');
?>
<style>
/* 피닝 페이지 전용 스타일 */
.sub03_03_pinning .pinning_images {
    display: flex;
    gap: 20px;
    align-items: stretch;
    justify-content: center;
    margin: 20px 0;
}
.sub03_03_pinning .pinning_images li {
    flex: 1;
    /* max-width: 70%; */
    display: flex;
    flex-direction: column;
}
.sub03_03_pinning .pinning_images li > img {
    width: 100%;
    height: 400px;
    /* min-height: 380px;
    max-height: 480px; */
    object-fit: contain;
    object-position: center;
    background: #ffffff;
    border-radius: 10px;
    padding: 3px;
    box-sizing: border-box;
    /* border: 1px solid #e5e5e5; */
}
.sub03_03_pinning .pinning_images p {
    margin-top: 10px;
    margin-bottom: 5px;
    text-align: center;
    font-size: 18px;
    font-weight: 600;
    line-height: 1.6;
}
.sub03_03_pinning .pinning_images p span {
    display: block;
    margin-top: 5px;
    font-size: 14px;
    font-weight: 400;
    color: #666;
}

/* 전기적 방식 강조 카드 섹션 */
.sub03_03_pinning .highlight_card_section {
    margin: 30px 0 40px 0;
    padding: 40px 30px;
    background: linear-gradient(135deg, rgba(26, 70, 145, 0.05) 0%, rgba(72, 200, 241, 0.05) 100%);
    border-radius: 20px;
    border: 2px solid rgba(26, 70, 145, 0.1);
}
.sub03_03_pinning .highlight_card_section .highlight_card {
    display: flex;
    flex-direction: column;
    gap: 25px;
    max-width: 1200px;
    margin: 0 auto;
}
.sub03_03_pinning .highlight_card_section .highlight_card_icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #1a4691 0%, #48c8f1 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 28px;
    margin: 0 auto;
}
.sub03_03_pinning .highlight_card_section .highlight_card_title {
    text-align: center;
    font-size: 32px;
    font-weight: 700;
    color: #1a4691;
    margin-bottom: 10px;
}
.sub03_03_pinning .highlight_card_section .highlight_card_subtitle {
    text-align: center;
    font-size: 18px;
    color: #666;
    margin-bottom: 30px;
}
.sub03_03_pinning .highlight_card_section .highlight_card_content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-top: 20px;
}
.sub03_03_pinning .highlight_card_section .highlight_card_item {
    background: #fff;
    padding: 30px 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    border-left: 4px solid #1a4691;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.sub03_03_pinning .highlight_card_section .highlight_card_item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}
.sub03_03_pinning .highlight_card_section .highlight_card_item .item_title {
    font-size: 20px;
    font-weight: 600;
    color: #1a4691;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sub03_03_pinning .highlight_card_section .highlight_card_item .item_title::before {
    content: "✓";
    width: 24px;
    height: 24px;
    background: #1a4691;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.sub03_03_pinning .highlight_card_section .highlight_card_item .item_text {
    font-size: 16px;
    line-height: 1.8;
    color: #333;
    word-break: keep-all;
}
.sub03_03_pinning .highlight_card_section .highlight_card_item .item_text_en {
    font-size: 14px;
    line-height: 1.6;
    color: #666;
    margin-top: 10px;
    font-style: italic;
}
.sub03_03_pinning .highlight_card_section .highlight_card_summary {
    text-align: center;
    margin-top: 35px;
    padding: 25px;
    background: rgba(26, 70, 145, 0.08);
    border-radius: 15px;
}
.sub03_03_pinning .highlight_card_section .highlight_card_summary .summary_text {
    font-size: 22px;
    font-weight: 700;
    color: #1a4691;
    line-height: 1.6;
    word-break: keep-all;
}
.sub03_03_pinning .highlight_card_section .highlight_card_summary .summary_text_en {
    font-size: 16px;
    color: #666;
    margin-top: 10px;
    font-style: italic;
}

@media (max-width: 768px) {
    .sub03_03_pinning .pinning_images {
        flex-direction: column;
        gap: 20px;
    }
    .sub03_03_pinning .pinning_images li {
        max-width: 100%;
    }
    .sub03_03_pinning .pinning_images li > img {
        min-height: 250px;
        max-height: 350px;
        padding: 3px;
    }
    .sub03_03_pinning .highlight_card_section {
        padding: 30px 20px;
    }
    .sub03_03_pinning .highlight_card_section .highlight_card_title {
        font-size: 24px;
    }
    .sub03_03_pinning .highlight_card_section .highlight_card_content {
        grid-template-columns: 1fr;
    }
    .sub03_03_pinning .highlight_card_section .highlight_card_summary .summary_text {
        font-size: 18px;
    }
}
</style>
<div id="sub_wrap" class="sub03_03 sub03_03_pinning">
  <section class="sub-section section01 sub-container">
    <div class="sub_content_tit" data-aos="fade-up"  data-aos-duration="2000">
      <span data-split="피닝 시스템"></span>
      <h5>피닝 시스템<br>
        Pinning System</h5>
    </div>
    <div class="sub_content">
      <!-- 전기적 방식 강조 카드 섹션 -->
      <section class="highlight_card_section" data-aos="fade-up" data-aos-duration="2000">
        <div class="highlight_card">
          <div class="highlight_card_icon">⚡</div>
          <h3 class="highlight_card_title">MEK 피닝시스템의 우수성</h3>
          <p class="highlight_card_subtitle">Superiority of MEK Pinning System</p>
          
          <div class="highlight_card_content">
            <div class="highlight_card_item">
              <div class="item_title">비접촉 밀착</div>
              <div class="item_text">전기적 방식으로 비접촉 밀착을 제공하여, 저비용 고순도 필름지 생산 라인에 적합합니다.</div>
              <div class="item_text_en">Non-contact adhesion through electrical method, suitable for low-cost, high-purity film production line.</div>
            </div>
            
            <div class="highlight_card_item">
              <div class="item_title">위험 요소 제거</div>
              <div class="item_text">물리적 압착이나 바람에 의한 방식이 아니므로 표면 마모, 온도 변화 등의 위험이 없습니다.</div>
              <div class="item_text_en">No physical compression or air-based bubble removal, eliminating risks of surface wear and temperature variations.</div>
            </div>
            
            <div class="highlight_card_item">
              <div class="item_title">균일하고 우수한 성능</div>
              <div class="item_text">밀착 성능이 균일하고 우수하여 품질과 효율을 모두 확보할 수 있는 방식입니다.</div>
              <div class="item_text_en">Uniform and superior adhesion performance ensures both quality and efficiency.</div>
            </div>
          </div>
          
          <div class="highlight_card_summary">
            <div class="summary_text">
              PET 필름 등 다양한 Web 제품 생산 라인에서 전기적 방식으로 비접촉 밀착 방식 제공. 물리적 압착 또는 바람에 의한 기포 제거 방식이 아니므로 
              표면 마모, 온도 변화 등의 위험이 없고, 밀착 성능이 균일하고 우수하여, 품질과 효율 모두 얻을 수 있는 방식입니다.
            </div>
            <div class="summary_text_en">
              Electrical method provides non-contact adhesion through electrical method, suitable for low-cost, high-purity film production line.<br>
              No physical compression or forced air-based bubble removal, eliminating risks of surface wear and temperature variations.<br>
              Uniform and superior adhesion performance ensures both quality and efficiency.
            </div>
          </div>
        </div>
      </section>
      
      <section class="section03 list_flex" data-aos="fade-up"  data-aos-duration="2000">
        <h6>시스템 개요<p>Systems</p></h6>
        <div class="info_list_box">
              <ul class="list_txt">
                <li>
                  <p>플라스틱 Film / sheet 압출 공정에 사용되는 장치로, 용융 수지의 냉각 Roll 에 대한 밀착을 비접촉, 전기적 방식으로 제공하므로 Nip-roll을 이용한 Calendar 방식 혹은 Air knife를 이용한 방식에 비하여 밀착 성능이 균일하고 우수.</p>
                  <span>Non-contact, electrical method of applying molten polymer on to cooling roll used in plastic film/sheet extrusion process, provides uniform and superior performance compared with air knife or calendar method using nip-roll.</span>
                </li>
            </ul>
        </div>
      </section>
      <section class="section02 list_flex" data-aos="fade-up"  data-aos-duration="2000" style="margin: 20px 0;">
        <h6>작동원리<p>How it works</p></h6>
        <div class="info_list_box">
          <ul class="list_txt">
                <li>
                  <p>고전압이 인가된 전극을 이용하여 용융 수지를 대전 시키면 정전기력에 의거한 자가 밀착성이 수지에 부여되고, 따라서 냉각 Roll 에 대한 균일하고 강한 밀착력을 나타냄.</p>
                  <span>Charging molten polymer using high-voltage electrode creates self-adhering property in the resin by static electric force and it therefore exhibits uniform and strong adhering force on to cooling roll.</span>
                </li>
            </ul>
              <ul class="list_img basis_2 pinning_images">
                <li>
                  <img src="/public/img/pinning.png" alt="Pinning System">
                  <p>비접촉 필름 밀착<br><span>Non-contact film adhesion</span></p>
                </li>
                <li>
                  <img src="/public/img/pin-level.gif" alt="Pin Level">
                  <p>방전에 의한 분자 이온화<br><span>Air Molecule lonization By discharge</span></p>
                </li>
            </ul>
        </div>
      </section>
      
      <section class="section03 list_flex" data-aos="fade-up"  data-aos-duration="2000">
        <h6>적용분야<p>Applications</p></h6>
        <div class="info_list_box">
              <ul class="list_txt">
                <li>
                  <p>PET 를 포함한 고분자 수지의 Film/sheet 압출 공정.</p>
                  <span>Film/sheet extrusion process of various resins including PET.</span>
                </li>
            </ul>
        </div>
      </section>
      <section class="section04 list_flex" data-aos="fade-up"  data-aos-duration="2000">
        <h6>적용 효과<p>Advantages of Pinning System</p></h6>
        <div class="info_list_box">
              <ul class="list_txt">
                <li>
                  <p>에어나이프나 배큐움 박스와 달리 고압 전원의 인가로 발생하는 정전기력을 이용하므로 성능이 균일하며 밀착력이 우수.</p>
                  <span>High voltage is applied on the wire or band electrode.</span>
                </li>
                <li>
                  <p>표면성 및 투명도가 대폭 향상되며, 폭 방향 두께 프로파일 개선 및 변부 수출 개선으로 제품 폭 증가 및 TDO 파단 감소 등의 이점이 있음.</p>
                  <span>Electrical pinning show uniform and superior performance comparing with air knife and vacuum box. Enables clear and enhanced surface, better CD thickness profile, enlarged product width from less neck-in and less breakage of TDO.</span>
                </li>
            </ul>
        </div>
      </section>
      <section class="section05 list_flex" data-aos="fade-up"  data-aos-duration="2000">
        <h6>SYSTEM 종류<p>Types of system</p></h6>
        <div class="info_list_box">
              <ul class="list_txt">
                <li>
                  <p>PWire, Doble Wire, 4mm Band, 8mm Ω Type</p>
                </li>
            </ul>
        </div>
      </section>
      <section class="section06 list_flex" data-aos="fade-up"  data-aos-duration="2000">
        <h6>피닝 전극<p>Pinning Electrode</p></h6>
        <div class="info_list_box">
          <div class="list_table_wrap">
            <table class="list_table list_table_01">
              <thead>
                <tr>
                  <th colspan="3">Band</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="3">Size : 8mm(W) x 40㎛(T) x 300(L)</td>
                </tr>
                <tr>
                  <td colspan="3">Operation Tension :8~25kg<br>
                    Maximum Tension : 75kg</td>
                </tr>
                <tr>
                  <td colspan="3">Precise Grinding On Edge Other Size</td>
                </tr>
                <tr>
                  <td class="border_right">T</td>
                  <td class="border_right">L</td>
                  <td>W</td>
                </tr>
                <tr>
                  <td class="border_right">40㎛</td>
                  <td class="border_right">100 / 300m</td>
                  <td class="border_right">3 / 4 / 8 / 12mm</td>
                </tr>
                <tr>
                  <td class="border_right">50㎛</td>
                  <td class="border_right">90/25m</td>
                  <td>3 / 4 / 8mm</td>
                </tr>
              </tbody>
            </table>
            <table class="list_table list_table_02">
              <thead>
                <tr>
                  <th colspan="3">Wire</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="3">Primary Electrode : <br>
                    Ø 0.1 / Ø 0.15 / Ø 0.2</td>
                </tr>
                <tr>
                  <td colspan="3">
                    Auxiliary Electrode : Ø 0.3<br>
                    Tension :0~4kg<br>
                    Type of Pinning System
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
      <section class="section07 list_flex" data-aos="fade-up"  data-aos-duration="2000">
        <h6>시스템 구성<p>System Components</p></h6>
        <div class="info_list_box">
              <ul class="list_img basis_2">
                <li>
                  <img src="<?php echo G5_IMG_URL?>/sub03_03_img03.png" alt="Pinning System">
                  <p>피닝시스템 (와이어 / 밴드)<br><span>Pinning System (Wire / Band)</span></p>
                </li>
                <li>
                  <img src="<?php echo G5_IMG_URL?>/sub03_03_img04.png" alt="Operating Panel">
                  <p>오퍼레이팅 판넬<br><span>Operating Panel</span></p>
                </li>
            </ul>
        </div>
      </section>
    </div>
  </section>
</div>
<?php
include_once(G5_PATH.'/tail.php');
?>

