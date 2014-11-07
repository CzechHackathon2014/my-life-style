/**
 * Block File Input
 *
 * for: b-file-input
 */

var Block_FileInput  = new Class({

	// Implements: [Options, Events],
	/*
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
		this.elements.input = this.wrapper.getElement('.b-file-input__input').getElement('input');
		this.elements.filename = this.wrapper.getElement('.b-file-input__filename');
	},

	attach: function(){
		this.elements.input.addEvent('change', this.getInputFileName.bind(this));
	},

	detach: function(){

	},

	getInputFileName: function(e){
		this.elements.filename.set('text', this.elements.input.get('value').replace(/^.*[\\\/]/, ''));
	}
});
