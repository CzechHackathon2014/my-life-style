/**
 * System Navigation
 *
 */
var System_Navigation = new Class({
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
		this.body = $$('body')[0];
	},

	attach: function(){
		this.wrapper.addEvent('click', this.openSystemNavigation.bind(this));
	},

	detach: function(){
		// todo implement
	},

	openSystemNavigation: function(e){
		this.body.toggleClass('system-navigation--active');
		this.wrapper.getParent().toggleClass('open');
	}
});