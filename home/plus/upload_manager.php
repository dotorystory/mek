<?php
/**
 * 파일 업로드 관리자
 * 
 * 회원 레벨 3 이상 사용 가능
 * elFinder 기반 윈도우 탐색기 UI 제공
 * 
 * @author AI Assistant
 * @version 1.0
 * @date 2025-12-05
 */

include_once('./_common.php');

// 로그인 확인
if (!$is_member) {
    alert('회원만 이용할 수 있습니다.', G5_BBS_URL.'/login.php?url='.urlencode($_SERVER['REQUEST_URI']));
}

// 권한 확인 (레벨 3 이상)
if ($member['mb_level'] < 3 && $is_admin != 'super') {
    alert('이 페이지는 회원 레벨 3 이상만 이용 가능합니다.\\n\\n레벨 업그레이드는 관리자에게 문의하세요.', G5_URL);
}

// 로그 라이브러리 로드
include_once(G5_LIB_PATH.'/file_log.lib.php');

// 접근 로그 기록
log_file_access($member['mb_id'], 'ACCESS', '파일 관리자', $_SERVER['REMOTE_ADDR'], '', 0, '파일 관리자 페이지 접속');

// 파일 통계 조회
$file_stats = get_file_stats($member['mb_id']);

$g5['title'] = '파일 업로드 관리자';
include_once(G5_PATH.'/head_simple.php');
?>

