<?php
// 한국 카탈로그 QR 코드 리다이렉트
// UTM 파라미터를 GET 방식으로 받아 변수로 저장
$utm_source = isset($_GET['utm_source']) ? $_GET['utm_source'] : "kr_catalog_qr";
$utm_medium = isset($_GET['utm_medium']) ? $_GET['utm_medium'] : "qr_code";
$utm_campaign = isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] : "korea_catalog";

// 사용자 에이전트로 디바이스 구분
$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
$device = "desktop"; // 기본값: 데스크탑

if (strpos($userAgent, 'android') !== false) {
    $device = "android";
} elseif (strpos($userAgent, 'iphone') !== false || strpos($userAgent, 'ipad') !== false) {
    $device = "ios";
} elseif (strpos($userAgent, 'mobile') !== false) {
    $device = "mobile";
}

// 접속 국가 감지 (여러 방법 사용)
$country = "unknown";

// 1. CloudFlare의 CF-IPCountry 헤더 확인 (CloudFlare 사용 시)
if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
    $country = strtolower($_SERVER['HTTP_CF_IPCOUNTRY']);
}
// 2. Accept-Language 헤더로 언어 선호도 확인
elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $acceptLanguage = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    
    // 언어 우선순위대로 확인
    if (strpos($acceptLanguage, 'ja') !== false) {
        $country = "jp";
    } elseif (strpos($acceptLanguage, 'ko') !== false) {
        $country = "kr";
    } elseif (strpos($acceptLanguage, 'zh') !== false || strpos($acceptLanguage, 'cn') !== false) {
        $country = "cn";
    } else {
        $country = "en"; // 기타 언어는 영어로
    }
}

// 국가별 리다이렉트 경로 설정
$langPath = "/home"; // 기본값: 한국 (한국 카탈로그이므로)

if ($country === "kr" || $country === "korea") {
    $langPath = "/home";
} elseif ($country === "jp" || $country === "japan") {
    $langPath = "/jp";
} elseif ($country === "cn" || $country === "china") {
    $langPath = "/cn";
} elseif ($country === "en" || !in_array($country, ["kr", "jp", "cn", "korea", "japan", "china"])) {
    $langPath = "/en";
}

// Google Analytics용 파라미터 생성 (표준 UTM 파라미터 사용)
$gaParams = http_build_query([
    "utm_source" => $utm_source,
    "utm_medium" => $utm_medium,
    "utm_campaign" => $utm_campaign,
    "utm_content" => $device,        // 디바이스 정보
    "utm_term" => $country           // 국가 정보
]);

// 최종 리다이렉트 URL 생성
$redirectUrl = "https://www.mekeng.com" . $langPath . "?" . $gaParams;

// 리다이렉트 수행
header("Location: $redirectUrl");
exit;
?>