var Page = Page || {};

Page.statusScrolling = false;
Page.initLoading = false;


Page.navigate = function(link){
	window.location = link;
}


Page.hideLoading = function(){
	document.id(document.body).setStyle('overflow');
	Page.initLoading = document.id('initLoading');
	Page.initLoading.set('tween', { duration: 700 });
	Page.initLoading.fade('out');
	//Page.initLoading.destroy.delay(150, this.initLoading);
}


Page.showLoading = function(){
	document.id(document.body).setStyle('overflow', 'hidden');
	// Page.initLoading.setStyle('height', $$('.base-panel')[0].getStyle('height'));
	Page.initLoading.set('tween', { duration: 300 });
	Page.initLoading.fade('in');
}


/**
 * We don't use it, we use CSS animation
 */
Page.hideFlashMessages = function(){
	$$('.flash').each(function(item) {
		item.addEvent('click', function(e){
			item.destroy();
		});
		item.set('tween', { duration: 700 });
		item.fade.delay(3000, item, 'out');
		item.destroy.delay(3500, item);
	});
}

Page.start = function (){

	$$('a[rel]').each(function(item) {
		if(item.get('rel') == 'external'){
			item.set('target', '_blank') ;
		}
		if(item.get('rel') == 'blank'){
			item.set('target', '_blank') ;
		}
	});

	$$('a[data-confirm]').each(function(item) {
		item.addEvent('click', function(e){
			if (!window.confirm(item.get('data-confirm'))) {
				e.stop();
				item.set('data-confirm-continue', 'false');
			} else {
				item.set('data-confirm-continue', 'true');
				if (!item.hasClass('ajax')){
					Page.navigate(item.get('href'));
					e.stop();
				}
			}
		});
	});

	// todo use delegation on body ?
	$$('*[data-loading]').each(function(item){
		item.addEvent('click', function(e){
			//param is Page or todo for cutom element ?
			Page.showLoading();
		});
	});

	/*
	$$('form').each(function(form){
		form.addEvent('submit', function(e){
			// todo: try if form is valid and sended
			// todo: what about ajax send?
			// (Nette.validateForm(form))
			Page.showLoading();
		});
	});
	*/


	// ======================================
	//   C O M P O N E N T S
	// ======================================

	// init b-datalist
	$$('.b-datalist').each(function(datalist) {
		new Block_Datalist(datalist, Page); // todo Page???
	});
	// init b-password-input
	$$('.b-password-input').each(function(input){
		new Block_PasswordInput(input);
	});
	// init b-quick-search
	$$('.b-quick-search').each(function(block){
		new Block_QuickSearch(block);
	});
	// init b-input-file
	$$('.b-file-input').each(function(input){
		new Block_FileInput(input);
	});
	// init b-confirm-button
	$$('.b-button-confirm').each(function(button){
		new Block_ButtonConfirm(button);
	});

	// == DATA ===
	// data toggle
	$$('*[data-toggle]').each(function(ele){
		new Data_Toggle(ele);
	});

	// == SYSTEM NAVIGATION ==
	var btnSystemNavigation = document.id('btn-open-system-navigation');
	if (btnSystemNavigation){
		new System_Navigation(btnSystemNavigation);
	}

	$$('.ajax').each(function(link){
		new System_Ajax(link, Page); // todo Page?
	});

	Page.hideLoading();

}
