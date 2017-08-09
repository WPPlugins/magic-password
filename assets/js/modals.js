(
	function ( $ ) {
		var qrCodeForm                = $( '#qr-code-form' ),
		    openTipsModal             = $( '.js-open-tips' ),
		    closeModalButton          = $( '.js-close-modal' ),
		    scanningTipsModal         = $( '.scanning-tips-modal' ),
		    DeleteConfigButton        = $( '.js-delete-config' ),
		    DeleteConfigModal         = $( '.delete-modal' ),
		    successModalBtn           = $( '.js-success-modal-continue' ),
		    subscriptionErrorModalBtn = $( '.js-subscription-error-modal-ok' ),
		    modals                    = $( '.mf-modal-backdrop' ),
		    isModalOpened             = false;

		modals.on( 'mf-modal-open', function ( e ) {
			$( this ).addClass( 'mf-modal-show' ).animate( {opacity: 1}, 500 );
			isModalOpened = true;
		} );

		modals.on( 'mf-modal-close', function ( e ) {
			$( this ).animate( {opacity: 0}, 250, function () {
				$( this ).removeClass( 'mf-modal-show' );
				isModalOpened = false;
			} );
		} );

		// open modals
		openTipsModal.click( function ( event ) {
			event.preventDefault();
			scanningTipsModal.trigger( 'mf-modal-open' );
		} );

		// close modals with backdrop click
		$( document ).mouseup( function ( e ) {
			if ( isModalOpened ) {
				var container = $( '.mf-modal' );

				if ( ! container.is( e.target ) && container.has( e.target ).length === 0 ) {
					container.trigger( 'mf-modal-close' );
				}
			}
		} );

		DeleteConfigButton.click( function ( event ) {
			event.preventDefault();
			DeleteConfigModal.trigger( 'mf-modal-open' );
		} );

		// success modal button
		successModalBtn.click( function ( event ) {
			event.preventDefault();
			qrCodeForm.submit();
			$( this ).trigger( 'mf-modal-close' );
		} );

		// close modals
		closeModalButton.click( findModalParentAndClose );
		subscriptionErrorModalBtn.click( findModalParentAndClose );

		function findModalParentAndClose( event ) {
			event.preventDefault();
			$( this ).trigger( 'mf-modal-close' );
		}
	}
)( jQuery );
