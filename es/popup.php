<?php
include_once('./_common.php');
?>

<!-- 이미지 팝업 레이어 -->
<div id="popup_layer" class="popup_layer">
    <div class="popup_content">
        <div class="popup_header">
            <button type="button" class="popup_close" onclick="closePopup()">×</button>
        </div>
        
        <div class="popup_image_container">
            <img src="<?php echo G5_IMG_URL; ?>/popup.png" alt="Popup Image" class="popup_image">
        </div>
        
        <div class="popup_footer">
            <div class="popup_checkbox_wrap">
                <input type="checkbox" id="popup_close_today" class="popup_checkbox">
                <label for="popup_close_today" class="popup_checkbox_label">No mostrar hoy</label>
            </div>
            <div class="popup_buttons">
                <button type="button" class="popup_close_btn" onclick="closePopupWithOption()">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function openPopup() {
    document.getElementById('popup_layer').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePopup() {
    document.getElementById('popup_layer').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function closePopupWithOption() {
    const checkbox = document.getElementById('popup_close_today');
    
    if (checkbox.checked) {
        // 오늘 날짜를 저장 (자정까지 유효)
        const today = new Date();
        const todayString = today.getFullYear() + '-' + 
                           String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                           String(today.getDate()).padStart(2, '0');
        localStorage.setItem('popup_close_date', todayString);
    }
    
    closePopup();
}

function shouldShowPopup() {
    const closeDate = localStorage.getItem('popup_close_date');
    if (!closeDate) return true;
    
    const today = new Date();
    const todayString = today.getFullYear() + '-' + 
                       String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(today.getDate()).padStart(2, '0');
    
    return closeDate !== todayString;
}

// ESC 키로 닫기
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePopup();
    }
});

// 레이어 외부 클릭시 닫기
document.getElementById('popup_layer').addEventListener('click', function(e) {
    if (e.target === this) {
        closePopup();
    }
});

// 페이지 로드시 자동으로 팝업 열기 (오늘 하루 열지 않기 체크)
window.addEventListener('load', function() {
    if (shouldShowPopup()) {
        openPopup();
    }
});
</script>
