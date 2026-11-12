$(function() {

  // AOS 초기화 (AOS가 로드된 경우만)
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 1500,
    });
  } else {
    // AOS가 로드되지 않은 경우: 모든 data-aos 요소를 즉시 표시
    $('[data-aos]').css({
      'opacity': '1',
      'transform': 'none',
      'visibility': 'visible'
    });
  }
  // Adobe Fonts 제거됨 (use.typekit.net) - Google Noto Sans로 대체
  
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
  // NOTE: 동일 컨테이너에 Swiper를 2번 초기화하면 loop가 슬라이드 DOM 순서를 깨서
  // 01에 마지막 이미지(지도)가 보이는 오프셋 버그가 난다. 반드시 단일 인스턴스만 사용.
  var mainVisualSwiper = new Swiper("#main_01 .swiper-container", {
    loop: true,
    initialSlide: 0,
    allowTouchMove: false,
    autoHeight: true,
    slidesPerView: 1,
    touchEventsTarget: 'wrapper',
    centeredSlides: false,
    spaceBetween: 0,
    autoplay: {
      delay: 6000,
      disableOnInteraction: false,
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
    on: {
      init: function (sw) {
        setupMainVisualBullets(sw);
      },
      slideChange: function (sw) {
        syncMainVisualBullets(sw);
      },
    },
  });
  // 하위 호환: 기존 코드가 swiper 변수를 참조할 수 있음
  var swiper = mainVisualSwiper;

  function setupMainVisualBullets(sw) {
    var $pag2 = $("#main_01 .swiper-pagination2");
    if (!$pag2.length) return;
    var total = $("#main_01 .swiper-wrapper > .swiper-slide:not(.swiper-slide-duplicate)").length;
    if (!total) total = 4;
    $pag2.empty();
    for (var i = 0; i < total; i++) {
      $pag2.append('<span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide ' + (i + 1) + '" data-index="' + i + '"></span>');
    }
    $pag2.off('click.mainVisual').on('click.mainVisual', '.swiper-pagination-bullet', function () {
      var idx = parseInt($(this).attr('data-index'), 10);
      if (!isNaN(idx)) sw.slideToLoop(idx);
    });
    syncMainVisualBullets(sw);
  }

  function syncMainVisualBullets(sw) {
    var $pag2 = $("#main_01 .swiper-pagination2");
    if (!$pag2.length) return;
    var idx = typeof sw.realIndex === 'number' ? sw.realIndex : 0;
    $pag2.find('.swiper-pagination-bullet')
      .removeClass('swiper-pagination-bullet-active')
      .eq(idx).addClass('swiper-pagination-bullet-active');
  }
  
  
  
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
        $("#sub_wrap.sub03_04 .surface").removeClass("active");
      });
      $("#sub_wrap.sub03_04 .snb_button ul .wc").click(function(){
        $("#sub_wrap.sub03_04 .wc").addClass("active");
        $("#sub_wrap.sub03_04 .t-die").removeClass("active");
        $("#sub_wrap.sub03_04 .surface").removeClass("active");
      });
      $("#sub_wrap.sub03_04 .snb_button ul .surface").click(function(){
        $("#sub_wrap.sub03_04 .surface").addClass("active");
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
  