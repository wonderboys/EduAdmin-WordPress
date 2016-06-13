var edu = edu ? edu : {};

edu.apiclient = {
	baseUrl: null,
	courseFolder: null,
	authToken: null,
	CookieBase: 'edu_',
	parseDocument: function() {
		if(wp_edu != undefined) {
			this.baseUrl = wp_edu.BaseUrl + '/wp-content/plugins/eduadmin/backend/edu.api.backend.php';
			this.courseFolder = wp_edu.CourseFolder;
			this.authJS(wp_edu.ApiKey, function() {
				edu.apiclient.replaceLoginWidget();
				edu.apiclient.replaceEventListWidget();
				edu.apiclient.replaceCourseListDates();
				edu.apiclient.replaceCourseEventList();
			});
		}
	},
	replaceLoginWidget: function() {
		var lw = document.querySelectorAll('[data-eduwidget="loginwidget"]');
		if(lw) {
			var widgets = lw.length;
			for(var i = 0; i < widgets; i++) {
				edu.apiclient.getLoginWidget(lw[i]);
			}
		}
	},
	replaceEventListWidget: function() {
		var evLists = document.querySelectorAll('[data-eduwidget="eventlist"]');
		for(var i = 0, len = evLists.length; i < len; i++) {
			edu.apiclient.getEventList(evLists[i]);
		}
	},
	replaceCourseListDates: function() {
		var courseDateObjects = document.querySelectorAll('[data-eduwidget="courseitem-date"]');
		var objectIds = [];
		for(var i = 0, len = courseDateObjects.length; i < len; i++) {
			objectIds.push(courseDateObjects[i].attributes['data-objectid'].value);
		}
		if(objectIds.length > 0) {
			edu.apiclient.getCourseListDates(objectIds);
		}
	},
	replaceCourseEventList: function() {
		var eventList = document.querySelectorAll('[data-eduwidget="listview-eventlist"]');
		var eventLength = eventList.length;
		for(var i = 0; i < eventLength; i++) {
			edu.apiclient.getCourseEventList(eventList[i]);
		}
	},
	authJS: function(apiKey, next) {
		if(this.GetCookie('apiToken') == null || this.GetCookie('apiToken') == '') {
			jQuery.ajax({
				url: this.baseUrl + '?authenticate',
				type: 'POST',
				data: {
					key: apiKey
				},
				success: function(d) {
					edu.apiclient.SetCookie('apiToken', d, 1000 * 60 * 30);
					edu.apiclient.authToken = d;
					next();
				}
			});
		} else {
			var t = this.GetCookie('apiToken');
			edu.apiclient.authToken = t;
			next();
		}
	},
	getCourseListDates: function(objectIds) {
		jQuery.ajax({
			url: this.baseUrl + '?module=listview_courselist',
			beforeSend: function(xhr) {
				xhr.setRequestHeader('Edu-Auth-Token', edu.apiclient.authToken);
			},
			type: 'POST',
			data: {
				objectIds: objectIds,
				phrases: wp_edu.Phrases
			},
			success: function(d) {
				var o = d;
				if(typeof d !== "object")
					o = JSON.parse(d);
				for(var k in o) {
					if(o.hasOwnProperty(k)) {
						var target = document.querySelector('[data-eduwidget="courseitem-date"][data-objectid="' + k + '"]');
						if(target) {
							target.innerHTML = o[k];
						}
					}
				}
			}
		});
	},
	getCourseEventList: function(target) {
		jQuery.ajax({
			url: this.baseUrl + '?module=listview_eventlist',
			beforeSend: function(xhr) {
				xhr.setRequestHeader('Edu-Auth-Token', edu.apiclient.authToken);
			},
			type: 'POST',
			data: {
				phrases: wp_edu.Phrases
			},
			success: function(d) {
				edu.apiclient.renderEventListTemplate(jQuery(target).data('template'), d);
			}
		});
	},
	getEventList: function(target) {
		jQuery.ajax({
			url: this.baseUrl + '?module=detailinfo_eventlist',
			beforeSend: function(xhr) {
				xhr.setRequestHeader('Edu-Auth-Token', edu.apiclient.authToken);
			},
			type: 'POST',
			data: {
				objectid: jQuery(target).data('objectid'),
				city: jQuery(target).data('city'),
				groupbycity: jQuery(target).data('groupbycity'),
				baseUrl: wp_edu.BaseUrl,
				courseFolder: wp_edu.CourseFolder,
				showmore: jQuery(target).data('showmore'),
				spotsleft: jQuery(target).data('spotsleft'),
				fewspots: jQuery(target).data('fewspots'),
				spotsettings: jQuery(target).data('spotsettings'),
				eid: jQuery(target).data('eid'),
				phrases: wp_edu.Phrases
			},
			success: function(d) {
				jQuery(target).replaceWith(d);
			}
		});
	},
	renderEventListTemplate: function(template, data) {
		console.log(template);
		console.log(data);
	},
	getNextEvent: function(target) {
	},
	getLoginWidget: function(target) {
		var loginText = wp_edu.Phrases['Log in'];
		var logoutText = wp_edu.Phrases['Log out'];
		var guestText = wp_edu.Phrases['Guest'];
		if(jQuery(target).data('logintext')) {
			loginText = jQuery(target).data('logintext');
		}

		if(jQuery(target).data('logouttext')) {
			logoutText = jQuery(target).data('logouttext');
		}

		if(jQuery(target).data('guesttext')) {
			guestText = jQuery(target).data('guesttext');
		}

		jQuery.ajax({
			url: this.baseUrl + '?module=login_widget',
			type: 'POST',
			data: {
				baseUrl: wp_edu.BaseUrl,
				courseFolder: wp_edu.CourseFolder,
				logintext: loginText,
				logouttext: logoutText,
				guesttext: guestText
			},
			success: function(d) {
				jQuery(target).replaceWith(d);
			}
		});
	},
	GetCookie: function (name) {
        try {
            var cookie = document.cookie;
            name = this.CookieBase + name;
            var valueStart = cookie.indexOf(name + "=") + 1;
            if (valueStart === 0) {
                return null;
            }
            valueStart += name.length;
            var valueEnd = cookie.indexOf(";", valueStart);
            if (valueEnd == -1)
                valueEnd = cookie.length;
            return decodeURIComponent(cookie.substring(valueStart, valueEnd));
        } catch (e) {
            ;
        }
        return null;
    },
    SetCookie: function (name, value, expire) {
        var temp = this.CookieBase + name + "=" + escape(value) + (expire !== 0 ? "; path=/; expires=" + ((new Date((new Date()).getTime() + expire)).toUTCString()) + ";" : "; path=/;");
        document.cookie = temp;
    },
    CanSetCookies: function () {
        SetCookie('_eduCookieTest', 'true', 0);
        var can = GetCookie('_eduCookieTest') != null;
        DelCookie('_eduCookieTest');
        return can;
    },
    DelCookie: function (name) {
        document.cookie = this.CookieBase + name + '=0; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
};

(function() {
	if(jQuery != undefined) {
		jQuery('document').ready(function() {
			edu.apiclient.parseDocument();
		});
	} else {
		edu.apiclient.parseDocument();
	}
})();