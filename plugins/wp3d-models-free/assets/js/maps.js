//http://google-maps-utility-library-v3.googlecode.com/svn/tags/markerwithlabel/1.1.9/
/**
 * @name MarkerWithLabel for V3
 * @version 1.1.9 [March 6, 2014]
 * @author Gary Little (inspired by code from Marc Ridey of Google).
 * @copyright Copyright 2012 Gary Little [gary at luxcentral.com]
 * @fileoverview MarkerWithLabel extends the Google Maps JavaScript API V3
 *  <code>google.maps.Marker</code> class.
 *  <p>
 *  MarkerWithLabel allows you to define markers with associated labels. As you would expect,
 *  if the marker is draggable, so too will be the label. In addition, a marker with a label
 *  responds to all mouse events in the same manner as a regular marker. It also fires mouse
 *  events and "property changed" events just as a regular marker would. Version 1.1 adds
 *  support for the raiseOnDrag feature introduced in API V3.3.
 *  <p>
 *  If you drag a marker by its label, you can cancel the drag and return the marker to its
 *  original position by pressing the <code>Esc</code> key. This doesn't work if you drag the marker
 *  itself because this feature is not (yet) supported in the <code>google.maps.Marker</code> class.
 */

/*!
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*jslint browser:true */
/*global document,google */

/**
 * @param {Function} childCtor Child class.
 * @param {Function} parentCtor Parent class.
 * @private
 */
function inherits(childCtor, parentCtor) {
  /* @constructor */
  function tempCtor() {}
  tempCtor.prototype = parentCtor.prototype;
  childCtor.superClass_ = parentCtor.prototype;
  childCtor.prototype = new tempCtor();
  /* @override */
  childCtor.prototype.constructor = childCtor;
}

/**
 * This constructor creates a label and associates it with a marker.
 * It is for the private use of the MarkerWithLabel class.
 * @constructor
 * @param {Marker} marker The marker with which the label is to be associated.
 * @param {string} crossURL The URL of the cross image =.
 * @param {string} handCursor The URL of the hand cursor.
 * @private
 */
function MarkerLabel_(marker, crossURL, handCursorURL) {
  this.marker_ = marker;
  this.handCursorURL_ = marker.handCursorURL;

  this.labelDiv_ = document.createElement("div");
  this.labelDiv_.style.cssText = "position: absolute; overflow: hidden;";

  // Set up the DIV for handling mouse events in the label. This DIV forms a transparent veil
  // in the "overlayMouseTarget" pane, a veil that covers just the label. This is done so that
  // events can be captured even if the label is in the shadow of a google.maps.InfoWindow.
  // Code is included here to ensure the veil is always exactly the same size as the label.
  this.eventDiv_ = document.createElement("div");
  this.eventDiv_.style.cssText = this.labelDiv_.style.cssText;

  // This is needed for proper behavior on MSIE:
  this.eventDiv_.setAttribute("onselectstart", "return false;");
  this.eventDiv_.setAttribute("ondragstart", "return false;");

  // Get the DIV for the "X" to be displayed when the marker is raised.
  this.crossDiv_ = MarkerLabel_.getSharedCross(crossURL);
}

inherits(MarkerLabel_, google.maps.OverlayView);

/**
 * Returns the DIV for the cross used when dragging a marker when the
 * raiseOnDrag parameter set to true. One cross is shared with all markers.
 * @param {string} crossURL The URL of the cross image =.
 * @private
 */
MarkerLabel_.getSharedCross = function (crossURL) {
  var div;
  if (typeof MarkerLabel_.getSharedCross.crossDiv === "undefined") {
    div = document.createElement("img");
    div.style.cssText = "position: absolute; z-index: 1000002; display: none;";
    // Hopefully Google never changes the standard "X" attributes:
    div.style.marginLeft = "-8px";
    div.style.marginTop = "-9px";
    div.src = crossURL;
    MarkerLabel_.getSharedCross.crossDiv = div;
  }
  return MarkerLabel_.getSharedCross.crossDiv;
};

/**
 * Adds the DIV representing the label to the DOM. This method is called
 * automatically when the marker's <code>setMap</code> method is called.
 * @private
 */
