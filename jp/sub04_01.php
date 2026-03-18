<?php
include_once('./_common.php');
include_once(G5_PATH.'/plus/mail_sender.php');

$menuCodeParent = 4;
$menuCodeChild = 1;
include_once(G5_PATH.'/head.php');
?>
<div id="sub_wrap" class="sub04_01">
  <section class="sub-section section01 sub-container" data-aos="fade-up"  data-aos-duration="2000">


    <div class="send_box02">
      <fieldset>
        <form name="contactform" method="post" action="/jp/sub04_01_send.php" enctype="multipart/form-data" class="fields">
          <ul>
            <!-- <h4>기본정보</h4> -->
            <li>
              <label for="title" class="lbl required">会社名</label>
              <div class="desc">
                <input name="title" type="text" class="input full" id="title" maxlength="50" required placeholder="相談参照用の会社名を入力してください。（非公開）">
              </div>
            </li>

            <li id="equipment_area">
              <span id="equipment" class="lbl">問い合わせ設備</span>
              <div class="desc flex ">
                <div>
                  <input name="group[]" type="checkbox" id="surface" class="input full check" value="表面欠陥検査機">
                  <p>表面欠陥検査機</p>
                </div>
                <div>
                  <input name="group[]" type="checkbox" id="webcleaner" class="input full check" value="ウェブクリーナー">
                  <p>ウェブクリーナー</p>
                </div>
                <div>
                  <input name="group[]" type="checkbox" id="pinning" class="input full check"  value="ピニングシステム">
                  <p>ピニングシステム</p>
                </div>
                <div>
                  <input name="group[]" type="checkbox" id="etc_chk" class="input full check"  value="その他（直接入力）">
                  <p>その他（直接入力）</p>
                  <input name="group[]" type="text" class="input" id="etc_txt" maxlength="80">
                </div>
              </div>
            </li>

            <li>
              <label for="place" class="lbl required">設置地域（または国）</label>
              <div class="desc">
                <input name="place" type="text" class="input full" id="place" maxlength="80" required placeholder="設置地域（または国）を入力してください">
              </div>
            </li>
            <li>
            <label for="email" class="lbl required">メール</label>
            <div class="desc">
              <input name="email" type="email" class="input full" id="email" maxlength="80" required placeholder="Eメールを入力してください">
            </div>
          </li>
          <li>
              <label for="name" class="lbl required">氏名（担当者名）</label>
              <div class="desc">
                <input name="name" type="text" class="input full" id="name" maxlength="50" required placeholder="担当者名を入力してください">
              </div>
            </li>
          <li>
            <label for="phone" class="lbl required">電話番号</label>
            <div class="desc">
            <input name="phone" type="tel" class="input full" id="phone" maxlength="30" required placeholder="通話可能な電話番号を入力してください">
            </div>
          </li>

            <!-- 상세 정보 토글 버튼 -->
            <li style="margin-top: 20px; margin-bottom: 10px;">
              <button type="button" id="toggleDetailBtn" style="background: #e0f0f0; border: 1px solid #ddd; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-size: 14px;">
                + 詳細情報を追加入力
              </button>
            </li>

            <!-- 상세 정보 영역 (기본 숨김) -->
            <div id="detailInfoArea" style="display: none; width: 100%; padding-bottom: 30px; border-bottom: 1px solid #ddd; margin-bottom: 30px;">
            <li>
              <label for="component" class="lbl">製品成分</label>
              <div class="desc">
                <input name="component" type="text" class="input full" id="component" maxlength="80" placeholder="例: PET, PVC">
              </div>
            </li>
            <li>
              <label for="color" class="lbl">色と透明度</label>
              <div class="desc">
                <input name="color" type="text" class="input full" id="color" maxlength="80">
              </div>
            </li>
            <li>
              <label for="area" class="lbl">適用面</label>
              <div class="desc">
                <input name="area" type="text" class="input full" id="area" maxlength="80" placeholder="片面/両面">
              </div>
            </li>
              <li>
                <label for="thickness" class="lbl">厚さ</label>
                <div class="desc">
                  <input name="thickness" type="text" class="input full" id="thickness" maxlength="80" >
                </div>
              </li>
              <li>
                <label for="width" class="lbl">幅</label>
                <div class="desc">
                  <input name="width" type="text" class="input full" id="width" maxlength="80" >
                </div>
              </li>
              <li>
                <label for="lineSpeed" class="lbl">ライン速度</label>
                <div class="desc">
                  <input name="lineSpeed" type="text" class="input full" id="lineSpeed" maxlength="80" >
                </div>
              </li>
              <li>
                <label for="requests" class="lbl">要求の削除＆検出対象</label>
                <div class="desc">
                  <input name="requests" type="text" class="input full" id="requests" maxlength="80" placeholder="例：シリカ、髪の毛">
                </div>
              </li>
            </div>

            <!-- <h4>상담 내용</h4> -->
            <li>
              <label for="comments" class="lbl">内容</label>
              <div class="desc">
              <textarea type="text" class="input full" name="comments" id="comments" rows="10" cols="80" placeholder="お問い合わせ内容を入力してください"></textarea>
              </div>
            </li>
            <li>
              <label for="file" class="lbl">添付ファイル</label>
              <div class="desc">
                <input type="file" name="attachment" style="border: 0; padding:0; ">
              </div>
            </li>
            <li>
              <label for="agree" class="lbl">個人情報の収集·利用</label>
              <div class="desc">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                  <div style="display: flex; align-items: center;">
                    <input type="checkbox" name="agree" id="agree" checked required class="required" style="width: auto; margin-right: 8px;">
                    <label for="agree" style="margin: 0; display: inline;">'個人情報の収集·利用'に同意します。</label>
                  </div>
                  <a href="javascript:void(0);" id="showPrivacyModal" style="color: #1a4691; text-decoration: underline; font-size: 13px; margin-left: 10px; cursor: pointer;">個人情報の収集·利用</a>
                </div>
              </div>
            </li>
          </ul>
          <div class="btn-group">
            <p><span>*</span> 印がついている項目は必ず入力してください。お問い合わせ内容を確認次第、連絡いたします。</p>
            <button type="submit" class="btn-submit">確認</button>
           </div>
        </form>
      </fieldset>
    </div>
  </div>

  </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const etcChk = document.getElementById("etc_chk");
    const etcTxt = document.getElementById("etc_txt");
    const toggleDetailBtn = document.getElementById("toggleDetailBtn");
    const detailInfoArea = document.getElementById("detailInfoArea");

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

    // Toggle detail info area
    toggleDetailBtn.addEventListener("click", function() {
        if (detailInfoArea.style.display === "none") {
            detailInfoArea.style.display = "block";
            toggleDetailBtn.textContent = "- 詳細情報を閉じる";
        } else {
            detailInfoArea.style.display = "none";
            toggleDetailBtn.textContent = "+ 詳細情報を追加入力";
        }
    });

    // 個人情報の収集·利用内容モーダル表示
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
                    <h3 style="margin-top: 0; margin-bottom: 20px; color: #333;">個人情報の収集·利用</h3>
                    <div style="line-height: 1.6; color: #666;">${privacyContent}</div>
                    <button onclick="document.getElementById('privacyModal').remove()" style="margin-top: 20px; padding: 10px 20px; background: #1a4691; color: #fff; border: none; border-radius: 5px; cursor: pointer;">確認</button>
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
