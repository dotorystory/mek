<?php
/**
 * KeyShotXR 초기화 스크립트 (body 안 include용)
 * KeyShotXR.js는 head.sub.php에서 이미 로드됨.
 * view.skin에서 이 파일 include 전에 window.folderName 이 설정되어 있어야 함.
 */
?>
<style type="text/css">#KeyShotXR{margin: 0 auto;} body { -ms-touch-action: none; }</style>
<script type="text/javascript">
(function(){
  function initKeyShotXR() {
    if (typeof window.folderName === 'undefined' || !document.getElementById('KeyShotXR') || typeof window.keyshotXR !== 'function') return;
    var nameOfDiv = "KeyShotXR";
    var viewPortWidth = 1157;
    var viewPortHeight = 771;
    var backgroundColor = "#FFFFFF";
    var uCount = 288;
    var vCount = 1;
    var uWrap = true;
    var vWrap = true;
    var uMouseSensitivity = -0.09;
    var vMouseSensitivity = 1;
    var uStartIndex = 144;
    var vStartIndex = 0;
    var minZoom = 1;
    var maxZoom = 1;
    var rotationDamping = 1;
    var downScaleToBrowser = true;
    var addDownScaleGUIButton = false;
    var downloadOnInteraction = false;
    var imageExtension = "png";
    var showLoading = true;
    var loadingIcon = "ks_logo.png";
    var allowFullscreen = true;
    var uReverse = false;
    var vReverse = false;
    var hotspots = {};
    var isIBooksWidget = false;
    new window.keyshotXR(nameOfDiv, window.folderName, viewPortWidth, viewPortHeight, backgroundColor, uCount, vCount, uWrap, vWrap, uMouseSensitivity, vMouseSensitivity, uStartIndex, vStartIndex, minZoom, maxZoom, rotationDamping, downScaleToBrowser, addDownScaleGUIButton, downloadOnInteraction, imageExtension, showLoading, loadingIcon, allowFullscreen, uReverse, vReverse, hotspots, isIBooksWidget);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initKeyShotXR);
  } else {
    initKeyShotXR();
  }
})();
</script>
