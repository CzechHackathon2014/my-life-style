/**
 * Block b-Button Confirm
 *
 */
var Block_ButtonConfirm = new Class({

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
		this.elements.bQuestion = this.wrapper.getElement('.b-button-confirm__question');
		this.elements.bConfirm = this.wrapper.getElement('.b-button-confirm__confirm');
		this.elements.bCancel = this.wrapper.getElement('.b-button-confirm__cancel');
	},

	attach: function(){
		this.wrapper.addEvent('click', this.toggleConfirmDialog.bind(this));
		this.elements.bCancel.addEvent('click', this.cancelConfirmDialog.bind(this));
		this.elements.bConfirm.addEvent('click', this.confirmConfirmDialog.bind(this));
	},

	detach: function(){

	},

	toggleConfirmDialog: function(e){
		this.wrapper.toggleClass('b-button-confirm--question');
		this.elements.bQuestion.toggleClass('b-button-confirm__button--hide');
		this.elements.bCancel.toggleClass('b-button-confirm__button--hide');
		this.elements.bConfirm.toggleClass('b-button-confirm__button--hide');
	},

	cancelConfirmDialog: function(e){
		this.wrapper.toggleClass('b-button-confirm--question');
		this.elements.bQuestion.toggleClass('b-button-confirm__button--hide');
		this.elements.bCancel.toggleClass('b-button-confirm__button--hide');
		this.elements.bConfirm.toggleClass('b-button-confirm__button--hide');
		e.stopPropagation();
	},

	confirmConfirmDialog: function(e){
		// todo spinner ?
		e.stopPropagation();
	}
});