MarkerLabel_.prototype.onAdd = function () {
  var me = this;
  var cMouseIsDown = false;
  var cDraggingLabel = false;
  var cSavedZIndex;
  var cLatOffset, cLngOffset;
  var cIgnoreClick;
  var cRaiseEnabled;
  var cStartPosition;
  var cStartCenter;
  // Constants:
  var cRaiseOffset = 20;
  var cDraggingCursor = "url(" + this.handCursorURL_ + ")";

  // Stops all processing of an event.
  //
  var cAbortEvent = function (e) {
    if (e.preventDefault) {
      e.preventDefault();
    }
    e.cancelBubble = true;
    if (e.stopPropagation) {
      e.stopPropagation();
    }
  };

  var cStopBounce = function () {
    me.marker_.setAnimation(null);
  };

  this.getPanes().overlayImage.appendChild(this.labelDiv_);
  this.getPanes().overlayMouseTarget.appendChild(this.eventDiv_);
  // One cross is shared with all markers, so only add it once:
  if (typeof MarkerLabel_.getSharedCross.processed === "undefined") {
    this.getPanes().overlayImage.appendChild(this.crossDiv_);
    MarkerLabel_.getSharedCross.processed = true;
  }

  this.listeners_ = [
    google.maps.event.addDomListener(this.eventDiv_, "mouseover", function (e) {
      if (me.marker_.getDraggable() || me.marker_.getClickable()) {
        this.style.cursor = "pointer";
        google.maps.event.trigger(me.marker_, "mouseover", e);
      }
    }),
    google.maps.event.addDomListener(this.eventDiv_, "mouseout", function (e) {
      if ((me.marker_.getDraggable() || me.marker_.getClickable()) && !cDraggingLabel) {
        this.style.cursor = me.marker_.getCursor();
        google.maps.event.trigger(me.marker_, "mouseout", e);
      }
    }),
    google.maps.event.addDomListener(this.eventDiv_, "mousedown", function (e) {
      cDraggingLabel = false;
      if (me.marker_.getDraggable()) {
        cMouseIsDown = true;
        this.style.cursor = cDraggingCursor;
      }
      if (me.marker_.getDraggable() || me.marker_.getClickable()) {
        google.maps.event.trigger(me.marker_, "mousedown", e);
        cAbortEvent(e); // Prevent map pan when starting a drag on a label
      }
    }),
    google.maps.event.addDomListener(document, "mouseup", function (mEvent) {
      var position;
      if (cMouseIsDown) {
        cMouseIsDown = false;
        me.eventDiv_.style.cursor = "pointer";
        google.maps.event.trigger(me.marker_, "mouseup", mEvent);
      }
      if (cDraggingLabel) {
        if (cRaiseEnabled) { // Lower the marker & label
          position = me.getProjection().fromLatLngToDivPixel(me.marker_.getPosition());
          position.y += cRaiseOffset;
          me.marker_.setPosition(me.getProjection().fromDivPixelToLatLng(position));
          // This is not the same bouncing style as when the marker portion is dragged,
          // but it will have to do:
          try { // Will fail if running Google Maps API earlier than V3.3
            me.marker_.setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(cStopBounce, 1406);
          } catch (e) {}
        }
        me.crossDiv_.style.display = "none";
        me.marker_.setZIndex(cSavedZIndex);
        cIgnoreClick = true; // Set flag to ignore the click event reported after a label drag
        cDraggingLabel = false;
        mEvent.latLng = me.marker_.getPosition();
        google.maps.event.trigger(me.marker_, "dragend", mEvent);
      }
    }),
    google.maps.event.addListener(me.marker_.getMap(), "mousemove", function (mEvent) {
      var position;
      if (cMouseIsDown) {
        if (cDraggingLabel) {
          // Change the reported location from the mouse position to the marker position:
          mEvent.latLng = new google.maps.LatLng(mEvent.latLng.lat() - cLatOffset, mEvent.latLng.lng() - cLngOffset);
          position = me.getProjection().fromLatLngToDivPixel(mEvent.latLng);
          if (cRaiseEnabled) {
            me.crossDiv_.style.left = position.x + "px";
            me.crossDiv_.style.top = position.y + "px";
            me.crossDiv_.style.display = "";
            position.y -= cRaiseOffset;
          }
          me.marker_.setPosition(me.getProjection().fromDivPixelToLatLng(position));
          if (cRaiseEnabled) { // Don't raise the veil; this hack needed to make MSIE act properly
            me.eventDiv_.style.top = (position.y + cRaiseOffset) + "px";
          }
          google.maps.event.trigger(me.marker_, "drag", mEvent);
        } else {
          // Calculate offsets from the click point to the marker position:
          cLatOffset = mEvent.latLng.lat() - me.marker_.getPosition().lat();
          cLngOffset = mEvent.latLng.lng() - me.marker_.getPosition().lng();
          cSavedZIndex = me.marker_.getZIndex();
          cStartPosition = me.marker_.getPosition();
          cStartCenter = me.marker_.getMap().getCenter();
          cRaiseEnabled = me.marker_.get("raiseOnDrag");
          cDraggingLabel = true;
          me.marker_.setZIndex(1000000); // Moves the marker & label to the foreground during a drag
          mEvent.latLng = me.marker_.getPosition();
          google.maps.event.trigger(me.marker_, "dragstart", mEvent);
        }
      }
    }),
    google.maps.event.addDomListener(document, "keydown", function (e) {
      if (cDraggingLabel) {
        if (e.keyCode === 27) { // Esc key
          cRaiseEnabled = false;
          me.marker_.setPosition(cStartPosition);
          me.marker_.getMap().setCenter(cStartCenter);
          google.maps.event.trigger(document, "mouseup", e);
        }
      }
    }),
    google.maps.event.addDomListener(this.eventDiv_, "click", function (e) {
      if (me.marker_.getDraggable() || me.marker_.getClickable()) {
        if (cIgnoreClick) { // Ignore the click reported when a label drag ends
          cIgnoreClick = false;
        } else {
          google.maps.event.trigger(me.marker_, "click", e);
          cAbortEvent(e); // Prevent click from being passed on to map
        }
      }
    }),
    google.maps.event.addDomListener(this.eventDiv_, "dblclick", function (e) {
      if (me.marker_.getDraggable() || me.marker_.getClickable()) {
        google.maps.event.trigger(me.marker_, "dblclick", e);
        cAbortEvent(e); // Prevent map zoom when double-clicking on a label
      }
    }),
    google.maps.event.addListener(this.marker_, "dragstart", function (mEvent) {
      if (!cDraggingLabel) {
        cRaiseEnabled = this.get("raiseOnDrag");
      }
    }),
    google.maps.event.addListener(this.marker_, "drag", function (mEvent) {
      if (!cDraggingLabel) {
        if (cRaiseEnabled) {
          me.setPosition(cRaiseOffset);
          // During a drag, the marker's z-index is temporarily set to 1000000 to
          // ensure it appears above all other markers. Also set the label's z-index
          // to 1000000 (plus or minus 1 depending on whether the label is supposed
          // to be above or below the marker).
          me.labelDiv_.style.zIndex = 1000000 + (this.get("labelInBackground") ? -1 : +1);
        }
      }
    }),
    google.maps.event.addListener(this.marker_, "dragend", function (mEvent) {
      if (!cDraggingLabel) {
        if (cRaiseEnabled) {
          me.setPosition(0); // Also restores z-index of label
        }
      }
    }),
    google.maps.event.addListener(this.marker_, "position_changed", function () {
      me.setPosition();
    }),
    google.maps.event.addListener(this.marker_, "zindex_changed", function () {
      me.setZIndex();
    }),
    google.maps.event.addListener(this.marker_, "visible_changed", function () {
      me.setVisible();
    }),
    google.maps.event.addListener(this.marker_, "labelvisible_changed", function () {
      me.setVisible();
    }),
    google.maps.event.addListener(this.marker_, "title_changed", function () {
      me.setTitle();
    }),
    google.maps.event.addListener(this.marker_, "labelcontent_changed", function () {
      me.setContent();
    }),
    google.maps.event.addListener(this.marker_, "labelanchor_changed", function () {
      me.setAnchor();
    }),
    google.maps.event.addListener(this.marker_, "labelclass_changed", function () {
      me.setStyles();
    }),
    google.maps.event.addListener(this.marker_, "labelstyle_changed", function () {
      me.setStyles();
    })
  ];
};

