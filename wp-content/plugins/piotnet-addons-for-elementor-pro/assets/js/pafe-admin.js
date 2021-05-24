jQuery(document).ready(function( $ ) {

	$('.pafe-wrap').closest('body').find('#footer-left').hide();

	$('[data-pafe-features-save]').click( function() {
	    $('[data-pafe-features]').submit();
	});

	$('input[name="piotnet-addons-for-elementor-pro-remove-license"]').click( function() {
	    if( $(this).is(':checked') ) {
	    	$('input[name="piotnet-addons-for-elementor-pro-username"]').val('').prop('disabled', true);
	    	$('input[name="piotnet-addons-for-elementor-pro-password"]').val('').prop('disabled', true);
	    	$('input[name="piotnet-addons-for-elementor-pro-license-key"]').val('').prop('disabled', true);
	    } else {
	    	$('input[name="piotnet-addons-for-elementor-pro-username"]').prop('disabled', false);
	    	$('input[name="piotnet-addons-for-elementor-pro-password"]').prop('disabled', false);
	    	$('input[name="piotnet-addons-for-elementor-pro-license-key"]').prop('disabled', false);
	    }
	});

	$('[data-pafe-toggle-features-enable]').click( function() {
	    $('.pafe-switch input').prop('checked', true);
	});

	$('[data-pafe-toggle-features-disable]').click( function() {
	    $('.pafe-switch input').prop('checked', false);
	});

	$('[data-pafe-dropdown-trigger]').click( function(e) {
	    e.preventDefault();
	    $(this).closest('[data-pafe-dropdown]').find('[data-pafe-dropdown-content]').toggle();
	}); 

	// Zoho API
	zohoCheckButton();
	function zohoCheckButton(){
		var clientId = jQuery('[name=piotnet-addons-for-elementor-pro-zoho-client-id]').val();
		var clientSecret = jQuery('[name=piotnet-addons-for-elementor-pro-zoho-client-secret]').val();
		if(clientId != '' && clientSecret != ''){
			jQuery('[authenticate-zoho-crm]').removeAttr('disabled');
		}else{
			jQuery('[authenticate-zoho-crm]').attr('disabled', 'disabled');
		}
	}
	jQuery('[name=piotnet-addons-for-elementor-pro-zoho-client-id], [name=piotnet-addons-for-elementor-pro-zoho-client-secret]').on('change', function(){
		zohoCheckButton();
	});
});  