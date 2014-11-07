/**
 * Block Quick Search
 *
 * for: .b-quick-search
 * @require Element.Event.Pseudos
 */
var Block_QuickSearch = new Class({

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
		this.requestForm = false;

		this.setupElements();
		this.attach();
	},

	setupElements: function(){
		this.elements.input = this.wrapper.getElement('.b-quick-search__input');
		this.elements.form = this.wrapper.getElement('form');
		this.elements.btnClear = this.wrapper.getElement('.b-quick-search__clear');

		// disable ? or autofocus
		if (this.wrapper.hasClass('b-quick-search--disabled')){
			this.elements.input.set('disabled', 'disabled');
		} else {
			this.elements.input.focus();
		}


		this.requestForm = new Request.JSON({
			url: this.elements.form.get('action'),
			onSuccess: function(response){
				/* TODO: do samostatne tridy na zpracovani Nette.Response */
				for (var i in response.snippets) {
					document.id(i).set('html', response.snippets[i]);
				}
			}
		});
	},

	attach: function(){
		this.elements.btnClear.addEvent('click', this.doSomeThing.bind(this));
		this.elements.input.addEvent('input:pause', this.doRequest.bind(this));
	},

	detach: function(){
		// todo: implement
	},

	doSomeThing: function(e){
		e.stop();
		this.elements.input.set('value', '');
		this.requestForm.get(this.getFormParams());
		this.elements.btnClear.addClass('b-quick-search__clear--hide');
	},

	doRequest: function(e){
		var stringLength = this.elements.input.get('value').length;
		if (stringLength >= 3){
			this.elements.btnClear.removeClass('b-quick-search__clear--hide');
			this.requestForm.get(this.getFormParams());
		}
		if (stringLength == 0){
			this.elements.btnClear.addClass('b-quick-search__clear--hide');
			this.requestForm.get(this.getFormParams());
		}
	},

	getFormParams: function(){
		return this.elements.form.toQueryString().trim();
	}


});