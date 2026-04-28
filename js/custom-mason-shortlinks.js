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
		const $downloadBtnSVG = jQuery(this).find('.gmuw-sl-qr-code-download-svg');
		const $downloadBtnPNG = jQuery(this).find('.gmuw-sl-qr-code-download-png');
		const $transparentToggle = jQuery(this).find('.gmuw-sl-qr-code-transparent-toggle');

		//get the custom filename from the data attribute
		//fallback to 'qr-code' if the attribute is missing
		const qrCodeFilename = jQuery(this).data('filename') || 'go-gmu-edu-qr-code';

		// Create QR code instance
		const qr = new QRCodeStyling({
			type: "canvas",
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

		//update the qr code visual preview when the user clicks the checkbox
		$transparentToggle.on("change", function() {
		    const isTransparent = jQuery(this).is(':checked');

		    qr.update({
		        backgroundOptions: {
		            color: isTransparent ? "transparent" : "#ffffff"
		        }
		    });

		    //toggle a CSS class for that checkerboard preview
		    if (isTransparent) {
		        jQuery($container).addClass('qr-preview-transparent');
		    } else {
		        jQuery($container).removeClass('qr-preview-transparent');
		    }
		});

		// Download SVG handler
		$downloadBtnSVG.on("click", function () {

			qr.download({
				name: qrCodeFilename,
				extension: "svg"
			});
		});

		// Download PNG handler
		$downloadBtnPNG.on("click", function () {

			qr.download({
				name: qrCodeFilename,
				extension: "png"
			});
		});

	});

});
