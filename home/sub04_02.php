<?php
include_once('./_common.php');
include_once(G5_PATH.'/plus/mail_sender.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

$menuCodeParent = 4;
$menuCodeChild = 0;
include_once(G5_PATH.'/head.php');
?>
<div id="sub_wrap" class="sub04_02">
  <section class="sub-section section01 sub-container" data-aos="fade-up"  data-aos-duration="2000">


    <div class="send_box02">
      <fieldset>
        <form name="contactform" method="post" action="/home/sub04_02_send.php" enctype="multipart/form-data" class="fields">
          <ul>
            <!-- <h4>기본정보</h4> -->
            <li>
              <label for="title" class="lbl required">업체명</label>
              <div class="desc">
                <input name="title" type="text" class="input full" id="title" maxlength="50" required placeholder="상담 참조용 업체명을 입력하세요. (비공개)">
              </div>
            </li>
            <li id="equipment_area">
              <span id="equipment" class="lbl">문의설비</span>
              <div class="desc flex ">
                <input name="group[]" type="checkbox" id="online" class="input full check" value="온라인 두께 측정기">온라인 두께 측정기
                <input name="group[]" type="checkbox" id="offline" class="input full check" value="오프라인 두께 측정기">오프라인 두께 측정기
              </div>
            </li>
            <li>
              <label for="place" class="lbl required">설치 지역(또는 국가)</label>
              <div class="desc">
                <input name="place" type="text" class="input full" id="place" maxlength="80" required placeholder="설치 지역(또는 국가)를 입력하세요">
              </div>
            </li>
            <li>
              <label for="email" class="lbl required">이메일</label>
              <div class="desc">
                <input name="email" type="email" class="input full" id="email" maxlength="80" required placeholder="E-mail을 입력하세요">
              </div>
            </li>
            <li>
              <label for="name" class="lbl required">성명(담당자명)</label>
              <div class="desc">
                <input name="name" type="text" class="input full" id="name" maxlength="50" required placeholder="담당자명을 입력하세요">
              </div>
            </li>
            <li>
              <label for="phone" class="lbl required">전화번호</label>
              <div class="desc">
              <input name="phone" type="tel" class="input full" id="phone" maxlength="30" required placeholder="통화 가능한 전화번호를 입력하세요">
              </div>
            </li>

            <!-- 상세 정보 토글 버튼 -->
            <li style="margin-top: 20px; margin-bottom: 10px;">
              <button type="button" id="toggleDetailBtn" style="background: #e0f0f0; border: 1px solid #ddd; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-size: 14px;">
                + 상세 정보 추가 입력
              </button>
            </li>

            <!-- 상세 정보 영역 (기본 숨김) -->
            <div id="detailInfoArea" style="display: none; width: 100%; padding-bottom: 30px; border-bottom: 1px solid #ddd; margin-bottom: 30px;">
            <li>
              <label for="component" class="lbl">제품 성분</label>
              <div class="desc">
                <input name="component" type="text" class="input full" id="component" maxlength="80" placeholder="ex. PET, PVC">
              </div>
            </li>
            <li>
              <label for="color" class="lbl">색상 및 투명도</label>
              <div class="desc">
                <input name="color" type="text" class="input full" id="color" maxlength="80">
              </div>
            </li>
            <li>
              <label for="process" class="lbl">적용 공정</label>
              <div class="desc">
                <input name="process" type="text" class="input full" id="process" maxlength="80" placeholder="ex. 압출, 코팅">
              </div>
            </li>
              <li>
                <label for="thickness" class="lbl">측정 두께</label>
                <div class="desc">
                  <input name="thickness" type="text" class="input full" id="thickness" maxlength="80" >
                </div>
              </li>
              <li>
                <label for="width" class="lbl">측정 폭</label>
                <div class="desc">
                  <input name="width" type="text" class="input full" id="width" maxlength="80" >
                </div>
              </li>
              <li>
                <label for="lineSpeed" class="lbl">라인 속도</label>
                <div class="desc">
                  <input name="lineSpeed" type="text" class="input full" id="lineSpeed" maxlength="80" >
                </div>
              </li>
              <li id="apc_area">
                <span id="apply_apc" class="lbl">APC 적용 여부</span>
                <div class="desc flex">
                  <input name="apc[]" type="checkbox" id="apcOn" class="input full check" value="APC 적용">APC 적용
                  <input name="apc[]" type="checkbox" id="apcOff" class="input full check" value="APC 미적용">APC 미적용
                </div>
              </li>
              <li id="line_area">
                <span id="line_status" class="lbl">라인 상태</span>
                <div class="desc flex">
                  <input name="lineStatus[]" type="checkbox" id="lineNew" class="input full check" value="신규 라인">신규 라인
                  <input name="lineStatus[]" type="checkbox" id="lineExisting" class="input full check" value="기존 라인">기존 라인
                </div>
              </li>
            </div>

              <!-- <h4>상담 내용</h4> -->
              <li>
                <label for="comments" class="lbl">내용</label>
                <div class="desc">
                <textarea type="text" class="input full" name="comments" id="comments" rows="10" cols="80" placeholder="문의 내용을 입력하세요"></textarea>
                </div>
              </li>
              <li>
                <label for="file" class="lbl">첨부파일</label>
                <div class="desc">
                  <input type="file" name="attachment" style="border: 0; padding:0; ">
                </div>
              </li>
              <li>
                <label for="agree" class="lbl">개인정보 수집·이용</label>
                <div class="desc">
                  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center;">
                      <input type="checkbox" name="agree" id="agree" required class="required" style="width: auto; margin-right: 8px;">
                      <label for="agree" style="margin: 0; display: inline;">'개인정보 수집·이용'에 동의 합니다.</label>
                    </div>
                    <a href="javascript:void(0);" id="showPrivacyModal" style="color: #1a4691; text-decoration: underline; font-size: 13px; margin-left: 10px; cursor: pointer;">개인정보 수집·이용</a>
                  </div>
                </div>
              </li>
            <li>
              <label class="lbl">자동등록방지</label>
              <div class="desc">
                <?php echo captcha_html(); ?>
              </div>
            </li>
            </ul>
            <div class="btn-group">
              <p><span>*</span> 표시가 있는 항목은 반드시 입력해야 합니다. 문의내용을 확인하는 대로 연락드리겠습니다.</p>
              <button type="submit" class="btn-submit">확인</button>
            </div>
        </form>
      </fieldset>
    </div>
  </div>

  </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const toggleDetailBtn = document.getElementById("toggleDetailBtn");
    const detailInfoArea = document.getElementById("detailInfoArea");

    // Toggle detail info area
    toggleDetailBtn.addEventListener("click", function() {
        if (detailInfoArea.style.display === "none") {
            detailInfoArea.style.display = "block";
            toggleDetailBtn.textContent = "- 상세 정보 접기";
        } else {
            detailInfoArea.style.display = "none";
            toggleDetailBtn.textContent = "+ 상세 정보 추가 입력";
        }
    });

    // 개인정보 수집·이용 내용 모달 표시
    const showPrivacyModal = document.getElementById("showPrivacyModal");
    
    if (showPrivacyModal) {
        showPrivacyModal.addEventListener("click", function(e) {
            e.preventDefault();
            const privacyContent = `<?php echo addslashes(nl2br(get_text($config['cf_privacy']))); ?>`;
            const modal = document.createElement('div');
            modal.id = 'privacyModal';
            modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100000; display: flex; align-items: center; justify-content: center;';
            modal.innerHTML = `
                <div style="background: #fff; padding: 30px; border-radius: 10px; max-width: 600px; max-height: 80vh; overflow-y: auto; position: relative; width: 90%;">
                    <button onclick="document.getElementById('privacyModal').remove()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">×</button>
                    <h3 style="margin-top: 0; margin-bottom: 20px; color: #333;">개인정보 수집·이용</h3>
                    <div style="line-height: 1.6; color: #666;">${privacyContent}</div>
                    <button onclick="document.getElementById('privacyModal').remove()" style="margin-top: 20px; padding: 10px 20px; background: #1a4691; color: #fff; border: none; border-radius: 5px; cursor: pointer;">확인</button>
                </div>
            `;
            document.body.appendChild(modal);
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        });
    }
});
</script>

<?php
include_once(G5_PATH.'/tail.php');
?>
