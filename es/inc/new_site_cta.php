<?php
/**
 * new.mekeng.com 교차 안내 버튼 세트
 *
 * 사용:
 *   $mek_new_cta_type = 'applications'; // applications | products | support | company
 *   $mek_new_cta_key  = 'battery';      // 유형별 키 (없으면 기본 세트)
 *   include_once G5_PATH . '/inc/new_site_cta.php';
 *
 * 언어:
 *   jp 사이트 → ?lang=ja
 *   en/cn/de/es 사이트 → ?lang=en
 *   home(ko) → ?lang=ko
 */
if (!defined('_GNUBOARD_')) {
    return;
}

if (empty($mek_new_cta_type)) {
    return;
}

$mek_new_base = 'https://new.mekeng.com';

// 사이트 경로로 new.mekeng.com 표시 언어 결정
if (!isset($mek_new_cta_lang) || $mek_new_cta_lang === '') {
    $mek_path = defined('G5_PATH') ? str_replace('\\', '/', (string) G5_PATH) : '';
    if (preg_match('#/jp$#', $mek_path) || strpos($mek_path, '/jp/') !== false) {
        $mek_new_cta_lang = 'ja';
    } elseif (preg_match('#/(en|cn|de|es)$#', $mek_path) || preg_match('#/(en|cn|de|es)/#', $mek_path)) {
        $mek_new_cta_lang = 'en';
    } else {
        $mek_new_cta_lang = 'ko';
    }
}
$mek_new_cta_lang = in_array($mek_new_cta_lang, array('ko', 'en', 'ja'), true) ? $mek_new_cta_lang : 'en';

$mek_new_urls = array(
    'battery'     => $mek_new_base . '/pages/business/battery/',
    'electronic'  => $mek_new_base . '/pages/business/electronic/',
    'display'     => $mek_new_base . '/pages/business/display/',
    'metal'       => $mek_new_base . '/pages/business/metal/',
    'sheet'       => $mek_new_base . '/pages/business/sheet/',
    'film'        => $mek_new_base . '/pages/business/film/',
    'biaxial'     => $mek_new_base . '/pages/business/biaxial/',
    'calendar'    => $mek_new_base . '/pages/business/calendar/',
    'etc'         => $mek_new_base . '/pages/business/etc/',
    'gauge'       => $mek_new_base . '/gauge',
    'pinning'     => $mek_new_base . '/pinning',
    'cleaner'     => $mek_new_base . '/cleaner',
    'sensor'      => $mek_new_base . '/sensor',
    'partner'     => $mek_new_base . '/partner',
    'search'      => $mek_new_base . '/public/search/',
    'greeting'    => $mek_new_base . '/pages/company/greeting/',
    'history'     => $mek_new_base . '/pages/company/history/',
    'customer'    => $mek_new_base . '/customer',
    'cer'         => $mek_new_base . '/cer',
    'research'    => $mek_new_base . '/pages/company/research/',
    'philosophy'  => $mek_new_base . '/pages/company/philosophy/',
    'location'    => $mek_new_base . '/pages/company/location/',
    'notice'      => $mek_new_base . '/notice',
    'webzine'     => $mek_new_base . '/webzine',
    'faq'         => $mek_new_base . '/bbs/faq.php',
    'subscribe'   => $mek_new_base . '/public/subscribe/',
    'sitemap'     => $mek_new_base . '/public/sitemap/',
);