/**
 * Removes the DIV for the label from the DOM. It also removes all event handlers.
 * This method is called automatically when the marker's <code>setMap(null)</code>
 * method is called.
 * @private
 */
MarkerLabel_.prototype.onRemove = function () {
  var i;
  this.labelDiv_.parentNode.removeChild(this.labelDiv_);
  this.eventDiv_.parentNode.removeChild(this.eventDiv_);

  // Remove event listeners:
  for (i = 0; i < this.listeners_.length; i++) {
    google.maps.event.removeListener(this.listeners_[i]);
  }
};

/**
 * Draws the label on the map.
 * @private
 */
MarkerLabel_.prototype.draw = function () {
  this.setContent();
  this.setTitle();
  this.setStyles();
};

/**
 * Sets the content of the label.
 * The content can be plain text or an HTML DOM node.
 * @private
 */
MarkerLabel_.prototype.setContent = function () {
  var content = this.marker_.get("labelContent");
  if (typeof content.nodeType === "undefined") {
    this.labelDiv_.innerHTML = content;
    this.eventDiv_.innerHTML = this.labelDiv_.innerHTML;
  } else {
    this.labelDiv_.innerHTML = ""; // Remove current content
    this.labelDiv_.appendChild(content);
    content = content.cloneNode(true);
    this.eventDiv_.appendChild(content);
  }
};

