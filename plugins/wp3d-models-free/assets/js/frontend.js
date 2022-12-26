// Needed to fix WP3D Models Tools Menu (on the front end)
function promptJSShow(label, id, plink, title) {
	prompt(label, '<div id="wp3d-'+id+'"><a href="'+plink+'">LOADING - '+title+'</a><script src="//wp3d-models.s3.us-east-2.amazonaws.com/js/embed-iframe.js?id=wp3d-'+id+'"></script></div>');
}

function promptIFShow(label, plink) {
	prompt(label, '<iframe width="853" height="480" src="'+plink+'" frameborder="0" allow="vr" allowfullscreen="allowfullscreen"></iframe>');
}

// detect drag vs click
(function(jQuery){
    var thisdoc = jQuery(document),
        moved = false,
        pos = {x: null, y: null},
        abs = Math.abs,
        mclick = {
        'mousedown.mclick': function(e) {
            pos.x = e.pageX;
            pos.y = e.pageY;
            moved = false;
        },
        'mouseup.mclick': function(e) {
            moved = abs(pos.x - e.pageX) > jQuery.clickMouseMoved.threshold
                || abs(pos.y - e.pageY) > jQuery.clickMouseMoved.threshold;
        }
    };
    
    thisdoc.on(mclick);
    
    jQuery.clickMouseMoved = function () {
        return moved;
    };
    
    jQuery.clickMouseMoved.threshold = 3;
})(jQuery);


// Used in the Gallery to greatly reduce load times
function preload(arrayOfImages) {
    jQuery(arrayOfImages).each(function(){
         (new Image()).src = this;
    });
}

function detectIOS() {
    var t = window.navigator.userAgent,
        e = /iPad|iPhone|iPod/;
    return e.test(t)
}

