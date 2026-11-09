<?php
include_once('./_common.php');
include_once(G5_PATH.'/plus/mail_sender.php');
// include_once(G5_PATH.'/plus/mail_security.php'); // 필요 시 사용

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 관리자 및 회원등급 5등급 이상 체크
if (!$is_admin && $member['mb_level'] < 5) {
    $return_url = urlencode(G5_URL.'/plus/mailer/');
    goto_url(G5_BBS_URL.'/login.php?url='.$return_url);
    exit;
}

// 테스트 메일 발송
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test_mail') {
    $test_email = sql_escape_string(trim($_POST['test_email'] ?? ''));
    
    if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        alert('유효한 이메일 주소를 입력해주세요.', './');
        exit;
    }
    
    // 문의폼과 동일하게 로컬 Postfix(config)만 사용 (DB 외부 SMTP 분기 제외)
    $result = send_mail_via_smtp(
        $test_email,
        '테스트 메일',
        '<h2>테스트 메일</h2><p>이 메일은 테스트를 위해 발송되었습니다.</p><p>발송 시간: ' . date('Y-m-d H:i:s') . '</p>',
        false
    );
    
    if ($result['success']) {
        alert('테스트 메일이 성공적으로 발송되었습니다. 수신함을 확인해주세요.', './');
    } else {
        alert('테스트 메일 발송 실패: ' . $result['error'], './');
    }
}

$g5['title'] = '메일 발송';
$contact_css = G5_URL.'/css/contact.css';
add_stylesheet('<link rel="stylesheet" href="'.$contact_css.'">', 0);

$menuCodeParent = 1;
$menuCodeChild = 5;
include_once(G5_PATH.'/head.php');

// 새글 목록 가져오기
$sql_common = " from {$g5['board_new_table']} a, {$g5['board_table']} b, {$g5['group_table']} c 
                where a.bo_table = b.bo_table 
                and b.gr_id = c.gr_id 
                and b.bo_use_search = 1 
                and a.wr_parent = a.wr_id "; // 댓글 제외
$sql_order = " order by a.bn_id desc ";
$sql = " select a.*, b.bo_subject, c.gr_subject, c.gr_id {$sql_common} {$sql_order} limit 0, 30 ";
$result = sql_query($sql);
$new_articles = array();
for ($i=0; $row=sql_fetch_array($result); $i++) {
    $tmp_write_table = $g5['write_prefix'].$row['bo_table'];
    $row2 = sql_fetch(" select * from {$tmp_write_table} where wr_id = '{$row['wr_id']}' ");
    
    // 게시판 이름 가져오기
    $board_name = $row['bo_subject'];
    
    $new_articles[] = array(
        'wr_id' => $row['wr_id'],
        'bo_table' => $row['bo_table'],
        'bo_subject' => $board_name,
        'wr_subject' => $row2['wr_subject'],
        'wr_content' => $row2['wr_content'],
        'href' => get_pretty_url($row['bo_table'], $row2['wr_id'])
    );
}

