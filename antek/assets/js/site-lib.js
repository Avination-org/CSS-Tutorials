(function ($, root, undefined) {
	"use strict";
	
	try {
		document.createEvent("TouchEvent");
		root.ideapark_is_mobile = true;
	} catch (e) {
		root.ideapark_is_mobile = false;
	}
	root.ideapark_is_responsinator = false;
	if (document.referrer) {
		root.ideapark_is_responsinator = (document.referrer.split('/')[2] == 'www.responsinator.com');
	}
	
	var ideapark_scroll_busy = false;
	var ideapark_resize_busy = false;
	var ideapark_defer_action_enabled = true;
	var ideapark_defer_action_list = [];
	var ideapark_scroll_action_list = [];
	var ideapark_resize_action_list = [];
	var ideapark_resize_action_list_500 = [];
	var ideapark_resize_action_list_layout = [];
	
	root.ideapark_nav_text = [
		'<i class="ip-right h-carousel__prev"></i>',
		'<i class="ip-right h-carousel__next"></i>'
	];
	root.ideapark_is_rtl = $("body").hasClass('h-rtl');
	root.ideapark_on_transition_end = 'transitionend webkitTransitionEnd oTransitionEnd';
	root.ideapark_on_animation_end = 'animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd';
	root.ideapark_window_width = window.innerWidth;
	root.ideapark_is_mobile_layout = window.innerWidth < 1190;
	root.$ideapark_admin_bar = null;
	root.ideapark_adminbar_height = 0;
	root.ideapark_adminbar_visible_height = 0;
	root.ideapark_adminbar_position = 0;
	
	root.ideapark_debounce = function (func, wait, immediate) {
		var timeout;
		return function () {
			var context = this, args = arguments;
			var later = function () {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	};
	
	root.ideapark_isset = function (obj) {
		return typeof (obj) != 'undefined';
	};
	
	root.ideapark_empty = function (obj) {
		return typeof (obj) == 'undefined' || (typeof (obj) == 'object' && obj == null) || (typeof (obj) == 'array' && obj.length == 0) || (typeof (obj) == 'string' && ideapark_alltrim(obj) == '') || obj === 0;
	};
	
	root.ideapark_is_array = function (obj) {
		return typeof (obj) == 'array';
	};
	
	root.ideapark_is_function = function (obj) {
		return typeof (obj) == 'function';
	};
	
	root.ideapark_is_object = function (obj) {
		return typeof (obj) == 'object';
	};
	
	root.ideapark_alltrim = function (str) {
		var dir = arguments[1] !== undefined ? arguments[1] : 'a';
		var rez = '';
		var i, start = 0, end = str.length - 1;
		if (dir == 'a' || dir == 'l') {
			for (i = 0; i < str.length; i++) {
				if (str.substr(i, 1) != ' ') {
					start = i;
					break;
				}
			}
		}
		if (dir == 'a' || dir == 'r') {
			for (i = str.length - 1; i >= 0; i--) {
				if (str.substr(i, 1) != ' ') {
					end = i;
					break;
				}
			}
		}
		return str.substring(start, end + 1);
	};
	
	root.ideapark_ltrim = function (str) {
		return ideapark_alltrim(str, 'l');
	};
	
	root.ideapark_rtrim = function (str) {
		return ideapark_alltrim(str, 'r');
	};
	
	root.ideapark_dec2hex = function (n) {
		return Number(n).toString(16);
	};
	
	root.ideapark_hex2dec = function (hex) {
		return parseInt(hex, 16);
	};
	
	root.ideapark_in_array = function (val, thearray) {
		var rez = false;
		for (var i = 0; i < thearray.length; i++) {
			if (thearray[i] == val) {
				rez = true;
				break;
			}
		}
		return rez;
	};
	
	root.ideapark_detectIE = function () {
		var ua = window.navigator.userAgent;
		var msie = ua.indexOf('MSIE ');
		if (msie > 0) {
			return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
		}
		
		var trident = ua.indexOf('Trident/');
		if (trident > 0) {
			var rv = ua.indexOf('rv:');
			return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
		}
		
		var edge = ua.indexOf('Edge/');
		if (edge > 0) {
			return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
		}
		return false;
	};
	
	root.ideapark_loadScript = function (src, cb, async) {
		var script = document.createElement('script');
		script.async = !!(typeof async !== 'undefined' && async);
		script.src = src;
		
		script.onerror = function () {
			if (typeof cb !== 'undefined') {
				cb(new Error("Failed to load" + src));
			}
		};
		
		script.onload = function () {
			if (typeof cb !== 'undefined') {
				cb();
			}
		};
		
		document.getElementsByTagName("head")[0].appendChild(script);
	};
	
	root.ideapark_cookies = {
		
		get: function (name) {
			var e, b,
				cookie = document.cookie,
				p = name + '=';
			
			if (!cookie) {
				return;
			}
			
			b = cookie.indexOf('; ' + p);
			
			if (b === -1) {
				b = cookie.indexOf(p);
				
				if (b !== 0) {
					return null;
				}
			} else {
				b += 2;
			}
			
			e = cookie.indexOf(';', b);
			
			if (e === -1) {
				e = cookie.length;
			}
			
			return decodeURIComponent(cookie.substring(b + p.length, e));
		},
		
		set: function (name, value, expires, path, domain, secure) {
			var d = new Date();
			
			if (typeof (expires) === 'object' && expires.toGMTString) {
				expires = expires.toGMTString();
			} else if (parseInt(expires, 10)) {
				d.setTime(d.getTime() + (parseInt(expires, 10) * 1000)); // time must be in milliseconds
				expires = d.toGMTString();
			} else {
				expires = '';
			}
			
			if (typeof path == 'undefined') {
				path = ideapark_wp_vars.cookiePath;
			}
			
			if (typeof domain == 'undefined') {
				domain = ideapark_wp_vars.cookieDomain;
			}
			
			document.cookie = name + '=' + encodeURIComponent(value) +
				(expires ? '; expires=' + expires : '') +
				(path ? '; path=' + path : '') +
				(domain ? '; domain=' + domain : '') +
				(secure ? '; secure' : '');
		},
		
		remove: function (name, path, domain, secure) {
			this.set(name, '', -1000, path, domain, secure);
		}
	};
	
	root.ideapark_wpadminbar_resize = function () {
		$ideapark_admin_bar = $('#wpadminbar');
		if ($ideapark_admin_bar.length) {
			var window_width = $(window).width();
			if (window_width > 782 && $ideapark_admin_bar.hasClass('mobile')) {
				$ideapark_admin_bar.removeClass('mobile');
			} else if (window_width <= 782 && !$ideapark_admin_bar.hasClass('mobile')) {
				$ideapark_admin_bar.addClass('mobile');
			}
			ideapark_adminbar_height = $ideapark_admin_bar.outerHeight();
			ideapark_adminbar_position = $ideapark_admin_bar.css('position');
			
			if (ideapark_adminbar_position === 'fixed' || ideapark_adminbar_position === 'absolute') {
				$(".js-fixed").css({
					top         : ideapark_adminbar_visible_height,
					'max-height': 'calc(100% - ' + ideapark_adminbar_visible_height + 'px)'
				});
			} else {
				$(".js-fixed").css({
					top         : 0,
					'max-height': '100%'
				});
			}
			
			ideapark_wpadminbar_scroll();
		}
	};
	
	root.ideapark_wpadminbar_scroll = function () {
		if ($ideapark_admin_bar === null) {
			$ideapark_admin_bar = $('#wpadminbar');
		}
		if ($ideapark_admin_bar.length) {
			var scroll_top_mobile = window.scrollY;
			var top_new = 0;
			
			if (ideapark_adminbar_position === 'fixed') {
				top_new = ideapark_adminbar_height;
			} else {
				top_new = ideapark_adminbar_height - scroll_top_mobile;
				if (top_new < 0) {
					top_new = 0;
				}
			}
			
			if (ideapark_adminbar_visible_height != top_new) {
				ideapark_adminbar_visible_height = top_new;
				$(document).trigger('ideapark.wpadminbar.scroll', ideapark_adminbar_visible_height);
			}
		}
	};
	
	root.ideapark_scroll_action_add = function ($action) {
		ideapark_scroll_action_list.push($action);
	};
	
	root.ideapark_resize_action_add = function ($action) {
		ideapark_resize_action_list.push($action);
	};
	
	root.ideapark_resize_action_500_add = function ($action) {
		ideapark_resize_action_list_500.push($action);
	};
	
	root.ideapark_resize_action_layout_add = function ($action) {
		ideapark_resize_action_list_layout.push($action);
	};
	
	root.ideapark_scroll_actions = function () {
		
		ideapark_wpadminbar_scroll();
		
		ideapark_scroll_action_list.forEach(function (item) {
			if (ideapark_is_function(item)) {
				item();
			}
		});
		
		ideapark_scroll_busy = false;
	};
	
	root.ideapark_resize_actions = function () {
		
		var ideapark_is_mobile_layout_new = (window.innerWidth < 1190);
		var is_layout_changed = (ideapark_is_mobile_layout !== ideapark_is_mobile_layout_new);
		var is_width_changed = (ideapark_window_width != window.innerWidth);
		ideapark_is_mobile_layout = ideapark_is_mobile_layout_new;
		ideapark_window_width = window.innerWidth;
		
		ideapark_wpadminbar_resize();
		
		ideapark_resize_action_list.forEach(function (item) {
			if (ideapark_is_function(item)) {
				item();
			}
		});
		
		if (is_layout_changed) { // switch between mobile and desktop layouts
			
			$(document).addClass('block-transition');
			setTimeout(function () {
				$(document).removeClass('block-transition');
			}, 500);
			
			ideapark_resize_action_list_layout.forEach(function (item) {
				if (ideapark_is_function(item)) {
					item();
				}
			});
		}
		
		if (is_width_changed) {
			ideapark_debounce_500();
			setTimeout(function () {
				ideapark_wpadminbar_resize();
				$(document).trigger('ideapark.wpadminbar.scroll', ideapark_adminbar_visible_height);
			}, 100);
		}
		
		ideapark_resize_busy = false;
	};
	
	root.ideapark_on_transition_end_callback = function ($element, callback){
		var callback_inner = function () {
			$element.off(ideapark_on_transition_end, callback_inner);
			callback();
		};
		$element.on(ideapark_on_transition_end, callback_inner);
	}
	
	root.ideapark_on_animation_end_callback = function ($element, callback){
		var callback_inner = function () {
			$element.off(ideapark_on_animation_end, callback_inner);
			callback();
		};
		$element.on(ideapark_on_animation_end, callback_inner);
	}
	
	root.ideapark_debounce_500 = ideapark_debounce(function () {
		ideapark_resize_action_list_500.forEach(function (item) {
			if (ideapark_is_function(item)) {
				item();
			}
		});
	}, 500);
	
	$(window).scroll(
		function () {
			if (window.requestAnimationFrame) {
				if (!ideapark_scroll_busy) {
					ideapark_scroll_busy = true;
					window.requestAnimationFrame(ideapark_scroll_actions);
				}
			} else {
				ideapark_scroll_actions();
			}
		}
	);
	
	$(window).resize(
		function () {
			if (window.requestAnimationFrame) {
				if (!ideapark_resize_busy) {
					ideapark_resize_busy = true;
					window.requestAnimationFrame(ideapark_resize_actions);
				}
			} else {
				ideapark_resize_actions();
			}
		}
	);
	
	root.ideapark_replace_url_param = function (s, key, value) {
		key = encodeURIComponent(key);
		value = encodeURIComponent(value);
		var kvp = key + "=" + value;
		var r = new RegExp("(&|\\?)" + key + "=[^\&]*");
		s = s.replace(r, "$1" + kvp);
		if (!RegExp.$1) {
			s += (s.indexOf('?') !== -1 ? '&' : '?') + kvp;
		}
		return s;
	}
	
	root.ideapark_defer_action_add = function ($action) {
		if (ideapark_defer_action_enabled) {
			ideapark_defer_action_list.push($action);
		} else if (ideapark_is_function($action)) {
			$action();
		}
	};
	
	root.ideapark_defer_action_run = function () {
		if (ideapark_defer_action_enabled) {
			ideapark_defer_action_enabled = false;
			ideapark_defer_action_list.forEach(function (item) {
				if (ideapark_is_function(item)) {
					item();
				}
			});
		}
	};
	
	class ideapark_defer_loading {
		constructor(e) {
			this.triggerEvents = e, this.eventOptions = {passive: !0}, this.userEventListener = this.triggerListener.bind(this), this.delayedScripts = {
				normal: [],
				async : [],
				defer : []
			}
		}
		
		_addUserInteractionListener(e) {
			this.triggerEvents.forEach((t => window.addEventListener(t, e.userEventListener, e.eventOptions)))
		}
		
		_removeUserInteractionListener(e) {
			this.triggerEvents.forEach((t => window.removeEventListener(t, e.userEventListener, e.eventOptions)))
		}
		
		triggerListener(e) {
			this._removeUserInteractionListener(this);
			if (e.type === 'touchstart') {
				setTimeout(this._loadEverythingNow, 500);
			} else {
				this._loadEverythingNow();
			}
		}
		
		async _loadEverythingNow() {
			ideapark_defer_action_run();
		}
		
		static run() {
			if (window.scrollY > 100) {
				ideapark_defer_action_enabled = false;
			} else {
				const e = new ideapark_defer_loading(["keydown", "mousemove", "touchmove", "touchstart", "touchend", "wheel"]);
				e._addUserInteractionListener(e)
			}
		}
	}
	
	ideapark_defer_loading.run();
	
})(jQuery, window);

