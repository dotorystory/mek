<meta charset="utf-8">
<?php
include_once('./_common.php');
include_once(G5_PATH.'/plus/mail_sender.php');

if(isset($_POST['email'])) {

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
      !isset($_POST['place']) ||
      !isset($_POST['name']) ||
      !isset($_POST['email']) ||
      !isset($_POST['phone']) ||
      !isset($_POST['component']) ||
      !isset($_POST['color']) ||
      !isset($_POST['process']) ||
      !isset($_POST['thickness']) ||
      !isset($_POST['width']) ||
      !isset($_POST['lineSpeed']) ||
      !isset($_POST['comments']) ||
      !isset($_POST['agree'])) {
      died('抱歉，\n您提交的表格有问题。\n请重新检查表格。');
  }

  $title = $_POST['title']; // required
  $group = isset($_POST['group']) ? implode(', ', $_POST['group']) : ''; // 선택항목
  $place = $_POST['place']; // required
  $name = $_POST['name']; // required
  $email_from = $_POST['email']; // required
  $phone = $_POST['phone'];
  $component = $_POST['component']; // required
  $color = $_POST['color'];
  $process = $_POST['process'];
  $thickness = $_POST['thickness'];
  $width = $_POST['width'];
  $lineSpeed = $_POST['lineSpeed'];
  $apc = isset($_POST['apc']) ? implode(', ', $_POST['apc']) : ''; // 선택항목
  $lineStatus = isset($_POST['lineStatus']) ? implode(', ', $_POST['lineStatus']) : ''; // 선택항목
  $requests = $_POST['requests'];
  $comments = $_POST['comments']; // required
  // $file_name = $file['name'];
  $file_url = G5_DATA_URL . "/mail/" . $filename; // 파일 경로

  $email_to = "sales@webmail.mekeng.com";

  $email_subject = "[厚度测量仪 产品咨询]";
  $email_subject = '=?UTF-8?B?'.base64_encode($email_subject).'?=';

  function clean_string($string) {
    $bad = array("content-type","bcc:","to:","cc:","href");
    return str_replace($bad,"",$string);
  }

  $email_message .= "公司名 : ".clean_string($title)."<br><br>";
  $email_message .= "查询设备 : ".(!empty($group) ? clean_string($group) : '无')."<br><br>"; //선택항목
  $email_message .= "安装地区（或国家） : ".clean_string($place)."<br><br>";
  $email_message .= "电子邮件 : ".clean_string($email_from)."<br><br>";
  $email_message .= "姓名（负责人姓名） : ".clean_string($name)."<br><br>";
  $email_message .= "电话号码 : ".clean_string($phone)."<br><br>";
  $email_message .= "产品成分 : ".clean_string($component)."<br><br>";
  $email_message .= "颜色和透明度 : ".clean_string($color)."<br><br>";
  $email_message .= "适用工序 : ".clean_string($process)."<br><br>";
  $email_message .= "测量厚度 : ".clean_string($thickness)."<br><br>";
  $email_message .= "测量宽度 : ".clean_string($width)."<br><br>";
  $email_message .= "线速度 : ".clean_string($lineSpeed)."<br><br>";
  $email_message .= "APC应用可否 : ".(!empty($apc) ? clean_string($apc) : '无')."<br><br>"; //선택항목
  $email_message .= "线状态 : ".(!empty($lineStatus) ? clean_string($lineStatus) : '无')."<br><br>"; //선택항목
  $email_message .= "内容 : ".clean_string(nl2br($comments))."<br><br>";

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

@mail($email_to, $email_subject, $email_message, $headers);

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
location.href='/cn/sub04_02.php';
</script>

<?php
}
?>
