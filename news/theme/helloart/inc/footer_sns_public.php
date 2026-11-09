<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

/**
 * 푸터 SNS 링크 (데스크탑 tail 영역용).
 * 실제 채널 URL은 아래 배열을 수정하세요. 빈 문자열이면 해당 항목은 링크 없이 아이콘만 표시합니다.
 */
$ft_sns_urls = array(
    'naver' => '',
    'kakao' => '',
    'facebook' => '',
    'youtube' => '',
);

$ft_sns_item = function ($key, $label, $href, $inner_html) {
    $label_esc = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
    if ($href !== '') {
        $u = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
        return '<li class="ft_sns_item"><a href="'.$u.'" class="ft_sns_link" target="_blank" rel="noopener noreferrer" title="'.$label_esc.'">'.$inner_html.'<span class="sound_only">'.$label_esc.'</span></a></li>';
    }
    return '<li class="ft_sns_item"><span class="ft_sns_link ft_sns_link--static" title="'.$label_esc.'">'.$inner_html.'<span class="sound_only">'.$label_esc.' (링크 미설정)</span></span></li>';
};

$ico_naver = '<span class="ft_sns_svg ft_sns_svg--naver" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30"><rect width="24" height="24" rx="5" fill="#03C75A"/><path fill="#fff" d="M6 5.5h3.2L13 13.7V5.5h2.8V18.5h-3L9 10.3v8.2H6V5.5z"/></svg></span>';
$ico_kakao = '<span class="ft_sns_svg ft_sns_svg--kakao" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30"><rect width="24" height="24" rx="5" fill="#F2DA00"/><text x="12" y="12" dominant-baseline="central" text-anchor="middle" font-family="system-ui, -apple-system, \'Segoe UI\', \'Malgun Gothic\', sans-serif" font-weight="700" font-size="12.5" fill="#191919">K</text></svg></span>';
$ico_fb = '<span class="ft_sns_svg ft_sns_svg--facebook" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30"><rect width="24" height="24" rx="5" fill="#1877F2"/><path fill="#fff" d="M14.2 5.5h-1.8c-2 0-3.3 1.2-3.3 3.4v1.6H7v2.7h2.1V22h3.2v-8.8h2.6l.5-2.7h-3.1V9.2c0-.8.2-1.3 1.3-1.3h1.6V5.5z"/></svg></span>';
$ico_yt = '<span class="ft_sns_svg ft_sns_svg--youtube" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30"><rect width="24" height="24" rx="5" fill="#FF0000"/><path fill="#fff" d="M10.2 8.8v6.4L16 12l-5.8-3.2z"/></svg></span>';
?>
<section id="ft_sns_public" class="ft_cnt ft_sns_public" aria-label="소셜 채널">
    <h2 class="sound_only">소셜 채널</h2>
    <ul class="ft_sns_list">
        <?php
        echo $ft_sns_item('naver', '네이버', isset($ft_sns_urls['naver']) ? $ft_sns_urls['naver'] : '', $ico_naver);
        echo $ft_sns_item('kakao', '카카오톡', isset($ft_sns_urls['kakao']) ? $ft_sns_urls['kakao'] : '', $ico_kakao);
        echo $ft_sns_item('facebook', '페이스북', isset($ft_sns_urls['facebook']) ? $ft_sns_urls['facebook'] : '', $ico_fb);
        echo $ft_sns_item('youtube', '유튜브', isset($ft_sns_urls['youtube']) ? $ft_sns_urls['youtube'] : '', $ico_yt);
        ?>
    </ul>
</section>
