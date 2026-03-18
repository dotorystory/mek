<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!defined('G5_IS_ADMIN') && defined('G5_THEME_PATH') && is_file(G5_THEME_PATH.'/tail.sub.php')) {
    require_once(G5_THEME_PATH.'/tail.sub.php');
    return;
}
?>

<?php if ($is_admin == 'super') {  ?><!-- <div style='float:left; text-align:center;'>RUN TIME : <?php echo get_microtime()-$begin_time; ?><br></div> --><?php }  ?>


<?php run_event('tail_sub'); ?>

<!-- MR Script Ver 2.0 -->
<!-- <script async="true" src="//log1.toup.net/mirae_log_chat_common.js?adkey=pkrdg" charset="UTF-8"></script> -->
<!-- MR Script END Ver 2.0 -->

</body>
</html>
<?php echo html_end(); // HTML 마지막 처리 함수 : 반드시 넣어주시기 바랍니다.