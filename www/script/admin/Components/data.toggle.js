/**
 * Data Toggle
 *
 */
var Data_Toggle = new Class({
	/*
	Implements: [Options, Events],

	options: {
		// onClose : function(){}
	},
	*/
	wrapper: false,
	elements : {},

	initialize : function(wrapper, options){
		// this.setOptions(options);

		this.wrapper = wrapper;

		this.setupElements();
		this.attach();
	},

	setupElements: function(){
		this.elements.toggleElements = $$(this.wrapper.get('data-toggle'));
	},

	attach: function(){
		this.wrapper.addEvent('click', this.toggleElements.bind(this));
	},

	detach: function(){
		// todo implement
	},

	toggleElements: function(e){
		if (this.wrapper.get('tag') == 'a'){
			e.preventDefault();
		}
		this.elements.toggleElements.toggleClass('hide');
	}
});