// 메일 발송 처리 (뉴스레터 발송)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] !== 'test_mail')) {
    try {
        // 이메일 발송 제한 체크
        if (function_exists('check_email_limit')) {
            $limit_check = check_email_limit('mail_sender');
            if (!$limit_check['allowed']) {
                alert($limit_check['message'], './');
                exit;
            }
        }
        // 허니팟 체크
        if (function_exists('check_honeypot') && !check_honeypot('address')) {
            alert('잘못된 접근입니다.', './');
            exit;
        }
        
        // sanitize_input 함수가 없을 경우를 대비
        if (!function_exists('sanitize_input')) {
            function sanitize_input($input) {
                return trim(strip_tags($input));
            }
        }
        
        $newsletter_title = sanitize_input(isset($_POST['newsletter_title']) ? trim($_POST['newsletter_title']) : '');
        $subject = $newsletter_title;
        $content = sanitize_input($_POST['content'] ?? '');
        $mail_type = sanitize_input($_POST['mail_type'] ?? '');
        $custom_emails = sanitize_input($_POST['custom_emails'] ?? '');
        $selected_articles = isset($_POST['selected_articles']) ? array_map('sanitize_input', $_POST['selected_articles']) : array();
        $ad_articles = isset($_POST['ad_articles']) ? array_map('sanitize_input', $_POST['ad_articles']) : array();
        
        if (empty($newsletter_title)) {
            alert('뉴스레터 제목을 입력해주세요.', './');
            exit;
        }

        // 선택된 새글 내용 추가
        $newsletter_content = '';
        if (!empty($content)) {
            $newsletter_content .= $content . "\n\n";
        }
        if (!empty($selected_articles)) {
            $newsletter_content .= "<h2>새로운 소식</h2>\n";
            foreach ($selected_articles as $article_id) {
                foreach ($new_articles as $article) {
                    if ($article['wr_id'] == $article_id) {
                        // 광고 여부 설정
                        $article['is_ad'] = in_array($article_id, $ad_articles);
                        if (function_exists('get_article_html')) {
                            $newsletter_content .= get_article_html($article);
                        } else {
                            $newsletter_content .= '<div><h3>' . htmlspecialchars($article['wr_subject']) . '</h3><p>' . htmlspecialchars($article['wr_content']) . '</p></div>';
                        }
                        break;
                    }
                }
            }
        }

        // 이메일 목록 수집
        $emails = array();

        switch ($mail_type) {
            case 'subscribe':
                // g5_subscribe 테이블에서 이메일 가져오기
                $sql = "SELECT sb_email FROM g5_subscribe WHERE sb_subscribe = 1";
                $result = sql_query($sql);
                while ($row = sql_fetch_array($result)) {
                    $emails[] = $row['sb_email'];
                }
                break;

            case 'member':
                // g5_member 테이블에서 메일 수신 동의한 회원 이메일 가져오기
                $sql = "SELECT mb_email FROM {$g5['member_table']} WHERE mb_mailling = 1";
                $result = sql_query($sql);
                while ($row = sql_fetch_array($result)) {
                    $emails[] = $row['mb_email'];
                }
                break;

            case 'custom':
                // 직접 입력한 이메일 처리
                $custom_emails = str_replace(array("\r\n", "\r", "\n"), ',', $custom_emails);
                $emails = array_filter(array_map('trim', explode(',', $custom_emails)));
                break;

            case 'csv':
                // CSV 파일 처리
                $csv_file = $_FILES['csv_file'] ?? null;
                if ($csv_file && $csv_file['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = G5_PATH . '/data/file/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_name = $upload_dir . basename($csv_file['name']);
                    move_uploaded_file($csv_file['tmp_name'], $file_name);
                    
                    if (($handle = fopen($file_name, "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            if (filter_var($data[0], FILTER_VALIDATE_EMAIL)) {
                                $emails[] = $data[0];
                            }
                        }
                        fclose($handle);
                    }
                    unlink($file_name); // 처리 후 파일 삭제
                }
                break;
        }

        if (empty($emails)) {
            alert('발송 대상 이메일이 없습니다.', './');
            exit;
        }

        // 이메일 발송 (로컬 postfix 직접 사용)
        $success_count = 0;
        $fail_count = 0;
        $fail_emails = array();

        foreach ($emails as $email) {
            try {
                // 구독 취소 키 생성 (mail_sender.php의 generate_unsubscribe_key 함수 사용)
                if (function_exists('generate_unsubscribe_key')) {
                    $unsubscribe_key = generate_unsubscribe_key($email);
                } else {
                    $unsubscribe_key = base64_encode($email . '|' . md5($email . (defined('G5_STRING_ENCRYPT_FUNCTION') ? G5_STRING_ENCRYPT_FUNCTION : 'create_hash')));
                }
                if (function_exists('get_newsletter_template')) {
                    $newsletter_html = get_newsletter_template($subject, $newsletter_content, $unsubscribe_key, $email);
                } else {
                    $newsletter_html = '<html><body><h1>' . htmlspecialchars($subject) . '</h1>' . $newsletter_content . '</body></html>';
                }
                
                $result = send_mail_via_smtp($email, $subject, $newsletter_html, false);
                
                if ($result['success']) {
                    $success_count++;
                } else {
                    $fail_count++;
                    $fail_emails[] = $email . ' (' . htmlspecialchars($result['error']) . ')';
                }
            } catch (Exception $e) {
                $fail_count++;
                $fail_emails[] = $email . ' (예외: ' . htmlspecialchars($e->getMessage()) . ')';
            }
        }

        $result_message = "총 {$success_count}개의 메일이 성공적으로 발송되었습니다.";
        if ($fail_count > 0) {
            $result_message .= "\n{$fail_count}개의 메일 발송에 실패했습니다.";
            if (!empty($fail_emails)) {
                $result_message .= "\n\n실패한 이메일:\n" . implode("\n", array_slice($fail_emails, 0, 10));
                if (count($fail_emails) > 10) {
                    $result_message .= "\n... 외 " . (count($fail_emails) - 10) . "개";
                }
            }
        }
        
        if (function_exists('log_email_attempt')) {
            log_email_attempt('mail_sender', 'bulk_send', $success_count > 0);
        }
        
        // 결과를 세션에 저장하고 리다이렉트
        $_SESSION['mailer_result_message'] = $result_message;
        $_SESSION['mailer_fail_emails'] = $fail_emails;
        $_SESSION['mailer_success_count'] = $success_count;
        $_SESSION['mailer_fail_count'] = $fail_count;
        
        // alert 후 리다이렉트 (정상 작동하는 페이지들과 동일한 방식)
        alert($result_message, './');
        exit;
        
    } catch (Exception $e) {
        alert('뉴스레터 발송 중 오류가 발생했습니다: ' . htmlspecialchars($e->getMessage()), './');
        exit;
    }
}

