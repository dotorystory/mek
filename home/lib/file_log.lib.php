<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/**
 * 파일 업로드/다운로드 로그 라이브러리
 * 
 * @author AI Assistant
 * @version 1.0
 * @date 2025-12-05
 */

/**
 * 파일 작업 로그 기록
 * 
 * @param string $mb_id 회원 ID
 * @param string $type 작업 타입 (UPLOAD/DOWNLOAD/DELETE/ACCESS)
 * @param string $filename 파일명
 * @param string $ip IP 주소
 * @param string $filepath 파일 경로 (선택)
 * @param int $filesize 파일 크기 bytes (선택)
 * @param string $note 비고 (선택)
 */
function log_file_access($mb_id, $type, $filename, $ip, $filepath = '', $filesize = 0, $note = '') {
    global $g5;
    
    $mb_id = sql_escape_string($mb_id);
    $type = sql_escape_string($type);
    $filename = sql_escape_string($filename);
    $filepath = sql_escape_string($filepath);
    $ip = sql_escape_string($ip);
    $note = sql_escape_string($note);
    
    $sql = "INSERT INTO g5_file_log 
            (fl_mb_id, fl_type, fl_filename, fl_filepath, fl_filesize, fl_ip, fl_datetime, fl_note) 
            VALUES 
            ('{$mb_id}', '{$type}', '{$filename}', '{$filepath}', {$filesize}, '{$ip}', NOW(), '{$note}')";
    
    sql_query($sql);
    
    // 통계 업데이트
    if (in_array($type, ['UPLOAD', 'DOWNLOAD', 'DELETE'])) {
        update_file_stats($mb_id, $type, $filesize);
    }
}

/**
 * 파일 업로드 로그
 * 
 * @param string $mb_id 회원 ID
 * @param string $filename 파일명
 * @param string $filepath 파일 경로
 * @param int $filesize 파일 크기 bytes
 * @param string $ip IP 주소
 */
function log_file_upload($mb_id, $filename, $filepath, $filesize, $ip) {
    log_file_access($mb_id, 'UPLOAD', $filename, $ip, $filepath, $filesize, '파일 업로드');
}

/**
 * 파일 다운로드 로그
 * 
 * @param string $mb_id 회원 ID
 * @param string $filename 파일명
 * @param string $filepath 파일 경로
 * @param int $filesize 파일 크기 bytes
 * @param string $ip IP 주소
 */
function log_file_download($mb_id, $filename, $filepath, $filesize, $ip) {
    log_file_access($mb_id, 'DOWNLOAD', $filename, $ip, $filepath, $filesize, '파일 다운로드');
}

/**
 * 파일 삭제 로그
 * 
 * @param string $mb_id 회원 ID
 * @param string $filename 파일명
 * @param string $filepath 파일 경로
 * @param int $filesize 파일 크기 bytes
 * @param string $ip IP 주소
 */
function log_file_delete($mb_id, $filename, $filepath, $filesize, $ip) {
    log_file_access($mb_id, 'DELETE', $filename, $ip, $filepath, $filesize, '파일 삭제');
}

/**
 * 회원별 파일 통계 업데이트
 * 
 * @param string $mb_id 회원 ID
 * @param string $type 작업 타입
 * @param int $filesize 파일 크기
 */
function update_file_stats($mb_id, $type, $filesize = 0) {
    global $g5;
    
    $mb_id = sql_escape_string($mb_id);
    
    // 통계 레코드 존재 확인
    $stats = sql_fetch("SELECT * FROM g5_file_stats WHERE fs_mb_id = '{$mb_id}'");
    
    if (!$stats) {
        // 신규 생성
        $sql = "INSERT INTO g5_file_stats 
                (fs_mb_id, fs_total_files, fs_total_size, fs_upload_count, fs_download_count, 
                 fs_last_upload, fs_last_download, fs_update_datetime) 
                VALUES 
                ('{$mb_id}', 0, 0, 0, 0, NULL, NULL, NOW())";
        sql_query($sql);
        $stats = sql_fetch("SELECT * FROM g5_file_stats WHERE fs_mb_id = '{$mb_id}'");
    }
    
    $filesize = (int) $filesize;

    if ($type == 'UPLOAD') {
        $sql = "UPDATE g5_file_stats SET 
                fs_upload_count = fs_upload_count + 1,
                fs_total_files = fs_total_files + 1,
                fs_total_size = fs_total_size + {$filesize},
                fs_last_upload = NOW(),
                fs_update_datetime = NOW()
                WHERE fs_mb_id = '{$mb_id}'";
    } elseif ($type == 'DOWNLOAD') {
        $sql = "UPDATE g5_file_stats SET 
                fs_download_count = fs_download_count + 1,
                fs_last_download = NOW(),
                fs_update_datetime = NOW()
                WHERE fs_mb_id = '{$mb_id}'";
    } elseif ($type == 'DELETE') {
        $sql = "UPDATE g5_file_stats SET 
                fs_total_files = GREATEST(0, CAST(fs_total_files AS SIGNED) - 1),
                fs_total_size = GREATEST(0, CAST(fs_total_size AS SIGNED) - {$filesize}),
                fs_update_datetime = NOW()
                WHERE fs_mb_id = '{$mb_id}'";
    }
    
    if (isset($sql)) {
        sql_query($sql);
    }
}

