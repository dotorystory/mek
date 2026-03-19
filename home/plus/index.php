<?php
include_once('./_common.php');

if (!defined('_GNUBOARD_')) exit;

// 레벨 3 미만이고 로그인하지 않은 경우 로그인 페이지로 이동
if (!$is_member || (!isset($member['mb_level']) || $member['mb_level'] < 2)) {
    if (!$is_member) {
        $return_url = urlencode(G5_URL.'/plus/');
        goto_url(G5_BBS_URL.'/login.php?url='.$return_url);
        exit;
    }
}

$g5['title'] = 'MEK+ 홈';
$contact_css = G5_URL.'/css/contact.css';
add_stylesheet('<link rel="stylesheet" href="'.$contact_css.'">', 0);

$menuCodeParent = 1;
$menuCodeChild = 5;
include_once(G5_PATH.'/head_simple.php');

// simple_inquiry.php 포함 (팝업 문의창)
include_once(G5_PATH.'/simple_inquiry.php');
?>

<div class="mail-sender-container">
    <h1 class="sound_only">MEK+ 홈</h1>
    <br>

    <div class="plus-menu-section">
        <h2>MEK+ 홈</h2>
        
        <div class="plus-menu-grid">
            <?php if ($is_member && isset($member['mb_level']) && $member['mb_level'] >= 3) { ?>
            <div class="plus-menu-item">
                <div class="plus-menu-icon">📁</div>
                <h3>파일 관리자</h3>
                <p>이미지/영상/문서 파일 업로드</p>
                <a href="<?php echo G5_URL; ?>/plus/upload_manager.php" class="plus-menu-btn">파일 매니저</a>
            </div>
            <?php } ?>

            <?php if ($is_member && isset($member['mb_level']) && $member['mb_level'] >= 3) { ?>
            <div class="plus-menu-item">
                <div class="plus-menu-icon">📋</div>
                <h3>영업팀 게시판</h3>
                <p>MEK 기술영업팀 전용 게시판</p>
                <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=team" class="plus-menu-btn">게시판 바로가기</a>
            </div>
            <?php } ?>

            <?php if ($is_member && isset($member['mb_level']) && $member['mb_level'] >= 4) { ?>
            <div class="plus-menu-item">
                <div class="plus-menu-icon">📧</div>
                <h3>MEK+ Mailer</h3>
                <p>SMTP 설정 및 뉴스레터 발송 관리</p>
                <a href="./mailer" class="plus-menu-btn">메일 관리자</a>
            </div>
            <?php } ?>
            
            <?php if ($is_member && isset($member['mb_level']) && $member['mb_level'] >= 9) { ?>
            <div class="plus-menu-item">
                <div class="plus-menu-icon">⚠</div>
                <h3>품질 검사</h3>
                <p>품질 검사표 작성 및 관리</p>
                <a href="<?php echo G5_URL; ?>/plus/quality/" class="plus-menu-btn">품질 검사</a>
            </div>
            <?php } ?>

            <!-- 담당자 등록 영역 -->
            <?php if ($is_member && isset($member['mb_level']) && $member['mb_level'] >= 3) { ?>
            <div class="plus-menu-item">
                <div class="plus-menu-icon" style="display: flex; justify-content: center; align-items: center;">
                    <?php 
                    $profile_img = get_member_profile_img($member['mb_id'], '80', '80');
                    if ($profile_img) {
                        echo $profile_img;
                    } else {
                        echo '<div style="width: 80px; height: 80px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; font-size: 40px;">👤</div>';
                    }
                    ?>
                </div>
                <h3><?php echo htmlspecialchars($member['mb_id']); ?></h3>
                <p><?php echo htmlspecialchars($member['mb_name']); ?> | 포인트: <?php echo number_format($member['mb_point']); ?>P</p>
                <div style="display: flex; gap: 10px; justify-content: center; margin-top: 15px; flex-wrap: wrap;">
                    <?php if ($is_admin == 'super') { ?>
                    <a href="<?php echo G5_ADMIN_URL; ?>" target="_blank" class="plus-menu-btn" style="background: #007bff; padding: 10px 20px; font-size: 14px;">⚙️ 설정</a>
                    <?php } else { ?>
                    <button type="button" onclick="openSimpleInquiry()" class="plus-menu-btn" style="background: #007bff; padding: 10px 20px; font-size: 14px; border: none; cursor: pointer;">권한 문의</button>
                    <?php } ?>
                    <a href="<?php echo G5_BBS_URL; ?>/logout.php" class="plus-menu-btn" style="background: #777; padding: 10px 20px; font-size: 14px;">로그아웃</a>
                </div>
            </div>
            <?php } ?>

        </div>
    </div>
</div>

<style>
.plus-menu-section {
    background: #fff;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}
.plus-menu-section h2 {
    margin-top: 0;
    margin-bottom: 30px;
    color: #333;
    border-bottom: 2px solid #aaa;
    padding-bottom: 15px;
    font-size: 24px;
}
.plus-menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-top: 20px;
}
.plus-menu-item {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
}
.plus-menu-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-color: #555;
}
.plus-menu-icon {
    font-size: 48px;
    margin-bottom: 15px;
}
.plus-menu-icon img {
    border-radius: 50%;
    width: 80px;
    height: 80px;
    object-fit: cover;
}
.plus-menu-item h3 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 20px;
}
.plus-menu-item p {
    margin: 0 0 20px 0;
    color: #666;
    font-size: 14px;
    line-height: 1.5;
}
.plus-menu-btn {
    display: inline-block;
    padding: 12px 30px;
    /* background: #863a86; */
    background: #555;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}
.plus-menu-btn:hover {
    background: #863a86;
    transform: scale(1.05);
    color: #fff;
    text-decoration: none;
}
.plus-menu-btn:active {
    transform: scale(0.98);
}

@media (max-width: 768px) {
    .plus-menu-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    .plus-menu-section {
        padding: 20px;
    }
    .plus-menu-item {
        padding: 20px;
    }
}
</style>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

