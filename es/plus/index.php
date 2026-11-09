<?php
include_once('./_common.php');

if (!defined('_GNUBOARD_')) exit;

// 관리자 및 회원등급 5등급 이상 체크
if (!$is_admin && $member['mb_level'] < 5) {
    // 로그인한 회원이지만 5등급 미만인 경우 구독 페이지로 이동
    if ($is_member) {
        goto_url(G5_URL.'/plus/subscribe/');
        exit;
    }
    // 로그인하지 않은 경우 로그인 페이지로 이동
    $return_url = urlencode(G5_URL.'/plus/');
    goto_url(G5_BBS_URL.'/login.php?url='.$return_url);
    exit;
}

$g5['title'] = 'MEK+ 홈';
$contact_css = G5_URL.'/css/contact.css';
add_stylesheet('<link rel="stylesheet" href="'.$contact_css.'">', 0);

$menuCodeParent = 1;
$menuCodeChild = 5;
include_once(G5_PATH.'/head.php');
?>

<div class="mail-sender-container">
    <h1 class="sound_only">MEK+ 홈</h1>
    <br>

    <div class="plus-menu-section">
        <h2>MEK+ 홈</h2>
        
        <div class="plus-menu-grid">
            <div class="plus-menu-item">
                <div class="plus-menu-icon">📧</div>
                <h3>MEK+ Mailer</h3>
                <p>SMTP 설정 및 뉴스레터 발송 관리</p>
                <a href="./mailer" class="plus-menu-btn">메일 발송 관리</a>
            </div>

            <div class="plus-menu-item">
                <div class="plus-menu-icon">⚙️</div>
                <h3>Administration</h3>
                <p>그누보드 관리자 페이지로 이동</p>
                <a href="<?php echo G5_ADMIN_URL; ?>" class="plus-menu-btn" target="_blank" style="background: #888;">관리자 페이지</a>
            </div>
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
    border-bottom: 2px solid #007bff;
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
    border-color: #007bff;
}
.plus-menu-icon {
    font-size: 48px;
    margin-bottom: 15px;
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
    background: #007bff;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}
.plus-menu-btn:hover {
    background: #0056b3;
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
include_once(G5_PATH.'/tail.php');
?>

