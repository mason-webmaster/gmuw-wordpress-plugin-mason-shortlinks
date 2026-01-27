/* custom scripts for site */


jQuery(document).ready(function(){

	// Debug
	//console.log('custom js file loaded');

 	//load QR codes

	//loop through the QR code divs
	jQuery.each(jQuery('.gmuw-sl-admin-list-qr-code'), function(index, value) {

		//get QR code-related elements for this record
		$my_qr_code_element=jQuery(this).children('.gmuw-sl-qr-code-output')[0];
		$my_qr_code_value=jQuery(this).children('.gmuw-sl-qr-code-value').val();

		//set QR code options
		var qrcode_options = {
		    text: $my_qr_code_value,
		    width: 600,
		    height: 600,
		    colorDark: "#000000",
		    colorLight: "#ffffff",
		    correctLevel: QRCode.CorrectLevel.H, // "H" is required for logos (High error correction)
		    
		    // --- LOGO SETTINGS ---
		    logo: "/wp-content/plugins/gmuw-wordpress-plugin-mason-shortlinks/images/mason-logo-qrcode.svg",
		    logoWidth: 198, 
		    logoHeight: 198,
		    logoBackgroundTransparent: false,
		    logoBackgroundColor: '#ffffff' 
		};

	    // Create the QR Code
	    new QRCode($my_qr_code_element, qrcode_options);

	});

});
