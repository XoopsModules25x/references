window.addEvent('domready', function() {

	//create our Accordion instance
	var myAccordion = new Accordion($('accordion'), 'h3.toggler', 'div.element', {
		opacity: false,
		alwaysHide: false,
		onActive: function(toggler, element){
			toggler.setStyle('color', '#41464D');
		},
		onBackground: function(toggler, element){
			toggler.setStyle('color', '#528CE0');
		}
	});
});