$mek_label_i18n = array(
    'ko' => array(
        'title_applications' => 'new.mekeng.com 적용분야 자세히 보기',
        'title_products'     => 'new.mekeng.com 제품정보 자세히 보기',
        'title_support'      => 'new.mekeng.com 고객지원 바로가기',
        'title_company'      => 'new.mekeng.com 회사소개 자세히 보기',
        'desc'               => '더 자세한 안내와 최신 정보는 신규 사이트에서 확인하실 수 있습니다.',
        '배터리' => '배터리', '전자 소재' => '전자 소재', '각종 시트' => '각종 시트', '기타' => '기타',
        '디스플레이' => '디스플레이', '캐스팅 필름' => '캐스팅 필름', '금속' => '금속',
        '카렌다 공정' => '카렌다 공정', '2축 연신 라인' => '2축 연신 라인',
        '두께 측정기' => '두께 측정기', '센서 종류' => '센서 종류', '피닝시스템' => '피닝시스템',
        '웹크리너' => '웹크리너', '파트너사 제품' => '파트너사 제품', '통합 검색' => '통합 검색',
        '공지사항' => '공지사항', '웹진' => '웹진', '자주하시는 질문' => '자주하시는 질문',
        '뉴스레터 구독' => '뉴스레터 구독', '인사말' => '인사말', '연혁' => '연혁',
        '글로벌 파트너' => '글로벌 파트너', '찾아오시는 길' => '찾아오시는 길',
        '특허 및 인증' => '특허 및 인증', '기업 연구소' => '기업 연구소', '경영 이념' => '경영 이념',
    ),
    'en' => array(
        'title_applications' => 'Explore Applications on new.mekeng.com',
        'title_products'     => 'Explore Products on new.mekeng.com',
        'title_support'      => 'Customer Support on new.mekeng.com',
        'title_company'      => 'About Us on new.mekeng.com',
        'desc'               => 'Visit the new site for more details and the latest information.',
        '배터리' => 'Battery', '전자 소재' => 'Electronic Materials', '각종 시트' => 'Sheets', '기타' => 'Others',
        '디스플레이' => 'Display', '캐스팅 필름' => 'Casting Film', '금속' => 'Metal',
        '카렌다 공정' => 'Calender Process', '2축 연신 라인' => 'Biaxial Stretching',
        '두께 측정기' => 'Thickness Gauge', '센서 종류' => 'Sensors', '피닝시스템' => 'Pinning System',
        '웹크리너' => 'Web Cleaner', '파트너사 제품' => 'Partner Products', '통합 검색' => 'Search',
        '공지사항' => 'Notice', '웹진' => 'Webzine', '자주하시는 질문' => 'FAQ',
        '뉴스레터 구독' => 'Newsletter', '인사말' => 'Greeting', '연혁' => 'History',
        '글로벌 파트너' => 'Global Partners', '찾아오시는 길' => 'Location',
        '특허 및 인증' => 'Patents & Certificates', '기업 연구소' => 'R&D Center', '경영 이념' => 'Philosophy',
    ),
    'ja' => array(
        'title_applications' => 'new.mekeng.com 適用分野を見る',
        'title_products'     => 'new.mekeng.com 製品情報を見る',
        'title_support'      => 'new.mekeng.com カスタマーサポート',
        'title_company'      => 'new.mekeng.com 会社紹介を見る',
        'desc'               => '詳細なご案内と最新情報は新サイトでご確認ください。',
        '배터리' => 'バッテリー', '전자 소재' => '電子材料', '각종 시트' => '各種シート', '기타' => 'その他',
        '디스플레이' => 'ディスプレイ', '캐스팅 필름' => 'キャスティングフィルム', '금속' => '金属',
        '카렌다 공정' => 'カレンダー工程', '2축 연신 라인' => '2軸延伸ライン',
        '두께 측정기' => '厚さ測定器', '센서 종류' => 'センサー', '피닝시스템' => 'ピニングシステム',
        '웹크리너' => 'ウェブクリーナー', '파트너사 제품' => 'パートナー製品', '통합 검색' => '統合検索',
        '공지사항' => 'お知らせ', '웹진' => 'ウェブジン', '자주하시는 질문' => 'よくある質問',
        '뉴스레터 구독' => 'ニュースレター', '인사말' => 'ご挨拶', '연혁' => '沿革',
        '글로벌 파트너' => 'グローバルパートナー', '찾아오시는 길' => 'アクセス',
        '특허 및 인증' => '特許・認証', '기업 연구소' => '企業研究所', '경영 이념' => '経営理念',
    ),
);

