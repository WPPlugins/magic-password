(
	function ( $ ) {
		var modal              = $( '.mf-modal' ),
		    showQRButton       = $( '.js-show-qr' ),
		    QRPlaceholderImage = $( '.qr-code-placeholder' ),
		    QRHided            = $( '.qr-code-hide' ),
		    saveOptionButton   = $( '.js-show-on-change' ),
		    optionsRadio       = $( 'input[name="mf-only"]' );

		showQRButton.click( function ( event ) {
			event.preventDefault();
			QRPlaceholderImage.hide();
			QRHided.show();
			$( this ).text( 'Magic Code Ready' ).prop( 'disabled', true );
		} );

		optionsRadio.on( 'change', function () {
			saveOptionButton.show();
		} );
	}
)( jQuery );
