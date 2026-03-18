<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 품질 검사표 사진 관련 함수
 */

/**
 * 사진 등록
 */
function insert_quality_photo($data) {
    global $g5;
    
    $sql = "INSERT INTO {$g5['quality_photo_table']} SET
            qrd_id = '{$data['qrd_id']}',
            qr_id = '{$data['qr_id']}',
            qi_id = '{$data['qi_id']}',
            qp_filename = '{$data['qp_filename']}',
            qp_filepath = '{$data['qp_filepath']}',
            qp_filesize = '{$data['qp_filesize']}',
            qp_width = '{$data['qp_width']}',
            qp_height = '{$data['qp_height']}',
            qp_thumbnail = '{$data['qp_thumbnail']}',
            qp_order = '{$data['qp_order']}',
            qp_uploaded_at = NOW()";
    
    sql_query($sql);
    
    return sql_insert_id();
}

/**
 * 사진 정보 조회
 */
function get_quality_photo($qp_id) {
    global $g5;
    
    $sql = "SELECT * FROM {$g5['quality_photo_table']} WHERE qp_id = '{$qp_id}'";
    $row = sql_fetch($sql);
    
    return $row;
}

/**
 * 검사 결과 상세의 사진 목록 조회
 */
function get_quality_photos($qrd_id) {
    global $g5;
    
    $sql = "SELECT * FROM {$g5['quality_photo_table']} 
            WHERE qrd_id = '{$qrd_id}'
            ORDER BY qp_order ASC, qp_id ASC";
    
    return sql_query($sql);
}

/**
 * 검사 결과의 모든 사진 조회
 */
function get_quality_photos_by_result($qr_id) {
    global $g5;
    
    $sql = "SELECT * FROM {$g5['quality_photo_table']} 
            WHERE qr_id = '{$qr_id}'
            ORDER BY qp_order ASC, qp_id ASC";
    
    return sql_query($sql);
}

/**
 * 사진 삭제
 */
function delete_quality_photo($qp_id) {
    global $g5;
    
    // 파일 삭제
    $photo = get_quality_photo($qp_id);
    if ($photo) {
        if (file_exists($photo['qp_filepath'])) {
            @unlink($photo['qp_filepath']);
        }
        if (!empty($photo['qp_thumbnail']) && file_exists($photo['qp_thumbnail'])) {
            @unlink($photo['qp_thumbnail']);
        }
    }
    
    // DB에서 삭제
    $sql = "DELETE FROM {$g5['quality_photo_table']} WHERE qp_id = '{$qp_id}'";
    
    return sql_query($sql);
}

/**
 * 이미지 리사이징 및 썸네일 생성
 */
function resize_quality_photo($source_path, $max_width = 2048, $max_height = 2048, $thumbnail_size = 150) {
    if (!file_exists($source_path)) {
        return false;
    }
    
    $image_info = getimagesize($source_path);
    if (!$image_info) {
        return false;
    }
    
    $source_width = $image_info[0];
    $source_height = $image_info[1];
    $source_type = $image_info[2];
    
    // 이미지 타입에 따라 리소스 생성
    switch ($source_type) {
        case IMAGETYPE_JPEG:
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $source_image = imagecreatefrompng($source_path);
            break;
        case IMAGETYPE_GIF:
            $source_image = imagecreatefromgif($source_path);
            break;
        default:
            return false;
    }
    
    if (!$source_image) {
        return false;
    }
    
    // 원본이 최대 크기보다 작으면 리사이징 불필요
    if ($source_width <= $max_width && $source_height <= $max_height) {
        $resized_image = $source_image;
        $resized_width = $source_width;
        $resized_height = $source_height;
    } else {
        // 비율 유지하며 리사이징
        $ratio = min($max_width / $source_width, $max_height / $source_height);
        $resized_width = intval($source_width * $ratio);
        $resized_height = intval($source_height * $ratio);
        
        $resized_image = imagecreatetruecolor($resized_width, $resized_height);
        imagecopyresampled($resized_image, $source_image, 0, 0, 0, 0, $resized_width, $resized_height, $source_width, $source_height);
    }
    
    // 썸네일 생성
    $thumb_ratio = min($thumbnail_size / $resized_width, $thumbnail_size / $resized_height);
    $thumb_width = intval($resized_width * $thumb_ratio);
    $thumb_height = intval($resized_height * $thumb_ratio);
    
    $thumbnail_image = imagecreatetruecolor($thumb_width, $thumb_height);
    imagecopyresampled($thumbnail_image, $resized_image, 0, 0, 0, 0, $thumb_width, $thumb_height, $resized_width, $resized_height);
    
    // 파일 저장
    $path_info = pathinfo($source_path);
    $resized_path = $path_info['dirname'] . '/' . $path_info['filename'] . '_resized.' . $path_info['extension'];
    $thumbnail_path = $path_info['dirname'] . '/../thumbnail/' . $path_info['filename'] . '_thumb.' . $path_info['extension'];
    
    // 썸네일 디렉터리 생성
    $thumb_dir = dirname($thumbnail_path);
    if (!is_dir($thumb_dir)) {
        @mkdir($thumb_dir, 0755, true);
        @chown($thumb_dir, 'apache');
        @chgrp($thumb_dir, 'user');
    }
    
    // 리사이징된 이미지 저장
    switch ($source_type) {
        case IMAGETYPE_JPEG:
            imagejpeg($resized_image, $resized_path, 85);
            imagejpeg($thumbnail_image, $thumbnail_path, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($resized_image, $resized_path, 8);
            imagepng($thumbnail_image, $thumbnail_path, 8);
            break;
        case IMAGETYPE_GIF:
            imagegif($resized_image, $resized_path);
            imagegif($thumbnail_image, $thumbnail_path);
            break;
    }
    
    imagedestroy($source_image);
    if ($resized_image !== $source_image) {
        imagedestroy($resized_image);
    }
    imagedestroy($thumbnail_image);
    
    return array(
        'resized_path' => $resized_path,
        'thumbnail_path' => $thumbnail_path,
        'width' => $resized_width,
        'height' => $resized_height
    );
}