/**
 * Featherlight - ultra slim jQuery lightbox
 * Version 1.7.7 - http://noelboss.github.io/featherlight/
 *
 * Copyright 2017, Noël Raoul Bossart (http://www.noelboss.com)
 * MIT Licensed.
**/
(function($) {
	"use strict";

	if('undefined' === typeof $) {
		if('console' in window){ window.console.info('Too much lightness, Featherlight needs jQuery.'); }
		return;
	}

	/* Featherlight is exported as $.featherlight.
	   It is a function used to open a featherlight lightbox.

	   [tech]
	   Featherlight uses prototype inheritance.
	   Each opened lightbox will have a corresponding object.
	   That object may have some attributes that override the
	   prototype's.
	   Extensions created with Featherlight.extend will have their
	   own prototype that inherits from Featherlight's prototype,
	   thus attributes can be overriden either at the object level,
	   or at the extension level.
	   To create callbacks that chain themselves instead of overriding,
	   use chainCallbacks.
	   For those familiar with CoffeeScript, this correspond to
	   Featherlight being a class and the Gallery being a class
	   extending Featherlight.
	   The chainCallbacks is used since we don't have access to
	   CoffeeScript's `super`.
	*/

	function Featherlight($content, config) {
		if(this instanceof Featherlight) {  /* called with new */
			this.id = Featherlight.id++;
			this.setup($content, config);
			this.chainCallbacks(Featherlight._callbackChain);
		} else {
			var fl = new Featherlight($content, config);
			fl.open();
			return fl;
		}
	}

	var opened = [],
		pruneOpened = function(remove) {
			opened = $.grep(opened, function(fl) {
				return fl !== remove && fl.$instance.closest('body').length > 0;
			} );
			return opened;
		};

	// Removes keys of `set` from `obj` and returns the removed key/values.
	function slice(obj, set) {
		var r = {};
		for (var key in obj) {
			if (key in set) {
				r[key] = obj[key];
				delete obj[key];
			}
		}
		return r;
	}

	// NOTE: List of available [iframe attributes](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/iframe).
	var iFrameAttributeSet = {
		allowfullscreen: 1, frameborder: 1, height: 1, longdesc: 1, marginheight: 1, marginwidth: 1,
		name: 1, referrerpolicy: 1, scrolling: 1, sandbox: 1, src: 1, srcdoc: 1, width: 1
	};

	// Converts camelCased attributes to dasherized versions for given prefix:
	//   parseAttrs({hello: 1, hellFrozeOver: 2}, 'hell') => {froze-over: 2}
	function parseAttrs(obj, prefix) {
		var attrs = {},
			regex = new RegExp('^' + prefix + '([A-Z])(.*)');
		for (var key in obj) {
			var match = key.match(regex);
			if (match) {
				var dasherized = (match[1] + match[2].replace(/([A-Z])/g, '-$1')).toLowerCase();
				attrs[dasherized] = obj[key];
			}
		}
		return attrs;
	}

	/* document wide key handler */
	var eventMap = { keyup: 'onKeyUp', resize: 'onResize' };

	var globalEventHandler = function(event) {
		$.each(Featherlight.opened().reverse(), function() {
			if (!event.isDefaultPrevented()) {
				if (false === this[eventMap[event.type]](event)) {
					event.preventDefault(); event.stopPropagation(); return false;
			  }
			}
		});
	};

	var toggleGlobalEvents = function(set) {
			if(set !== Featherlight._globalHandlerInstalled) {
				Featherlight._globalHandlerInstalled = set;
				var events = $.map(eventMap, function(_, name) { return name+'.'+Featherlight.prototype.namespace; } ).join(' ');
				$(window)[set ? 'on' : 'off'](events, globalEventHandler);
			}
		};

	Featherlight.prototype = {
		constructor: Featherlight,
		/*** defaults ***/
		/* extend featherlight with defaults and methods */
		namespace:      'featherlight',        /* Name of the events and css class prefix */
		targetAttr:     'data-featherlight',   /* Attribute of the triggered element that contains the selector to the lightbox content */
		variant:        null,                  /* Class that will be added to change look of the lightbox */
		resetCss:       false,                 /* Reset all css */
		background:     null,                  /* Custom DOM for the background, wrapper and the closebutton */
		openTrigger:    'click',               /* Event that triggers the lightbox */
		closeTrigger:   'click',               /* Event that triggers the closing of the lightbox */
		filter:         null,                  /* Selector to filter events. Think $(...).on('click', filter, eventHandler) */
		root:           'body',                /* Where to append featherlights */
		openSpeed:      250,                   /* Duration of opening animation */
		closeSpeed:     250,                   /* Duration of closing animation */
		closeOnClick:   'background',          /* Close lightbox on click ('background', 'anywhere' or false) */
		closeOnEsc:     true,                  /* Close lightbox when pressing esc */
		closeIcon:      '&#10005;',            /* Close icon */
		loading:        '',                    /* Content to show while initial content is loading */
		persist:        false,                 /* If set, the content will persist and will be shown again when opened again. 'shared' is a special value when binding multiple elements for them to share the same content */
		otherClose:     null,                  /* Selector for alternate close buttons (e.g. "a.close") */
		beforeOpen:     $.noop,                /* Called before open. can return false to prevent opening of lightbox. Gets event as parameter, this contains all data */
		beforeContent:  $.noop,                /* Called when content is loaded. Gets event as parameter, this contains all data */
		beforeClose:    $.noop,                /* Called before close. can return false to prevent opening of lightbox. Gets event as parameter, this contains all data */
		afterOpen:      $.noop,                /* Called after open. Gets event as parameter, this contains all data */
		afterContent:   $.noop,                /* Called after content is ready and has been set. Gets event as parameter, this contains all data */
		afterClose:     $.noop,                /* Called after close. Gets event as parameter, this contains all data */
		onKeyUp:        $.noop,                /* Called on key up for the frontmost featherlight */
		onResize:       $.noop,                /* Called after new content and when a window is resized */
		type:           null,                  /* Specify type of lightbox. If unset, it will check for the targetAttrs value. */
		contentFilters: ['jquery', 'image', 'html', 'ajax', 'iframe', 'text'], /* List of content filters to use to determine the content */

		/*** methods ***/
		/* setup iterates over a single instance of featherlight and prepares the background and binds the events */
		setup: function(target, config){
			/* all arguments are optional */
			if (typeof target === 'object' && target instanceof $ === false && !config) {
				config = target;
				target = undefined;
			}

			var self = $.extend(this, config, {target: target}),
				css = !self.resetCss ? self.namespace : self.namespace+'-reset', /* by adding -reset to the classname, we reset all the default css */
				$background = $(self.background || [
					'<div class="'+css+'-loading '+css+'">',
						'<div class="'+css+'-content">',
							'<button class="'+css+'-close-icon '+ self.namespace + '-close" aria-label="Close">',
								self.closeIcon,
							'</button>',
							'<div class="'+self.namespace+'-inner">' + self.loading + '</div>',
						'</div>',
					'</div>'].join('')),
				closeButtonSelector = '.'+self.namespace+'-close' + (self.otherClose ? ',' + self.otherClose : '');

			self.$instance = $background.clone().addClass(self.variant); /* clone DOM for the background, wrapper and the close button */

			/* close when click on background/anywhere/null or closebox */
			self.$instance.on(self.closeTrigger+'.'+self.namespace, function(event) {
				var $target = $(event.target);
				if( ('background' === self.closeOnClick  && $target.is('.'+self.namespace))
					|| 'anywhere' === self.closeOnClick
					|| $target.closest(closeButtonSelector).length ){
					self.close(event);
					event.preventDefault();
				}
			});

			return this;
		},

		/* this method prepares the content and converts it into a jQuery object or a promise */
		getContent: function(){
			if(this.persist !== false && this.$content) {
				return this.$content;
			}
			var self = this,
				filters = this.constructor.contentFilters,
				readTargetAttr = function(name){ return self.$currentTarget && self.$currentTarget.attr(name); },
				targetValue = readTargetAttr(self.targetAttr),
				data = self.target || targetValue || '';

			/* Find which filter applies */
			var filter = filters[self.type]; /* check explicit type like {type: 'image'} */

			/* check explicit type like data-featherlight="image" */
			if(!filter && data in filters) {
				filter = filters[data];
				data = self.target && targetValue;
			}
			data = data || readTargetAttr('href') || '';

			/* check explicity type & content like {image: 'photo.jpg'} */
			if(!filter) {
				for(var filterName in filters) {
					if(self[filterName]) {
						filter = filters[filterName];
						data = self[filterName];
					}
				}
			}

			/* otherwise it's implicit, run checks */
			if(!filter) {
				var target = data;
				data = null;
				$.each(self.contentFilters, function() {
					filter = filters[this];
					if(filter.test)  {
						data = filter.test(target);
					}
					if(!data && filter.regex && target.match && target.match(filter.regex)) {
						data = target;
					}
					return !data;
				});
				if(!data) {
					if('console' in window){ window.console.error('Featherlight: no content filter found ' + (target ? ' for "' + target + '"' : ' (no target specified)')); }
					return false;
				}
			}
			/* Process it */
			return filter.process.call(self, data);
		},

		/* sets the content of $instance to $content */
		setContent: function($content){
			var self = this;
			/* we need a special class for the iframe */
			if($content.is('iframe')) {
				self.$instance.addClass(self.namespace+'-iframe');
			}

			self.$instance.removeClass(self.namespace+'-loading');

			/* replace content by appending to existing one before it is removed
			   this insures that featherlight-inner remain at the same relative
				 position to any other items added to featherlight-content */
			self.$instance.find('.'+self.namespace+'-inner')
				.not($content)                /* excluded new content, important if persisted */
				.slice(1).remove().end()      /* In the unexpected event where there are many inner elements, remove all but the first one */
				.replaceWith($.contains(self.$instance[0], $content[0]) ? '' : $content);

			self.$content = $content.addClass(self.namespace+'-inner');

			return self;
		},

		/* opens the lightbox. "this" contains $instance with the lightbox, and with the config.
			Returns a promise that is resolved after is successfully opened. */
		open: function(event){
			var self = this;
			self.$instance.hide().appendTo(self.root);
			if((!event || !event.isDefaultPrevented())
				&& self.beforeOpen(event) !== false) {

				if(event){
					event.preventDefault();
				}
				var $content = self.getContent();

				if($content) {
					opened.push(self);

					toggleGlobalEvents(true);

					self.$instance.fadeIn(self.openSpeed);
					self.beforeContent(event);

					/* Set content and show */
					return $.when($content)
						.always(function($content){
							self.setContent($content);
							self.afterContent(event);
						})
						.then(self.$instance.promise())
						/* Call afterOpen after fadeIn is done */
						.done(function(){ self.afterOpen(event); });
				}
			}
			self.$instance.detach();
			return $.Deferred().reject().promise();
		},

		/* closes the lightbox. "this" contains $instance with the lightbox, and with the config
			returns a promise, resolved after the lightbox is successfully closed. */
		close: function(event){
			var self = this,
				deferred = $.Deferred();

			if(self.beforeClose(event) === false) {
				deferred.reject();
			} else {

				if (0 === pruneOpened(self).length) {
					toggleGlobalEvents(false);
				}

				self.$instance.fadeOut(self.closeSpeed,function(){
					self.$instance.detach();
					self.afterClose(event);
					deferred.resolve();
				});
			}
			return deferred.promise();
		},

		/* resizes the content so it fits in visible area and keeps the same aspect ratio.
				Does nothing if either the width or the height is not specified.
				Called automatically on window resize.
				Override if you want different behavior. */
		resize: function(w, h) {
			if (w && h) {
				/* Reset apparent image size first so container grows */
				this.$content.css('width', '').css('height', '');
				/* Calculate the worst ratio so that dimensions fit */
				 /* Note: -1 to avoid rounding errors */
				var ratio = Math.max(
					w  / (this.$content.parent().width()-1),
					h / (this.$content.parent().height()-1));
				/* Resize content */
				if (ratio > 1) {
					ratio = h / Math.floor(h / ratio); /* Round ratio down so height calc works */
					this.$content.css('width', '' + w / ratio + 'px').css('height', '' + h / ratio + 'px');
				}
			}
		},

		/* Utility function to chain callbacks
		   [Warning: guru-level]
		   Used be extensions that want to let users specify callbacks but
		   also need themselves to use the callbacks.
		   The argument 'chain' has callback names as keys and function(super, event)
		   as values. That function is meant to call `super` at some point.
		*/
		chainCallbacks: function(chain) {
			for (var name in chain) {
				this[name] = $.proxy(chain[name], this, $.proxy(this[name], this));
			}
		}
	};

	$.extend(Featherlight, {
		id: 0,                                    /* Used to id single featherlight instances */
		autoBind:       '[data-featherlight]',    /* Will automatically bind elements matching this selector. Clear or set before onReady */
		defaults:       Featherlight.prototype,   /* You can access and override all defaults using $.featherlight.defaults, which is just a synonym for $.featherlight.prototype */
		/* Contains the logic to determine content */
		contentFilters: {
			jquery: {
				regex: /^[#.]\w/,         /* Anything that starts with a class name or identifiers */
				test: function(elem)    { return elem instanceof $ && elem; },
				process: function(elem) { return this.persist !== false ? $(elem) : $(elem).clone(true); }
			},
			image: {
				regex: /\.(png|jpg|jpeg|gif|tiff|bmp|svg)(\?\S*)?$/i,
				process: function(url)  {
					var self = this,
						deferred = $.Deferred(),
						img = new Image(),
						$img = $('<img src="'+url+'" alt="" class="'+self.namespace+'-image" />');
					img.onload  = function() {
						/* Store naturalWidth & height for IE8 */
						$img.naturalWidth = img.width; $img.naturalHeight = img.height;
						deferred.resolve( $img );
					};
					img.onerror = function() { deferred.reject($img); };
					img.src = url;
					return deferred.promise();
				}
			},
			html: {
				regex: /^\s*<[\w!][^<]*>/, /* Anything that starts with some kind of valid tag */
				process: function(html) { return $(html); }
			},
			ajax: {
				regex: /./,            /* At this point, any content is assumed to be an URL */
				process: function(url)  {
					var self = this,
						deferred = $.Deferred();
					/* we are using load so one can specify a target with: url.html #targetelement */
					var $container = $('<div></div>').load(url, function(response, status){
						if ( status !== "error" ) {
							deferred.resolve($container.contents());
						}
						deferred.fail();
					});
					return deferred.promise();
				}
			},
			iframe: {
				process: function(url) {
					var deferred = new $.Deferred();
					var $content = $('<iframe/>');
					var css = parseAttrs(this, 'iframe');
					var attrs = slice(css, iFrameAttributeSet);
					$content.hide()
						.attr('src', url)
						.attr(attrs)
						.css(css)
						.on('load', function() { deferred.resolve($content.show()); })
						// We can't move an <iframe> and avoid reloading it,
						// so let's put it in place ourselves right now:
						.appendTo(this.$instance.find('.' + this.namespace + '-content'));
					return deferred.promise();
				}
			},
			text: {
				process: function(text) { return $('<div>', {text: text}); }
			}
		},

		functionAttributes: ['beforeOpen', 'afterOpen', 'beforeContent', 'afterContent', 'beforeClose', 'afterClose'],

		/*** class methods ***/
		/* read element's attributes starting with data-featherlight- */
		readElementConfig: function(element, namespace) {
			var Klass = this,
				regexp = new RegExp('^data-' + namespace + '-(.*)'),
				config = {};
			if (element && element.attributes) {
				$.each(element.attributes, function(){
					var match = this.name.match(regexp);
					if (match) {
						var val = this.value,
							name = $.camelCase(match[1]);
						if ($.inArray(name, Klass.functionAttributes) >= 0) {  /* jshint -W054 */
							val = new Function(val);                           /* jshint +W054 */
						} else {
							try { val = JSON.parse(val); }
							catch(e) {}
						}
						config[name] = val;
					}
				});
			}
			return config;
		},

		/* Used to create a Featherlight extension
		   [Warning: guru-level]
		   Creates the extension's prototype that in turn
		   inherits Featherlight's prototype.
		   Could be used to extend an extension too...
		   This is pretty high level wizardy, it comes pretty much straight
		   from CoffeeScript and won't teach you anything about Featherlight
		   as it's not really specific to this library.
		   My suggestion: move along and keep your sanity.
		*/
		extend: function(child, defaults) {
			/* Setup class hierarchy, adapted from CoffeeScript */
			var Ctor = function(){ this.constructor = child; };
			Ctor.prototype = this.prototype;
			child.prototype = new Ctor();
			child.__super__ = this.prototype;
			/* Copy class methods & attributes */
			$.extend(child, this, defaults);
			child.defaults = child.prototype;
			return child;
		},

		attach: function($source, $content, config) {
			var Klass = this;
			if (typeof $content === 'object' && $content instanceof $ === false && !config) {
				config = $content;
				$content = undefined;
			}
			/* make a copy */
			config = $.extend({}, config);

			/* Only for openTrigger and namespace... */
			var namespace = config.namespace || Klass.defaults.namespace,
				tempConfig = $.extend({}, Klass.defaults, Klass.readElementConfig($source[0], namespace), config),
				sharedPersist;
			var handler = function(event) {
				var $target = $(event.currentTarget);
				/* ... since we might as well compute the config on the actual target */
				var elemConfig = $.extend(
					{$source: $source, $currentTarget: $target},
					Klass.readElementConfig($source[0], tempConfig.namespace),
					Klass.readElementConfig(event.currentTarget, tempConfig.namespace),
					config);
				var fl = sharedPersist || $target.data('featherlight-persisted') || new Klass($content, elemConfig);
				if(fl.persist === 'shared') {
					sharedPersist = fl;
				} else if(fl.persist !== false) {
					$target.data('featherlight-persisted', fl);
				}
				if (elemConfig.$currentTarget.blur) {
					elemConfig.$currentTarget.blur(); // Otherwise 'enter' key might trigger the dialog again
				}
				fl.open(event);
			};

			$source.on(tempConfig.openTrigger+'.'+tempConfig.namespace, tempConfig.filter, handler);

			return handler;
		},

		current: function() {
			var all = this.opened();
			return all[all.length - 1] || null;
		},

		opened: function() {
			var klass = this;
			pruneOpened();
			return $.grep(opened, function(fl) { return fl instanceof klass; } );
		},

		close: function(event) {
			var cur = this.current();
			if(cur) { return cur.close(event); }
		},

		/* Does the auto binding on startup.
		   Meant only to be used by Featherlight and its extensions
		*/
		_onReady: function() {
			var Klass = this;
			if(Klass.autoBind){
				/* Bind existing elements */
				$(Klass.autoBind).each(function(){
					Klass.attach($(this));
				});
				/* If a click propagates to the document level, then we have an item that was added later on */
				$(document).on('click', Klass.autoBind, function(evt) {
					if (evt.isDefaultPrevented()) {
						return;
					}
					/* Bind featherlight */
					var handler = Klass.attach($(evt.currentTarget));
					/* Dispatch event directly */
					handler(evt);
				});
			}
		},

		/* Featherlight uses the onKeyUp callback to intercept the escape key.
		   Private to Featherlight.
		*/
		_callbackChain: {
			onKeyUp: function(_super, event){
				if(27 === event.keyCode) {
					if (this.closeOnEsc) {
						$.featherlight.close(event);
					}
					return false;
				} else {
					return _super(event);
				}
			},

			beforeOpen: function(_super, event) {
				// Remember focus:
				this._previouslyActive = document.activeElement;

				// Disable tabbing:
				// See http://stackoverflow.com/questions/1599660/which-html-elements-can-receive-focus
				this._$previouslyTabbable = $("a, input, select, textarea, iframe, button, iframe, [contentEditable=true]")
					.not('[tabindex]')
					.not(this.$instance.find('button'));

				this._$previouslyWithTabIndex = $('[tabindex]').not('[tabindex="-1"]');
				this._previousWithTabIndices = this._$previouslyWithTabIndex.map(function(_i, elem) {
					return $(elem).attr('tabindex');
				});

				this._$previouslyWithTabIndex.add(this._$previouslyTabbable).attr('tabindex', -1);

				if (document.activeElement.blur) {
					document.activeElement.blur();
				}
				return _super(event);
			},

			afterClose: function(_super, event) {
				var r = _super(event);
				var self = this;
				this._$previouslyTabbable.removeAttr('tabindex');
				this._$previouslyWithTabIndex.each(function(i, elem) {
					$(elem).attr('tabindex', self._previousWithTabIndices[i]);
				});
				this._previouslyActive.focus();
				return r;
			},

			onResize: function(_super, event){
				this.resize(this.$content.naturalWidth, this.$content.naturalHeight);
				return _super(event);
			},

			afterContent: function(_super, event){
				var r = _super(event);
				this.$instance.find('[autofocus]:not([disabled])').focus();
				this.onResize(event);
				return r;
			}
		}
	});

	$.featherlight = Featherlight;

	/* bind jQuery elements to trigger featherlight */
	$.fn.featherlight = function($content, config) {
		Featherlight.attach(this, $content, config);
		return this;
	};

	/* bind featherlight on ready if config autoBind is set */
	$(document).ready(function(){ Featherlight._onReady(); });
}(jQuery));

/*
 * Get Viewport Dimensions
 * returns object with viewport dimensions to match css in width and height properties
 * ( source: http://andylangton.co.uk/blog/development/get-viewport-size-width-and-height-javascript )
*/
function updateViewportDimensions() {
	var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],x=w.innerWidth||e.clientWidth||g.clientWidth,y=w.innerHeight||e.clientHeight||g.clientHeight;
	return { width:x,height:y }
}
// setting the viewport width
var viewport = updateViewportDimensions();	

