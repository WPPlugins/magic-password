(
	function ( $ ) {
		var loginWithMPButton = $( '.login-button' ),
		    normalLogin       = $( '.normal-login' ),
		    modal             = $( '.mf-modal' ),
		    loginCookie       = $( '#login-cookie' );

		function setQRView() {
			$( '#login' ).addClass( 'with-qr' );
		}

		normalLogin.click( function () {
			$( '#login' ).removeClass( 'with-qr' );
		} );

		if ( '1' === loginCookie.val() ) {
			setQRView();
		}

		loginWithMPButton.click( function () {
			setQRView();
		} );
	}
)( jQuery );