/**
 * Sets the content of the tool tip for the label. It is
 * always set to be the same as for the marker itself.
 * @private
 */
MarkerLabel_.prototype.setTitle = function () {
  this.eventDiv_.title = this.marker_.getTitle() || "";
};

/**
 * Sets the style of the label by setting the style sheet and applying
 * other specific styles requested.
 * @private
 */
MarkerLabel_.prototype.setStyles = function () {
  var i, labelStyle;

  // Apply style values from the style sheet defined in the labelClass parameter:
  this.labelDiv_.className = this.marker_.get("labelClass");
  this.eventDiv_.className = this.labelDiv_.className;

  // Clear existing inline style values:
  this.labelDiv_.style.cssText = "";
  this.eventDiv_.style.cssText = "";
  // Apply style values defined in the labelStyle parameter:
  labelStyle = this.marker_.get("labelStyle");
  for (i in labelStyle) {
    if (labelStyle.hasOwnProperty(i)) {
      this.labelDiv_.style[i] = labelStyle[i];
      this.eventDiv_.style[i] = labelStyle[i];
    }
  }
  this.setMandatoryStyles();
};

/**
 * Sets the mandatory styles to the DIV representing the label as well as to the
 * associated event DIV. This includes setting the DIV position, z-index, and visibility.
 * @private
 */
MarkerLabel_.prototype.setMandatoryStyles = function () {
  this.labelDiv_.style.position = "absolute";
  this.labelDiv_.style.overflow = "hidden";
  // Make sure the opacity setting causes the desired effect on MSIE:
  if (typeof this.labelDiv_.style.opacity !== "undefined" && this.labelDiv_.style.opacity !== "") {
    this.labelDiv_.style.MsFilter = "\"progid:DXImageTransform.Microsoft.Alpha(opacity=" + (this.labelDiv_.style.opacity * 100) + ")\"";
    this.labelDiv_.style.filter = "alpha(opacity=" + (this.labelDiv_.style.opacity * 100) + ")";
  }

  this.eventDiv_.style.position = this.labelDiv_.style.position;
  this.eventDiv_.style.overflow = this.labelDiv_.style.overflow;
  this.eventDiv_.style.opacity = 0.01; // Don't use 0; DIV won't be clickable on MSIE
  this.eventDiv_.style.MsFilter = "\"progid:DXImageTransform.Microsoft.Alpha(opacity=1)\"";
  this.eventDiv_.style.filter = "alpha(opacity=1)"; // For MSIE

  this.setAnchor();
  this.setPosition(); // This also updates z-index, if necessary.
  this.setVisible();
};

/**
 * Sets the anchor point of the label.
 * @private
 */
MarkerLabel_.prototype.setAnchor = function () {
  var anchor = this.marker_.get("labelAnchor");
  this.labelDiv_.style.marginLeft = -anchor.x + "px";
  this.labelDiv_.style.marginTop = -anchor.y + "px";
  this.eventDiv_.style.marginLeft = -anchor.x + "px";
  this.eventDiv_.style.marginTop = -anchor.y + "px";
};

/**
 * Sets the position of the label. The z-index is also updated, if necessary.
 * @private
 */
MarkerLabel_.prototype.setPosition = function (yOffset) {
  var position = this.getProjection().fromLatLngToDivPixel(this.marker_.getPosition());
  if (typeof yOffset === "undefined") {
    yOffset = 0;
  }
  this.labelDiv_.style.left = Math.round(position.x) + "px";
  this.labelDiv_.style.top = Math.round(position.y - yOffset) + "px";
  this.eventDiv_.style.left = this.labelDiv_.style.left;
  this.eventDiv_.style.top = this.labelDiv_.style.top;

  this.setZIndex();
};

/**
 * Sets the z-index of the label. If the marker's z-index property has not been defined, the z-index
 * of the label is set to the vertical coordinate of the label. This is in keeping with the default
 * stacking order for Google Maps: markers to the south are in front of markers to the north.
 * @private
 */
