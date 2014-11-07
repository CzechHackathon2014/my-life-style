/**
 * Block Datalist
 *
 * for: b-datalist
 */
var Block_Datalist = new Class({

	// Implements: [/*Options,*/ Events],
	/*
	options: {
		// onClose : function(){}
	},
	*/

	wrapper: false,
	page: false,
	elements : {},

	initialize : function(wrapper, page, options){
		// this.setOptions(options);

		this.wrapper = wrapper;
		this.page = page;

		// this.setupElements();
		this.attach();
	},
/*
	setupElements: function(){

	},
*/
	attach: function(){
		this.wrapper.addEvent('click:relay(.b-datalist__line)', this.defaultClickAction.bind(this));
	},

	detach: function(){
		// todo implement
	},

	defaultClickAction: function(e, line){
		e.preventDefault();
		// find .default-click-action
		var clickElement = line.getElement('.default-click-action');
		if (clickElement){
			this.page.showLoading();
			this.page.navigate(clickElement.get('href'));
		}
	}
});