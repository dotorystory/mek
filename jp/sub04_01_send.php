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
   echo "<script> alert('メール送信に失敗しました。');";
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
      !isset($_POST['area']) ||
      !isset($_POST['thickness']) ||
      !isset($_POST['width']) ||
      !isset($_POST['lineSpeed']) ||
      !isset($_POST['requests']) ||
      !isset($_POST['comments']) ||
      !isset($_POST['agree'])) {
      died('申し訳ございません。\n提出されたフォームに問題があります。\nフォームを再度ご確認ください。');
  }

  $title = $_POST['title']; // required
  $group = isset($_POST['group']) ? implode(', ', $_POST['group']) : ''; // 선택항목
  $place = $_POST['place']; // required
  $name = $_POST['name']; // required
  $email_from = $_POST['email']; // required
  $phone = $_POST['phone']; // required
  $component = $_POST['component']; // required
  $color = $_POST['color'];
  $area = $_POST['area'];
  $thickness = $_POST['thickness'];
  $width = $_POST['width'];
  $lineSpeed = $_POST['lineSpeed'];
  $requests = $_POST['requests'];
  $comments = $_POST['comments']; // required
  // $file_name = $file['name'];
  $file_url = G5_DATA_URL . "/mail/" . $filename; // 파일 경로

  $email_to = "sales@webmail.mekeng.com";

  $email_subject = "[表面欠陥検査機、ウェブクリーナー、ピニングシステム、その他 製品お問い合わせ]";
  $email_subject = '=?UTF-8?B?'.base64_encode($email_subject).'?=';

  function clean_string($string) {
    $bad = array("content-type","bcc:","to:","cc:","href");
    return str_replace($bad,"",$string);
  }

  $email_message .= "会社名 : ".clean_string($title)."<br><br>";
  $email_message .= "問い合わせ設備 : ".(!empty($group) ? clean_string($group) : 'なし')."<br><br>"; //선택항목
  $email_message .= "設置地域（または国） : ".clean_string($place)."<br><br>";
  $email_message .= "メール : ".clean_string($email_from)."<br><br>";
  $email_message .= "氏名（担当者名） : ".clean_string($name)."<br><br>";
  $email_message .= "電話番号 : ".clean_string($phone)."<br><br>";
  $email_message .= "製品成分 : ".clean_string($component)."<br><br>";
  $email_message .= "色と透明度 : ".clean_string($color)."<br><br>";
  $email_message .= "適用面 : ".clean_string($area)."<br><br>";
  $email_message .= "厚さ : ".clean_string($thickness)."<br><br>";
  $email_message .= "幅 : ".clean_string($width)."<br><br>";
  $email_message .= "ライン速度 : ".clean_string($lineSpeed)."<br><br>";
  $email_message .= "要求の削除＆検出対象 : ".clean_string($requests)."<br><br>";
  $email_message .= "内容 : ".clean_string(nl2br($comments))."<br><br>";

  if(!empty($filename) && file_exists($target_file)) {
      $email_message .= "添付ファイル : <a target='_blank' href='" . $file_url . "' download>".$filename."</a><br><br>";
  } else {
      $email_message .= "添付ファイル : なし<br><br>";
  }

// create email headers
$headers = 'From: '.$email_from."\r\n";
$headers .= 'Reply-to: '.$email_from."\r\n";
$headers .= 'Content-type: text/html'."\r\n";
// $headers .= 'Content-Disposition: attachment';
// $headers .= 'filename="example.txt"';

// 제목이 깨질경우 아래 캐릭터셋 적용

@mail($email_to, $email_subject, $email_message, $headers);

// ニュースレター購読処理（個人情報同意時に自動購読）
if (isset($_POST['agree']) && $_POST['agree'] == 'on') {
    // メールログ記録関数（subscribe/index.phpと同一）
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
    
    // メール重複チェック及び購読処理
    $subscribe = sql_fetch("SELECT sb_id, sb_email, sb_subscribe FROM g5_subscribe WHERE sb_email = '".sql_escape_string($email_from)."'");
    
    if ($subscribe) {
        if ($subscribe['sb_subscribe'] != 1) {
            // 購読状態更新
            sql_query("UPDATE g5_subscribe SET sb_subscribe = 1, sb_updatedate = '".G5_TIME_YMDHIS."' WHERE sb_id = '{$subscribe['sb_id']}'");
            log_mail_subscribe('subscribe', $email_from, $is_member ? $member['mb_id'] : null, true);
        }
    } else {
        // 新規購読者追加
        sql_query("INSERT INTO g5_subscribe (sb_email, sb_subscribe, sb_regdate, sb_updatedate) 
                  VALUES ('".sql_escape_string($email_from)."', 1, '".G5_TIME_YMDHIS."', '".G5_TIME_YMDHIS."')");
        log_mail_subscribe('subscribe', $email_from, $is_member ? $member['mb_id'] : null, true);
    }
    
    // 会員の場合mb_mailling更新
    if ($is_member) {
        sql_query("UPDATE {$g5['member_table']} SET mb_mailling = 1 WHERE mb_id = '{$member['mb_id']}'");
    }
}
?>

<!-- include your own success html here -->

<script>
alert ("お問い合わせありがとうございます。\nできるだけ早く回答いたします。");
location.href='/jp/sub04_01.php';
</script>

<?php
}
?>
