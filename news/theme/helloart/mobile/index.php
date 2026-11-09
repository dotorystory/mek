<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_MSHOP_PATH.'/index.php');
    return;
}

include_once(G5_THEME_MOBILE_PATH.'/head.php');
?>

<section class="ha-hero">
    <div class="ha-hero__inner">
        <div>
            <span class="ha-eyebrow">MEK News</span>
            <h2>산업·측정·공정의 이슈를<br>기술 뉴스로 전합니다</h2>
            <p>MEK News는 웹공정·측정·산업설비·품질검사·스마트팩토리·자동화 관련 칼럼과 이슈를 전하는 산업·기술 미디어입니다.</p>
            <div class="ha-hero__actions">
                <a class="ha-btn-primary" href="<?php echo get_pretty_url('free'); ?>">최신 칼럼 보기</a>
                <a class="ha-btn-ghost" href="https://new.mekeng.com" target="_blank" rel="noopener noreferrer">MEK 측정기</a>
            </div>
        </div>
        <div class="ha-hero-card">
            <h3>MEK News에서 다루는 이야기</h3>
            <p>산업·기술 칼럼과 제조·스마트팩토리 이슈, 공정·설비·측정 관련 영상을 발행합니다.</p>
            <ul class="ha-stats">
                <li><strong>칼럼</strong><span>공정 · 측정</span></li>
                <li><strong>현장</strong><span>설비 · 품질</span></li>
                <li><strong>트렌드</strong><span>스마트팩토리</span></li>
            </ul>
        </div>
    </div>
</section>


<h2 class="sound_only">최신글</h2>

<!-- 메인화면 최신글 시작 -->
<?php
$sql = " select bo_table
            from `{$g5['board_table']}` a left join `{$g5['group_table']}` b on (a.gr_id=b.gr_id)
            where a.bo_device <> 'pc' ";
if(!$is_admin) {
    $sql .= " and a.bo_use_cert = '' ";
}
$sql .= " and a.bo_table not in ('notice', 'gallery') ";
$sql .= " order by b.gr_order, a.bo_order ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    echo latest('theme/basic', $row['bo_table'], 12, 25);
}
?>
<!-- 메인화면 최신글 끝 -->

<div class="latest_wr_m gallery_section">
    <?php
    // trole/truerole 모바일 갤러리와 동일: theme/display_m
    echo latest('theme/display_m', 'gallery', 4, 30);
    ?>
</div>



<section class="ha-banner-row">
    <div class="ha-content-card">
        <h2>블로그의 역할</h2>
        <p>MEK 공식 블로그에서 제품 소식과 현장 이야기, 측정·공정 관련 정보를 이어 전합니다.</p>
        <a class="ha-banner-link" href="https://blog.naver.com/mekeng" target="_blank" rel="noopener noreferrer">MEK 블로그</a>
    </div>
    <div class="ha-content-card">
        <h2>최신 MEK 제품 소개</h2>
        <p>측정기·설비·자동화 관련 신제품과 기술 소개를 New 엠이케이에서 확인하세요.</p>
        <a class="ha-banner-link" href="https://new.mekeng.com" target="_blank" rel="noopener noreferrer">New 엠이케이</a>
    </div>
</section>

<?php
include_once(G5_THEME_MOBILE_PATH.'/tail.php');
