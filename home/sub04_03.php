<?php
include_once('./_common.php');
include_once(G5_PATH.'/plus/mail_sender.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

$menuCodeParent = 4;
$menuCodeChild = 2;
include_once(G5_PATH.'/head.php');
?>
<div id="sub_wrap" class="sub04_03">
  <section class="sub-section section01 sub-container" data-aos="fade-up"  data-aos-duration="2000">
    <div class="send_box02">
      <fieldset>
        <form name="contactform" method="post" action="/home/sub04_03_send.php" enctype="multipart/form-data" class="fields">
          <ul>
            <!-- <h4>기본정보</h4> -->
            <li>
              <label for="title" class="lbl required">업체명</label>
              <div class="desc">
                <input name="title" type="text" class="input full" id="title" maxlength="50" required placeholder="상담 참조용 업체명을 입력하세요. (비공개)">
              </div>
            </li>
            <li id="equipment_area">
              <span id="equipment" class="lbl required">문의설비</span>
              <div class="desc flex">
                <input name="group[]" type="checkbox" id="webcleaner" class="input check full" value="온라인 두께 측정기">온라인 두께 측정기
                <input name="group[]" type="checkbox" id="surface" class="input check full" value="표면결함검사기">표면결함검사기
                <input name="group[]" type="checkbox" id="etc_chk" class="input check full"  value="기타(직접입력)">기타 (직접입력)
                <input name="group[]" type="text" class="input full" id="etc_txt" maxlength="80">
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
            <label for="phone" class="lbl">전화번호(유선)</label>
            <div class="desc">
            <input name="phone" type="tel" class="input full" id="phone" maxlength="30" placeholder="전화번호를 입력하세요">
            </div>
          </li>
          <li>
            <label for="cell" class="lbl required">핸드폰</label>
            <div class="desc">
              <input name="cell" type="tel" class="input full" id="cell" maxlength="80" required placeholder="핸드폰번호를 입력하세요">
            </div>
          </li>
            <li>
              <label for="date" class="lbl required">구매시기</label>
              <div class="desc">
                <input name="date" type="text" class="input full" id="date" maxlength="80" required placeholder="구매시기를 입력하세요">
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

            <!-- <h4>상담 내용</h4> -->
            <li>
              <label for="comments" class="lbl">A/S 문의 내용</label>
              <div class="desc">
              <textarea type="text" class="input full" name="comments" id="comments" rows="10" cols="80" placeholder="적용 라인 현장 사진 및 이물 유형 별도 첨부 요망"></textarea>
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
    const contactForm = document.forms["contactform"];
    const equipmentArea = document.getElementById("equipment_area");
    const etcChk = document.getElementById("etc_chk");
    const etcTxt = document.getElementById("etc_txt");
    const equipmentMessageSpan = document.createElement("span");
    equipmentMessageSpan.id = "equipmentMessage";
    equipmentMessageSpan.style.color = "red";
    equipment.appendChild(equipmentMessageSpan);

    // Initially disable the text field
    etcTxt.disabled = true;

    // Event listener for checkbox
    etcChk.addEventListener("change", function() {
        if (this.checked) {
            etcTxt.disabled = false;  // Enable the text field
        } else {
            etcTxt.disabled = true;   // Disable the text field
            etcTxt.value = "";        // Clear the text field value
        }
    });

    // Check if any checkbox inside equipment_area is checked
    function isAnyCheckboxChecked() {
        const checkboxes = equipmentArea.querySelectorAll("input[type='checkbox']");
        for (let checkbox of checkboxes) {
            if (checkbox.checked) {
                return true;
            }
        }
        return false;
    }

    contactForm.addEventListener("submit", function(e) {
        if (!isAnyCheckboxChecked()) {
            e.preventDefault();  // Stop form from submitting
            equipmentArea.scrollIntoView();  // Scroll to equipment_area
            equipmentMessageSpan.innerHTML = "<br>*문의설비를 체크하세요.";  // Show the error message
        } else {
            equipmentMessageSpan.innerHTML = "";  // Remove the error message if any checkbox is checked
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
