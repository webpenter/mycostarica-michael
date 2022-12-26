if(!jQuery().qtip) { // if another plugin (Yoast SEO) hasn't already called qTip, go get 'er
	var tipurl = "https://cdnjs.cloudflare.com/ajax/libs/qtip2/3.0.2/jquery.qtip.min.js";
	jQuery.getScript(tipurl);
	//console.log('Got Qtip')
}

// Needed to fix WP3D Models Tools Menu (on the back end)

function promptJSShow(label, id, plink, title) {
	prompt(label, '<div id="wp3d-'+id+'"><a href="'+plink+'">LOADING - '+title+'</a><script src="//wp3d-models.s3.us-east-2.amazonaws.com/js/embed-iframe.js?id=wp3d-'+id+'"></script></div>');
}

function promptIFShow(label, plink) {
	prompt(label, '<iframe width="853" height="480" src="'+plink+'" frameborder="0" allow="vr" allowfullscreen="allowfullscreen"></iframe>');
}

function promptSCShow(label, id) {
	prompt(label, '[wp3d-model id="'+id+'"]');
}

jQuery(document).ready(function () {
	jQuery(document).ready(function($) {
		$("#wp3d-tools-tabs .hidden").removeClass('hidden');
		$("#wp3d-tools-tabs").tabs();
	});
	
	const $ = jQuery;

	$('.wp3d_pro_modal .close').click(function(){
		$(this).closest('.wp3d_pro_modal').hide();
		if ($(this).hasClass('back')) {
			history.go(-1);
		}
	});
	
	$('a.page-title-action[href$="?post_type=model"]').click(function(e){
		if ($('.wp3d_pro_modal').length) {
			e.preventDefault();
			$('.wp3d_pro_modal').show();	
		}
	});
	
	$(document).on('click', '#wp3d_free_notice .notice-dismiss', function(){
		console.log('disable');
		$.post(ajaxurl, {
			action: 'wp3d_disable_notice',
		});
	});
	
	$('a.acf-tab-button').click(function(){
		console.log($(this).attr('data-key'))
	});
	
	$('a[data-key="field_55fb9cbe295d2"], #WP3D_Models_settings .nav-tab-wrapper a:nth-child(2)').click(function(e){
		e.preventDefault()
		$('.wp3d_pro_modal').show()
		setTimeout(function(){
			$('a[data-key="field_5504b8036d565"]').click()
		}, 500)
	});

	// Dan's request for some feedback re: total twitter count.  This enhances Yoast's SEO plugin...not WP3D Models, but it's a nice polish and isn't hurting anyone/anything to add it here.
	jQuery('#yoast_wpseo_twitter-description').keyup(function () {
		jQuery('#yoast_twitter_count').remove();
		var max = 140;
		var len = jQuery(this).val().length;
		if (len >= max) {
			jQuery(this).after(function() {
				return '<div id="yoast_twitter_count" style="color: #CC0000;">You have reached/exceeded the 140 character limit</div>';
			});
			//console.log('You have reached the limit.');
		} else {
			var char = max - len;
			//jQuery('#charNum').text(char + ' characters left');
			jQuery(this).after(function() {
				return '<div id="yoast_twitter_count" style="color: #00B200;">'+char + ' Characters Left</div>';
			});
			//console.log(char + ' Characters Left');
		}
	});

	if(jQuery().qtip) { // tests to see if the function is available or not...sheesh
		//console.log('Qtip enabled');
		jQuery( '.wp3d-help' ).qtip(
			{
				content: {
					attr: 'alt'
				},
				position: {
					my: 'bottom left',
					at: 'top center'
				},
				style: {
					tip: {
						corner: true
					},
					classes: 'wp3d-qtip'
					//classes: 'wp3d-qtip qtip-rounded qtip-blue'
				},
				show: 'click',
				hide: {
					fixed: true,
					delay: 500
				}

			}

		);

		jQuery('.wp3d-help-text').click( function() {

			jQuery(this).prev('.wp3d-help').click();

		});

	}

// settings image, if empty -> HIDE!	
	var previewImgSrc = jQuery('img.image_preview').attr('src');
	if (previewImgSrc == '') { jQuery('img.image_preview').css('width','auto'); }

	if (jQuery('.wp3d_post_link').length) {
		jQuery('.wp3d_post_link').each(function() {
			var link = jQuery(this);
			jQuery.post(ajaxurl, {
				action: 'wp3d_get_permalink',
				id: link.data('post_id')
			}, function(permalink){
				var href = link.attr('href').replace('wp3d_post_url', permalink);
				link.attr('href', href);
			});
		});
	}

	function matterport_zip_link_toggle() {
		if (jQuery('#acf-field_587557a304734').val() === 'matterport') {
			jQuery('.matterport_zip').show();
		} else {
			jQuery('.matterport_zip').hide();
		}
	}

	console.log(jQuery('.matterport_zip').length)
	console.log(jQuery('#acf-field_587557a304734').length)

	if (jQuery('.matterport_zip').length && jQuery('#acf-field_587557a304734').length) {
		matterport_zip_link_toggle();
		jQuery('#acf-field_587557a304734').change(function(){
			matterport_zip_link_toggle()
		});
	}

});