/*
 * Throttle Resize-triggered Events
 * Wrap your actions in this function to throttle the frequency of firing them off, for better performance, esp. on mobile.
 * ( source: http://stackoverflow.com/questions/2854407/javascript-jquery-window-resize-how-to-fire-after-the-resize-is-completed )
*/
var waitForFinalEvent = (function () {
	var timers = {};
	return function (callback, ms, uniqueId) {
		if (!uniqueId) { uniqueId = "Don't call this twice without a uniqueId"; }
		if (timers[uniqueId]) { clearTimeout (timers[uniqueId]); }
		timers[uniqueId] = setTimeout(callback, ms);
	};
})();

// how long to wait before deciding the resize has stopped, in ms. Around 50-100 should work ok.
var timeToWaitForLast = 100;

jQuery(window).resize(function () {

    waitForFinalEvent( function() {
 		viewport = updateViewportDimensions();
    	//console.log('Window resized - width = '+viewport.width);
    }, timeToWaitForLast, "general-resize"); 
    
 });


///////////////////////////////
// JQUERY READY
///////////////////////////////
jQuery( document ).ready( function ( e ) {
	
	// check for iOS
	if ( detectIOS() ) {
		jQuery('body').addClass('is-ios');
	}	
    
    /* TABS */
    jQuery('ul.wp3d-prop-tabs li a').click(function(e){
	
      e.preventDefault();
      
    	var tab_id = jQuery(this).attr('href');
    
    	jQuery('ul.wp3d-prop-tabs li').removeClass('active');
    	jQuery('.wp3d-tab-content div').removeClass('active');
    
    	jQuery(this).parent('li').addClass('active');
    	jQuery(tab_id).addClass('active');
    })
    
	// GALLERY CAPTIONS
	jQuery('span.wp3d-caption-open').click( function() {
		jQuery(this).hide();
		jQuery(this).siblings('div.wp3d-gallery-caption').addClass('enabled');
	});	
	
	jQuery('span.wp3d-caption-close').click( function() {
		jQuery(this).parent().removeClass('enabled');
		jQuery(this).parent().siblings('span.wp3d-caption-open').show();
	});
	
	// ZOOM GALLERY EXPAND
	jQuery('#wp3d-zoom-gallery a').featherlight({
            targetAttr: 'href',
            type: 'image',
            beforeOpen: function(event){
                if (jQuery.clickMouseMoved()) {
			        //console.log('click aborted');
			        return false;           
			    }
            }
        });	

    /* FLOORPLAN MODAL */
  	jQuery('#wp3d-floorplan-images a').on('click', function (e) {
  	    e.preventDefault();
  	    var imgtarget = jQuery(this).data('featherlight');
  	    var imgsrc = jQuery(imgtarget + ' img').data('src');
  	    jQuery(imgtarget + ' img').attr('src', imgsrc);
  	});
  
    /* STOCK LIST FILTERING */
    jQuery('#filter-3d-models a').click(function(e){
    	e.preventDefault();
    	
    	var selector = jQuery(this).attr('data-filter');
    	jQuery('#filter-3d-models .active').removeClass('active');
    	jQuery(this).addClass('active');
    
    	if(selector == 'all-models') {
    		//jQuery('#projects .thumbs .project.inactive .inside').fadeIn('slow').removeClass('inactive').addClass('active');
    		jQuery('#wp3d-models div.model-list-wrap').fadeIn('slow').removeClass('inactive').addClass('active');
    	} else {
    		jQuery('#wp3d-models div.model-list-wrap').each(function() {
    			if(!jQuery(this).hasClass(selector)) {
    				jQuery(this).removeClass('active').addClass('inactive');
    				jQuery(this).fadeOut('normal');
    			} else {
    				jQuery(this).addClass('active').removeClass('inactive');
    				jQuery(this).fadeIn('slow');
                }
    		});
    	}
    
    	return false;
    });
    
	/* Preload Functionality */
	var preloadModel = jQuery( "#mp-iframe").data('preload');
	if (preloadModel === true ) {
		console.log('preload');
		if( viewport.width >= 768 ) { // do the preload
			var mp_iframe_preload_src = jQuery( "#mp-iframe").data('src'); // get the iframe src
			// do the preload
			if (mp_iframe_preload_src !== '') {
				jQuery( "#mp-iframe").attr("src", mp_iframe_preload_src);
			}
		}
	} else {
		preloadModel = false;
	}    
	    
	/* Intro Functionality */
	jQuery( ".wp3d-embed-wrap #wp3d-intro a" ).click(function(e) {
		e.preventDefault();
		
		var mp_iframe_src = jQuery( "#mp-iframe").data('src'); // get the iframe src
		var mp_iframe_allow = jQuery( "#mp-iframe").data('allow'); // get the iframe allow
		var logo_overlay = jQuery('.wp3d-embed-wrap .iframe-logo-overlay').clone(); // console.log(logo_overlay);
		var mp_iframe_close = '<a href="#" id="tour-close" title="CLOSE TOUR"><span class="fa-stack"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-times fa-stack-1x fa-inverse"></i></span></a>';
		
		if( viewport.width < 768 || detectIOS() ) { // go fullscreen on small devices
			// add the fullscreen iframe (only on mobile)
			jQuery('html').addClass('wp3d-fullscreen');
			jQuery('<div id="wp3d-fullscreen-wrap"><iframe src="'+mp_iframe_src+'" frameborder="0" allow="vr' + mp_iframe_allow + '" allowfullscreen></div>').appendTo('body');
			jQuery('<div id="wp3d-fullscreen-header">'+mp_iframe_close+'</div>').appendTo('body');
			logo_overlay.appendTo('#wp3d-fullscreen-wrap');
			if( viewport.width < 481 ) { // fade out the overlay on small widths...too much overlapping
				jQuery('.iframe-logo-overlay').delay(3000).fadeOut(); // if there's an overlay, fade 'er out after a few on small screens
			}
			if (jQuery('.wp3d-embed-wrap').hasClass('wp3d-sold')) { 
				markedSold = true; 
				jQuery('.wp3d-embed-wrap').removeClass('wp3d-sold'); // if it exists, remove the sold when actually viewing the model
			} else if (jQuery('.wp3d-embed-wrap').hasClass('wp3d-pending')) {
				markedPending = true; 
				jQuery('.wp3d-embed-wrap').removeClass('wp3d-pending'); // if it exists, remove the pending when actually viewing the model
			} else {
				markedSold = false;
				markedPending = false;
			}		

			// remove the fullscreen iframe
			jQuery( "#wp3d-fullscreen-header a#tour-close" ).click(function(e) {
				e.preventDefault();
				jQuery( "#wp3d-fullscreen-wrap" ).hide( "fast" );
				jQuery( "#wp3d-fullscreen-header" ).hide( "fast" );	
				jQuery('#wp3d-fullscreen-header, #wp3d-fullscreen-wrap').remove();		 
				jQuery('html').removeClass('wp3d-fullscreen');
				if (markedSold) {
					jQuery('.wp3d-embed-wrap').addClass('wp3d-sold');
				} else if (markedPending) {
					jQuery('.wp3d-embed-wrap').addClass('wp3d-pending');
				}
			}); 			
		} else { 
			if (mp_iframe_src !== '' && preloadModel === false) { // only swap out the src if we're not preloading
				jQuery( "#mp-iframe").attr("src",mp_iframe_src);
			}
	  		jQuery( "#wp3d-intro" ).fadeOut("slow");
	  		jQuery('.wp3d-embed-wrap').removeClass('wp3d-sold wp3d-pending'); // if it exists, remove the sold when actually viewing the model
		}
	});    

   if (jQuery.fn.swiper && jQuery(".wp3d-swiper-container").length) { // checking for existance of the swiper & element
       
    //Swiper Gallery  
    var swiper = new Swiper ('.wp3d-swiper-container', {
		pagination: '.swiper-pagination',
		paginationClickable: '.swiper-pagination',
		nextButton: '.swiper-button-next',
		prevButton: '.swiper-button-prev',
		keyboardControl: true,
		loop: true,
		onInit : function(swiper) {
			//console.log('init');
		    jQuery( ".swiper-slide-active" ).each(function ( index ) {
		    var src = jQuery( this ).attr( "data-src" );
		    var prevsrc = jQuery( this ).siblings('.swiper-slide-prev').attr( "data-src" );
		        jQuery(this).css('background-image', 'url(' + src + ')');
		        preload([prevsrc]);
		    });
		},			
		onSlideNextStart : function(swiper) {
			//console.log('next');
		    jQuery( ".swiper-slide-active" ).each(function ( index ) {
		    var src = jQuery( this ).attr( "data-src" );
		    var nextsrc = jQuery( this ).siblings('.swiper-slide-next').attr( "data-src" );
		        jQuery(this).css('background-image', 'url(' + src + ')');
		        preload([nextsrc]);
		    });
		},
		onSlidePrevStart : function(swiper) {
			//console.log('prev');
		    jQuery( ".swiper-slide-active" ).each(function ( index ) {
		    var src = jQuery( this ).attr( "data-src" );
		    var current = jQuery(this),
			    index = current.index();
			if (index === 0) {
				// when we're on the FIRST (cloned) element AND we're going backwards we need to find the third-from-last (because of cloning) to preload the correct image
				var prevsrc = jQuery( this ).siblings('.swiper-slide:nth-last-child(3)').attr( "data-src" );
			} else {
			    var prevsrc = jQuery( this ).siblings('.swiper-slide-prev').attr( "data-src" );
			}
			jQuery(this).css('background-image', 'url(' + src + ')');
			preload([prevsrc]);
		    });
		}
    });
    
  }
  
   if (jQuery.fn.slick) { // checking for existance of slick
   
	// ZOOM GALLERY
	// Slick Variable Width Gallery 
    
    // Slick Lazy Load Last Slide
    
    var _ = jQuery('.slick-slide.cloned-slide');
	
    function loadClone() {

        jQuery('.slick-slide.cloned-slide img[data-lazy]').each(function() {

            var image = jQuery(this),
                imageSource = jQuery(this).attr('data-lazy'),
                imageToLoad = document.createElement('img');

            imageToLoad.onload = function() {

                image
                    .animate({ opacity: 0 }, 100, function() {
                        image
                            .attr('src', imageSource)
                            .animate({ opacity: 1 }, 200, function() {
                                image
                                    .removeAttr('data-lazy')
                                    .removeClass('slick-loading');
                            });
                        _.trigger('lazyLoaded', [_, image, imageSource]);
                    });

            };

            imageToLoad.onerror = function() {

                image
                    .removeAttr( 'data-lazy' )
                    .removeClass( 'slick-loading' )
                    .addClass( 'slick-lazyload-error' );

                _.trigger('lazyLoadError', [ _, image, imageSource ]);

            };

            imageToLoad.src = imageSource;

        });

    }     

	jQuery('.wp3d-zoom-slider')
		.on('init', function(event, slick){
			jQuery('.slick-track .slick-slide').last().clone().appendTo( ".slick-track" ).addClass('cloned-slide last-slide');
			jQuery('.slick-track .slick-slide').first().clone().appendTo( ".slick-track" ).removeClass('slick-current slick-center').addClass('cloned-slide first-slide');
			loadClone();
		})	
		.on('init reInit afterChange', function (event, slick, currentSlide, nextSlide) {
			var i = (currentSlide ? currentSlide : 0) + 1;
        	jQuery('.slick-current .slick-counter').text(i + '/' + slick.slideCount);
	    })
		.on('lazyLoaded', function(event, slick, image, imageSource){
			setTimeout( function() {  
				jQuery('#wp3d-zoom-gallery').removeClass('gallery-loading');
			}, 300);
		})
		.slick({
			lazyLoad: 'ondemand',
			dots: true,
			infinite: false,
			speed: 300,
			slidesToShow: 1,
			slidesToScroll: 1,
			swipeToSlide: true,
			centerMode: true,
			variableWidth: true
		});
	
	jQuery(window).load(function() {
		jQuery('body').addClass('loaded');
	    jQuery('body').delay( 800 ).addClass('loaded-delay');
		// $('body').removeClass('is-loading');
	});		

	// RELATED MODELS SLIDER
	jQuery('.wp3d-related-slider').slick({
	  dots: true,
	  infinite: false,
	  speed: 300,
	  slidesToShow: 5,
	  slidesToScroll: 5,
	  responsive: [
	    {
	      breakpoint: 1024,
	      settings: {
	        slidesToShow: 4,
	        slidesToScroll: 4,
	        infinite: true,
	        dots: true
	      }
	    },
	    {
	      breakpoint: 600,
	      settings: {
	        slidesToShow: 3,
	        slidesToScroll: 3
	      }
	    },
	    {
	      breakpoint: 480,
	      settings: {
	        slidesToShow: 2,
	        slidesToScroll: 2
	      }
	    }
	    // You can unslick at a given breakpoint now by adding:
	    // settings: "unslick"
	    // instead of a settings object
	  ]
	});	
	
	
	}
	
	// Remove is-loading intro class
	window.setTimeout(function() {
		jQuery('#wp3d-intro').removeClass('is-loading');
	}, 500);	

	jQuery(window).load(function() {
        jQuery('#wp3d-intro .wp3d-start > img').delay( 800 ).addClass('loaded');
        
        /* If the #start hash is present in the skinned URL, jump right in */
		if (window.location.hash){
			var hash = window.location.hash.substring(1);
			if (hash == "start"){
				console.log ("WP3D Starting!");
			   jQuery( ".wp3d-embed-wrap #wp3d-intro a" ).click();
			}
		}
        
    })
	
});
