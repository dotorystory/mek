<?php
include_once('./_common.php');
include_once(G5_PATH.'/plus/mail_sender.php');

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/**
 * 메일 로그 기록 함수
 */
function log_mail_subscribe($type, $email, $mb_id = null, $success = true) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $mb_id = $mb_id ?? ($GLOBALS['member']['mb_id'] ?? null);
    
    sql_query("INSERT INTO g5_mail_log (ml_type, ml_email, ml_mb_id, ml_ip, ml_success, ml_datetime) 
              VALUES ('".sql_escape_string($type)."', '".sql_escape_string($email)."', 
                      ".($mb_id ? "'".sql_escape_string($mb_id)."'" : "NULL").", 
                      '".sql_escape_string($ip)."', ".($success ? 1 : 0).", '".G5_TIME_YMDHIS."')");
}

/**
 * 구독 취소 키 검증 (mail_sender.php의 generate_unsubscribe_key와 호환)
 */
if (!function_exists('verify_unsubscribe_key')) {
    function verify_unsubscribe_key($key, $email) {
        $decoded = base64_decode($key);
        if (!$decoded) return false;
        
        $parts = explode('|', $decoded);
        if (count($parts) !== 2) return false;
        
        list($key_email, $key_hash) = $parts;
        $encrypt_func = defined('G5_STRING_ENCRYPT_FUNCTION') ? G5_STRING_ENCRYPT_FUNCTION : 'create_hash';
        $expected_hash = md5($email . $encrypt_func);
        
        return ($key_email === $email && $key_hash === $expected_hash);
    }
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$key = $_GET['key'] ?? '';
$email = $_GET['email'] ?? $_POST['email'] ?? '';

// 페이지 제목 및 스타일 설정
$g5['title'] = '订阅我们的新闻简报';
$contact_css = G5_URL.'/css/contact.css';
add_stylesheet('<link rel="stylesheet" href="'.$contact_css.'">', 0);

$menuCodeParent = 4;
$menuCodeChild = 4;
include_once(G5_PATH.'/head.php');

$success_message = '';
$error_message = '';

// 구독 취소 처리
if ($action === 'unsubscribe' && $key && $email) {
    // 키 검증
    if (!verify_unsubscribe_key($key, $email)) {
        $error_message = "无效的取消订阅链接。";
    } else {
        // 구독 취소 처리
        $subscribe = sql_fetch("SELECT sb_id, sb_subscribe FROM g5_subscribe WHERE sb_email = '".sql_escape_string($email)."'");
        
        if ($subscribe) {
            if ($subscribe['sb_subscribe'] == 0) {
                $error_message = "该电子邮件已取消订阅。";
            } else {
                // 구독 취소
                sql_query("UPDATE g5_subscribe SET sb_subscribe = 0, sb_updatedate = '".G5_TIME_YMDHIS."' WHERE sb_id = '{$subscribe['sb_id']}'");
                
                // 회원인 경우 mb_mailling도 업데이트
                if ($is_member && isset($member['mb_email']) && $member['mb_email'] === $email) {
                    sql_query("UPDATE {$g5['member_table']} SET mb_mailling = 0 WHERE mb_id = '{$member['mb_id']}'");
                }
                
                // 메일 로그 기록
                log_mail_subscribe('unsubscribe', $email, $is_member ? $member['mb_id'] : null, true);
                
                $success_message = "订阅已成功取消。";
            }
        } else {
            $error_message = "找不到订阅信息。";
        }
    }
}
// 구독 신청 처리
elseif ($action === 'subscribe' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = get_text($_POST['email'] ?? '');
    $newsletter_agree = isset($_POST['newsletter_agree']) ? 1 : 0;
    $privacy_agree = isset($_POST['privacy_agree']) ? 1 : 0;
    
    if (!$newsletter_agree || !$privacy_agree) {
        $error_message = "需要同意接收新闻简报和收集个人信息。";
    } elseif (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "请输入有效的电子邮件地址。";
    } else {
        // 이메일 중복 체크
        $subscribe = sql_fetch("SELECT sb_id, sb_email, sb_subscribe FROM g5_subscribe WHERE sb_email = '".sql_escape_string($email)."'");
        
        if ($subscribe) {
            if ($subscribe['sb_subscribe'] == 1) {
                $error_message = "该电子邮件已订阅。";
                log_mail_subscribe('subscribe', $email, $is_member ? $member['mb_id'] : null, false);
            } else {
                // 구독 상태 업데이트
                sql_query("UPDATE g5_subscribe SET sb_subscribe = 1, sb_updatedate = '".G5_TIME_YMDHIS."' WHERE sb_id = '{$subscribe['sb_id']}'");
                
                // 회원인 경우 mb_mailling 업데이트
                if ($is_member) {
                    sql_query("UPDATE {$g5['member_table']} SET mb_mailling = 1 WHERE mb_id = '{$member['mb_id']}'");
                }
                
                // 메일 로그 기록
                log_mail_subscribe('subscribe', $email, $is_member ? $member['mb_id'] : null, true);
                
                $success_message = "订阅已成功申请。";
            }
        } else {
            // 새로운 구독자 추가
            sql_query("INSERT INTO g5_subscribe (sb_email, sb_subscribe, sb_regdate, sb_updatedate) 
                      VALUES ('".sql_escape_string($email)."', 1, '".G5_TIME_YMDHIS."', '".G5_TIME_YMDHIS."')");
            
            // 회원인 경우 mb_mailling 업데이트
            if ($is_member) {
                sql_query("UPDATE {$g5['member_table']} SET mb_mailling = 1 WHERE mb_id = '{$member['mb_id']}'");
            }
            
            // 메일 로그 기록
            log_mail_subscribe('subscribe', $email, $is_member ? $member['mb_id'] : null, true);
            
            $success_message = "订阅已成功申请。";
        }
    }
}

// 회원 이메일 가져오기
$member_email = '';
if ($is_member) {
    $member_email = $member['mb_email'] ?? '';
}

// 구독 취소 페이지인지 확인
$is_unsubscribe_page = ($action === 'unsubscribe' && $key && $email);
?>

<!-- 구독하기 섹션 -->
<section>
    <div class="contact-container">
        <h1 class="tableAi"><span class="sound_only"><?php echo $is_unsubscribe_page ? '取消订阅' : '订阅'; ?></span></h1>
    
    <div class="contact-messages">
        <?php if (isset($success_message) && $success_message): ?>
        <div class="messageHuman" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <p><?php echo $success_message; ?></p>
        </div>
        <?php endif; ?>

        <?php if (isset($error_message) && $error_message): ?>
        <div class="messageHuman" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <p><?php echo $error_message; ?></p>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($is_unsubscribe_page): ?>
        <!-- 구독 취소 확인 페이지 -->
        <?php if (!$success_message && !$error_message): ?>
        <div style="text-align: center; padding: 20px;">
            <p style="margin-bottom: 20px;">您要取消以下电子邮件地址的订阅吗？</p>
            <p style="font-weight: bold; font-size: 18px; margin-bottom: 30px;"><?php echo htmlspecialchars($email); ?></p>
            <form method="get" style="display: inline;">
                <input type="hidden" name="action" value="unsubscribe">
                <input type="hidden" name="key" value="<?php echo htmlspecialchars($key); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <button type="submit" class="contact-submit-btn" style="background: #dc3545; margin-right: 10px;">取消订阅</button>
            </form>
            <a href="<?php echo G5_URL; ?>/plus/" class="contact-submit-btn" style="display: inline-block; text-decoration: none; background: #6c757d;">取消</a>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 20px;">
            <a href="<?php echo G5_URL; ?>/plus/" class="contact-submit-btn" style="display: inline-block; text-decoration: none; background: #6c757d;">返回首页</a>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- 구독 신청 폼 -->
        <!-- <div class="messageAi" style="margin-bottom: 30px; font-size: 20px; font-weight: 600;">
            <p>🌈 안녕하세요, MEK+ 뉴스레터를 구독하시면 최신 정보와 특별한 이벤트 소식을 받아보실 수 있습니다.</p>
        </div> -->

        <div class="contact-form" style="border: 1px solid #ddd; margin-bottom: 50px;">
            <form method="post" action="?action=subscribe" onsubmit="return validateForm()">
                <!-- 허니팟 필드 (CSS로 숨김) -->
                <div style="display: none;">
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>
                
                <div class="contact-form-group">
                    <label for="email">💌 请输入接收新闻简报的电子邮件地址。</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($member_email); ?>" 
                           placeholder="<?php echo $is_member ? '请修改电子邮件' : '请输入电子邮件'; ?>" 
                           style="background: #f5f5f5; color: royalblue;"
                           required>
                </div>

                <div style="text-align: left;">
                    <div style="display: flex; align-items: flex-start; margin: 10px 0;">
                        <input type="checkbox" id="newsletter_agree" name="newsletter_agree" required style="margin: 2px 8px 0 0;">
                        <label for="newsletter_agree" style="font-size: 14px; color: #666;">我同意通过该电子邮件接收<?php echo $config['cf_title']; ?>的新闻简报。</label>
                    </div>
                </div>

                <div style="text-align: left;">
                    <div style="display: flex; align-items: flex-start; margin: 10px 0;">
                        <input type="checkbox" id="privacy_agree" name="privacy_agree" required style="margin: 2px 8px 0 0;">
                        <label for="privacy_agree" style="font-size: 14px; color: #666;">我同意<?php echo $config['cf_title']; ?>收集与新闻简报发送相关的个人信息。</label>
                    </div>
                </div>

                <br>
                
                <button type="submit" class="contact-submit-btn">订阅</button>
            </form>
        </div>
    <?php endif; ?>
</div>
</section>

<script>
function validateForm() {
    var newsletter_agree = document.getElementById('newsletter_agree');
    var privacy_agree = document.getElementById('privacy_agree');
    
    if (!newsletter_agree || !privacy_agree) {
        alert('需要同意接收新闻简报和收集个人信息。');
        return false;
    }
    
    if (!newsletter_agree.checked || !privacy_agree.checked) {
        alert('需要同意接收新闻简报和收集个人信息。');
        return false;
    }
    
    return true;
}
</script>

<?php
include_once(G5_PATH.'/tail.php');
?>