MarkerLabel_.prototype.setZIndex = function () {
  var zAdjust = (this.marker_.get("labelInBackground") ? -1 : +1);
  if (typeof this.marker_.getZIndex() === "undefined") {
    this.labelDiv_.style.zIndex = parseInt(this.labelDiv_.style.top, 10) + zAdjust;
    this.eventDiv_.style.zIndex = this.labelDiv_.style.zIndex;
  } else {
    this.labelDiv_.style.zIndex = this.marker_.getZIndex() + zAdjust;
    this.eventDiv_.style.zIndex = this.labelDiv_.style.zIndex;
  }
};

/**
 * Sets the visibility of the label. The label is visible only if the marker itself is
 * visible (i.e., its visible property is true) and the labelVisible property is true.
 * @private
 */
MarkerLabel_.prototype.setVisible = function () {
  if (this.marker_.get("labelVisible")) {
    this.labelDiv_.style.display = this.marker_.getVisible() ? "block" : "none";
  } else {
    this.labelDiv_.style.display = "none";
  }
  this.eventDiv_.style.display = this.labelDiv_.style.display;
};

/**
 * @name MarkerWithLabelOptions
 * @class This class represents the optional parameter passed to the {@link MarkerWithLabel} constructor.
 *  The properties available are the same as for <code>google.maps.Marker</code> with the addition
 *  of the properties listed below. To change any of these additional properties after the labeled
 *  marker has been created, call <code>google.maps.Marker.set(propertyName, propertyValue)</code>.
 *  <p>
 *  When any of these properties changes, a property changed event is fired. The names of these
 *  events are derived from the name of the property and are of the form <code>propertyname_changed</code>.
 *  For example, if the content of the label changes, a <code>labelcontent_changed</code> event
 *  is fired.
 *  <p>
 * @property {string|Node} [labelContent] The content of the label (plain text or an HTML DOM node).
 * @property {Point} [labelAnchor] By default, a label is drawn with its anchor point at (0,0) so
 *  that its top left corner is positioned at the anchor point of the associated marker. Use this
 *  property to change the anchor point of the label. For example, to center a 50px-wide label
 *  beneath a marker, specify a <code>labelAnchor</code> of <code>google.maps.Point(25, 0)</code>.
 *  (Note: x-values increase to the right and y-values increase to the top.)
 * @property {string} [labelClass] The name of the CSS class defining the styles for the label.
 *  Note that style values for <code>position</code>, <code>overflow</code>, <code>top</code>,
 *  <code>left</code>, <code>zIndex</code>, <code>display</code>, <code>marginLeft</code>, and
 *  <code>marginTop</code> are ignored; these styles are for internal use only.
 * @property {Object} [labelStyle] An object literal whose properties define specific CSS
 *  style values to be applied to the label. Style values defined here override those that may
 *  be defined in the <code>labelClass</code> style sheet. If this property is changed after the
 *  label has been created, all previously set styles (except those defined in the style sheet)
 *  are removed from the label before the new style values are applied.
 *  Note that style values for <code>position</code>, <code>overflow</code>, <code>top</code>,
 *  <code>left</code>, <code>zIndex</code>, <code>display</code>, <code>marginLeft</code>, and
 *  <code>marginTop</code> are ignored; these styles are for internal use only.
 * @property {boolean} [labelInBackground] A flag indicating whether a label that overlaps its
 *  associated marker should appear in the background (i.e., in a plane below the marker).
 *  The default is <code>false</code>, which causes the label to appear in the foreground.
 * @property {boolean} [labelVisible] A flag indicating whether the label is to be visible.
 *  The default is <code>true</code>. Note that even if <code>labelVisible</code> is
 *  <code>true</code>, the label will <i>not</i> be visible unless the associated marker is also
 *  visible (i.e., unless the marker's <code>visible</code> property is <code>true</code>).
 * @property {boolean} [raiseOnDrag] A flag indicating whether the label and marker are to be
 *  raised when the marker is dragged. The default is <code>true</code>. If a draggable marker is
 *  being created and a version of Google Maps API earlier than V3.3 is being used, this property
 *  must be set to <code>false</code>.
 * @property {boolean} [optimized] A flag indicating whether rendering is to be optimized for the
 *  marker. <b>Important: The optimized rendering technique is not supported by MarkerWithLabel,
 *  so the value of this parameter is always forced to <code>false</code>.
 * @property {string} [crossImage="http://maps.gstatic.com/intl/en_us/mapfiles/drag_cross_67_16.png"]
 *  The URL of the cross image to be displayed while dragging a marker.
 * @property {string} [handCursor="http://maps.gstatic.com/intl/en_us/mapfiles/closedhand_8_8.cur"]
 *  The URL of the cursor to be displayed while dragging a marker.
 */
