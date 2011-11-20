window.addEvent('domready',function(){
	var barservice = 0;
	var fass = 0;
	var p = $$('.fadein');
	p.each(function(el){
	el.effect('opacity',{ duration:2500, wait:false, transition:Fx.Transitions.Back.easeOut }).start(0,1);
	})
	var p = new avScroll({container:'innerScroller', imgWidth:233, scrollRange:24, leftHandle:'leftClicker', rightHandle:'rightScroller', rw:'leftFF', ff:'rightFF', speed:700});

})
