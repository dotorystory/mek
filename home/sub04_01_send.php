<meta charset="utf-8">
<?php
include_once('./_common.php');
include_once(G5_PATH.'/plus/mail_sender.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

if(isset($_POST['email'])) {

  if (!chk_captcha()) {
    echo "<script> alert('자동등록방지 숫자가 틀렸습니다.');";
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
   echo "<script> alert('메일전송이 실패하였습니다.');";
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
      died('죄송합니다.\n제출하신 양식에  문제가 있습니다.\n양식을 다시 확인해주세요.');
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

  $email_to = "msk@mekeng.com";

  $email_subject = "[표면결함검사기, 웹크리너, 피닝시스템, 기타 제품문의]";
  $email_subject = '=?UTF-8?B?'.base64_encode($email_subject).'?=';

  function clean_string($string) {
    $bad = array("content-type","bcc:","to:","cc:","href");
    return str_replace($bad,"",$string);
  }

  $email_message .= "업체명 : ".clean_string($title)."<br><br>";
  $email_message .= "문의 설비 : ".(!empty($group) ? clean_string($group) : '없음')."<br><br>"; //선택항목
  $email_message .= "설치 지역(또는 국가) : ".clean_string($place)."<br><br>";
  $email_message .= "이메일 : ".clean_string($email_from)."<br><br>";
  $email_message .= "성명(담당자명) : ".clean_string($name)."<br><br>";
  $email_message .= "전화번호 : ".clean_string($phone)."<br><br>";
  $email_message .= "제품 성분 : ".clean_string($component)."<br><br>";
  $email_message .= "색상 및 투명도 : ".clean_string($color)."<br><br>";
  $email_message .= "적용 면 : ".clean_string($area)."<br><br>";
  $email_message .= "두께 : ".clean_string($thickness)."<br><br>";
  $email_message .= "폭 : ".clean_string($width)."<br><br>";
  $email_message .= "라인 속도 : ".clean_string($lineSpeed)."<br><br>";
  $email_message .= "요구 제거 & 검출 대상 : ".clean_string($requests)."<br><br>";
  $email_message .= "내용 : ".clean_string(nl2br($comments))."<br><br>";

  if(!empty($filename) && file_exists($target_file)) {
      $email_message .= "첨부파일 : <a target='_blank' href='" . $file_url . "' download>".$filename."</a><br><br>";
  } else {
      $email_message .= "첨부파일 : 없음<br><br>";
  }

// create email headers
$headers = 'From: '.$email_from."\r\n";
$headers .= 'Reply-to: '.$email_from."\r\n";
$headers .= 'Content-type: text/html'."\r\n";
// $headers .= 'Content-Disposition: attachment';
// $headers .= 'filename="example.txt"';

// 제목이 깨질경우 아래 캐릭터셋 적용

mekeng_form_send_html_mail($email_to, $email_subject, $email_message, $email_from);

// 뉴스레터 구독 처리 (개인정보 동의 시 자동 구독)
if (isset($_POST['agree']) && $_POST['agree'] == 'on') {
    // 메일 로그 기록 함수 (subscribe/index.php와 동일)
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
    
    // 이메일 중복 체크 및 구독 처리
    $subscribe = sql_fetch("SELECT sb_id, sb_email, sb_subscribe FROM g5_subscribe WHERE sb_email = '".sql_escape_string($email_from)."'");
    
    if ($subscribe) {
        if ($subscribe['sb_subscribe'] != 1) {
            // 구독 상태 업데이트
            sql_query("UPDATE g5_subscribe SET sb_subscribe = 1, sb_updatedate = '".G5_TIME_YMDHIS."' WHERE sb_id = '{$subscribe['sb_id']}'");
            log_mail_subscribe('subscribe', $email_from, $is_member ? $member['mb_id'] : null, true);
        }
    } else {
        // 새로운 구독자 추가
        sql_query("INSERT INTO g5_subscribe (sb_email, sb_subscribe, sb_regdate, sb_updatedate) 
                  VALUES ('".sql_escape_string($email_from)."', 1, '".G5_TIME_YMDHIS."', '".G5_TIME_YMDHIS."')");
        log_mail_subscribe('subscribe', $email_from, $is_member ? $member['mb_id'] : null, true);
    }
    
    // 회원인 경우 mb_mailling 업데이트
    if ($is_member) {
        sql_query("UPDATE {$g5['member_table']} SET mb_mailling = 1 WHERE mb_id = '{$member['mb_id']}'");
    }
}
?>

<!-- include your own success html here -->

<script>
alert ("문의주셔서 감사합니다.\n빠른 시일안에 답변드리겠습니다.");
location.href='/home/sub04_01.php';
</script>

<?php
}
?>
