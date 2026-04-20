/* global crpToolsPage, ajaxurl */

jQuery( document ).ready( function ( $ ) {

	// Migrate Post Meta.
	( function () {
		var lastId        = 0;
		var limit         = crpToolsPage.batchSize;
		var totalMigrated = 0;

		$( '#crp_migrate_meta' ).on( 'click', function () {
			$( this ).prop( 'disabled', true );
			$( '#crp-migration-progress' ).show();
			migrateBatch();
		} );

		function migrateBatch() {
			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'crp_migrate_meta',
					security: $( '#crp_migrate_meta_nonce' ).val(),
					last_id: lastId,
					limit: limit,
				},
				success: function ( response ) {
					if ( response.success ) {
						totalMigrated += response.data.migrated;
						$( '#crp-migration-status' ).text( response.data.message );
						if ( response.data.last_id !== undefined ) {
							lastId = response.data.last_id;
						}
						if ( response.data.complete ) {
							$( '#crp-migration-bar' ).css( 'width', '100%' );
							$( '#crp_migrate_meta' ).text( crpToolsPage.strings.migrationComplete ).prop( 'disabled', true );
							setTimeout( function () {
								location.reload();
							}, 2000 );
						} else {
							var progress = Math.min( ( totalMigrated / ( totalMigrated + response.data.remaining ) ) * 100, 100 );
							$( '#crp-migration-bar' ).css( 'width', progress + '%' );
							migrateBatch();
						}
					} else {
						$( '#crp-migration-status' ).text( crpToolsPage.strings.migrationFailed );
						$( '#crp_migrate_meta' ).prop( 'disabled', false );
					}
				},
				error: function () {
					$( '#crp-migration-status' ).text( crpToolsPage.strings.migrationFailed );
					$( '#crp_migrate_meta' ).prop( 'disabled', false );
				},
			} );
		}
	}() );

	// Undo Migration.
	( function () {
		var undoLastId  = 0;
		var undoLimit   = crpToolsPage.batchSize;
		var totalUndone = 0;

		$( '#crp_undo_migration' ).on( 'click', function () {
			if ( ! window.confirm( crpToolsPage.strings.confirmUndo ) ) {
				return;
			}
			$( this ).prop( 'disabled', true );
			$( '#crp-undo-progress' ).show();
			undoBatch();
		} );

		function undoBatch() {
			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'crp_undo_migrate_meta',
					security: $( '#crp_undo_migrate_meta_nonce' ).val(),
					last_id: undoLastId,
					limit: undoLimit,
				},
				success: function ( response ) {
					if ( response.success ) {
						totalUndone += response.data.undone;
						$( '#crp-undo-status' ).text( response.data.message );
						if ( response.data.complete ) {
							$( '#crp-undo-bar' ).css( 'width', '100%' );
							$( '#crp_undo_migration' ).text( crpToolsPage.strings.undoComplete ).prop( 'disabled', true );
							setTimeout( function () {
								location.reload();
							}, 2000 );
						} else {
							var progress = Math.min( ( totalUndone / ( totalUndone + response.data.remaining ) ) * 100, 100 );
							$( '#crp-undo-bar' ).css( 'width', progress + '%' );
							undoBatch();
						}
					} else {
						$( '#crp-undo-status' ).text( crpToolsPage.strings.undoFailed );
						$( '#crp_undo_migration' ).prop( 'disabled', false );
					}
				},
				error: function () {
					$( '#crp-undo-status' ).text( crpToolsPage.strings.undoFailed );
					$( '#crp_undo_migration' ).prop( 'disabled', false );
				},
			} );
		}
	}() );
} );
