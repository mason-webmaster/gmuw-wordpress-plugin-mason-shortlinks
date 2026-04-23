/* custom scripts for site */


jQuery(document).ready(function(){

	// Debug
	//console.log('custom js file loaded');

 	//load QR codes

	//loop through the QR code divs
	jQuery.each(jQuery('.gmuw-sl-admin-list-qr-code'), function(index, value) {

		// Get elements
		const $container = jQuery(this).find('.gmuw-sl-qr-code-output')[0];
		const qrValue = jQuery(this).find('.gmuw-sl-qr-code-value').val();
		const $downloadBtn = jQuery(this).find('.gmuw-sl-qr-code-download');

		//get the custom filename from the data attribute
		//fallback to 'qr-code' if the attribute is missing
		const qrCodeFilename = jQuery(this).data('filename') || 'go-gmu-edu-qr-code';

		// Create QR code instance
		const qr = new QRCodeStyling({
			type: "svg",
			data: qrValue,
			qrOptions: {
				errorCorrectionLevel: "H"
			},
			dotsOptions: {
				color: "#000000",
				type: "square"
			},
			backgroundOptions: {
				color: "#ffffff"
			},
			image: "/wp-content/plugins/gmuw-wordpress-plugin-mason-shortlinks/images/mason-logo-qrcode.svg",
			imageOptions: {
				crossOrigin: "anonymous",
				margin: 0,
				imageSize: 0.5
			}
		});

		// Render into the container
		qr.append($container);

		// Download SVG handler
		$downloadBtn.on("click", function () {
			qr.download({
				name: qrCodeFilename,
				extension: "svg"
			});
		});

	});

});
