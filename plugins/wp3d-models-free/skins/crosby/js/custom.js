// JavaScript Document

// detect drag vs click
(function($){
    var $doc = $(document),
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
            moved = abs(pos.x - e.pageX) > $.clickMouseMoved.threshold
                || abs(pos.y - e.pageY) > $.clickMouseMoved.threshold;
        }
    };
    
    $doc.on(mclick);
    
    $.clickMouseMoved = function () {
        return moved;
    };
    
    $.clickMouseMoved.threshold = 3;
})(jQuery);

// Used in the Gallery to greatly reduce load times
function preload(arrayOfImages) {
    jQuery(arrayOfImages).each(function(){
         (new Image()).src = this;
    });
}

// Ensure focus
function setIframeFocus() {
    var iframe = jQuery("#mp-iframe")[0];
    iframe.contentWindow.focus();
}

//iOS Check
function detectIOS() {
    var t = window.navigator.userAgent,
        e = /iPad|iPhone|iPod/;
    return e.test(t)
}

function inIframe() {
	if (window.self === window.top) {
	  return false;
	} else {
	  // in an iframe (or other frames), act accordingly
	  return true;
	}
}

function getSecondPart(str) {
    return str.split('-')[1];
}

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


(function(){
  "use strict";

jQuery(document).ready(function($) {
	
	// check for iOS
	if ( detectIOS() ) {
		$('body').addClass('is-ios');
	}
	
	// check for iFrame
	if ( inIframe() ) {
		$('body').addClass('is-framed');
	}	
	
	// Remove is-loading intro class
	window.setTimeout(function() {
		$('#wp3d-intro').removeClass('is-loading');
	}, 500);
	
	// Page Smooth Scrolling
	$('a#email-form').bind('click',function(event){
		var $anchor = $(this);

			$('html, body').stop().animate({
			scrollTop:( $($anchor.attr('href')).offset().top-0) 
			}, 1000,'easeInOutExpo');

		event.preventDefault();
	});
	
	// Overlays
	$( "#share" ).click(function(e) {
		e.preventDefault();
		$( "#share-overlay" ).addClass("show");
		$( "html").addClass("overlaid");
		$( ".navbar").css('visibility', 'hidden');
	});	
	
	$( "#agents" ).click(function(e) {
		e.preventDefault();
		$( "#agents-overlay" ).addClass("show");
		$( "html").addClass("overlaid");
		$( ".navbar").css('visibility', 'hidden');
	});		
	
	$( ".close-overlay" ).click(function(e) {
		e.preventDefault();
		$( ".overlay" ).removeClass("show");
		$( "html").removeClass("overlaid");
		$( ".navbar").css('visibility', 'visible');
	});		

	// Gallery Captions
	$('span.wp3d-caption-open').click( function() {
		$(this).hide();
		$(this).siblings('div.wp3d-zoom-gallery-caption, div.wp3d-gallery-caption').addClass('enabled');
	});	
	
	$('span.wp3d-caption-close').click( function() {
		$(this).parent().removeClass('enabled');
		$(this).parent().siblings('span.wp3d-caption-open').show();
	});	
	
	//Floorplan Images Load after modal click (eases the page load a hint)
	$('#floorplan-images a').on('click', function (e) {
	    var imgtarget = $(this).data('target');
	    var imgsrc = $(imgtarget + ' img').data('src');
	    //console.log(imgsrc);
	    $(imgtarget + ' img').attr('src', imgsrc);
	});
	
	/* Preload Functionality */
	var preloadModel = $( "#mp-iframe").data('preload');
	if (preloadModel === true ) {
		console.log('preload');
		if( viewport.width >= 768 ) { // do the preload
			var mp_iframe_preload_src = $( "#mp-iframe").data('src'); // get the iframe src
			// do the preload
			if (mp_iframe_preload_src !== '') {
				$( "#mp-iframe").attr("src", mp_iframe_preload_src);
			}
		}
	} else {
		preloadModel = false;
	}
	
	/* Intro Functionality */
	$( ".wp3d-embed-wrap #wp3d-intro a.no-iframe" ).click(function(e) {
		e.preventDefault();	
	});
	
	$( ".wp3d-embed-wrap #wp3d-intro a.load-iframe" ).click(function(e) {
		e.preventDefault();
		
		var mp_iframe_src = $( "#mp-iframe").data('src'); // get the iframe src
		var mp_iframe_allow = $( "#mp-iframe").data('allow'); // get the iframe allow
		var logo_overlay = $('.wp3d-embed-wrap .iframe-logo-overlay').clone(); // console.log(logo_overlay);
		var mp_iframe_close = '<a href="#" id="tour-close" title="CLOSE TOUR"><span class="fa-stack"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-times fa-stack-1x fa-inverse"></i></span></a>';
		
		//if( viewport.width < 768 || detectIOS() ) { // go fullscreen on small devices, and all of iOS
		if( viewport.width < 768 ) { // go fullscreen on small devices
			var markedSold = false; // setting this early on
			var markedPending = false; // setting this early on
			
			// add the fullscreen iframe (only on mobile)
			$('html').addClass('wp3d-fullscreen');
			$('<div id="wp3d-fullscreen-wrap"><iframe src="'+mp_iframe_src+'" frameborder="0" allow="vr' + mp_iframe_allow + '" allowfullscreen></div>').appendTo('body');
			$('<div id="wp3d-fullscreen-header">'+mp_iframe_close+'</div>').appendTo('body');
			logo_overlay.appendTo('#wp3d-fullscreen-wrap');
			
			if( viewport.width < 481 ) { // fade out the overlay on small widths...too much overlapping
				$('.iframe-logo-overlay').delay(4000).fadeOut(); // if there's an overlay, fade 'er out after a few on small screens
			}
			if ($('.wp3d-embed-wrap').hasClass('wp3d-sold')) { 
				markedSold = true; 
				$('.wp3d-embed-wrap').removeClass('wp3d-sold'); // if it exists, remove the sold when actually viewing the model
			} else if ($('.wp3d-embed-wrap').hasClass('wp3d-pending')) {
				markedPending = true; 
				$('.wp3d-embed-wrap').removeClass('wp3d-pending'); // if it exists, remove the pending when actually viewing the model
			} else {
				markedSold = false;
				markedPending = false;
			}
			
			// remove the fullscreen iframe
			$( "#wp3d-fullscreen-header a#tour-close" ).click(function(e) {
				e.preventDefault();
				$( "#wp3d-fullscreen-wrap" ).hide( "fast" );
				$( "#wp3d-fullscreen-header" ).hide( "fast" );	
				$('#wp3d-fullscreen-header, #wp3d-fullscreen-wrap').remove();		 
				$('html').removeClass('wp3d-fullscreen');
				if (markedSold) {
					$('.wp3d-embed-wrap').addClass('wp3d-sold');
				} else if (markedPending) {
					$('.wp3d-embed-wrap').addClass('wp3d-pending');
				}
			}); 			
			
		} else { 
			
			if (mp_iframe_src !== '' && preloadModel === false) { // only swap out the src if we're not preloading
				$( "#mp-iframe").attr("src",mp_iframe_src);
			}
	  		$( "#wp3d-intro" ).fadeOut("slow");
	  		$('.wp3d-embed-wrap').removeClass('wp3d-sold wp3d-pending'); // if it exists, remove the sold/pending when actually viewing the model
	  		setTimeout(setIframeFocus, 100);
	  					
		}
	});
	
	if ($.fn.YTPlayer && !/Mobi/.test(navigator.userAgent)) { // checking for existance of the ytp js & not mobile check
	
		// init YTPlayer YouTube Background Player
		$(function(){
			$(".wp3d-videobg-player").YTPlayer();
		});
		
	// } else if ($.fn.YTPlayer && /Edge\/12./i.test(navigator.userAgent)) { // checking for existance of the ytp js & Microsoft Edge
		
	// 	$("#wp3d-fallbackbg-img").addClass('wp3d-show');
	
	} else if ($.fn.YTPlayer) { // checking for existance of the ytp js & is mobile
	
		$("#wp3d-fallbackbg-img").addClass('wp3d-show');
	
	}

	if ($.fn.swiper) { // checking for existance of the swiper
   
	    //Swiper Gallery  
	    var swiper = new Swiper ('.swiper-container', {
			pagination: '.swiper-pagination',
			paginationClickable: '.swiper-pagination',
			nextButton: '.swiper-button-next',
			prevButton: '.swiper-button-prev',
			keyboardControl: true,
			loop: true,
			onInit : function(swiper) {
				//console.log('init');
			    $( ".swiper-slide-active" ).each(function ( index ) {
			    var src = $( this ).attr( "data-src" );
			    var prevsrc = $( this ).siblings('.swiper-slide-prev').attr( "data-src" );
			        $(this).css('background-image', 'url(' + src + ')');
			        preload([prevsrc]);
			    });
			},			
			onSlideNextStart : function(swiper) {
				//console.log('next');
			    $( ".swiper-slide-active" ).each(function ( index ) {
			    var src = $( this ).attr( "data-src" );
			    var nextsrc = $( this ).siblings('.swiper-slide-next').attr( "data-src" );
			        $(this).css('background-image', 'url(' + src + ')');
			        preload([nextsrc]);
			    });
			},
			onSlidePrevStart : function(swiper) {
				//console.log('prev');
			    $( ".swiper-slide-active" ).each(function ( index ) {
			    var src = $( this ).attr( "data-src" );
			    var current = $(this),
				    index = current.index();
				if (index === 0) {
					// when we're on the FIRST (cloned) element AND we're going backwards we need to find the third-from-last (because of cloning) to preload the correct image
					var prevsrc = $( this ).siblings('.swiper-slide:nth-last-child(3)').attr( "data-src" );
				} else {
				    var prevsrc = $( this ).siblings('.swiper-slide-prev').attr( "data-src" );
				}
				$(this).css('background-image', 'url(' + src + ')');
				preload([prevsrc]);
			    });
			}			
	    });
	}
	
	// Slick no drag click
	
	if ($.fn.featherlight) { // checking for existance of the swiper	
	
		$('#wp3d-zoom-gallery a').featherlight({
            targetAttr: 'href',
            type: 'image',
            closeIcon: '&#xf00d;',
            beforeOpen: function(event){
                if ($.clickMouseMoved()) {
			        //console.log('click aborted');
			        return false;           
			    }
            }
        });
        
	}
	
   if ($.fn.slick) { // checking for existance of slick	
			
		// Slick Variable Width Gallery
		$('.wp3d-zoom-slider')
			.on('init', function(event, slick){
				$('.slick-track .slick-slide').last().clone().appendTo( ".slick-track" ).addClass('cloned-slide last-slide');
				$('.slick-track .slick-slide').first().clone().appendTo( ".slick-track" ).removeClass('slick-current slick-center').addClass('cloned-slide first-slide');
				loadClone();
			})	
			.on('init reInit afterChange', function (event, slick, currentSlide, nextSlide) {
				var i = (currentSlide ? currentSlide : 0) + 1;
	        	$('.slick-current .slick-counter').text(i + '/' + slick.slideCount);
		    })
			.on('lazyLoaded', function(event, slick, image, imageSource){
				setTimeout( function() {  
					$('#wp3d-zoom-gallery').removeClass('gallery-loading');
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
		
   } // if slick exists 
	
	$(window).load(function() {
		$('body').addClass('loaded');
	    $('body').delay( 800 ).addClass('loaded-delay');
		// $('body').removeClass('is-loading');
		
		/* If the #start hash is present in the skinned URL, jump right in */
		if (window.location.hash){
			var hash = window.location.hash.substring(1);
			if (hash == "start"){
				console.log ("WP3D Starting!");
			  $( ".wp3d-embed-wrap #wp3d-intro a" ).click();
			}
			//if (hash == "delay5000"){
			if (hash.indexOf("delay-") >= 0) {
				var delayAmt = getSecondPart(hash);
				setTimeout(function(){
				console.log ("WP3D Starting after delay of "+delayAmt+"ms");
					$( ".wp3d-embed-wrap #wp3d-intro a" ).click();
				},delayAmt);
			}
		}
	});	



});	// end ready
	
})(); // end strict
