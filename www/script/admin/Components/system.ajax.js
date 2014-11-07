/**
 * System Ajax
 *
 */
var System_Ajax = new Class({

	Implements: [Options, Events],

	options: {
		// onClose : function(){}
	},
	page: false,
	wrapper: false,
	elements : {},

	initialize : function(wrapper, page, options){
		this.setOptions(options);

		this.wrapper = wrapper;
		this.page = page;

		this.setupElements();
		this.attach();
	},

	setupElements: function(){
		// todo meybe partial ajax loading indicator, not whole page
	},

	attach: function(){
		this.wrapper.addEvent('click', this.xhrCall.bind(this));
	},

	detach: function(){

	},

	xhrCall: function(event){
		event.stop();

		var jsonRequest = new Request.JSON({
			url: this.wrapper.get('href'),
			onRequest: function(){
				this.page.showLoading();
			}.bind(this),
			onSuccess: function(response){
				// dirty version of remove line from data-list
				this.wrapper.getParent().getParent().remove();
				this.page.hideLoading();
				/* TODO : hide flash message
				for (var i in response.snippets) {
					document.id(i).set('html', response.snippets[i]);
				}
				*/
			}.bind(this)
		});

		// TODO: remove this hot fix
		if (this.wrapper.get('data-confirm')) {
			if (this.wrapper.get('data-confirm-continue') == 'true'){
				jsonRequest.send();
			}
		} else {
			jsonRequest.send();
		}
	}
});