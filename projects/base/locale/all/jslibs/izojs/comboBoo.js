window.addEvent('domready', function() {	
	$$('.comboBoo').each(function(el){			
		new comboBoo(el);
	});
});