/**
 * Creates a MarkerWithLabel with the options specified in {@link MarkerWithLabelOptions}.
 * @constructor
 * @param {MarkerWithLabelOptions} [opt_options] The optional parameters.
 */
function MarkerWithLabel(opt_options) {
  opt_options = opt_options || {};
  opt_options.labelContent = opt_options.labelContent || "";
  opt_options.labelAnchor = opt_options.labelAnchor || new google.maps.Point(0, 0);
  opt_options.labelClass = opt_options.labelClass || "markerLabels";
  opt_options.labelStyle = opt_options.labelStyle || {};
  opt_options.labelInBackground = opt_options.labelInBackground || false;
  if (typeof opt_options.labelVisible === "undefined") {
    opt_options.labelVisible = true;
  }
  if (typeof opt_options.raiseOnDrag === "undefined") {
    opt_options.raiseOnDrag = true;
  }
  if (typeof opt_options.clickable === "undefined") {
    opt_options.clickable = true;
  }
  if (typeof opt_options.draggable === "undefined") {
    opt_options.draggable = false;
  }
  if (typeof opt_options.optimized === "undefined") {
    opt_options.optimized = false;
  }
  opt_options.crossImage = opt_options.crossImage || "http" + (document.location.protocol === "https:" ? "s" : "") + "://maps.gstatic.com/intl/en_us/mapfiles/drag_cross_67_16.png";
  opt_options.handCursor = opt_options.handCursor || "http" + (document.location.protocol === "https:" ? "s" : "") + "://maps.gstatic.com/intl/en_us/mapfiles/closedhand_8_8.cur";
  opt_options.optimized = false; // Optimized rendering is not supported

  this.label = new MarkerLabel_(this, opt_options.crossImage, opt_options.handCursor); // Bind the label to the marker

  // Call the parent constructor. It calls Marker.setValues to initialize, so all
  // the new parameters are conveniently saved and can be accessed with get/set.
  // Marker.set triggers a property changed event (called "propertyname_changed")
  // that the marker label listens for in order to react to state changes.
  google.maps.Marker.apply(this, arguments);
}

inherits(MarkerWithLabel, google.maps.Marker);

/**
 * Overrides the standard Marker setMap function.
 * @param {Map} theMap The map to which the marker is to be added.
 * @private
 */
MarkerWithLabel.prototype.setMap = function (theMap) {

  // Call the inherited function...
  google.maps.Marker.prototype.setMap.apply(this, arguments);

  // ... then deal with the label:
  this.label.setMap(theMap);
};


// *************************************************** //
// ************ DOCUMENT READY FUNCTIONS ************* // 
// *************************************************** //


