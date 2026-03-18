<?php
// Namecard QR code redirect (English)
// UTM parameters from GET or defaults for English namecard
$utm_source = isset($_GET['utm_source']) ? $_GET['utm_source'] : "en_namecard_qr";
$utm_medium = isset($_GET['utm_medium']) ? $_GET['utm_medium'] : "qr_code";
$utm_campaign = isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] : "namecard_en";

// Device detection from User-Agent
$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
$device = "desktop";

if (strpos($userAgent, 'android') !== false) {
    $device = "android";
} elseif (strpos($userAgent, 'iphone') !== false || strpos($userAgent, 'ipad') !== false) {
    $device = "ios";
} elseif (strpos($userAgent, 'mobile') !== false) {
    $device = "mobile";
}

// Country/locale detection (CF-IPCountry or Accept-Language)
$country = "unknown";

if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
    $country = strtolower($_SERVER['HTTP_CF_IPCOUNTRY']);
} elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $acceptLanguage = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    if (strpos($acceptLanguage, 'ko') !== false) {
        $country = "kr";
    } elseif (strpos($acceptLanguage, 'ja') !== false) {
        $country = "jp";
    } elseif (strpos($acceptLanguage, 'zh') !== false || strpos($acceptLanguage, 'cn') !== false) {
        $country = "cn";
    } else {
        $country = "en";
    }
}

// Language path by country (namecard QR: English default)
$langPath = "/en"; // default: English

if ($country === "kr" || $country === "korea") {
    $langPath = "/home";
} elseif ($country === "jp" || $country === "japan") {
    $langPath = "/jp";
} elseif ($country === "cn" || $country === "china") {
    $langPath = "/cn";
} elseif ($country === "en" || !in_array($country, ["kr", "jp", "cn", "korea", "japan", "china"])) {
    $langPath = "/en";
}

// GA UTM query string
$gaParams = http_build_query([
    "utm_source" => $utm_source,
    "utm_medium" => $utm_medium,
    "utm_campaign" => $utm_campaign,
    "utm_content" => $device,
    "utm_term" => $country
]);

$redirectUrl = "https://www.mekeng.com" . $langPath . "?" . $gaParams;
header("Location: $redirectUrl");
exit;
?>