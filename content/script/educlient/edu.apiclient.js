var edu = edu ? edu : {};

edu.apiclient = {
	parseDocument: function(doc) {
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
		console.log(target);
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