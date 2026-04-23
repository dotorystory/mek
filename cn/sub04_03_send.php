<meta charset="utf-8">
<?php
include_once('./_common.php');
include_once(G5_PATH.'/plus/mail_sender.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

if(isset($_POST['email'])) {

  if (!chk_captcha()) {
    echo "<script> alert('验证码错误，请重新输入。');";
    echo "history.go(-1);";
    echo "</script>";
    die();
  }

  // 파일 업로드 처리
  $file = $_FILES['attachment'];
  $tmpName = $file['tmp_name'];
  $filename = basename($file['name']);
  $target_dir = G5_DATA_PATH . "/mail/"; // 파일 업로드 디렉토리
  $target_file = $target_dir . $filename; // 업로드할 파일 경로

  // Rename file if it already exists
  $i = 1;
  while (file_exists($target_file)) {
      $filename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $i . '.' . pathinfo($filename, PATHINFO_EXTENSION);
      $target_file = $target_dir . $filename;
      $i++;
  }

  // 디렉토리 생성
  if (!is_dir($target_dir)) {
    @mkdir($target_dir, 0707);
    @chmod($target_dir, 0707);
  }

  move_uploaded_file($tmpName, $target_file);

  function died($error) {
   // your error code can go here
   echo "<script> alert('邮件发送失败');";
   echo "history.go(-1);";
   echo "</script>";
   die();
  }

  function generateRandomString($length = 10) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
  }

  if(!isset($_POST['title']) ||
      !isset($_POST['group']) || //다중체크
      !isset($_POST['place']) ||
      !isset($_POST['name']) ||
      !isset($_POST['email']) ||
      !isset($_POST['cell']) ||
      !isset($_POST['date']) ||
      !isset($_POST['process']) ||
      !isset($_POST['thickness']) ||
      !isset($_POST['width']) ||
      !isset($_POST['comments']) ||
      !isset($_POST['agree'])) {
      died('抱歉，\n您提交的表格有问题。\n请重新检查表格。');
  }

  $title = $_POST['title']; // required
  $group = implode(', ', $_POST['group']); // 필수항목
  $place = $_POST['place']; // required
  $name = $_POST['name']; // required
  $email_from = $_POST['email']; // required
  $phone = $_POST['phone']; // 선택
  $cell = $_POST['cell']; // required
  $date = $_POST['date'];
  $process = $_POST['process'];
  $thickness = $_POST['thickness'];
  $width = $_POST['width'];
  $comments = $_POST['comments']; // required
  // $file_name = $file['name'];
  $file_url = G5_DATA_URL . "/mail/" . $filename; // 파일 경로

  $email_to = "msk@mekeng.com";

  $email_subject = "[A/S 咨询]";
  $email_subject = '=?UTF-8?B?'.base64_encode($email_subject).'?=';

  function clean_string($string) {
    $bad = array("content-type","bcc:","to:","cc:","href");
    return str_replace($bad,"",$string);
  }

  $email_message .= "公司名 : ".clean_string($title)."<br><br>";
  $email_message .= "查询设备 : ".clean_string($group)."<br><br>"; //필수항목
  $email_message .= "安装地区（或国家） : ".clean_string($place)."<br><br>";
  $email_message .= "电子邮件 : ".clean_string($email_from)."<br><br>";
  $email_message .= "姓名（负责人姓名） : ".clean_string($name)."<br><br>";
  $email_message .= "电话号码（有线） : ".clean_string($phone)."<br><br>";
  $email_message .= "手机 : ".clean_string($cell)."<br><br>";
  $email_message .= "购买时间 : ".clean_string($date)."<br><br>";
  $email_message .= "适用工序 : ".clean_string($process)."<br><br>";
  $email_message .= "测量厚度 : ".clean_string($thickness)."<br><br>";
  $email_message .= "测量宽度 : ".clean_string($width)."<br><br>";
  $email_message .= "A/S咨询内容 : ".clean_string(nl2br($comments))."<br><br>";

  if(!empty($filename) && file_exists($target_file)) {
      $email_message .= "附件 : <a target='_blank' href='" . $file_url . "' download>".$filename."</a><br><br>";
  } else {
      $email_message .= "附件 : 无<br><br>";
  }

// create email headers
$headers = 'From: '.$email_from."\r\n";
$headers .= 'Reply-to: '.$email_from."\r\n";
$headers .= 'Content-type: text/html'."\r\n";
// $headers .= 'Content-Disposition: attachment';
// $headers .= 'filename="example.txt"';

// 제목이 깨질경우 아래 캐릭터셋 적용

mekeng_form_send_html_mail($email_to, $email_subject, $email_message, $email_from);

// 新闻订阅处理（个人信息同意时自动订阅）
if (isset($_POST['agree']) && $_POST['agree'] == 'on') {
    // 邮件日志记录函数（与subscribe/index.php相同）
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
    
    // 邮箱重复检查及订阅处理
    $subscribe = sql_fetch("SELECT sb_id, sb_email, sb_subscribe FROM g5_subscribe WHERE sb_email = '".sql_escape_string($email_from)."'");
    
    if ($subscribe) {
        if ($subscribe['sb_subscribe'] != 1) {
            // 更新订阅状态
            sql_query("UPDATE g5_subscribe SET sb_subscribe = 1, sb_updatedate = '".G5_TIME_YMDHIS."' WHERE sb_id = '{$subscribe['sb_id']}'");
            log_mail_subscribe('subscribe', $email_from, $is_member ? $member['mb_id'] : null, true);
        }
    } else {
        // 添加新订阅者
        sql_query("INSERT INTO g5_subscribe (sb_email, sb_subscribe, sb_regdate, sb_updatedate) 
                  VALUES ('".sql_escape_string($email_from)."', 1, '".G5_TIME_YMDHIS."', '".G5_TIME_YMDHIS."')");
        log_mail_subscribe('subscribe', $email_from, $is_member ? $member['mb_id'] : null, true);
    }
    
    // 会员情况下更新mb_mailling
    if ($is_member) {
        sql_query("UPDATE {$g5['member_table']} SET mb_mailling = 1 WHERE mb_id = '{$member['mb_id']}'");
    }
}
?>

<!-- include your own success html here -->

<script>
alert ("感谢您的咨询。\n我们会在最短时间内回复您。");
location.href='/cn/sub04_03.php';
</script>

<?php
}
?>