// 세션에서 결과 메시지 가져오기
if (isset($_SESSION['mailer_result_message'])) {
    $result_message = $_SESSION['mailer_result_message'];
    $fail_emails = $_SESSION['mailer_fail_emails'] ?? array();
    $success_count = $_SESSION['mailer_success_count'] ?? 0;
    $fail_count = $_SESSION['mailer_fail_count'] ?? 0;
    
    // 세션 변수 제거
    unset($_SESSION['mailer_result_message']);
    unset($_SESSION['mailer_fail_emails']);
    unset($_SESSION['mailer_success_count']);
    unset($_SESSION['mailer_fail_count']);
}
?>

<div class="mail-sender-container">
    <h1 class="sound_only">메일 발송</h1>

    <!-- 뉴MEK+ Mail 발송 섹션 -->
    <div class="mail-form-section" style="border: 1px solid #ddd;">
        <h2>MEK+ Mail 발송</h2>
        <br>

    <?php if (isset($result_message)): ?>
    <div class="mail-result">
        <p><?php echo nl2br(htmlspecialchars($result_message)); ?></p>
        <?php if (!empty($fail_emails)): ?>
        <p>실패한 이메일 목록:</p>
        <ul>
            <?php foreach ($fail_emails as $email): ?>
            <li><?php echo htmlspecialchars($email); ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <!-- 허니팟 필드 (CSS로 숨김) -->
        <div style="display: none;">
            <input type="text" name="address" tabindex="-1" autocomplete="off">
        </div>
        
        <div class="mail-form-group">
            <label for="newsletter_title">제목</label>
            <input type="text" id="newsletter_title" name="newsletter_title" required 
                   value="<?php echo htmlspecialchars($config['cf_title'] . ' ' . date('Y년 m월') . ' 뉴스레터'); ?>">
        </div>

        <div class="mail-form-group">
            <label for="content">추가 메시지 (선택사항)</label>
            <textarea id="content" name="content" rows="5" placeholder="기본 인사말 외에 추가로 전달할 메시지가 있다면 입력해주세요."></textarea>
        </div>

        <div class="mail-form-group">
            <label for="mail_type">메일 대상 선택</label>
            <select id="mail_type" name="mail_type" required>
                <option value="custom">직접 입력</option>
                <option value="subscribe">구독자 (g5_subscribe)</option>
                <option value="member">회원 (g5_member)</option>
                <option value="csv">CSV 파일 업로드</option>
            </select>
        </div>

        <div class="mail-form-group" id="custom_emails_group">
            <label for="custom_emails">이메일 목록 (쉼표 또는 줄바꿈으로 구분)</label>
            <textarea id="custom_emails" name="custom_emails" rows="5" style="background: #f5f5f5; color: royalblue; padding: 5px; border-radius: 5px;"></textarea>
        </div>

        <div class="mail-form-group" id="csv_file_group" style="display:none;">
            <label for="csv_file">CSV 파일 업로드</label>
            <input type="file" id="csv_file" name="csv_file" accept=".csv">
            <p class="help-text">*주의: CSV 파일의 제1행(메뉴)을 제회하고, 반드시 첫 번째 열에 이메일 주소를 입력하세요. </p>
        </div>

        <div class="mail-form-group">
            <label>새글 목록</label>
            <div class="new-articles-list">
                <?php foreach ($new_articles as $article): ?>
                <div class="article-item" data-board-name="<?php echo htmlspecialchars($article['bo_subject']); ?>">
                    <input type="checkbox" name="selected_articles[]" value="<?php echo htmlspecialchars($article['wr_id']); ?>">
                    <input type="checkbox" name="ad_articles[]" value="<?php echo htmlspecialchars($article['wr_id']); ?>" class="ad-checkbox" title="광고 표시">
                    <span class="article-title">[<?php echo htmlspecialchars($article['bo_subject']); ?>] <?php echo htmlspecialchars($article['wr_subject']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="help-text">
                <span class="checkbox-legend">☐ 글 선택</span>
                <span class="checkbox-legend">🏷️ 광고 표시</span>
            </p>
        </div>

        <button type="submit" class="mail-submit-btn" style="width: 150px;">MEK+ Mail 발송</button>
    </form>
    </div>

    <!-- 테스트 메일 발송 섹션 -->
    <div class="mail-form-section" style="margin-top: 50px; background: #f5f5f5; border: 1px solid #ddd;">
        <h2>테스트 메일 발송</h2>
        <form method="post" id="testMailForm">
            <input type="hidden" name="action" value="test_mail">
            
            <div class="mail-form-group">
                <label for="test_email">테스트 이메일 주소 <span class="required">*</span></label>
                <input type="email" id="test_email" name="test_email" required 
                    style="background: #fff; color: royalblue; padding: 5px; border-radius: 5px; border: 1px solid #ddd;"
                    placeholder="수신 이메일 주소 입력">
                <p class="help-text">(일반 메일 및 포털 메일 주소로 테스트해보세요.)</p>
            </div>

            <button type="submit" class="mail-submit-btn" style="background: #888; color: #fff; width: 150px;">Test Mail 발송</button>
        </form>
    </div>

</div>

<style>
.mail-form-section {
    background: #fff;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}
.mail-form-section h2 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #111;
    font-size: 20px;
    font-weight: 700;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}
.required {
    color: #dc3545;
}
.help-text {
    font-size: 0.9em;
    color: #666;
    margin-top: 5px;
    margin-bottom: 0;
}
.mail-result {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    border-left: 4px solid #007bff;
}
.mail-result ul {
    margin: 10px 0;
    padding-left: 20px;
}
.new-articles-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #ddd;
    padding: 10px;
    margin-bottom: 10px;
}
.article-item {
    padding: 5px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 8px;
}
.article-item:last-child {
    border-bottom: none;
}
.ad-checkbox {
    width: 16px;
    height: 16px;
    position: relative;
}
.ad-checkbox::after {
    content: '🏷️';
    position: absolute;
    top: -2px;
    left: 0;
    opacity: 0.5;
}
.checkbox-legend {
    margin-right: 15px;
    font-size: 0.9em;
    color: #666;
}
</style>

<script>
// 메일 타입 변경 시 필드 표시/숨김
document.getElementById('mail_type').addEventListener('change', function() {
    const customEmailsGroup = document.getElementById('custom_emails_group');
    const csvFileGroup = document.getElementById('csv_file_group');
    
    if (this.value === 'custom') {
        customEmailsGroup.style.display = 'block';
        csvFileGroup.style.display = 'none';
    } else if (this.value === 'csv') {
        customEmailsGroup.style.display = 'none';
        csvFileGroup.style.display = 'block';
    } else {
        customEmailsGroup.style.display = 'none';
        csvFileGroup.style.display = 'none';
    }
});
</script>

<div style="text-align: center; margin-top: 10px; padding: 20px;">
    <a href="<?php echo G5_URL; ?>/plus/" class="mail-submit-btn" style="display: inline-block; text-decoration: none; background: #555; color: #fff; padding: 12px 30px; border-radius: 5px; border: none; cursor: pointer;">
        Go back to MEK+ 홈
    </a>
</div>

<?php
include_once(G5_PATH.'/tail.php');
?>
