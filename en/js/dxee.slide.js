// dxee animation
var dxee = dxee || {};
(function (){
  function Slide (option) {

    // 슬라이드 포지션
    var pos = 0

    // 자동 실행
    if (option.autoPlay)
      timer = setTimeout(play(option.type), option.setTime)

    // 슬라이드 실행
    function play (type) {
      // 함수 형태로 반환하여 setTimeout에서 실행될 수 있도록 함
      return {
        // 오른쪽에서 왼쪽으로 진행하는 슬라이드
        rightToLeft: function () {
          // 슬라이드 선택
          if (++pos > option.len) pos = 0
          else if (pos < 0) pos = option.len - 1

          // 이전 슬라이드 비활성, 현재 슬라이드 활성
          var before = option.wrap.find('.active').removeClass('active')

          // 슬라이드 실행
          function realPlay () {
            option.wrap.stop().animate({
              marginLeft: -pos * 100 + '%'
            }, option.playTime, function () {
              // 마지막 장면에서 첫번째 장면으로
              if (pos === option.len) {
                $(this).css({marginLeft: 0})
                pos = 0
              }
              var after = option.wrap.find('>*').eq(pos).addClass('active')
              // 애니메이션이 있다면, 실행
              if (dxee.Animation) {
                dxee.Animation({obj: after })
              }
              // 일정 시간 후 자동 실행
              if (option.autoPlay) {
                timer = setTimeout(play(type), option.setTime)
              }
            })
          }

          // 애니메이션이 있다면, 실행
          if (dxee.Animation) {
            dxee.Animation({obj: before, reverse: true, callback: realPlay })
          } else {
            realPlay()
          }
        }
      }[type]
    }
  }

  // 슬라이드 세팅
  Slide.init = function (obj) {
    $('.dxee-slide').each(function () {
      // option 설정
      var target = $(this)
      var option = {
        autoPlay: target.data('autoPlay') || true,      // 자동실행 여부
        playTime: target.data('playTime') || 1000,      // 슬라이드 실행 시간
        setTime: target.data('setTime') || 5000,        // 전환 시간
        type: target.data('type') || 'rightToLeft',     // 슬라이드 타입
        wrap: target.find('.slide-wrap'),               // 이동하는 대상
        len: target.find('.slide-wrap>*').length        // 슬라이드 갯수
      }

      // 첫번째 슬라이드 활성화
      target.find('.slide-wrap>*:first-child').addClass('active')

      // 슬라이드 타입별 요소 사이즈 지정
      switch (option.type) {
        case 'rightToLeft' :
          option.wrap.css({
            display: 'flex',
            width: (option.len + 1) * 100 + '%',
            height: '100%'
          })
          .find('>*').css({
            width: 100 / (option.len + 1) + '%',
            height: '100%'
          })
          option.wrap.find('>*:first-child').clone().appendTo(option.wrap)
        break;
      }
      var timer = null
      new Slide(option)
    })
  }

  dxee.Slide = Slide
}());