$L = isset($mek_label_i18n[$mek_new_cta_lang]) ? $mek_label_i18n[$mek_new_cta_lang] : $mek_label_i18n['en'];

$mek_new_cta_sets = array(
    'applications' => array(
        'title_key' => 'title_applications',
        'keys' => array(
            'battery' => array(
                array('배터리', 'battery'),
                array('전자 소재', 'electronic'),
                array('각종 시트', 'sheet'),
                array('기타', 'etc'),
            ),
            'electronic' => array(
                array('전자 소재', 'electronic'),
                array('디스플레이', 'display'),
                array('배터리', 'battery'),
                array('캐스팅 필름', 'film'),
            ),
            'display' => array(
                array('디스플레이', 'display'),
                array('전자 소재', 'electronic'),
                array('캐스팅 필름', 'film'),
                array('각종 시트', 'sheet'),
            ),
            'metal' => array(
                array('금속', 'metal'),
                array('카렌다 공정', 'calendar'),
                array('각종 시트', 'sheet'),
                array('기타', 'etc'),
            ),
            'packaging' => array(
                array('각종 시트', 'sheet'),
                array('캐스팅 필름', 'film'),
                array('2축 연신 라인', 'biaxial'),
                array('카렌다 공정', 'calendar'),
            ),
            'etc' => array(
                array('기타', 'etc'),
                array('카렌다 공정', 'calendar'),
                array('2축 연신 라인', 'biaxial'),
                array('각종 시트', 'sheet'),
            ),
            'default' => array(
                array('배터리', 'battery'),
                array('전자 소재', 'electronic'),
                array('디스플레이', 'display'),
                array('각종 시트', 'sheet'),
            ),
        ),
    ),
    'products' => array(
        'title_key' => 'title_products',
        'keys' => array(
            'pro_ko' => array(
                array('두께 측정기', 'gauge'),
                array('센서 종류', 'sensor'),
                array('피닝시스템', 'pinning'),
                array('웹크리너', 'cleaner'),
            ),
            'pro02_ko' => array(
                array('웹크리너', 'cleaner'),
                array('두께 측정기', 'gauge'),
                array('피닝시스템', 'pinning'),
                array('센서 종류', 'sensor'),
            ),
            'pro03_ko' => array(
                array('피닝시스템', 'pinning'),
                array('두께 측정기', 'gauge'),
                array('웹크리너', 'cleaner'),
                array('파트너사 제품', 'partner'),
            ),
            'pro04_ko' => array(
                array('파트너사 제품', 'partner'),
                array('두께 측정기', 'gauge'),
                array('웹크리너', 'cleaner'),
                array('통합 검색', 'search'),
            ),
            'default' => array(
                array('두께 측정기', 'gauge'),
                array('피닝시스템', 'pinning'),
                array('웹크리너', 'cleaner'),
                array('센서 종류', 'sensor'),
            ),
        ),
    ),
    'support' => array(
        'title_key' => 'title_support',
        'keys' => array(
            'default' => array(
                array('공지사항', 'notice'),
                array('웹진', 'webzine'),
                array('자주하시는 질문', 'faq'),
                array('뉴스레터 구독', 'subscribe'),
            ),
            'news' => array(
                array('공지사항', 'notice'),
                array('웹진', 'webzine'),
                array('자주하시는 질문', 'faq'),
                array('뉴스레터 구독', 'subscribe'),
            ),
            'subscribe' => array(
                array('뉴스레터 구독', 'subscribe'),
                array('공지사항', 'notice'),
                array('웹진', 'webzine'),
                array('자주하시는 질문', 'faq'),
            ),
        ),
    ),
    'company' => array(
        'title_key' => 'title_company',
        'keys' => array(
            'greeting' => array(
                array('인사말', 'greeting'),
                array('연혁', 'history'),
                array('글로벌 파트너', 'customer'),
                array('찾아오시는 길', 'location'),
            ),
            'cert' => array(
                array('특허 및 인증', 'cer'),
                array('인사말', 'greeting'),
                array('기업 연구소', 'research'),
                array('연혁', 'history'),
            ),
            'philosophy' => array(
                array('경영 이념', 'philosophy'),
                array('인사말', 'greeting'),
                array('연혁', 'history'),
                array('찾아오시는 길', 'location'),
            ),
            'research' => array(
                array('기업 연구소', 'research'),
                array('인사말', 'greeting'),
                array('특허 및 인증', 'cer'),
                array('연혁', 'history'),
            ),
            'default' => array(
                array('인사말', 'greeting'),
                array('연혁', 'history'),
                array('글로벌 파트너', 'customer'),
                array('찾아오시는 길', 'location'),
            ),
        ),
    ),
);