jQuery(document).on('acf/validate_field', function( e, field ){

	// vars
	var $field;
	$field = jQuery(field);

	//console.log($field);

	// checking for the MP model link field...running custom validation
	if ($field.get(0).id == 'acf-model_link') {

		//DEBUG
		console.log($field.find('#acf-field-model_link').val());

		// set validation to false on this field
		if( $field.find('#acf-field-model_link').val().indexOf("https://my.matterport") == '-1' &&
			$field.find('#acf-field-model_link').val().indexOf("https://mpembed.com") == '-1') // if this never occurs, FAIL
		{
			$field.data('validation', false);
			acf.l10n.validation.error = "You must enter a valid Matterport URL matching one of the following formats: <br><b>https://my.matterport.com/show/?m=XXXXXXXXXX</b><br><b>https://my.matterportvr.cn/show/?m=XXXXXXXXXX</b>"; // Replace the validation message here
		}

	}

	// checking for the TST model base field...running custom validation
	if ($field.get(0).id == 'acf-tst_link') {

		// set validation to false on this field
		if( $field.find('#acf-field-tst_link').val().indexOf("https://my.threesixty.tours/") == '-1' ) // if this never occurs, FAIL
		{
			$field.data('validation', false);
			acf.l10n.validation.error = "You must enter a ThreeSixty Tours URL: <b>https://my.threesixty.tours/app/v/XXXXXX/XXXXXX/[XXXXXX]</b>"; // Replace the validation message here
		}

	}

	// checking for the YOUTUBE model base fields...running custom validation
	if ($field.get(0).id == 'acf-base_youtube_video_link') {

		// set validation to false on this field
		if( $field.find('#acf-field-base_youtube_video_link').val().indexOf("https://www.youtube.com/") == '-1' ) // if this never occurs, FAIL
		{
			$field.data('validation', false);
			acf.l10n.validation.error = "You must enter a full YOUTUBE URL: <b>https://www.youtube.com/watch?v=XXXXXXXXXXX</b>"; // Replace the validation message here
		}

	}

	// checking for the VIMEO model base fields...running custom validation
	if ($field.get(0).id == 'acf-base_vimeo_video_link') {

		// set validation to false on this field
		if( $field.find('#acf-field-base_vimeo_video_link').val().indexOf("https://vimeo.com/") == '-1' ) // if this never occurs, FAIL
		{
			$field.data('validation', false);
			acf.l10n.validation.error = "You must enter a full VIMEO URL: <b>https://vimeo.com/XXXXXXXXX</b>"; // Replace the validation message here
		}
	}
	
	
});
