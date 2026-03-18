<?php
include_once('./_common.php');

$qrd_id = isset($_GET['qrd_id']) ? (int)$_GET['qrd_id'] : 0;

if (!$qrd_id) {
    alert('결과 상세 ID가 없습니다.', './result_list.php');
}

// 사진 목록 조회
$photos = get_quality_photos($qrd_id);

if (sql_num_rows($photos) == 0) {
    alert('등록된 사진이 없습니다.', './result_list.php');
}

$g5['title'] = '사진 보기';
add_stylesheet('<link rel="stylesheet" href="'.G5_QUALITY_URL.'/common_style.css">', 0);
include_once(G5_PATH.'/head_simple.php');
?>

<style>
.photo-view-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    text-align: center;
}
.photo-main {
    margin-bottom: 20px;
}
.photo-main img {
    max-width: 100%;
    max-height: 600px;
    border: 2px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.photo-thumbnails {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 20px;
}
.photo-thumb {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border: 2px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
}
.photo-thumb:hover {
    border-color: #007bff;
    transform: scale(1.1);
}
.photo-thumb.active {
    border-color: #007bff;
    border-width: 3px;
}
.photo-info {
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}
.photo-actions {
    margin-top: 20px;
}
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 40px;
    padding: 10px 24px;
    margin: 0 5px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    text-decoration: none;
    vertical-align: middle;
    line-height: 1.2;
    box-sizing: border-box;
}
.btn-download {
    background: #28a745;
    color: #fff;
}
.btn-close {
    background: #6c757d;
    color: #fff;
}
</style>

<div class="photo-view-container">
    <h2>사진 보기</h2>
    
    <div class="photo-main" id="photoMain">
        <?php
        $first_photo = sql_fetch_array($photos);
        $photo_path = $first_photo['qp_filepath'];
        if (file_exists($photo_path)) {
            $photo_url = str_replace(G5_PATH, G5_URL, $photo_path);
        } else {
            $photo_url = '';
        }
        ?>
        <img src="<?php echo $photo_url; ?>" alt="사진" id="mainPhoto">
    </div>
    
    <div class="photo-thumbnails">
        <?php
        sql_data_seek($photos, 0);
        $index = 0;
        while ($photo = sql_fetch_array($photos)) {
            $thumb_path = $photo['qp_thumbnail'];
            if (file_exists($thumb_path)) {
                $thumb_url = str_replace(G5_PATH, G5_URL, $thumb_path);
            } else {
                $thumb_url = str_replace(G5_PATH, G5_URL, $photo['qp_filepath']);
            }
            $photo_url = str_replace(G5_PATH, G5_URL, $photo['qp_filepath']);
        ?>
        <img src="<?php echo $thumb_url; ?>" 
             alt="썸네일" 
             class="photo-thumb <?php echo $index == 0 ? 'active' : ''; ?>"
             data-photo-url="<?php echo $photo_url; ?>"
             data-photo-id="<?php echo $photo['qp_id']; ?>"
             onclick="showPhoto('<?php echo $photo_url; ?>', <?php echo $photo['qp_id']; ?>)">
        <?php
            $index++;
        }
        ?>
    </div>
    
    <div class="photo-info" id="photoInfo">
        <p><strong>파일명:</strong> <span id="photoFilename"><?php echo htmlspecialchars($first_photo['qp_filename']); ?></span></p>
        <p><strong>크기:</strong> <span id="photoSize"><?php echo number_format($first_photo['qp_filesize'] / 1024, 2); ?> KB</span></p>
        <p><strong>해상도:</strong> <span id="photoResolution"><?php echo $first_photo['qp_width']; ?> × <?php echo $first_photo['qp_height']; ?> px</span></p>
    </div>
    
    <div class="photo-actions">
        <a href="./photo_download.php?qp_id=<?php echo $first_photo['qp_id']; ?>" class="btn btn-download" id="downloadBtn">다운로드</a>
        <a href="javascript:window.close();" class="btn btn-close">닫기</a>
    </div>
</div>

<script>
var currentPhotoId = <?php echo $first_photo['qp_id']; ?>;

function showPhoto(photoUrl, photoId) {
    document.getElementById('mainPhoto').src = photoUrl;
    currentPhotoId = photoId;
    
    // 썸네일 활성화 상태 변경
    document.querySelectorAll('.photo-thumb').forEach(function(thumb) {
        thumb.classList.remove('active');
        if (thumb.getAttribute('data-photo-id') == photoId) {
            thumb.classList.add('active');
        }
    });
    
    // 다운로드 링크 업데이트
    document.getElementById('downloadBtn').href = './photo_download.php?qp_id=' + photoId;
}
</script>

<?php
include_once(G5_PATH.'/tail_simple.php');
?>