$type = $mek_new_cta_type;
$key = isset($mek_new_cta_key) ? $mek_new_cta_key : 'default';
// 제품 게시판 키 정규화: pro_en / pro02_jp → pro_ko / pro02_ko
if ($type === 'products' && is_string($key) && preg_match('/^(pro(?:0[2-4])?)_(ko|en|jp|cn)$/', $key, $km)) {
    $key = $km[1] . '_ko';
}

if (!isset($mek_new_cta_sets[$type])) {
    return;
}

$set = $mek_new_cta_sets[$type];
$items = isset($set['keys'][$key]) ? $set['keys'][$key] : $set['keys']['default'];
$title_key = isset($set['title_key']) ? $set['title_key'] : 'title_applications';
$title = isset($L[$title_key]) ? $L[$title_key] : $title_key;
$desc = isset($L['desc']) ? $L['desc'] : '';

static $mek_new_cta_style_printed = false;
?>
<?php if (!$mek_new_cta_style_printed) { $mek_new_cta_style_printed = true; ?>
<style>
.mek-new-cta {
    margin: 48px auto 24px;
    max-width: 1440px;
    width: 100%;
    box-sizing: border-box;
    padding: 28px 24px;
    border: 1px solid #e2e8f0;
    background: #f7f9fc;
    border-radius: 8px;
}
.mek-new-cta__title {
    margin: 0 0 8px;
    font-size: 18px;
    font-weight: 700;
    color: #12325c;
    letter-spacing: -0.02em;
}
.mek-new-cta__desc {
    margin: 0 0 18px;
    font-size: 14px;
    color: #5a5a5a;
    line-height: 1.5;
}
.mek-new-cta__links {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.mek-new-cta__links a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 10px 16px;
    border: 1px solid #1a4691;
    border-radius: 4px;
    background: #fff;
    color: #1a4691;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: background .2s ease, color .2s ease;
}
.mek-new-cta__links a:hover {
    background: #1a4691;
    color: #fff;
}
@media (max-width: 768px) {
    .mek-new-cta { margin: 32px auto 16px; padding: 20px 16px; }
    .mek-new-cta__links a { flex: 1 1 calc(50% - 10px); text-align: center; }
}
</style>
<?php } ?>
<section class="mek-new-cta" aria-label="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
    <h3 class="mek-new-cta__title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h3>
    <p class="mek-new-cta__desc"><?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'); ?></p>
    <div class="mek-new-cta__links">
        <?php foreach ($items as $item) {
            $label_key = $item[0];
            $url_key = $item[1];
            if (empty($mek_new_urls[$url_key])) {
                continue;
            }
            $label = isset($L[$label_key]) ? $L[$label_key] : $label_key;
            $href = $mek_new_urls[$url_key];
            $href .= (strpos($href, '?') === false ? '?' : '&') . 'lang=' . rawurlencode($mek_new_cta_lang);
            ?>
            <a href="<?php echo htmlspecialchars($href, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
            </a>
        <?php } ?>
    </div>
</section>
<?php
unset($mek_new_cta_type, $mek_new_cta_key, $mek_new_cta_lang);
?>
