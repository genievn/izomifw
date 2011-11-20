// The following should be put in your external js file,
// with the rest of your ondomready actions.

window.addEvent('domready', function(){
	$$('input.DatePicker').each( function(el){		
		new DatePicker(el);
	});

});