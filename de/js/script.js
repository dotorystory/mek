$(function() {

  AOS.init({
     duration: 1500,
  });
  // Adobe Fonts 제거됨 (use.typekit.net)
  
  // 사이트맵
  // let mapList = $("#map-gnb .map_menu");
  // mapList.hover(function(){
  //   $(this).addClass("active").find(".lnb_two li").css({display:"block"})
  // },function(){
  //   $(this).removeClass("active").find(".lnb_two li").css({display:"none"})
  // })
  $(".m-gnb").click(function(){
    $("#sitemap").slideDown(400, 'linear').show();
  });
  
  $(".site_close").click(function(){
    $("#sitemap").slideUp(400, 'linear').hide();
  });
  
  // 서브 lnb
  // $(document).ready(function() {
  //     $("#snb > li").click(function() {
  //         $(this).find(".lnb").slideToggle().toggleClass("on");
  //     });
  //     $("#snb > li").mouseleave(function() {
  //         $(this).find(".lnb").slideUp().removeClass("on");
  //     });
  // });
  
  
  
  // 메인 비주얼 스와이퍼
    var swiper = new Swiper("#main_01 .swiper-container", {
    loop: true,
    allowTouchMove: false,
    autoHeight: true,
    slidesPerView: '1',
    touchEventsTarget: 'wrapper',
    centeredSlides: true,
    maxBackfaceHiddenSlides:4,
    spaceBetween: 0, // 슬라이드 간의 거리(px 단위)
    autoplay: {
    delay: 6000,
    disableOnInteraction: false,
    },
    controller: {
    control: pagingSwiper,
    },
    pagination: {
    el: ".swiper-pagination",
    type: "fraction",
    formatFractionCurrent: function (number) {
                return ('0' + number).slice(-2);
            },
            formatFractionTotal: function (number) {
                return ('0' + number).slice(-2);
            },
    },
    navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
    },
    });
  //두번째 페이지네이션
    var pagingSwiper = new Swiper("#main_01 .swiper-container", {
    loop: true,
  
    pagination: {
    el: ".swiper-pagination2",
    clickable: true,
    },
    controller: {
    control: swiper
    },
    });
    swiper.controller.control = pagingSwiper;
    pagingSwiper.controller.control = swiper;
  
  
  
  //푸터 언어
  $("#footer .footer_link .ft_global").click(function() {
      $(".ft_global_list").slideToggle();
  });
  
  $("#footer .footer_link .ft_global").mouseleave(function() {
      $(".ft_global_list").slideUp();
  });
  
  
            var swiper = new Swiper("#main_06 .main_cert_patent .main_cert_bond .swiper-container", {
              loop: true,
              loopAdditionalSlides: 1,
              spaceBetween: 8,
              centeredSlides: false,
              slidesPerView: 1,
              loopedSlides:2,
              navigation: {
                nextEl: ".swiper-button-next3",
                prevEl: ".swiper-button-prev3",
              },
              breakpoints: {
          500: {
            slidesPerView: 2,
            spaceBetween: 10,
          },
          725: {
            slidesPerView: 3,
            spaceBetween: 15,
          },
          950: {
            slidesPerView: 4,
            spaceBetween: 20,
          },
          1300: {
            slidesPerView: 5,
            spaceBetween: 25,
          },
        },
            });
  
            var swiper = new Swiper("#main_06 .main_cert .main_cert_wrap .main_cert_certification .main_cert_bond .swiper-container", {
              loop: true,
              loopAdditionalSlides: 1,
              spaceBetween: 8,
              centeredSlides: false,
              slidesPerView: 1,
              loopedSlides:2,
              navigation: {
                nextEl: ".swiper-button-next4",
                prevEl: ".swiper-button-prev4",
              },
              breakpoints: {
          500: {
            slidesPerView: 2,
            spaceBetween: 10,
          },
          725: {
            slidesPerView: 3,
            spaceBetween: 15,
          },
          950: {
            slidesPerView: 4,
            spaceBetween: 20,
          },
          1300: {
            slidesPerView: 5,
            spaceBetween: 25,
          },
        },
            });
  
      $("#sub_wrap.sub03_04 .snb_button ul .t-die").click(function(){
        $("#sub_wrap.sub03_04 .t-die").addClass("active");
        $("#sub_wrap.sub03_04 .wc").removeClass("active");
        $("#sub_wrap.sub03_04 .win").removeClass("active");
      });
      $("#sub_wrap.sub03_04 .snb_button ul .wc").click(function(){
        $("#sub_wrap.sub03_04 .wc").addClass("active");
        $("#sub_wrap.sub03_04 .t-die").removeClass("active");
        $("#sub_wrap.sub03_04 .win").removeClass("active");
      });
      $("#sub_wrap.sub03_04 .snb_button ul .win").click(function(){
        $("#sub_wrap.sub03_04 .win").addClass("active");
        $("#sub_wrap.sub03_04 .wc").removeClass("active");
        $("#sub_wrap.sub03_04 .t-die").removeClass("active");
      });
  
    });
    document.addEventListener("DOMContentLoaded", function() {
        var snbItems = document.querySelectorAll("#snb > li");
  
        snbItems.forEach(function(item) {
            item.addEventListener("click", function() {
                var lnb = this.querySelector(".lnb");
                if (lnb.style.display === "none" || lnb.style.display === "") {
                    lnb.style.display = "block";
                    lnb.classList.toggle("on");
                } else {
                    lnb.style.display = "none";
                    lnb.classList.remove("on");
                }
            });
  
            item.addEventListener("mouseleave", function() {
                var lnb = this.querySelector(".lnb");
                lnb.style.display = "none";
                lnb.classList.remove("on");
            });
        });
  
  
    });
  