<style>
    #elfinder { 
        height: 70vh !important;
        min-height: 500px !important;
        max-width: 1200px;
        margin: 10px auto;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .elfinder {
        height: 100% !important;
    }
    .elfinder-workzone {
        height: calc(100% - 50px) !important;
    }
    /* 툴바 버튼: 아이콘 + 명칭 함께 표시 */
    .elfinder-toolbar .elfinder-button-text {
        display: inline !important;
        margin-left: 3px;
    }
    /* 뒤로/앞으로/새로고침/홈/상위는 아이콘만 표시 */
    .elfinder-toolbar .elfinder-button:has(.elfinder-button-icon-back) .elfinder-button-text,
    .elfinder-toolbar .elfinder-button:has(.elfinder-button-icon-forward) .elfinder-button-text,
    .elfinder-toolbar .elfinder-button:has(.elfinder-button-icon-reload) .elfinder-button-text,
    .elfinder-toolbar .elfinder-button:has(.elfinder-button-icon-home) .elfinder-button-text,
    .elfinder-toolbar .elfinder-button:has(.elfinder-button-icon-up) .elfinder-button-text {
        display: none !important;
    }
    .elfinder-cwd-wrapper {
        min-height: 300px !important;
        height: auto !important;
    }
    .file-stats {
        background: linear-gradient(135deg,#999 0%,#bbb 100%);
        color: #fff;
        padding: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
    }
    .stat-item {
        text-align: center;
        padding: 7px 15px;
        min-width: 200px;
        display: inline-block;
    }
    .stat-value {
        font-size: 20px;
        font-weight: bold;
        color: #fff;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .stat-label {
        font-size: 13px;
        color: rgba(255,255,255,0.9);
        margin-top: 5px;
    }
    .page-header {
        margin-bottom: 15px;
    }
    .page-header h2 {
        color: #333;
        margin: 0 0 7px 0;
    }
    .page-header p {
        color: #666;
        margin: 0;
    }
    .user-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .user-info .name {
        font-weight: bold;
        color: #333;
    }
    .user-info .level {
        background: #1a4691;
        color: #fff;
        padding: 3px 10px;
        border-radius: 3px;
        font-size: 12px;
    }
    @media (max-width: 768px) {
        /* #elfinder {
            height: 60vh;
        } */
        /* .file-stats {
            flex-direction: column;
        } */
        .stat-item {
            padding: 5px;
            min-width: 70px;
        }
        .stat-value {
            font-size: 15px;
        }
        /* 모바일에서 navbar 너비 축소 */
        .elfinder-navbar {
            width: 100px !important;
            min-width: 100px !important;
        }
        .elfinder .elfinder-tree {
            font-size: 12px;
        }
        /* 모바일에서 toolbar 강제 표시 */
        .elfinder-toolbar {
            display: block !important;
            visibility: visible !important;
            height: auto !important;
            overflow-x: auto;
            overflow-y: visible;
            white-space: nowrap;
        }
        .elfinder-buttonset {
            display: inline-flex !important;
            flex-wrap: nowrap !important;
        }
        .elfinder-button {
            min-width: 30px !important;
            padding: 5px !important;
        }
        .elfinder-button-text {
            display: none !important;  /* 모바일에서는 아이콘만 */
        }
    }
</style>

<div class="page-header" style="display: none;">
    <h2>📁 파일 업로드 관리자</h2>
    <p>휴대폰 또는 PC에서 파일을 업로드하고 관리할 수 있습니다. (최대 500MB)</p>
</div>

<div class="user-info">
    <div>
        <span class="name"><?php echo $member['mb_name'] ? $member['mb_name'] : $member['mb_id']; ?>님</span>
        <span class="level">레벨 <?php echo $member['mb_level']; ?></span>
    </div>
    <div style="font-size: 12px;">
        <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=team" style="color:royalblue; font-weight: bold;">📝팀게시판</a> |
        <a href="<?php echo G5_URL; ?>/plus/upload_manager.php">🔄새로고침</a>
        <!-- <a href="<?php echo G5_URL; ?>/plus/">Main</a> -->
    </div>
</div>

<!-- 파일 통계 표시 -->
<div class="file-stats">
    <div class="stat-item">
        <div class="stat-value"><?php echo number_format($file_stats['total_files']); ?></div>
        <div class="stat-label">📄 총 파일 개수</div>
    </div>
    <div class="stat-item">
        <div class="stat-value"><?php echo $file_stats['total_size_mb']; ?> MB</div>
        <div class="stat-label">💾 총 파일 용량</div>
    </div>
    <div class="stat-item">
        <div class="stat-value"><?php echo number_format($file_stats['upload_count']); ?></div>
        <div class="stat-label">⬆️ 업로드 횟수</div>
    </div>
    <div class="stat-item">
        <div class="stat-value"><?php echo number_format($file_stats['download_count']); ?></div>
        <div class="stat-label">⬇️ 다운로드 횟수</div>
    </div>
    <?php if ($file_stats['last_upload']) { ?>
    <div class="stat-item">
        <div class="stat-value" style="font-size: 16px;"><?php echo date('Y-m-d H:i', strtotime($file_stats['last_upload'])); ?></div>
        <div class="stat-label">🕐 마지막 업로드</div>
    </div>
    <?php } ?>
</div>

<!-- elFinder 컨테이너 -->
<div id="elfinder"></div>

<!-- elFinder CSS -->
<link rel="stylesheet" href="./elfinder/css/elfinder.min.css">
<link rel="stylesheet" href="./elfinder/css/theme.css">

<!-- jQuery UI (elFinder 필수) -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="//code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- elFinder JS -->
<script src="./elfinder/js/elfinder.min.js"></script>
<script src="./elfinder/js/i18n/elfinder.ko.js"></script>

<script>
jQuery(document).ready(function($) {
    // 모바일 감지
    var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    var windowWidth = $(window).width();
    
    var elf = $('#elfinder').elfinder({
        url: './elfinder/php/connector.minimal.php',
        lang: 'ko',
        height: windowWidth <= 768 ? 450 : 500,  // 모바일에서 약간 낮춤
        resizable: !isMobile,
        uiOptions: {
            toolbar: [
                ['back', 'forward'],
                ['reload'],
                ['home', 'up'],
                ['mkdir', 'upload'],
                ['download'],
                ['rm'],
                ['duplicate', 'rename'],
                ['search'],
                ['view']
            ]
        },
        // 툴바 버튼에 아이콘+명칭 함께 표시 (저장 기본값)
        start: function() {
            var fm = this;
            if (!fm.storage('toolbarTextLabel')) {
                fm.storage('toolbarTextLabel', '1');
            }
        },
        handlers: {
            upload: function(event, fm) {
                if (event.data && event.data.added && event.data.added.length > 0) {
                    fm.reload();
                }
            },
            rm: function(event, fm) {
                if (event.data && event.data.removed && event.data.removed.length > 0) {
                    fm.reload();
                }
            },
            // 초기 로드 완료 시 - UI 펼치기
            load: function(event, instance) {
                console.log('elFinder 로드 완료');
                // 파일 영역 강제 확장
                setTimeout(function() {
                    var workzone = $('.elfinder-workzone');
                    if (workzone.length) {
                        workzone.css('height', '450px');
                    }
                    var cwdWrapper = $('.elfinder-cwd-wrapper');
                    if (cwdWrapper.length) {
                        cwdWrapper.css('height', '400px');
                    }
                }, 500);
            }
        }
    }).elfinder('instance');
});
</script>

<!-- 사용 안내 영역 (elfinder 아래로 이동) -->
<div style="display: block; width: 100%; margin: 20px auto; text-align: center; padding: 15px; border-radius: 5px; clear: both;">
    <h3 style="margin: 0 0 10px 0; color: #333;">📌 MEK 자료실 이용 안내</h3>
    <ul style="margin: 0; padding-left: 20px; color: #666;">
        <li><strong>업로드</strong>: 드래그 앤 드롭 또는 '업로드' 버튼 사용 | 
            <strong>폴더 생성</strong>: '새 폴더' 버튼으로 생성 가능 | 
            <strong>다운로드</strong>: 파일 선택 후 '다운로드' 버튼 | 
            <strong>허용파일</strong>: 문서(pdf, 엑셀 등), 이미지(jpg, png 등), 영상(mp4 등) | 
            <strong>크기</strong>: 파일당 최대 500MB 
        </li> 
        <!-- <li><strong>자동 백업</strong>: 업로드된 파일은 매일 2회 회사 내부 서버로 자동 동기화됩니다.</li> -->
    </ul>
</div>

<?php if ($is_admin == 'super') { ?>
<!-- 빠른 링크 -->
<div style="text-align: center; margin-top: 30px; padding-bottom: 30px;">
    <a href="./upload_manager.php" style="padding: 12px 30px; background: #863a86; color: #fff; text-decoration: none; border-radius: 5px; margin: 0 5px;">📁 파일 관리자</a>
    <a href="./upload_stats.php" style="padding: 12px 30px; background: #2a5298; color: #fff; text-decoration: none; border-radius: 5px; margin: 0 5px;">� 통계 보기</a>
    <a href="./upload_log.php" style="padding: 12px 30px; background: #28a745; color: #fff; text-decoration: none; border-radius: 5px; margin: 0 5px;">📋 전체 로그 보기</a>
    <!-- <a href="<?php echo G5_URL; ?>/plus/" style="padding: 12px 30px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; margin: 0 5px;">⚙️ Plus 메인</a> -->
</div>
<?php } ?>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

