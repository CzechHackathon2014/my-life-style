/**
 * Block Password Input
 *
 * for: b-password-input
 *
 * required: StrongPass
 */
var Block_PasswordInput = new Class({

	// Implements: [/*Options,*/ Events],
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
		this.elements.input = this.wrapper.getElement('input');
		this.elements.status = this.wrapper.getElement('.b-password-input__indicator');
		this.elements.switcher = this.wrapper.getElement('.b-password-input__switcher');
	},

	attach: function(){
		if (this.elements.switcher){
			this.elements.switcher.addEvent('click', this.switchInputType.bind(this));
		}
		if (this.elements.status){
			new StrongPass(this.elements.input, {
				render: false,
				onFail: function(score, verdict) {
					this.elements.status.set('text', 'weak');
					this.elements.status.addClass('b-password-input__indicator--weak');
					this.elements.status.removeClass('b-password-input__indicator--strong');
					this.elements.status.removeClass('b-password-input__indicator--normal');
				}.bind(this),
				onPass: function(score, verdict) {
					this.elements.status.removeClass('b-password-input__indicator--weak');
					if (score > 3){
						this.elements.status.set('text', 'strong');
						this.elements.status.addClass('b-password-input__indicator--strong');
						this.elements.status.removeClass('b-password-input__indicator--normal');
					} else {
						this.elements.status.set('text', 'good');
						this.elements.status.addClass('b-password-input__indicator--normal');
						this.elements.status.removeClass('b-password-input__indicator--strong');
					}
				}.bind(this)/*,
				onBanned: function(word) {
					console.log('banned');
				}.bind(this)*/
			});
		}
	},

	detach: function(){
		// todo implement
	},

	switchInputType: function(e){
		e.stop();
		if (this.elements.input.get('type') == 'text'){
			this.elements.input.set('type', 'password');
			this.elements.switcher.set('text', 'show');
		} else {
			this.elements.input.set('type', 'text');
			this.elements.switcher.set('text', 'hide');
		}
	}

});
