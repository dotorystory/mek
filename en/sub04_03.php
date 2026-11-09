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
        <form name="contactform" method="post" action="/en/sub04_03_send.php" enctype="multipart/form-data" class="fields">
          <ul>
            <!-- <h4>Basic Information</h4> -->
            <li>
              <label for="title" class="lbl required">Company Name</label>
              <div class="desc">
                <input name="title" type="text" class="input full" id="title" maxlength="50" required placeholder="Enter your company name for consultation reference (confidential)">
              </div>
            </li>
            <li id="equipment_area">
              <span id="equipment" class="lbl required">Inquiry Equipment</span>
              <div class="desc flex">
                <input name="group[]" type="checkbox" id="webcleaner" class="input check full" value="Online Thickness Gauge">Online Thickness Gauge
                <input name="group[]" type="checkbox" id="surface" class="input check full" value="Surface Defect Inspector">Surface Defect Inspector
                <input name="group[]" type="checkbox" id="etc_chk" class="input check full"  value="Other (Direct Input)">Other (Direct Input)
                <input name="group[]" type="text" class="input full" id="etc_txt" maxlength="80">
              </div>
            </li>
            <li>
              <label for="place" class="lbl required">Installation Area (or Country)</label>
              <div class="desc">
                <input name="place" type="text" class="input full" id="place" maxlength="80" required placeholder="Enter installation area (or country)">
              </div>
            </li>
            <li>
            <label for="email" class="lbl required">Email</label>
            <div class="desc">
              <input name="email" type="email" class="input full" id="email" maxlength="80" required placeholder="Enter your E-mail">
            </div>
          </li>
          <li>
              <label for="name" class="lbl required">Name (Contact Person)</label>
              <div class="desc">
                <input name="name" type="text" class="input full" id="name" maxlength="50" required placeholder="Enter contact person name">
              </div>
            </li>
          <li>
            <label for="phone" class="lbl">Phone Number (Landline)</label>
            <div class="desc">
            <input name="phone" type="tel" class="input full" id="phone" maxlength="30" placeholder="Enter phone number">
            </div>
          </li>
          <li>
            <label for="cell" class="lbl required">Mobile Phone</label>
            <div class="desc">
              <input name="cell" type="tel" class="input full" id="cell" maxlength="80" required placeholder="Enter mobile phone number">
            </div>
          </li>
            <li>
              <label for="date" class="lbl required">Purchase Date</label>
              <div class="desc">
                <input name="date" type="text" class="input full" id="date" maxlength="80" required placeholder="Enter purchase date">
              </div>
            </li>
            <li>
              <label for="process" class="lbl">Applied Process</label>
              <div class="desc">
                <input name="process" type="text" class="input full" id="process" maxlength="80" placeholder="e.g., Extrusion, Coating">
              </div>
            </li>
              <li>
                <label for="thickness" class="lbl">Measurement Thickness</label>
                <div class="desc">
                  <input name="thickness" type="text" class="input full" id="thickness" maxlength="80" >
                </div>
              </li>
              <li>
                <label for="width" class="lbl">Measurement Width</label>
                <div class="desc">
                  <input name="width" type="text" class="input full" id="width" maxlength="80" >
                </div>
              </li>

            <!-- <h4>Consultation Details</h4> -->
            <li>
              <label for="comments" class="lbl">A/S Inquiry Message</label>
              <div class="desc">
              <textarea type="text" class="input full" name="comments" id="comments" rows="10" cols="80" placeholder="Please attach on-site photos and foreign material types separately"></textarea>
              </div>
            </li>
            <li>
              <label for="file" class="lbl">Attachment</label>
              <div class="desc">
                <input type="file" name="attachment" style="border: 0; padding:0; ">
              </div>
            </li>
            <li>
              <label for="agree" class="lbl">Collection and Use of Personal Information</label>
              <div class="desc">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                  <div style="display: flex; align-items: center;">
                    <input type="checkbox" name="agree" id="agree" required class="required" style="width: auto; margin-right: 8px;">
                    <label for="agree" style="margin: 0; display: inline;">I agree to the 'Collection and Use of Personal Information'.</label>
                  </div>
                  <a href="javascript:void(0);" id="showPrivacyModal" style="color: #1a4691; text-decoration: underline; font-size: 13px; margin-left: 10px; cursor: pointer;">Collection and Use of Personal Information</a>
                </div>
              </div>
            </li>
            <li>
              <label class="lbl">Anti-spam verification</label>
              <div class="desc">
                <?php echo captcha_html(); ?>
              </div>
            </li>
          </ul>
          <div class="btn-group">
            <p><span>*</span> Fields marked with an asterisk (*) are required. We will contact you as soon as we confirm your inquiry.</p>
            <button type="submit" class="btn-submit">Submit</button>
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
            equipmentMessageSpan.innerHTML = "<br>* Check the inquiry equipment.";  // Show the error message
        } else {
            equipmentMessageSpan.innerHTML = "";  // Remove the error message if any checkbox is checked
        }
    });

    // Show privacy policy modal
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
                    <h3 style="margin-top: 0; margin-bottom: 20px; color: #333;">Collection and Use of Personal Information</h3>
                    <div style="line-height: 1.6; color: #666;">${privacyContent}</div>
                    <button onclick="document.getElementById('privacyModal').remove()" style="margin-top: 20px; padding: 10px 20px; background: #1a4691; color: #fff; border: none; border-radius: 5px; cursor: pointer;">Confirm</button>
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
