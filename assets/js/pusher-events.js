(
	function ( $ ) {
		var sessionId              = $( '#session-id' ),
		    integrationId          = $( '#integration-id' ),
		    integrationUserId      = $( '#integration-user-id' ),
		    totpCode               = $( '#totp-code' ),
		    channelNameInput       = $( '#channel-name' ),
		    statusId               = $( '#status-id' ),
		    loginForm              = $( '#loginform' ),
		    modal                  = $( '.mf-modal' ),
		    nonce                  = $( '#_wpnonce' ),
		    authIdInput            = $( '#auth-id' ),
		    totpSecret             = $( '#totp-secret' ),
		    successModal           = $( '.success-modal' ),
		    subscriptionErrorModal = $( '.subscription-error-modal' ),
		    pairErrorModal         = $( '.pair-error-modal' );

		function subscribeChannel( channelName ) {
			var channel = pusher.subscribe( channelName );

			channel.bind( 'login-request', function ( data ) {
				$( '.qr-code-wrapper img, .qr-code-wrapper .sk-circle' ).addClass( 'loading' );

				channelNameInput.val( channelName );
				statusId.val( data.statusId );
				integrationUserId.val( data.integrationUserId );
				totpCode.val( data.totpToken );

				loginForm.submit();
			} );

			channel.bind( 'configuration-request', function ( data ) {
				channelNameInput.val( channelName );
				statusId.val( data.statusId );
				totpCode.val( data.totpToken );

				var pairData = {
					auth_id: authIdInput.val(),
					totp_secret: totpSecret.val(),
					totp_code: data.totpToken,
					channel_name: channelName,
					status_id: data.statusId,
					_wpnonce: nonce.val()
				};

				$.ajax( {
					type: 'POST',
					url: mpwd.pairEndpoint,
					data: pairData,
					success: function () {
						successModal.trigger( 'mf-modal-open' );
					},
					error: function () {
						pairErrorModal.trigger( 'mf-modal-open' );
					}
				} );
			} );

			channel.bind( 'pusher:subscription_error', function () {
				if ( retryCount < retryCountLimit ) {
					retryCount ++;
					subscribeChannel( channelName );
				} else {
					subscriptionErrorModal.trigger( 'mf-modal-open' );
				}
			} );
		}

		if ( sessionId.length ) {
			var pusherKey = mpwd.pusherKey;

			var pusher = new Pusher( pusherKey, {
				encrypted: true,
				authEndpoint: mpwd.authenticateEndpoint,
				auth: {
					headers: {
						'Session-Id': sessionId.val()
					}
				}
			} );

			var retryCount = 0;
			var retryCountLimit = 2;
			var channelName = 'private-wp_' + integrationId.val() + '_' + sessionId.val();

			subscribeChannel( channelName );
		}
	}
)( jQuery );
