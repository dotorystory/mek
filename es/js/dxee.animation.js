// animation set
var dxee = dxee || {};
(function (){
	function Animation (option) {

		/* set variable */
		this.delay     = 100
		this.lastTimer = 0
		this.obj       = option.obj
		this.reverse   = option.reverse || false
		this.callback  = option.callback || function () {}

		this.find = function (ele) { return this.obj.find(ele) }

		Animation.allClear()

		var timer = 0
		var i = this // instance
		var seq = i.reverse ? i.find('.target-reverse') : i.find('.animation')
		var len = seq.length
		seq.each(function (num) {
			var target = $(this)
			Animation.timeObj.push(setTimeout(function () {
				if(i.reverse === true){
					var idx = len - num - 1
					seq.eq(idx).addClass("animation-before type2").removeClass('target-reverse')
				} else {
					target.removeClass("animation-before type2").addClass('target-reverse')
				}
			}, timer))
			timer += i.delay + (target.data('delay') ? parseInt(target.data('delay')) : 0)
		})
		i.lastTimer = timer + 300
		setTimeout(i.callback, i.lastTimer)
	}

	Animation.allClear = function () {
		Animation.timeObj.forEach(function (element) { clearTimeout(element) })
		Animation.timeObj = []
	}

	Animation.styleSet = function () {
		var styleText = '\
			<style>\
				.animation{opacity:1;transform:inherit;transition:1s}\
				.animation.animation-before{opacity:0;transform:scale(0);transition:0s}\
				.animation.animation-before.top2btm{transform:translateY(-100px)}\
				.animation.animation-before.btm2top{transform:translateY(100px)}\
				.animation.animation-before.left2right{transform:translateX(-100px)}\
				.animation.animation-before.right2left{transform:translateX(100px)}\
				.animation.animation-before.big2small{transform:scale(2, 2)}\
				.animation.animation-before.type2{transition:1s}\
			</style>'
		$(styleText).appendTo('head')
	}

	Animation.init = function () {
		$('.child-animation>*:not(.child-animation)').each(function () {
			var _this = $(this)
			var parent = _this.parent()
			_this.addClass("animation")
			if (parent.data('type')) {
				_this.addClass(parent.data('type'))
			}
		})
		$('.animation').addClass('animation-before')
		Animation.timeObj = []
		Animation.styleSet()
	}

	dxee.Animation = Animation
}());