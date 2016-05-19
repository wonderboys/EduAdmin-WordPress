var edu = edu ? edu : {};

edu.apiclient = {
	baseUrl: null,
	parseDocument: function(doc) {
		if(wp_edu != undefined) {
			this.baseUrl = wp_edu.BaseUrl + '/wp-json/eduadmin/v1/';
		}
		var lw = doc.querySelector('[data-eduwidget="loginwidget"]');
		if(lw) {
			this.getLoginWidget(lw);
		}
	},
	getEventList: function(target, objectid) {
	},
	getNextEvent: function(target, objectid) {
	},
	getLoginWidget: function(target) {
		jQuery.ajax({
			url: this.baseUrl + 'login/widget',
			data: {
				logintext: 'Log in',
				logouttext: 'Log out',
				guesttext: 'Guest'
			},
			success: function(d) {
				jQuery(target).replaceWith(d);
			}
		});
	}
};

(function() {
	if(jQuery != undefined) {
		jQuery('document').ready(function() {
			edu.apiclient.parseDocument(document);
		});
	} else {
		edu.apiclient.parseDocument(document);
	}
})();