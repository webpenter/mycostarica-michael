if (typeof ai_adsense_ad_names !== 'undefined') {

//var ai_adsense_ad_names = [];
//var ai_preview_window = typeof ai_preview !== 'undefined';

function ai_process_adsense_ad (element) {
  var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//  var ai_debug = false;

//  var adsense_container = jQuery(element);
  var adsense_container = element;

//  var adsense_width = adsense_container.attr ('width');
  var adsense_width = adsense_container.getAttribute ('width');
//  var adsense_height = adsense_container.attr ('height');
  var adsense_height = adsense_container.getAttribute ('height');

//  var adsense_iframe2 = adsense_container.contents().find ('iframe[allowtransparency]');
//  var url_parameters = getAllUrlParams (adsense_iframe2.attr ('src'))
//  var url_parameters = getAllUrlParams (adsense_container.attr ('src'))
  var url_parameters = getAllUrlParams (adsense_container.getAttribute ('src'))

  if (typeof url_parameters ['client'] !== 'undefined') {
    var adsense_ad_client = url_parameters ['client'];
    var adsense_publisher_id = adsense_ad_client.replace ('ca-', '');
    var adsense_ad_slot = url_parameters ['slotname'];
    var adsense_index = url_parameters ['ifi'];

    if (ai_debug) console.log ('AI ADSENSE', adsense_index, adsense_ad_client, adsense_ad_slot, url_parameters ['format'], url_parameters ['w'], url_parameters ['h']);

//    var adsense_overlay = jQuery('<div class="ai-debug-ad-overlay"></div>');
    var adsense_overlay_class = 'ai-debug-ad-overlay';

    var adsense_ad_info = '';
    if (typeof adsense_ad_slot !== 'undefined') {
      var adsense_ad_name = '';
      if (typeof ai_adsense_ad_names ['publisher_id'] !== 'undefined' &&
          ai_adsense_ad_names ['publisher_id'] == adsense_publisher_id &&
          typeof ai_adsense_ad_names [adsense_ad_slot] !== 'undefined') {
        adsense_ad_name = '<div class="ai-info ai-info-2">' + ai_adsense_ad_names [adsense_ad_slot] + '</div>';
      }
      adsense_ad_info = '<div class="ai-info ai-info-1">' + adsense_ad_slot + '</div>' + adsense_ad_name;
    } else {
        var adsense_auto_ads = adsense_container.closest ('div.google-auto-placed').length != 0;
        if (adsense_auto_ads) {
//          adsense_overlay.addClass ('ai-auto-ads');
          adsense_overlay_class += ' ai-auto-ads';

          adsense_ad_info = '<div class="ai-info ai-info-1">Auto ads</div>';
//        } else adsense_overlay.addClass ('ai-no-slot');
        } else adsense_overlay_class += ' ai-no-slot';
      }

    var adsense_overlay = '<div class="' + adsense_overlay_class + '"></div>';

//    var adsense_info = jQuery('<div class="ai-debug-ad-info"><div class="ai-info ai-info-1">AdSense #' + adsense_index + '</div><div class="ai-info ai-info-2">' + adsense_width + 'x' + adsense_height + '</div>' + adsense_ad_info + '</div>');
    var adsense_info = '<div class="ai-debug-ad-info"><div class="ai-info ai-info-1">AdSense #' + adsense_index + '</div><div class="ai-info ai-info-2">' + adsense_width + 'x' + adsense_height + '</div>' + adsense_ad_info + '</div>';

//    adsense_container.after (adsense_info);
    adsense_container.insertAdjacentHTML ('afterend', adsense_info);

    if (!ai_preview_window) {
//      adsense_container.after (adsense_overlay);
      adsense_container.insertAdjacentHTML ('afterend', adsense_overlay);
    }
  }
}

//function ai_process_adsense_ads () {
////  jQuery('ins > ins > iframe[src*="google"]:visible').each (function () {
//  document.querySelectorAll ('ins iframe[src*="google"]').forEach ((el, index) => {
//    if (!!(el.offsetWidth || el.offsetHeight || el.getClientRects ().length)) {
////      ai_process_adsense_ad (this);
//      ai_process_adsense_ad (el);
//    }
//  });
//}


//jQuery(document).ready(function($) {
function ai_load_adsense_ad_units () {

  var ai_debug = typeof ai_debugging !== 'undefined'; // 2
//  var ai_debug = false;

//  var ai_ajax_url = 'AI_AJAXURL';
//  var ai_nonce = 'AI_NONCE';
//  var adsense_data = {'ai': 1}; // dummy

//  $.post (ai_ajax_url, {'action': 'ai_ajax', 'ai_check': ai_nonce, 'adsense-ad-units': adsense_data}
//  ).done (function (data) {

  var data = {
    'action': "ai_ajax",
    'ai_check': ai_nonce,
    'adsense-ad-units[ai]': 1
  };

  var formBody = [];
  for (var property in data) {
    var encodedKey = encodeURIComponent (property);
    var encodedValue = encodeURIComponent (data [property]);
    formBody.push (encodedKey + "=" + encodedValue);
  }
  formBody = formBody.join ("&");

  async function ai_load_adsense () {
    const response = await fetch (ai_ajax_url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: formBody
    });

    const text = await response.text ();

    return text;
  }

  ai_load_adsense ().then (data => {
    if (data != '') {
      try {
        ai_adsense_ad_names = JSON.parse (data);

        if (ai_debug) console.log ('');
        if (ai_debug) console.log ("AI ADSENSE DATA:", Object.keys (ai_adsense_ad_names).length - 1, 'ad units');

      } catch (error) {
        if (ai_debug) console.log ("AI ADSENSE DATA ERROR:", data);
      }
    }
    if (ai_debug) console.log ('AI ADSENSE DATA', 'END');
//  }).fail (function (xhr, status, error) {
  }).catch ((error) => {
    if (ai_debug) console.log ("AI ADSENSE DATA ERROR:", error.status, error.statusText);
//  }).always (function (data) {
  });

//  $(window).on ('load', function () {
//    if (!ai_preview_window) setTimeout (function() {ai_process_adsense_ads (jQuery);}, 500);
//  });
//});
}

