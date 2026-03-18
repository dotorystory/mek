<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$img_w = 270; // 이미지($img) 가로 크기
$img_h = 382; // 이미지($img) 세로 크기
?>

<style type="text/css">

img{-webkit-backface-visibility: hidden;-webkit-transition: opacity 0.3s ease-out;-moz-transition: opacity 0.3s ease-out;-o-transition: opacity 0.3s ease-out;transition: opacity 0.3s ease-out;}
img:hover{opacity:0.8}
p{word-wrap:break-word}
.cf:after{content:"";display:table;clear:both}
.cf{*zoom:1}
.fl,.layout .fl,.chief{float:left;display:inline}
.fr,.layout .fr,.extra{float:right;display:inline}


.picBox{overflow:hidden;zoom:1;margin:40px auto 0 auto;width:100%;}
.picL{overflow:hidden;zoom:1; display: flex; flex-wrap: wrap; gap:2vw 1%}
.picL li{overflow:hidden;position:relative; display: inline-block; width:15.5%; zoom:1  }
.picL li > a {width: 100%; height: 100%; display: inline-block;}
.picL li img {object-fit: cover; width: 100%; height: auto;}
.picL li .text{opacity: 0.8;  background:#144d98; position:absolute;width:100%;height:100%;left:0;}
.picL li .text b{display: none;}
.picL li .text p{font-size:18px;line-height:22px; font-weight:400; text-align:center; height: 100%}
.picL li .text a{color: #fff;font-weight:200;display: block;height: 100%;text-align: center; display: flex; justify-content: center; align-items: center;}

@media (max-width:1200px){
	.picL {gap:3vw 2%}
	.picL li {width:23%;}
	.picL li .text p{font-size:16px;}
}
@media (max-width:950px){
	.picL {gap:4vw 3%}
	.picL li {width:30%;}
	.picL li .text p{font-size:15px;}
}

@media (max-width:700px){
	.picL {gap:5vw 4%}
	.picL li {width:46%;}
}
@media (max-width:500px){
	.picL {gap:5vw 2%}
	.picL li {width:46%;}
	.picL li .text p{font-size:3vw;}
}

</style>
<div class="picBox">
    <ul class="picL" id="picLsy">
        <?php
            for($i=0; $i<count($list); $i++){
                $thumbs = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $img_w, $img_h, false, true);
                if($thumbs['src']) {
                    $img = $thumbs['src'];
                } ?>
                <li>
                    <?php if($is_admin) { ?>
                        <a title="<?php echo $list[$i]['subject']?>" href="<?php echo $list[$i]['href'].'&amp;sca='.urlencode($list[$i]['ca_name']); ?>" target="_self"><img src="<?php echo $img?>" alt="<?php echo $list[$i]['subject']?>"/></a>
                        <div class="text">
                            <b><?php echo $list[$i]['ca_name']?></b>
                            <p><a title="<?php echo $list[$i]['subject']?>" href="<?php echo $list[$i]['href'].'&amp;sca='.urlencode($list[$i]['ca_name']); ?>"><?php echo mb_strimwidth($list[$i]['subject'], 0, 25, '', 'utf-8');?></a></p>
                        </div>
                    <?php } else { ?>
                        <img src="<?php echo $img?>" alt="<?php echo $list[$i]['subject']?>"/>
                    <?php } ?>
                </li>
        <?php }?>
    </ul>
</div>


<script type="text/javascript" src="<?=$latest_skin_url?>/js/jquery.easing.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("#picLsy li").hover(function(){
		$(this).find('.text:not(:animated)').stop().animate({top:"0%"}, {easing:"swing"}, 50, function(){});
	},function () {
		$(this).find('.text').stop().animate({top:"100%"}, {easing:"swing"}, 50, function(){});
	});
});
</script>
