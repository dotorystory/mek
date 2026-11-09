<?php
/**
 * /public/contact/ — MEK News 문의 (메일: msk@mekeng.com)
 */
include_once dirname(dirname(__DIR__)) . '/common.php';

if (!defined('_GNUBOARD_')) {
    exit;
}

if (defined('G5_COMMUNITY_USE') && G5_COMMUNITY_USE === false) {
    goto_url(G5_SHOP_URL . '/');
}

$g5['title'] = 'MEK News 문의';
include_once G5_PATH . '/head.php';

add_stylesheet('<link rel="stylesheet" href="'.G5_THEME_URL.'/css/contact.css?ver='.@filemtime(G5_THEME_PATH.'/css/contact.css').'">', 0);

$member_name = '';
$member_email = '';
if ($is_member) {
    $member_name = get_text($member['mb_name']);
    $member_email = get_text($member['mb_email']);
}

include_once G5_CAPTCHA_PATH . '/captcha.lib.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['company'])) {
        $error_message = '잘못된 접근입니다.';
    } else if (!chk_captcha()) {
        $error_message = '자동등록방지 입력이 올바르지 않습니다.';
    } else {
        $name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
        $email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
        $subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';

        $name = get_text($name);
        $email = get_text($email);
        $subject = get_text($subject);
        $content_safe = nl2br(get_text($content));

        if ($name && $email && $subject && $content !== '') {
            if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
                $error_message = '유효한 이메일 주소를 입력해주세요.';
            } else {
                $to = 'msk@mekeng.com';
                $mail_subject = '[MEK News 문의] ' . $subject;
                $mail_content = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #15202b;'>MEK News 문의</h2>
                    <p><strong>이름:</strong> {$name}</p>
                    <p><strong>이메일:</strong> {$email}</p>
                    <p><strong>제목:</strong> {$subject}</p>
                    <div style='margin-top: 20px; padding: 15px; background-color: #f5f5f5; border-radius: 5px;'>
                        <p><strong>내용:</strong></p>
                        <p>{$content_safe}</p>
                    </div>
                </div>
                ";

                $sent = false;
                if (function_exists('mailer') && !empty($config['cf_email_use'])) {
                    $admin_name = isset($config['cf_admin_email_name']) && $config['cf_admin_email_name'] !== ''
                        ? $config['cf_admin_email_name']
                        : 'MEK News';
                    $from_mail = isset($config['cf_admin_email']) && $config['cf_admin_email'] !== ''
                        ? $config['cf_admin_email']
                        : $email;
                    $sent = (bool) mailer($admin_name, $from_mail, $to, $mail_subject, $mail_content, 1, '', '', '', $email);
                } else {
                    $headers = 'From: ' . $email . "\r\n" .
                        'Reply-To: ' . $email . "\r\n" .
                        'Content-Type: text/html; charset=UTF-8' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();
                    $sent = (bool) @mail($to, $mail_subject, $mail_content, $headers);
                }

                if ($sent) {
                    $success_message = '문의가 성공적으로 전송되었습니다.';
                } else {
                    $error_message = '문의 전송 중 오류가 발생했습니다. 다시 시도해주세요.';
                }
            }
        } else {
            $error_message = '모든 필드를 입력해주세요.';
        }
    }
}
?>

<section class="ha-contact-section">
    <div class="contact-container">
        <h1 class="ha-contact-title">문의하기</h1>
        <p class="ha-contact-lead">MEK News 관련 문의를 남겨 주시면 <strong>msk@mekeng.com</strong>으로 전달됩니다.</p>

        <div class="contact-messages">
            <?php if ($success_message !== '') { ?>
            <div class="messageHuman">
                <p><?php echo $success_message; ?></p>
            </div>
            <?php } ?>

            <?php if ($error_message !== '') { ?>
            <div class="messageHuman">
                <p style="color:#c62828;"><?php echo $error_message; ?></p>
            </div>
            <?php } ?>
        </div>

        <div class="contact-form">
            <form method="post" action="">
                <div style="display:none;" aria-hidden="true">
                    <input type="text" name="company" tabindex="-1" autocomplete="off" value="">
                </div>

                <div class="contact-form-group">
                    <label for="name">이름</label>
                    <input type="text" id="name" name="name"
                           value="<?php echo $member_name; ?>"
                           placeholder="<?php echo $is_member ? '이름을 수정하세요' : '이름을 입력하세요'; ?>"
                           required>
                </div>

                <div class="contact-form-group">
                    <label for="email">이메일</label>
                    <input type="email" id="email" name="email"
                           value="<?php echo $member_email; ?>"
                           placeholder="<?php echo $is_member ? '이메일을 수정하세요' : '이메일을 입력하세요'; ?>"
                           required>
                </div>

                <div class="contact-form-group">
                    <label for="subject">제목</label>
                    <input type="text" id="subject" name="subject" placeholder="제목을 입력하세요" required>
                </div>

                <div class="contact-form-group">
                    <label for="content">문의 내용</label>
                    <textarea id="content" name="content" placeholder="문의 내용을 입력하세요" required></textarea>
                </div>

                <?php echo captcha_html(); ?>

                <br><br>
                <button type="submit" class="contact-submit-btn">문의하기</button>
            </form>
        </div>
    </div>
</section>

<?php
include_once G5_PATH . '/tail.php';