function ai_ready (fn) {
  if (document.readyState   === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
    fn ();
  } else {
     document.addEventListener ('DOMContentLoaded', fn);
  }
}

ai_ready (ai_load_adsense_ad_units);

if (!ai_preview_window) {
  const targetNode = document.querySelector ('body');
  const config = {attributes: false, childList: true, subtree: true};
  const ai_process_adsense_callback = function (mutationsList, observer) {
    // Use traditional 'for loops' for IE 11
    for (const mutation of mutationsList) {
      if (mutation.type === 'childList' &&
          mutation.addedNodes.length &&
          mutation.addedNodes [0].tagName == 'IFRAME' &&
          mutation.addedNodes [0].getAttribute ('width') != null &&
          mutation.addedNodes [0].getAttribute ('height') != null &&
          !!mutation.addedNodes [0].closest ('.adsbygoogle')) {
        ai_process_adsense_ad (mutation.addedNodes [0]);
      }
    }
  };

  const observer = new MutationObserver (ai_process_adsense_callback);
  observer.observe (targetNode, config);
}


function getAllUrlParams (url) {

  // get query string from url (optional) or window
  var queryString = url ? url.split('?')[1] : window.location.search.slice(1);

  // we'll store the parameters here
  var obj = {};

  // if query string exists
  if (queryString) {

    // stuff after # is not part of query string, so get rid of it
    queryString = queryString.split('#')[0];

    // split our query string into its component parts
    var arr = queryString.split('&');

    for (var i=0; i<arr.length; i++) {
      // separate the keys and the values
      var a = arr[i].split('=');

      // in case params look like: list[]=thing1&list[]=thing2
      var paramNum = undefined;
      var paramName = a[0].replace(/\[\d*\]/, function(v) {
        paramNum = v.slice(1,-1);
        return '';
      });

      // set parameter value (use 'true' if empty)
//      var paramValue = typeof(a[1])==='undefined' ? true : a[1];
      var paramValue = typeof(a[1])==='undefined' ? '' : a[1];

      // (optional) keep case consistent
      paramName = paramName.toLowerCase();
      paramValue = paramValue.toLowerCase();

      // if parameter name already exists
      if (obj[paramName]) {
        // convert value to array (if still string)
        if (typeof obj[paramName] === 'string') {
          obj[paramName] = [obj[paramName]];
        }
        // if no array index number specified...
        if (typeof paramNum === 'undefined') {
          // put the value on the end of the array
          obj[paramName].push(paramValue);
        }
        // if array index number specified...
        else {
          // put the value at that index number
          obj[paramName][paramNum] = paramValue;
        }
      }
      // if param name doesn't exist yet, set it
      else {
        obj[paramName] = paramValue;
      }
    }
  }

  return obj;
}

}
