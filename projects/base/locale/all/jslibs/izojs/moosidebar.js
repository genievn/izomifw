function init(){
	$('sideBarTab').addEvent('click', function(){extendContract()});
}

window.addEvent('domready', function(){init()});