/**
 * 실제 파일 개수 및 용량 재계산
 * 
 * @param string $mb_id 회원 ID
 */
function recalculate_file_stats($mb_id) {
    global $g5;

    if (strpos((string) $mb_id, '..') !== false) {
        return;
    }

    $candidates = array(
        '/var/www/html/storage/upload/' . $mb_id,
        '/var/www/html/mekeng.com/upload/' . $mb_id,
    );
    if (defined('G5_PATH')) {
        $candidates[] = rtrim(G5_PATH, '/') . '/../upload/' . $mb_id;
    }

    $upload_path = '';
    foreach ($candidates as $p) {
        if (is_dir($p)) {
            $upload_path = $p;
            break;
        }
    }

    if ($upload_path === '') {
        return;
    }
    
    $total_size = 0;
    $file_count = 0;
    
    try {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($upload_path, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($files as $file) {
            if ($file->isFile() && !preg_match('/\.tmb/', $file->getPathname())) {
                $total_size += $file->getSize();
                $file_count++;
            }
        }
    } catch (Exception $e) {
        // 폴더가 없거나 접근 불가한 경우 무시
        return;
    }
    
    $mb_id_esc = sql_escape_string($mb_id);

    $exists = sql_fetch("SELECT fs_mb_id FROM g5_file_stats WHERE fs_mb_id = '{$mb_id_esc}' LIMIT 1");
    if (!$exists) {
        sql_query("INSERT INTO g5_file_stats
            (fs_mb_id, fs_total_files, fs_total_size, fs_upload_count, fs_download_count,
             fs_last_upload, fs_last_download, fs_update_datetime)
            VALUES
            ('{$mb_id_esc}', 0, 0, 0, 0, NULL, NULL, NOW())");
    }

    $sql = "UPDATE g5_file_stats SET 
            fs_total_files = {$file_count},
            fs_total_size = {$total_size},
            fs_update_datetime = NOW()
            WHERE fs_mb_id = '{$mb_id_esc}'";

    sql_query($sql);
}

/**
 * elFinder 커맨드 완료 후 g5_file_log / g5_file_stats 반영 (connector opts bind)
 */
function pro_elfinder_file_log_bind($cmd, &$result, $args, $elfinder, $dstVolume = null) {
    global $member;

    if (!function_exists('log_file_access') || empty($member['mb_id'])) {
        return false;
    }
    if (!empty($result['error'])) {
        return false;
    }

    $mb_id = $member['mb_id'];
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

    if ($cmd === 'upload' && !empty($result['added']) && is_array($result['added'])) {
        foreach ($result['added'] as $file) {
            if (!is_array($file) || (!empty($file['mime']) && $file['mime'] === 'directory')) {
                continue;
            }
            $name = isset($file['name']) ? $file['name'] : 'unknown';
            $size = isset($file['size']) ? (int) $file['size'] : 0;
            $path = isset($file['path']) ? $file['path'] : '';
            log_file_upload($mb_id, $name, $path, $size, $ip);
        }
        return false;
    }

    if ($cmd === 'rm' && !empty($result['removed']) && is_array($result['removed'])) {
        foreach ($result['removed'] as $file) {
            if (!is_array($file)) {
                continue;
            }
            if (!empty($file['mime']) && $file['mime'] === 'directory') {
                continue;
            }
            $name = isset($file['name']) ? $file['name'] : 'unknown';
            $size = isset($file['size']) ? (int) $file['size'] : 0;
            $path = isset($file['path']) ? $file['path'] : '';
            log_file_delete($mb_id, $name, $path, $size, $ip);
        }
        return false;
    }

    if ($cmd === 'file' && !empty($args['download']) && !empty($args['target']) && is_object($elfinder)) {
        $volume = $elfinder->getVolume($args['target']);
        if ($volume) {
            $file = $volume->file($args['target']);
            if (is_array($file) && (empty($file['mime']) || $file['mime'] !== 'directory')) {
                $name = isset($file['name']) ? $file['name'] : 'unknown';
                $size = isset($file['size']) ? (int) $file['size'] : 0;
                $path = isset($file['path']) ? $file['path'] : '';
                log_file_download($mb_id, $name, $path, $size, $ip);
            }
        }
        return false;
    }

    return false;
}

/**
 * 회원별 파일 통계 조회
 * 
 * @param string $mb_id 회원 ID
 * @return array 통계 정보
 */
function get_file_stats($mb_id) {
    global $g5;
    
    $mb_id = sql_escape_string($mb_id);
    
    // 통계 레코드가 없으면 생성
    $stats = sql_fetch("SELECT * FROM g5_file_stats WHERE fs_mb_id = '{$mb_id}'");
    
    if (!$stats) {
        // 신규 회원 통계 레코드 생성
        $sql = "INSERT INTO g5_file_stats 
                (fs_mb_id, fs_total_files, fs_total_size, fs_upload_count, fs_download_count, 
                 fs_last_upload, fs_last_download, fs_update_datetime) 
                VALUES 
                ('{$mb_id}', 0, 0, 0, 0, NULL, NULL, NOW())";
        sql_query($sql);
    }
    
    $stats = sql_fetch("SELECT * FROM g5_file_stats WHERE fs_mb_id = '{$mb_id}'");
    
    if (!$stats) {
        return [
            'total_files' => 0,
            'total_size' => 0,
            'total_size_mb' => 0,
            'upload_count' => 0,
            'download_count' => 0,
            'last_upload' => null,
            'last_download' => null
        ];
    }
    
    return [
        'total_files' => (int)$stats['fs_total_files'],
        'total_size' => (int)$stats['fs_total_size'],
        'total_size_mb' => round($stats['fs_total_size'] / 1024 / 1024, 2),
        'upload_count' => (int)$stats['fs_upload_count'],
        'download_count' => (int)$stats['fs_download_count'],
        'last_upload' => $stats['fs_last_upload'],
        'last_download' => $stats['fs_last_download']
    ];
}

/**
 * 회원별 최근 로그 조회
 * 
 * @param string $mb_id 회원 ID
 * @param int $limit 조회 개수
 * @return array 로그 목록
 */
function get_recent_file_logs($mb_id, $limit = 20) {
    global $g5;
    
    $mb_id = sql_escape_string($mb_id);
    $limit = (int)$limit;
    
    $sql = "SELECT * FROM g5_file_log 
            WHERE fl_mb_id = '{$mb_id}' 
            ORDER BY fl_datetime DESC 
            LIMIT {$limit}";
    
    $result = sql_query($sql);
    $logs = [];
    
    while ($row = sql_fetch_array($result)) {
        $logs[] = $row;
    }
    
    return $logs;
}

/**
 * 전체 파일 통계 조회 (관리자용)
 * 
 * @return array 전체 통계
 */
function get_total_file_stats() {
    $stats = sql_fetch("
        SELECT 
            COUNT(DISTINCT fs_mb_id) as total_users,
            SUM(fs_total_files) as total_files,
            SUM(fs_total_size) as total_size,
            SUM(fs_upload_count) as total_uploads,
            SUM(fs_download_count) as total_downloads
        FROM g5_file_stats
    ");
    
    return [
        'total_users' => (int)$stats['total_users'],
        'total_files' => (int)$stats['total_files'],
        'total_size' => (int)$stats['total_size'],
        'total_size_gb' => round($stats['total_size'] / 1024 / 1024 / 1024, 2),
        'total_uploads' => (int)$stats['total_uploads'],
        'total_downloads' => (int)$stats['total_downloads']
    ];
}

/**
 * 상위 사용자 조회 (관리자용)
 * 
 * @param int $limit 조회 개수
 * @return array 상위 사용자 목록
 */
function get_top_file_users($limit = 10) {
    global $g5;
    
    $limit = (int)$limit;
    
    $sql = "SELECT 
                fs.*,
                m.mb_name,
                m.mb_level
            FROM g5_file_stats fs
            LEFT JOIN {$g5['member_table']} m ON fs.fs_mb_id = m.mb_id
            WHERE fs.fs_total_files > 0
            ORDER BY fs.fs_total_size DESC
            LIMIT {$limit}";
    
    $result = sql_query($sql);
    $users = [];
    
    while ($row = sql_fetch_array($result)) {
        $users[] = $row;
    }
    
    return $users;
}
?>