jQuery(document).ready(function() {
  
// ************ URL DECODING ************* // 
  
function wp3d_urldecode(str) {
   return decodeURIComponent((str+'').replace(/\+/g, '%20'));
}  
  
// ************ VIEWPORT INFO ************* //

  function updateViewportDimensions() {
  	var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],x=w.innerWidth||e.clientWidth||g.clientWidth,y=w.innerHeight||e.clientHeight||g.clientHeight;
  	return { width:x,height:y }
  }
  var viewport = updateViewportDimensions();
  	  
// ************ COLLECT MARKER DATA FROM HTML ************* //	
	
    //var markers = jQuery("#wp3d-models").html();	
  
    var viewterm = jQuery("#wp3d-models").attr("data-viewterm");
    var locations = [];
      
    jQuery('#wp3d-models > div').each(function () {

            if(jQuery(this).attr("data-latlng")){
              var location = {};
              location.lat = parseFloat(jQuery(this).attr("data-latlng").split(",")[0]);
              location.lon = parseFloat(jQuery(this).attr("data-latlng").split(",")[1]);
              location.slug = jQuery(this).attr("data-slug");
              location.thumb = decodeURIComponent(jQuery(this).attr("data-thumb"));
              location.title = jQuery(this).attr("data-title");
              location.marker = jQuery(this).attr("data-marker-type");
              location.types = jQuery(this).attr("class").split(/\s+/); // build an array of class names
              locations.push(location);  
            }
    }); 
    
    
			      
 // ************ GOOGLE MAP OBJECT ************* //

    var map;
    var bounds;
    var gmarkers = [];
    var myLatlng = new google.maps.LatLng(42.877742,-97.380979); // center of USA
    
    // MAP TYPE
    if(jQuery('#wp3d-map').attr("data-map-type")){
        var wp3dmaptype = jQuery('#wp3d-map').attr("data-map-type");
    } else {
        var wp3dmaptype = "ROADMAP"; // default maptype            
    }    

	// This function picks up the click and opens the corresponding info window
	function myclick(i) {
		google.maps.event.trigger(gmarkers[i], "click");
		//console.log(i);
	}    
    
    // http://www.mapstylr.com/map-style-editor/
    // var styles = [{"featureType":"landscape","elementType":"all","stylers":[{"visibility":"off"},{"color":"#cccccc"}]},{"featureType":"landscape","elementType":"all","stylers":[{"visibility":"off"},{"color":"#cccccc"}]},{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape.natural","elementType":"all","stylers":[{"visibility":"on"},{"color":"#dddddd"}]},{"featureType":"landscape.man_made","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative.province","elementType":"all","stylers":[{"visibility":"on"},{"gamma":1.24}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"weight":1.01}]},{"featureType":"administrative.country","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"}]},{"featureType":"administrative.country","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"saturation":-96},{"lightness":-76},{"gamma":4.84}]},{"featureType":"administrative.country","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"weight":1}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"saturation":0},{"lightness":0},{"gamma":5}]},{"featureType":"administrative.locality","elementType":"all","stylers":[{"visibility":"on"},{"gamma":5.04}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"color":"#666666"},{"lightness":17},{"gamma":1.24}]},{"featureType":"administrative.country","elementType":"geometry","stylers":[{"visibility":"on"},{"color":"#aaaaaa"}]},{"featureType":"administrative.province","elementType":"geometry","stylers":[{"visibility":"on"},{"color":"#aaaaaa"},{"gamma":1.3}]},{"featureType":"administrative.province","elementType":"labels.text","stylers":[{"visibility":"on"},{"weight":1.54},{"gamma":1.84}]},{"featureType":"administrative.locality","elementType":"labels.icon","stylers":[{"visibility":"on"},{"weight":0.64},{"lightness":21},{"gamma":4.53}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":35},{"gamma":4.51}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"visibility":"on"},{"color":"#000000"},{"hue":"#000000"},{"lightness":30},{"gamma":5.17}]},{"featureType":"administrative.neighborhood","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"on"},{"saturation":-100},{"lightness":11},{"gamma":2.36}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"},{"weight":1.37},{"lightness":21}]},{"featureType":"road.local","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":84}]},{"featureType":"road.local","elementType":"labels.text.stroke","stylers":[{"color":"#000000"},{"lightness":76}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#000000"},{"lightness":54}]}];

    var dragtoggle;
    if (viewport.width > 768) { dragtoggle = true; } else { dragtoggle = false; };
    
    // Map options for how to display the Google map
    var mapOptions = { 
      draggable: dragtoggle,
      zoom:4,
      center: myLatlng,
      disableDefaultUI: false,
      streetViewControl: false,
      mapTypeControl: false, 
      scrollwheel:false,
      mapTypeId: google.maps.MapTypeId[wp3dmaptype],  
      //styles: styles
    };
    
    //console.log(mapOptions);

    map = new google.maps.Map(document.getElementById("wp3d-map"), mapOptions);
    
    bounds = new google.maps.LatLngBounds();
        

// ************ LOOP THROUGH AND PLACE MARKERS ************* //
    
        for(var i = 0; i < locations.length; i++) 
        {
            
            // specific location lat/long
            var latlng = new google.maps.LatLng( locations[ i ].lat, locations[ i ].lon );
            
            var description = "<div class=\"model-infowindow\">" +
                "<h4>"+ wp3d_urldecode(locations[ i ].title) + "</h4>" +
                "<img src=\""+ locations[ i ].thumb +"\">" +
                "<a href=\"" + locations[ i ].slug + "\" class=\"infowindow-btn\">" + viewterm + "</a>" + 
                "</div>";
                
            // PIN/MARKER TYPE
            if(locations[ i ].marker == 'stock'){
                // STOCK GOOGLE MARKER (w/o MarkerWithLabel library, included on line 2)
                var marker = new google.maps.Marker( { 
                    position: latlng,     
                    map: map,      
                    title: locations[i].place,
                    id: i,
                    html : description,
                    // Custom property to hold the filter option, it'a used below to filter the markers
                    filter: {
                        type: locations[i].types
                    }                    
                });                
            } else {
                // CSS-BASED MARKER w/CUSTOM COLOR
                var marker = new MarkerWithLabel( { 
                    position: latlng,     
                    map: map,
                    title: locations[i].place,
                    id: i,
                    html : description,
                    // Custom property to hold the filter option, it'a used below to filter the markers
                    filter: {
                        type: locations[i].types
                    },                      
                    icon: {
                      path: google.maps.SymbolPath.CIRCLE,
                      scale: 0, //tamaño 0
                    },
                    labelAnchor: new google.maps.Point(10, 10),
                    labelClass: "wp3d-pin", // the CSS class for the label                
                });
            }                    
                    
            // var marker = new google.maps.Marker( { 
            //     position: latlng,     
            //     map: map,      
            //     title: locations[i].place,
            //     //icon: defaultimage,
            //     id: i,
            //     html : description,
            //     // Custom property to hold the filter option, it'a used below to filter the markers
            //     filter: {
            //         type: locations[i].types
            //     }
            // });
            
// ************ ADD INFOWINDOW ************* //
            
            // Create default InfoWindow
            var infowindow = new google.maps.InfoWindow( { content: "Loading..." } );  
            
            // Click Function for default InfoWindow 
            google.maps.event.addListener(marker, "click", function () 
            {
                infowindow.setContent(this.html);
                infowindow.open(map, this);
                map.setOptions({draggable:true}); // this really only applies to mobile (desktop always has draggable:true)
            });             
                
			// save the info we need to use later for the model click
			gmarkers.push(marker);
			
            bounds.extend(latlng);
            map.fitBounds(bounds);
            
        }  // end the for
    
// ************ RESIZE LISTENER ************* //
        
        google.maps.event.addDomListener(window, "resize", function() {
            var center = map.getCenter(); // get the center value first
            google.maps.event.trigger(map, "resize"); // resize the map
            //map.setCenter(center);
            map.panTo(center); // animate to the center
        });      
    
// ************ MAP CLICK OPTIONS ************* //        
        
       google.maps.event.addListener(map, 'click', function(event){
          infowindow.close(); // close all infowindows
          this.setOptions({scrollwheel:true});
          this.setOptions({draggable:true}); // this really only applies to mobile (desktop always has draggable:true)
        }); 
        
// ************ MAP FILTER CLICK OPTIONS ************* //  

       // Filtering links click handler, it uses the filtering values (data-filterby and data-filtervalue)
        // to filter the markers based on the filter (custom) property set when the marker is created.
        
        jQuery('#filter-3d-models a').click(function(event){ 

            event.preventDefault();
            
            var bounds = new google.maps.LatLngBounds(); // reset the bounds
            infowindow.close(); // close all infowindows
            
            var $target = jQuery(event.target);
            var value = $target.data('filter');
            var lastposition = null;
            var currmarkers = 0;
            
            //console.log(gmarkers); 
            
            jQuery.each(gmarkers, function () {
                
                if(jQuery.inArray(value,this.filter.type) != -1){ // if the (filter) value is in the marker class array
                    
                    //console.log(jQuery(this.filter.type));
                    bounds.extend(this.position);
                    
                    if (this.map == null) {
                         this.setMap(map);
                     } 
                     
                     lastposition = this.position; // hold onto a marker's position, in case it is the only one
                     currmarkers++;
                    
                } else {
                    this.setMap(null);
                }                
                
            });
            
            // updated marker count
            //console.log('marker count: '+ currmarkers);
            
            if (currmarkers == 0) {
                map.setZoom(4);
                map.panTo(myLatlng); // animate to the US center
                
            } else if (currmarkers == 1) { // if there's only one marker, no need to zoom way in on it, per the "fitbounds" rules
                
                map.setZoom(8);
                map.panTo(lastposition); // animate to the US center
                
            } else {
                map.fitBounds(bounds);
            }
        });

        
// ************ MAP MOUSEOUT OPTIONS ************* //             
        
        google.maps.event.addListener(map, 'mouseout', function(event){
         this.setOptions({scrollwheel:false});  
        });
        
		jQuery('#wp3d-models .map-btn').click( function() { myclick(jQuery(this).attr("data-id")); });
		jQuery('#wp3d-models .map-btn').click( function() { jQuery('html, body').animate({scrollTop:jQuery('#wp3d-map').offset().top -70}, 200); });

}); // end document ready