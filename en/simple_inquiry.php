<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
include_once(G5_PATH.'/plus/mail_sender.php');

// 간단 문의폼 처리
if(isset($_POST['simple_email'])) {
    
    function died($error) {
        echo "<script> alert('Email sending failed.');";
        echo "history.go(-1);";
        echo "</script>";
        die();
    }

    if(!isset($_POST['simple_name']) ||
        !isset($_POST['simple_email']) ||
        !isset($_POST['simple_phone']) ||
        !isset($_POST['simple_message'])) {
        died('Sorry.\nThere is a problem with the submitted form.\nPlease check the form again.');
    }

    // Captcha verification
    if(!chk_captcha()) {
        died('The security code is incorrect.');
    }

    $name = $_POST['simple_name'];
    $email_from = $_POST['simple_email'];
    $phone = $_POST['simple_phone'];
    $message = $_POST['simple_message'];

    $email_to = "sales@webmail.mekeng.com";

    $email_subject = "[Simple Inquiry] Inquiry from ".$name;
    $email_subject = '=?UTF-8?B?'.base64_encode($email_subject).'?=';

    function clean_string($string) {
        $bad = array("content-type","bcc:","to:","cc:","href");
        return str_replace($bad,"",$string);
    }

    $email_message = "Company Name : ".clean_string($name)."<br><br>";
    $email_message .= "Email : ".clean_string($email_from)."<br><br>";
    $email_message .= "Phone : ".clean_string($phone)."<br><br>";
    $email_message .= "Message : ".clean_string(nl2br($message))."<br><br>";

    // create email headers
    $headers = 'From: '.$email_from."\r\n";
    $headers .= 'Reply-to: '.$email_from."\r\n";
    $headers .= 'Content-type: text/html'."\r\n";

    @mail($email_to, $email_subject, $email_message, $headers);
    
    // Newsletter subscription processing (auto-subscribe when privacy agreement is checked)
    if (isset($_POST['simple_agree']) && $_POST['simple_agree'] == 'on') {
        // Mail log recording function (same as subscribe/index.php)
        if (!function_exists('log_mail_subscribe')) {
            function log_mail_subscribe($type, $email, $mb_id = null, $success = true) {
                $ip = $_SERVER['REMOTE_ADDR'] ?? '';
                $mb_id = $mb_id ?? ($GLOBALS['member']['mb_id'] ?? null);
                
                sql_query("INSERT INTO g5_mail_log (ml_type, ml_email, ml_mb_id, ml_ip, ml_success, ml_datetime) 
                          VALUES ('".sql_escape_string($type)."', '".sql_escape_string($email)."', 
                                  ".($mb_id ? "'".sql_escape_string($mb_id)."'" : "NULL").", 
                                  '".sql_escape_string($ip)."', ".($success ? 1 : 0).", '".G5_TIME_YMDHIS."')");
            }
        }
        
        // Email duplicate check and subscription processing
        $subscribe = sql_fetch("SELECT sb_id, sb_email, sb_subscribe FROM g5_subscribe WHERE sb_email = '".sql_escape_string($email_from)."'");
        
        if ($subscribe) {
            if ($subscribe['sb_subscribe'] != 1) {
                // Update subscription status
                sql_query("UPDATE g5_subscribe SET sb_subscribe = 1, sb_updatedate = '".G5_TIME_YMDHIS."' WHERE sb_id = '{$subscribe['sb_id']}'");
                log_mail_subscribe('subscribe', $email_from, $is_member ? $member['mb_id'] : null, true);
            }
        } else {
            // Add new subscriber
            sql_query("INSERT INTO g5_subscribe (sb_email, sb_subscribe, sb_regdate, sb_updatedate) 
                      VALUES ('".sql_escape_string($email_from)."', 1, '".G5_TIME_YMDHIS."', '".G5_TIME_YMDHIS."')");
            log_mail_subscribe('subscribe', $email_from, $is_member ? $member['mb_id'] : null, true);
        }
        
        // Update mb_mailling for members
        if ($is_member) {
            sql_query("UPDATE {$g5['member_table']} SET mb_mailling = 1 WHERE mb_id = '{$member['mb_id']}'");
        }
    }
    
    echo "<script>
        alert('Thank you for your inquiry.\\nWe will reply as soon as possible.');
        window.parent.location.reload();
    </script>";
    exit;
}
?>

<!-- 간단 문의폼 레이어 -->
<div id="simple_inquiry_layer" class="simple_inquiry_layer">
    <div class="simple_inquiry_content">
        <div class="simple_inquiry_header">
            <h3>Simple Inquiry</h3>
            <button type="button" class="simple_inquiry_close" onclick="closeSimpleInquiry()">×</button>
        </div>
        
        <form name="simple_inquiry_form" method="post" action="/en/simple_inquiry.php" class="simple_inquiry_form">
            <div class="simple_inquiry_fields">
                <div class="field_group">
                    <label for="simple_name" class="required">Company Name</label>
                    <input type="text" name="simple_name" id="simple_name" required placeholder="Enter your company name (for reference, confidential)">
                </div>
                
                <div class="field_group">
                    <label for="simple_email" class="required">Email</label>
                    <input type="email" name="simple_email" id="simple_email" required placeholder="Enter a reachable email address">
                </div>
                
                <div class="field_group">
                    <label for="simple_phone">Phone</label>
                    <input type="tel" name="simple_phone" id="simple_phone" placeholder="Enter your mobile/phone number">
                </div>
                
                <div class="field_group">
                    <label for="simple_message" class="required">Message</label>
                    <textarea name="simple_message" id="simple_message" required placeholder="Enter your inquiry (max 500 characters)" rows="2"></textarea>
                </div>

                <div class="field_group">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center;">
                            <input type="checkbox" name="simple_agree" id="simple_agree" checked required class="required" style="width: auto; margin-right: 8px;">
                            <label for="simple_agree" style="margin: 0; display: inline;">I agree to the 'Privacy Policy'.</label>
                        </div>
                        <a href="javascript:void(0);" onclick="togglePrivacyContent()" style="color: #1a4691; text-decoration: underline; font-size: 13px; margin-left: 10px; cursor: pointer;">Privacy Policy</a>
                    </div>
                    <div id="privacy_content" style="display: none; margin-top: 15px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; line-height: 1.6; color: #666; max-height: 300px; overflow-y: auto;">
                        <?php echo nl2br(get_text($config['cf_privacy'])); ?>
                    </div>
                </div>
                
                <div class="field_group">
                    <label>Security Code</label>
                    <?php echo captcha_html(); ?>
                </div>
            </div>
            
            <div class="simple_inquiry_footer">
                <button type="submit" class="simple_inquiry_submit">Send Inquiry</button>
            </div>
        </form>
    </div>
</div>

<!-- 간단 문의폼 열기 버튼 -->
<div id="simple_inquiry_btn" class="simple_inquiry_btn" onclick="openSimpleInquiry()">
    <span>Inquiry</span>
</div>

<style>
/* 간단 문의폼 스타일 */
.simple_inquiry_layer {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
}

.simple_inquiry_content {
    background: #f5f5f5;
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.simple_inquiry_header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.simple_inquiry_header h3 {
    margin: 0;
    font-size: 18px;
    color: #333;
}

.simple_inquiry_close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.simple_inquiry_close:hover {
    color: #333;
}

.simple_inquiry_form {
    padding: 20px;
}

.simple_inquiry_fields {
    margin-bottom: 20px;
}

.field_group {
    margin-bottom: 15px;
}

.field_group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.field_group label.required::after {
    content: " *";
    color: #e74c3c;
}

.field_group input,
.field_group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    box-sizing: border-box;
}

.field_group input:focus,
.field_group textarea:focus {
    outline: none;
    border-color: #1a4691;
}

.simple_inquiry_footer {
    text-align: center;
}

.simple_inquiry_submit {
    background: #2c3e50;
    color: #fff;
    border: none;
    padding: 12px 30px;
    border-radius: 7px;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.3s;
}

.simple_inquiry_submit:hover {
    background: #144d98;
}

/* 간단 문의폼 열기 버튼 */
.simple_inquiry_btn {
    position: fixed;
    bottom: 30px;
    left: 35px;
    background: linear-gradient(135deg, #1a4691 0%, #2c3e50 50%, #34495e 100%);
    color: #fff;
    padding: 15px 35px;
    border-radius: 50px;
    font-size: 1.8em;
    cursor: pointer;
    z-index: 10000;
    box-shadow: 0 8px 25px rgba(26, 70, 145, 0.4), 
                0 4px 15px rgba(44, 62, 80, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    font-weight: 500;
    border: 1px solid rgba(255, 255, 255, 0.1);
    overflow: hidden;
    display: inline-block;
    width: auto;
}

.simple_inquiry_btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.6s;
}

.simple_inquiry_btn:hover {
    background: linear-gradient(135deg, #144d98 0%, #34495e 50%, #2c3e50 100%);
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 12px 35px rgba(26, 70, 145, 0.5), 
                0 8px 25px rgba(44, 62, 80, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.simple_inquiry_btn:hover::before {
    left: 100%;
}

.simple_inquiry_btn:active {
    transform: translateY(-2px) scale(1.02);
    transition: all 0.1s;
}

/* 모바일 반응형 */
@media (max-width: 768px) {
    .simple_inquiry_content {
        width: 95%;
        margin: 20px;
    }
    
    .simple_inquiry_btn {
        bottom: 20px;
        left: 25px;
        padding: 10px 20px;
        font-size: 20px;
    }
    
    .simple_inquiry_btn span {
        display: none;
    }
    
    .simple_inquiry_btn::after {
        /* content: "💬"; */
        content: "?";
        font-size: 30px;
    }
}

@media (max-width: 480px) {
    .simple_inquiry_form {
        padding: 15px;
    }
    
    .simple_inquiry_header {
        padding: 15px;
    }
}
</style>

<script>
function openSimpleInquiry() {
    document.getElementById('simple_inquiry_layer').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeSimpleInquiry() {
    document.getElementById('simple_inquiry_layer').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// ESC 키로 닫기
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSimpleInquiry();
    }
});

// 레이어 외부 클릭시 닫기
document.getElementById('simple_inquiry_layer').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSimpleInquiry();
    }
});

// 폼 제출 처리
document.querySelector('.simple_inquiry_form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/en/simple_inquiry.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Check server response for success/failure
        if(data.includes('The security code is incorrect')) {
            alert('The security code is incorrect.\nPlease try again.');
            // Refresh captcha (for kcaptcha)
            if(typeof g5_captcha_url !== 'undefined') {
                var captcha_img = document.getElementById('captcha_img');
                if(captcha_img) {
                    captcha_img.src = g5_captcha_url + '/kcaptcha.php?t=' + new Date().getTime();
                }
            }
        } else if(data.includes('Thank you for your inquiry')) {
            alert('Thank you for your inquiry.\nWe will reply as soon as possible.');
            closeSimpleInquiry();
            this.reset();
        } else {
            alert('An error occurred while sending the inquiry.');
        }
    })
    .catch(error => {
        alert('An error occurred while sending the inquiry.');
    });
});

// Toggle privacy policy content
function togglePrivacyContent() {
    const content = document.getElementById('privacy_content');
    if (content.style.display === 'none') {
        content.style.display = 'block';
    } else {
        content.style.display = 'none';
    }
}
</script>
