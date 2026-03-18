<?php
include_once('./_common.php');

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// _common.php에서 이미 권한 체크를 했으므로 여기서는 추가 체크 불필요
// (로그인하지 않은 사용자는 _common.php에서 로그인 페이지로 리다이렉트됨)

$g5['title'] = '품질 검사표 시스템';
add_stylesheet('<link rel="stylesheet" href="'.G5_QUALITY_URL.'/common_style.css">', 0);

$menuCodeParent = 1;
$menuCodeChild = 5;
include_once(G5_PATH.'/head_simple.php');
?>

<div class="container" style="max-width: 1200px; margin: 30px auto; padding: 20px;">
    <h2 style="margin-bottom: 30px;">품질 검사표 시스템</h2>
    
    <div class="quality-menu" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
        <!-- 검사자 메뉴 (레벨 3 이상) -->
        <div class="menu-item" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center;">
            <h3>검사표 작성</h3>
            <p style="margin: 15px 0;">품질 검사표를 작성합니다.</p>
            <a href="./inspection.php" class="btn btn-info">검사 시작</a>
        </div>
        
        <?php if ($member['mb_level'] >= 4) { ?>
        <!-- 검수자 메뉴 (레벨 4 이상) -->
        <div class="menu-item" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center;">
            <h3>검수 대기</h3>
            <p style="margin: 15px 0;">검수 대기 중인 검사표를 확인합니다.</p>
            <a href="./review.php" class="btn btn-warning">검수하기</a>
        </div>
        <?php } ?>
        
        <?php if ($member['mb_level'] >= 5) { ?>
        <!-- 최종결제자 메뉴 (레벨 5 이상) -->
        <div class="menu-item" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center;">
            <h3>최종결제</h3>
            <p style="margin: 15px 0;">최종결제 대기 중인 검사표를 확인합니다.</p>
            <a href="./approval.php" class="btn btn-purple">결제하기</a>
        </div>
        <?php } ?>     
        
        <!-- 결과 조회 메뉴 -->
        <div class="menu-item" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center;">
            <h3>검사 결과 조회</h3>
            <p style="margin: 15px 0;">작성한 검사 결과를 조회합니다.</p>
            <a href="./result_list.php" class="btn btn-primary">결과 보기</a>
        </div>

        <?php if ($member['mb_level'] >= 5) { ?>
        <!-- 템플릿 업로드 메뉴 (레벨 5 이상) -->            
        <div class="menu-item" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center;">
            <h3>템플릿 업로드</h3>
            <p style="margin: 15px 0;">엑셀/CSV 파일로 템플릿을 업로드합니다.</p>
            <a href="./template_upload.php" class="btn btn-success">템플릿 업로드</a>
        </div>
        <?php } ?>
        
        <?php if ($member['mb_level'] >= 3) { ?>
        <!-- 템플릿 관리 메뉴 (레벨 3 이상) -->
        <div class="menu-item" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center;">
            <h3>템플릿 관리</h3>
            <p style="margin: 15px 0;">검사표 템플릿을 등록하고 관리합니다.</p>
            <a href="./template_list.php" class="btn btn-secondary">템플릿 목록</a>
        </div>
        <?php } ?>   
    </div>
    
    <div style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
        <h3 style="font-weight: bold;">※ 시스템 안내</h3>
        <ul style="line-height: 1.8;">
            <li>품질 검사표 시스템은 회원 레벨 3 이상만 사용할 수 있습니다.</li>
            <li>검사자는 검사표를 작성하고 제출할 수 있습니다.</li>
            <li>검수자(레벨 4 이상)는 검사 결과를 검수할 수 있습니다.</li>
            <li>최종결제자(레벨 5 이상)는 최종 승인/반려를 처리할 수 있습니다.</li>
            <li>템플릿 업로드(레벨 5 이상)는 템플릿을 업로드할 수 있습니다.</li>
            <li>템플릿 조회 및 다운로드(레벨 3 이상)는 템플릿을 조회하고 다운로드할 수 있습니다.</li>
        </ul>
    </div>
</div>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

