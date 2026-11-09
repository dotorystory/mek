<?php
include_once('./_common.php');
// 게시판 관리자 이상 복사, 이동 가능
if ($is_admin != "board" && $is_admin != "group" && $is_admin != "super")
    alert_close("게시판 관리자 이상 접근이 가능합니다.");

$wr_id=$_POST[chk_wr_id][0];
if($_POST[chk_wr_id][1]) $wr_id2=$_POST[chk_wr_id][1];

if($sw=="change"){
    if(count($_POST[chk_wr_id])==2){

    $act = "순서변경";

    $sql = " select wr_num from g5_write_{$bo_table} where wr_id='$wr_id'"; //절대값이 높은(위쪽) wr_num값을 구한다.
    $result = sql_query($sql);
    $forth_wr_num_array=sql_fetch_array($result);
    $wr_id;
    $forth_wr_num = $forth_wr_num_array['wr_num']; //절대값이 높은 wr_num값
    $sql = " select wr_num from g5_write_{$bo_table} where wr_id='$wr_id2'"; //절대값이 낮은(아래쪽) wr_num값을 구한다.
    $result = sql_query($sql);
    $back_wr_num_array=sql_fetch_array($result);
    $wr_id2;
    $back_wr_num = $back_wr_num_array['wr_num']; //절대값이 낮은 wr_num값

    sql_query(" update g5_write_{$bo_table} set wr_num = '$back_wr_num' where wr_id = '$wr_id'"); //위쪽 게시물 wr_num을 아래쪽 wr_num으로 수정
    sql_query(" update g5_write_{$bo_table} set wr_num = '$forth_wr_num' where wr_id = '$wr_id2'"); //아래쪽 게시물 wr_num을 위쪽 wr_num으로 수정

    }else{
        alert_close("2개의 게시물을 선택해주세요.");
    }

}else if($sw == "prev"){
    if(count($_POST[chk_wr_id])>=2){alert_close("1개의 게시물만 선택해주세요."); }


    $act = "앞으로 이동";

    $sql = " select wr_num from g5_write_{$bo_table} where wr_id='$wr_id'"; //선택된 wr_num값을 구한다.
    $result = sql_query($sql);
    $selected_wr_num_array=sql_fetch_array($result);
    $selected_wr_num = $selected_wr_num_array['wr_num']; //선택된 wr_num값
    if($selected_wr_num=='-1') alert_close("가장 앞선 게시물입니다.");
    $prev_wr_num = $selected_wr_num+1; //앞의 wr_num 값
    $sql = " select wr_id from g5_write_{$bo_table} where wr_num='{$prev_wr_num}'"; //앞의 wr_num값을 갖는 wr_id를 구한다.
    $result = sql_query($sql);
    $prev_wr_id_array=sql_fetch_array($result);
    $prev_wr_id = $prev_wr_id_array['wr_id']; //앞의 wr_id 값

    sql_query(" update g5_write_{$bo_table} set wr_num = '$prev_wr_num' where wr_id = '$wr_id'"); //선택된 게시물을 앞번으로 수정
    sql_query(" update g5_write_{$bo_table} set wr_num = '$selected_wr_num' where wr_id = '$prev_wr_id'"); //앞번 게시물을 선택된번으로 수정

}else if ($sw == "next") {
    if(count($_POST[chk_wr_id])>=2){alert_close("1개의 게시물만 선택해주세요."); }

    $act = "뒤로 이동";

    $sql = " select wr_num from g5_write_{$bo_table} where wr_id='$wr_id'"; //선택된 wr_num값을 구한다.
    $result = sql_query($sql);
    $selected_wr_num_array=sql_fetch_array($result);
    $selected_wr_num = $selected_wr_num_array['wr_num']; //선택된 wr_num값
    $latest_wr_num_array = sql_fetch(" select min(wr_num) as latest from g5_write_{$bo_table} where 1");
    if($selected_wr_num == $latest_wr_num_array['latest']) alert_close("가장 뒤선 게시물입니다.");
    $next_wr_num = $selected_wr_num-1; //뒤의 wr_num 값
    $sql = " select wr_id from g5_write_{$bo_table} where wr_num='{$next_wr_num}'"; //뒤의 wr_num값을 갖는 wr_id를 구한다.
    $result = sql_query($sql);
    $next_wr_id_array=sql_fetch_array($result);
    $next_wr_id = $next_wr_id_array['wr_id']; //뒤의 wr_id 값

    sql_query(" update g5_write_{$bo_table} set wr_num = '$next_wr_num' where wr_id = '$wr_id'"); //선택된 게시물을 뒷번으로 수정
    sql_query(" update g5_write_{$bo_table} set wr_num = '$selected_wr_num' where wr_id = '$next_wr_id'"); //뒷번 게시물을 선택된번으로 수정


}else {
    alert("수행 값이 제대로 넘어오지 않았습니다.");
}

$msg = "순서변경완료!";
$opener_href = "./board.php?bo_table=$bo_table&page=$page&$qstr";

?>
<script language="javascript">
//alert("<?php echo $msg?>");
opener.document.location.href = "<?php echo $opener_href?>";
window.close();
</script>
