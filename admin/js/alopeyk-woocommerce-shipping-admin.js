(function( $j ) {
	
	'use strict';

	var alopeyk = alopeyk || { wcshm : {} };
	alopeyk.wcshm.admin = {};



	/*==========================================
	=            Defining Variables            =
	==========================================*/

	alopeyk.wcshm.admin.vars = {

		common : {

			info                         : window.awcshm,
			time                         : Date.now(),
			activeClass                  : 'active',
			loadingClass                 : 'loading',
			disabledClass                : 'disabled',
			alopeykPrefix                : 'awcshm-',
			checkboxToggleIdDataAttr     : 'checkbox-toggle-id',
			checkboxToggleTargetDataAttr : 'checkbox-toggle-target',
			knownImageExtensions         : [ 'jpg', 'jpeg', 'gif', 'tiff', 'png', 'apng', 'bmp', 'svg' ],
			modalContentClass            : 'modal-content'

		},

		maps : {
			
			selector                             : '.map-canvas',
			storeLatInputName                    : 'store_lat',
			storeLngInputName                    : 'store_lng',
			storeCityInputName                   : 'store_city',
			storeAddressInputName                : 'store_address',
			storeLocatorMapClass                 : 'map-canvas store-locator-map',
			storeLocatorInputWrapperClass        : 'store-locator-input-wrapper',
			storeLocatorAutocompleteResultsClass : 'store-locator-autocomplete-results',
			storeLocatorAutocompleteResultClass  : 'store-locator-autocomplete-result',
			storeLocatorHiddenableInput          : '.hide-parent-row',
			autocompletePlaceholderDataAttr      : 'autocomplete-placeholder',
			autocompleteKeyupTimeout             : null,
			positionKeyupTimeout                 : null,
			autoCompleteKeyupDelay               : 500,
			positionKeyupDelay                   : 500,
			defaultZoom                          : 15,
			defaultCenter                        : {
				lat : 35.732466,
				lng : 51.413811
			}

		},

		cost : {

			costTypeInputName           : 'cost_type',
			staticCostTypeInputName     : 'static_cost_type',
			fixedCostInputName          : 'static_cost_fixed',
			percentageCostInputName     : 'static_cost_percentage',
			costTypeDynamicVal          : 'dynamic',
			costTypeStaticVal           : 'static',
			staticCostTypeFixedVal      : 'fixed',
			staticCostTypePercentageVal : 'percentage',

		},

		upload : {

			inputSelector         : '.input-upload',
			assetsContainerClass  : 'upload-assets-container',
			previewContainerClass : 'preview-container',
			previewTypePrefix     : 'preview-type-',
			uploadButtonClass     : 'button-primary upload-button',
			removeButtonClass     : 'button button-small remove-button',
			filenameLabelClass    : 'filename-label',
			uploadLabelDataAttr   : 'upload-label',
			removeLabelDataAttr   : 'remove-label',
			uploadOptionsDataAttr : 'upload-options',
			isEmptyClass          : 'is-empty',
			isFilledClass         : 'is-filled',

		},

		prompts : {

			headingElement       : 'h2',
			headingContainer     : '#mainform',
			belowHeadingElements : '.below-heading',

		},

		bulkAction : {

			actionValue     : 'alopeyk_cumulative_shipping',
			formElement     : '.post-type-shop_order #posts-filter',
			submitElement   : '#doaction, #doaction2',
			actionInputs    : '[name="post[]"]:checked',
			dropdownElement : '#bulk-action-selector-bottom, #bulk-action-selector-top',

		},

		forms : {

			priceInputsClass           : 'price-input',
			dateDropdownElement        : '[name="ship_date"]',
			hourDropdownElement        : '[name="ship_hour"]',
			minuteDropdownElement      : '[name="ship_minute"]',
			shipNowTogglerElement      : '[name="ship_now"]',
			createOrderFormClass       : 'create-order-form',
			creditButtonElementClass   : 'amount-button',
			creditButtonAmountDataAttr : 'credit-amount',
			creditButtonTargetDataAttr : 'credit-target',
			addCouponFormClass         : 'add-coupon-form',
			cancelOrderFormClass       : 'cancel-order-form',
			rateOrderFormClass         : 'rate-order-form',

		},

		modals : {

			creditModalTogglerClass       : 'credit-modal-toggler',
			creditModalAmountDataAttr     : 'credit-amount',
			orderModalTogglerClass        : 'order-modal-toggler',
			orderModalTypeDataAttr        : 'order-types',
			orderModalOrdersDataAttr      : 'order-ids',
			orderModalDescriptionDataAttr : 'order-description',
			orderModalOrdersDelimiter     : ',',
			couponModalTogglerClass       : 'coupon-modal-toggler',
			cancelModalTogglerClass       : 'cancel-modal-toggler',
			cancelModalOrderDataAttr      : 'order-id',
			rateModalTogglerClass         : 'rate-modal-toggler',
			rateModalOrderDataAttr        : 'order-id',

		},

		chat : {

			togglerInput : '#awcshm-support-chat-toggler'

		}

	};



	/*==================================
	=            Prototypes            =
	==================================*/




	/*==========================================
	=            Defining Functions            =
	==========================================*/

	alopeyk.wcshm.admin.fn = {

		addPrefix : function ( classes ) {
			
			var prefixedClasses = [],
				classesArray    = classes.split ( ' ' );

			for ( var i = 0; i < classesArray.length; i++ ) {
				prefixedClasses.push ( alopeyk.wcshm.admin.vars.common.alopeykPrefix + classesArray[ i ] )
			}

			return prefixedClasses.join ( ' ' );

		},

		translate : function ( term ) {

			var translation = alopeyk.wcshm.admin.vars.common.info.translations[ term ];
			return translation ? translation : term;

		},

		getUrlVars : function ( url ) {

			url = url ? url : window.location.href;

			var hash,
				vars  = [],
				index = url.indexOf ( '?' ),
				query = index != -1 ? decodeURIComponent ( url ).slice ( index + 1 ) : null,
				vars  = query ? query.split ( '&' ) : [],
				query_string = [];

			for ( var i = 0; i < vars.length; i++ ) {

				var pair = vars[ i ].split ( '=' );

				pair[ 0 ] = pair[ 0 ].replace ( '[]', '' );

				if ( typeof pair[ 1 ] === 'undefined' ) {

					pair[ 1 ] = '';

				}

				if ( typeof query_string[ pair[ 0 ] ] === 'undefined' ) {

					query_string[ pair[ 0 ] ] = pair[ 1 ];

				} else if ( typeof query_string[ pair[ 0 ] ] === 'string' ) {

					var arr = [ query_string[ pair[ 0 ] ], pair[ 1 ] ];
					query_string[ pair[ 0 ] ] = arr;

				} else {

					query_string[ pair[ 0 ] ].push ( pair[ 1 ] );

				}

			}

			return ( typeof query_string === 'object' ? query_string : null );

		},

		openTab : function ( url ) {

			var form   = $j( '<form>' ),
				params = alopeyk.wcshm.admin.fn.getUrlVars ( url );

			if ( params && Object.keys ( params ).length ) {

				for ( var param in params ) {

					if ( ! params.hasOwnProperty ( param ) ) continue;

					form.append ( $j( '<input>' ).attr ({

						type  : 'hidden',
						name  : param,
						value : params[ param ]

					}));

				}

			}

			form.attr ({

				'action' : url,
				'method' : 'GET',
				'target' : '_blank',

			})
			.appendTo ( 'body' )
			.trigger ( 'submit' )
			.remove();

		},

		decodeToHtml : function ( string ) {

			var txt = document.createElement ( 'textarea' );
			txt.innerHTML = string;
			return txt.value;

		},

		injectScript : function ( src ) {

			if ( ! $j( '[src="' + src + '"]' ).length ) {

				var s, t;

				s       = document.createElement ( 'script' );
				s.type  = 'text/javascript';
				s.async = true;
				s.src   = src;
				t       = document.getElementsByTagName ( 'script' )[ 0 ];

				t.parentNode.insertBefore ( s, t );

			}

		},

		loadGoogleMaps : function () {

			window[ 'alopeykHandleMapsAdmin' ] = alopeyk.wcshm.admin.fn.handleMaps;

			if ( typeof alopeykHandleMapsAdmin === 'function' ) {

				if ( typeof window.google != 'undefined' && window.google.maps ) {

					window.googleMapIsLoading = false;
					alopeyk.wcshm.admin.fn.handleMaps();

				} else if ( window.googleMapIsLoading ) {

					alopeyk.wcshm.admin.vars.loadingMapInterval = setInterval ( function () {

						if ( window.google && window.google.maps ) {

							window.googleMapIsLoading = false;
							clearInterval ( alopeyk.wcshm.admin.vars.loadingMapInterval );
							alopeyk.wcshm.admin.fn.handleMaps();

						}

					}, 500 );

				} else {

					window.googleMapIsLoading = true;
					alopeyk.wcshm.admin.fn.injectScript('https://maps.googleapis.com/maps/api/js?key=' + alopeyk.wcshm.admin.vars.common.info.alopeyk.wcshm.map.api_key + '&language=fa&region=IR&callback=alopeykHandleMapsAdmin');

				}

			}

		},

		handleMaps : function () {

			$j( document ).trigger ( 'alopeyk:admin:map:loaded' );

		},

		initMaps : function () {

			alopeyk.wcshm.admin.fn.loadGoogleMaps();

			$j( document ).on ( 'alopeyk:admin:map:loaded', function () {

				var inputIdPrefix     = alopeyk.wcshm.admin.vars.common.info.alopeyk.wcshm.id + '_',
					storeLatInput     = $j( "[id$='" + inputIdPrefix + alopeyk.wcshm.admin.vars.maps.storeLatInputName + "']" ),
					storeLngInput     = $j( "[id$='" + inputIdPrefix + alopeyk.wcshm.admin.vars.maps.storeLngInputName + "']" ),
					storeCityInput    = $j( "[id$='" + inputIdPrefix + alopeyk.wcshm.admin.vars.maps.storeCityInputName + "']" ),
					storeAddressInput = $j( "[id$='" + inputIdPrefix + alopeyk.wcshm.admin.vars.maps.storeAddressInputName + "']" );

				if ( storeLatInput.length && storeLngInput.length && storeCityInput.length && storeAddressInput.length ) {
					alopeyk.wcshm.admin.fn.initStoreLocator( storeLatInput.first(), storeLngInput.first(), storeCityInput.first(), storeAddressInput.first() );
				}

			});

		},

		initStoreLocator : function ( storeLatInput, storeLngInput, storeCityInput, storeAddressInput ) {

			var mapCanvas = $j( '<div/>' ).attr ({

					id    : 'store-locator-map',
					class : alopeyk.wcshm.admin.vars.maps.storeLocatorMapClass,

				}),
				storeAutocompleteInput = storeAddressInput.clone().attr ({

					id             : storeAddressInput.attr ( 'id' ) + '_autocomplete',
					class          : storeAddressInput.attr ( 'class' ),
					placeholder    : storeAddressInput.data ( alopeyk.wcshm.admin.vars.maps.autocompletePlaceholderDataAttr ),
					name           : '',
					style          : '',
					value          : '',
					type           : 'text',
					spellcheck     : 'false',
					autocapitalize : 'off',
					autocorrect    : 'off',
					autocomplete   : 'off'

				}).removeClass ( alopeyk.wcshm.admin.vars.common.disabledClass ),
				storeAutocompleteInputWrapper = $j( '<div>' ).attr ({

					id    : 'store-locator-input-wrapper',
					class : alopeyk.wcshm.admin.vars.maps.storeLocatorInputWrapperClass,

				}),
				autoCompleteList = $j( '<ul>' ).attr ({

					id    : 'store-locator-autocomplete-results',
					class : alopeyk.wcshm.admin.vars.maps.storeLocatorAutocompleteResultsClass,

				});

			storeAutocompleteInputWrapper
			.insertAfter ( storeAddressInput )
			.append ( storeAutocompleteInput )
			.append ( autoCompleteList );

			mapCanvas.insertAfter( storeAutocompleteInputWrapper );

			var mapOptions = {

					zoom   : alopeyk.wcshm.admin.vars.maps.defaultZoom,
					center : {

						lat : storeLatInput.val().length ? parseFloat( storeLatInput.val() ) : alopeyk.wcshm.admin.vars.maps.defaultCenter.lat,
						lng : storeLngInput.val().length ? parseFloat( storeLngInput.val() ) : alopeyk.wcshm.admin.vars.maps.defaultCenter.lng,

					},
					mapTypeControl    : false,
					streetViewControl : false,
					fullscreenControl : false,

				},

				map    = new google.maps.Map ( mapCanvas.get ( 0 ), mapOptions ),

				marker = new google.maps.Marker ({

							map         : map,
							draggable   : true,
							position    : mapOptions.center,
							anchorPoint : new google.maps.Point ( 0, -29 ),

						}),

				setActiveAutocompleteItem = function ( index ) {

					var results     = autoCompleteList.children(),
						activeIndex = index == results.length ? 0 : ( index < 0 ? results.length - 1 : index ),
						activeClass = alopeyk.wcshm.admin.vars.common.activeClass;

					results.removeClass ( activeClass ).eq ( activeIndex ).addClass ( activeClass );

				},

				fetchAddressFromLocation = function () {

					if ( alopeyk.wcshm.admin.vars.maps.fetchAddressConnection ) {
						alopeyk.wcshm.admin.vars.maps.fetchAddressConnection.abort();
					}

					storeAddressInput.parents ( 'form' )
					.find ( 'button, input[type="button"], input[type="submit"]' )
					.filter ( function () {
						return ! $j( this ).is ( ':disabled' );
					})
					.prop ( 'disabled', true )
					.data ( 'alopeyk-disable', true );

					storeAutocompleteInputWrapper.addClass ( alopeyk.wcshm.admin.vars.common.loadingClass );
					alopeyk.wcshm.admin.vars.maps.fetchAddressConnection = $j.post ( alopeyk.wcshm.admin.vars.common.info.ajaxOptions.url, {

						nonce        : alopeyk.wcshm.admin.vars.common.info.ajaxOptions.nonce,
						action       : alopeyk.wcshm.admin.vars.common.info.alopeyk.wcshm.id,
						request      : 'get_address',
						authenticate : true,
						ask_google   : false,
						lat          : marker.getPosition().lat(),
						lng          : marker.getPosition().lng(),

					}, function ( response ) {

						if ( response ) {

							autoCompleteList.empty();
							storeCityInput.val ( response.success && response.data.city ? response.data.city : '' );
							storeAutocompleteInput.val ( response.data.address );
							storeAddressInput.val ( response.success ? response.data.address : '' );

							if ( response.success ) {

								storeAddressInput.parents ( 'form' )
								.find ( 'button, input[type="button"], input[type="submit"]' )
								.filter ( function () {
									return $j( this ).data ( 'alopeyk-disable' );
								})
								.prop ( 'disabled', false );

							} else {

								storeLatInput.val ( '' );
								storeLngInput.val ( '' );

							}

						}

					}).always ( function () {

						storeAutocompleteInputWrapper.removeClass ( alopeyk.wcshm.admin.vars.common.loadingClass );

					});

				};

			marker.addListener ( 'drag', function() {

				storeLatInput.val ( marker.getPosition().lat() );
				storeLngInput.val ( marker.getPosition().lng() );

			});

			marker.addListener ( 'dragend', function () {

				fetchAddressFromLocation();

			});

			$j.merge ( storeLatInput, storeLngInput ).on ( 'change paste keyup input propertychange', function () {

				if ( alopeyk.wcshm.admin.vars.maps.latitudeValue != storeLatInput.val() || alopeyk.wcshm.admin.vars.maps.longitudeValue != storeLngInput.val()  ) {

					alopeyk.wcshm.admin.vars.maps.latitudeValue = storeLatInput.val();
					alopeyk.wcshm.admin.vars.maps.longitudeValue = storeLngInput.val();

					if ( alopeyk.wcshm.admin.vars.maps.positionKeyupTimeout ) {
						clearTimeout(  alopeyk.wcshm.admin.vars.maps.positionKeyupTimeout );
					}

					alopeyk.wcshm.admin.vars.maps.positionKeyupTimeout = setTimeout ( function () {

						var location = { lat : parseFloat ( storeLatInput.val() ), lng : parseFloat ( storeLngInput.val() ) };
						map.setCenter ( location );
						marker.setPosition ( location );
						fetchAddressFromLocation();

					}, alopeyk.wcshm.admin.vars.maps.positionKeyupDelay );

				}

			});

			storeAutocompleteInput.on ({

				'keydown' : function ( e ) {

					var activeItem  = autoCompleteList.children ( '.' + alopeyk.wcshm.admin.vars.common.activeClass ),
						activeIndex = activeItem.length ? activeItem.index() : -1;

					switch ( e.which ) {

						case 13:
							e.preventDefault();
							if ( activeItem ) {
								activeItem.trigger ( 'click' );
							}
							break;
						case 27:
							storeAutocompleteInput.blur();
							break;
						case 38:
							e.preventDefault();
							setActiveAutocompleteItem( activeIndex - 1 );
							break;
						case 40:
							e.preventDefault();
							setActiveAutocompleteItem( activeIndex + 1 );
							break;

					}

				},

				'change paste keyup input propertychange' : function ( e ) {

					if ( alopeyk.wcshm.admin.vars.maps.autocompleteInputValue != storeAutocompleteInput.val() ) {

						autoCompleteList.empty();
						alopeyk.wcshm.admin.vars.maps.autocompleteInputValue = storeAutocompleteInput.val();

						if ( alopeyk.wcshm.admin.vars.maps.autocompleteKeyupTimeout ) {
							clearTimeout(  alopeyk.wcshm.admin.vars.maps.autocompleteKeyupTimeout );
						}

						alopeyk.wcshm.admin.vars.maps.autocompleteKeyupTimeout = setTimeout ( function () {

							if ( alopeyk.wcshm.admin.vars.maps.autocompleteConnection ) {
								alopeyk.wcshm.admin.vars.maps.autocompleteConnection.abort();
							}

							storeAutocompleteInputWrapper.addClass ( alopeyk.wcshm.admin.vars.common.loadingClass );
							alopeyk.wcshm.admin.vars.maps.autocompleteConnection = $j.post ( alopeyk.wcshm.admin.vars.common.info.ajaxOptions.url, {

								nonce        : alopeyk.wcshm.admin.vars.common.info.ajaxOptions.nonce,
								action       : alopeyk.wcshm.admin.vars.common.info.alopeyk.wcshm.id,
								request      : 'suggest_address',
								authenticate : true,
								ask_google   : false,
								input        : storeAutocompleteInput.val(),

							}, function ( response ) {

								if ( response && response.success && response.data.length ) {

									for ( var i = 0; i < response.data.length; i++ ) {

										var itemLat      = parseFloat( response.data[i].lat ),
											itemLng      = parseFloat( response.data[i].lng ),
											itemLocation = { lat : itemLat, lng : itemLng },
											itemAddress  = response.data[i].address,
											itemCity     = response.data[i].city,

											resultItem   = $j( '<li>' )
											.addClass( alopeyk.wcshm.admin.vars.maps.storeLocatorAutocompleteResultClass )
											.attr ( 'title', itemAddress )
											.data ({
												city     : itemCity,
												address  : itemAddress,
												location : itemLocation
											})
											.text ( itemAddress )
											.on ({

												'click' : function () {

													var city     = $j( this ).data ( 'city' ),
														address  = $j( this ).data ( 'address' ),
														location = $j( this ).data ( 'location' );

													storeAutocompleteInput.blur();
													storeAutocompleteInput.val ( address );
													storeAddressInput.val ( address );
													storeCityInput.val ( city );
													map.setCenter ( location );
													map.setZoom ( 17 );
													marker.setPosition ( location );
													marker.setVisible ( true );
													storeLatInput.val ( location.lat );
													storeLngInput.val ( location.lng );
													alopeyk.wcshm.admin.vars.maps.autocompleteInputValue = address;
													fetchAddressFromLocation();

												},

												'hover' : function () {

													setActiveAutocompleteItem ( $j( this ).index() );

												}

											});

										autoCompleteList.append ( resultItem );

									}

								}

							}).always ( function () {

								storeAutocompleteInputWrapper.removeClass ( alopeyk.wcshm.admin.vars.common.loadingClass );

							});

						}, alopeyk.wcshm.admin.vars.maps.autoCompleteKeyupDelay );

					}

				}

			});

			google.maps.event.addListenerOnce ( map, 'idle', function () {

				storeAutocompleteInputWrapper.css ({

					zIndex   : 2,
					position : 'absolute'

				});

			});

			google.maps.event.addDomListener ( window, 'resize', function () {

				if ( alopeyk.wcshm.admin.vars.maps.centerTimeout )
					clearTimeout ( alopeyk.wcshm.admin.vars.maps.centerTimeout );

				alopeyk.wcshm.admin.vars.maps.centerTimeout = setTimeout ( function () {

					map.setCenter ( marker.getPosition() );

				}, 100 );

			});

		},

		handleSettingFields : function () {

			var inputIdPrefix          = alopeyk.wcshm.admin.vars.common.info.alopeyk.wcshm.id + '_',
				costTypeInput          = $j( "[id$='" + inputIdPrefix + alopeyk.wcshm.admin.vars.cost.costTypeInputName + "']" ),
				staticCostTypeInput    = $j( "[id$='" + inputIdPrefix + alopeyk.wcshm.admin.vars.cost.staticCostTypeInputName + "']" ),
				fixedCostInput         = $j( "[id$='" + inputIdPrefix + alopeyk.wcshm.admin.vars.cost.fixedCostInputName + "']" ),
				percentageCostInput    = $j( "[id$='" + inputIdPrefix + alopeyk.wcshm.admin.vars.cost.percentageCostInputName + "']" ),
				checkboxTargetDataAttr = alopeyk.wcshm.admin.vars.common.checkboxToggleTargetDataAttr,
				checkboxIdDataAttr     = alopeyk.wcshm.admin.vars.common.checkboxToggleIdDataAttr,
				uploadInputs           = alopeyk.wcshm.admin.vars.upload.inputSelector,
				setCostInputVisibility = function () {

					if ( costTypeInput.val() == alopeyk.wcshm.admin.vars.cost.costTypeDynamicVal ) {

						staticCostTypeInput.parents ( 'tr' ).css ( 'display', 'none' );
						fixedCostInput.parents ( 'tr' ).css ( 'display', 'none' );
						percentageCostInput.parents ( 'tr' ).css ( 'display', 'none' );

					} else if ( costTypeInput.val() == alopeyk.wcshm.admin.vars.cost.costTypeStaticVal ) {

						staticCostTypeInput.parents ( 'tr' ).css ( 'display', '' );

						if ( staticCostTypeInput.val() == alopeyk.wcshm.admin.vars.cost.staticCostTypeFixedVal ) {

							fixedCostInput.parents ( 'tr' ).css ( 'display', '' );
							percentageCostInput.parents ( 'tr' ).css ( 'display', 'none' );

						} else {

							percentageCostInput.parents ( 'tr' ).css ( 'display', '' );
							fixedCostInput.parents ( 'tr' ).css ( 'display', 'none' );

						}

					}

				};

			$j.merge ( costTypeInput, staticCostTypeInput ).on ( 'change', setCostInputVisibility );
			setCostInputVisibility();

			$j( 'input:checkbox[data-' + checkboxTargetDataAttr + ']' ).on ( 'change', function () {

				var targetInput = $j( 'input:checkbox[data-' + checkboxIdDataAttr + '="' + $j( this ).data ( checkboxTargetDataAttr ) + '"]' ),
					childInput  = $j( 'input:checkbox[data-' + checkboxIdDataAttr + '="' + targetInput.data ( checkboxTargetDataAttr ) + '"]' );

				targetInput.parents ( 'tr' ).css ( 'display', $j( this ).prop ( 'checked' ) ? '' : 'none' );

				if ( childInput.length ) {
					childInput.parents ( 'tr' ).css ( 'display', $j( this ).prop ( 'checked' ) && targetInput.prop ( 'checked' ) ? '' : 'none' );
				}


			}).trigger ( 'change' );

			$j( alopeyk.wcshm.admin.vars.maps.storeLocatorHiddenableInput ).parents ( 'tr' ).first().css ( 'display', 'none' );

			$j.each ( $j( uploadInputs ), function ( index, uploadInput ) {

				uploadInput = $j( uploadInput );

				var inputUploadValue      = uploadInput.val(),

					defaultUploadOptions  = {

						frame    : 'post', 
						state    : 'insert',
						multiple : false

					},

					previewContainerClass = alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.upload.previewContainerClass ),

					uploadAssetsContainer = $j( '<div>' ).addClass ( alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.upload.assetsContainerClass ) ),
					previewContainer      = $j( '<div>' ).addClass ( previewContainerClass ).appendTo ( uploadAssetsContainer ),
					filenameLabel         = $j( '<span>' ).addClass ( alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.upload.filenameLabelClass ) ).appendTo ( previewContainer ),

					uploadButton          = $j( '<button>' ).attr ( 'type', 'button' )
															.addClass ( alopeyk.wcshm.admin.vars.upload.uploadButtonClass )
															.html ( uploadInput.data ( alopeyk.wcshm.admin.vars.upload.uploadLabelDataAttr ) )
															.appendTo ( uploadAssetsContainer )

															.on ( 'click', function ( e ) {

																e.preventDefault();

																var uploadOptions = uploadInput.data ( alopeyk.wcshm.admin.vars.upload.uploadOptionsDataAttr );
																uploadOptions = $j.extend( {}, defaultUploadOptions, uploadOptions ? uploadOptions : {} );

																var media_uploader = wp.media ( uploadOptions );

																media_uploader.on ( 'insert', function () {

																	var json = media_uploader.state().get ( 'selection' ).first().toJSON();
																	uploadInput.val ( json.url ).trigger ( 'change' );
																	filenameLabel.text ( json.filename );

																	previewContainer
																	.removeClass()
																	.addClass ( previewContainerClass )
																	.addClass ( alopeyk.wcshm.admin.vars.upload.previewTypePrefix + json.subtype );

																	if ( $j.inArray ( json.subtype, alopeyk.wcshm.admin.vars.common.knownImageExtensions ) != -1 ) {

																		previewContainer.css ( 'background-image', 'url(' + json.url + ')' );

																	}

																}).open();

															}),

					removeButton          = $j( '<button>' ).attr ( 'type', 'button' )
															.addClass ( alopeyk.wcshm.admin.vars.upload.removeButtonClass )
															.html ( uploadInput.data ( alopeyk.wcshm.admin.vars.upload.removeLabelDataAttr ) )
															.appendTo ( previewContainer )

															.on ( 'click', function ( e ) {

																e.preventDefault();
																uploadInput.val ( '' ).trigger ( 'change' );
																filenameLabel.text ( '' );

																previewContainer
																.removeClass()
																.addClass ( previewContainerClass )
																.css ( 'background-image', '' );

															});
					
				uploadAssetsContainer.insertAfter ( uploadInput );

				if ( inputUploadValue.length ) {


					var name      = inputUploadValue.split ( '/' ).pop(),
						extension = inputUploadValue.split ( '.' ).pop();

					filenameLabel.text ( name );
					previewContainer
					.removeClass()
					.addClass ( previewContainerClass )
					.addClass ( alopeyk.wcshm.admin.vars.upload.previewTypePrefix + extension );

					if ( $j.inArray ( extension, alopeyk.wcshm.admin.vars.common.knownImageExtensions ) != -1 ) {

						previewContainer.css ( 'background-image', 'url(' + inputUploadValue + ')' );

					}

				}

				uploadInput.on ( 'change paste keyup input propertychange', function () {

					var input       = $j( this ),
						isEmpty     = ! input.val().length,
						emptyClass  = alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.upload.isEmptyClass ),
						filledClass = alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.upload.isFilledClass );

						uploadAssetsContainer.removeClass ( isEmpty ? filledClass : emptyClass ).addClass ( isEmpty ? emptyClass : filledClass );

				}).trigger ( 'change' );

			});

		},

		handleBelowHeadingElements : function () {

			var headingContainer     = $j( alopeyk.wcshm.admin.vars.prompts.headingContainer ),
				belowHeadingElements = $j( alopeyk.wcshm.admin.vars.prompts.belowHeadingElements );

			if ( headingContainer.length ) {

				belowHeadingElements.insertBefore ( headingContainer.find ( alopeyk.wcshm.admin.vars.prompts.headingElement ).first() );

			}

		},

		handleBulkAction : function () {

			var submitElementSelector = alopeyk.wcshm.admin.vars.bulkAction.submitElement,
				bulkActionSubmit = $j( submitElementSelector );

			if ( bulkActionSubmit.length ) {

				$j( document ).on ( 'click', submitElementSelector, function ( e ) {

					var bulkActionValues   = [],
						bulkActionDropdown = $j( alopeyk.wcshm.admin.vars.bulkAction.dropdownElement );

					$j.each ( bulkActionDropdown, function () {
						bulkActionValues.push ( $j( this ).val() );
					});

					if ( $j.inArray( alopeyk.wcshm.admin.vars.bulkAction.actionValue, bulkActionValues ) != -1 ) {

						e.preventDefault();

						var orders = [];
						jQuery.each ( $j( alopeyk.wcshm.admin.vars.bulkAction.actionInputs ), function () { orders.push ( $j( this ).val() ); });
						alopeyk.wcshm.admin.fn.createOrderModal ( orders );

					}

				});

			}

		},

		showModal : function ( id, title, request, data, buttons, content, callback, modalOptions ) {

			var defaultModalOptions = {

				modal         : true,
				autoOpen      : true, 
				closeOnEscape : true,
				draggable     : false,
				resizable     : false,
				width         : 420,
				minHeight     : 24,

				open          : function () {

					var dialogElement = $j( this );

					if ( ! content ) {

						alopeyk.wcshm.admin.vars.activeModalConnection = $j.post ( alopeyk.wcshm.admin.vars.common.info.ajaxOptions.url, {

							nonce   : alopeyk.wcshm.admin.vars.common.info.ajaxOptions.nonce,
							action  : alopeyk.wcshm.admin.vars.common.info.alopeyk.wcshm.id,
							request : request,
							scope   : 'admin',
							data    : data

						}, function ( response ) {

							if ( response && response.success ) {

								dialogElement
								.data ( 'response', response.extra ? response.extra : null )
								.html ( response.data )
								.dialog( 'option', 'buttons', buttons );

								if ( callback && typeof callback == 'function' ) {
									callback ( dialogElement );
								}

							} else {

								var error = response ? ( response.data ? response.data : response.toString() ) : alopeyk.wcshm.admin.fn.translate ( 'Unkown error occurred.' );
								dialogElement.html ( '<div class="error notice"><p>' + error + '</p></div>' );

							}

						}).fail ( function ( jqXHR ) {

							dialogElement.html ( '<div class="error notice"><p><strong>' + alopeyk.wcshm.admin.fn.translate ( 'Request failed:' ) + '</strong> ' + alopeyk.wcshm.admin.fn.translate ( jqXHR.statusText ) + '</p></div>' );

						});

					} else {

						dialogElement
						.dialog( 'option', 'buttons', buttons );

					}

				},

				close         : function () {

					var dialogElement = $j( this ),
						activeConnection = alopeyk.wcshm.admin.vars.activeModalConnection;

					dialogElement.dialog ( 'destroy' );

					if ( activeConnection ) {

						activeConnection.abort();

					}

				}

			};

			modalOptions = $j.extend( {}, defaultModalOptions, modalOptions ? modalOptions : {} );

			if ( title ) {

				modalOptions.title = title;

			}

			var dialogLoader =  $j( '<div>' )
								.attr ( 'align', 'center' )
								.append ( $j( '<img>' ).attr ( 'src', alopeyk.wcshm.admin.vars.common.info.alopeyk.wcshm.loader ) );

			$j( '<div>' )
			.attr ( 'id', id ? alopeyk.wcshm.admin.fn.addPrefix ( 'modal-' + id ) : null )
			.addClass ( alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.common.modalContentClass ) )
			.html ( content ? content : dialogLoader )
			.dialog ( modalOptions );

		},

		createOrderModal : function ( orders, type, description ) {

			var buttons = [{

					text  : alopeyk.wcshm.admin.fn.translate ( 'Cancel' ),
					click : function () {

						$j( this ).dialog ( 'destroy' );

					}

				}, {

					text  : alopeyk.wcshm.admin.fn.translate ( 'Ship' ),
					class : 'button-primary',
					click : function () {

						$j( this ).find ( '[type="submit"]' ).trigger ( 'click' );

					}

				}],

				data = {

					type        : type,
					orders      : orders,
					description : description

				}

			alopeyk.wcshm.admin.fn.showModal ( 'create-order', alopeyk.wcshm.admin.fn.translate ( 'Ship via Alopeyk' ), 'create_order_modal', data, buttons, null, function () {

				alopeyk.wcshm.admin.fn.handleDateTimeFilters();

			});

		},

		createCreditModal : function ( amount ) {

			var buttons = [{

					text  : alopeyk.wcshm.admin.fn.translate ( 'Cancel' ),
					click : function () {

						$j( this ).dialog ( 'destroy' );

					}

				}, {

					text  : alopeyk.wcshm.admin.fn.translate ( 'Add Coupon' ),
					click : function () {

						alopeyk.wcshm.admin.fn.createCouponModal();

					}

				}, {

					text  : alopeyk.wcshm.admin.fn.translate ( 'Pay' ),
					class : 'button-primary',
					click : function () {

						if ( $j( this ).find ( '[type="submit"]' ).trigger ( 'click' ).parents ( 'form' ).get ( 0 ).checkValidity() ) {

							$j( this ).dialog ( 'destroy' );
							var orderCheckModal = $j( '#' + alopeyk.wcshm.admin.fn.addPrefix ( 'modal-check-order' ) );

							if ( orderCheckModal.length ) {
								orderCheckModal.dialog ( 'destroy' );
							}

						}

					}

				}],

				data = {

					amount : amount

				}

			alopeyk.wcshm.admin.fn.showModal ( 'create-credit', alopeyk.wcshm.admin.fn.translate ( 'Add Alopeyk Credit' ), 'create_credit_modal', data, buttons, null );

		},

		createCouponModal : function () {

			var buttons = [{

					text  : alopeyk.wcshm.admin.fn.translate ( 'Cancel' ),
					click : function () {

						$j( this ).dialog ( 'destroy' );

					}

				}, {

					text  : alopeyk.wcshm.admin.fn.translate ( 'Apply' ),
					class : 'button-primary',
					click : function () {

						$j( this ).find ( '[type="submit"]' ).trigger ( 'click' );

					}

				}],

				data = null;

			alopeyk.wcshm.admin.fn.showModal ( 'create-coupon', alopeyk.wcshm.admin.fn.translate ( 'Add Alopeyk Coupon' ), 'create_coupon_modal', data, buttons, null );

		},

		createCancelModal : function ( order ) {

			var buttons = [{

					text  : alopeyk.wcshm.admin.fn.translate ( 'Close' ),
					click : function () {

						$j( this ).dialog ( 'destroy' );

					}

				}, {

					text  : alopeyk.wcshm.admin.fn.translate ( 'Cancel' ),
					class : 'button-primary',
					click : function () {

						$j( this ).find ( '[type="submit"]' ).trigger ( 'click' );

					}

				}],

				data = {

					order : order

				};

			alopeyk.wcshm.admin.fn.showModal ( 'cancel-order', alopeyk.wcshm.admin.fn.translate ( 'Cancel Alopeyk Order' ), 'create_cancel_modal', data, buttons, null );

		},

		createRateModal : function ( order ) {

			var buttons = [{

					text  : alopeyk.wcshm.admin.fn.translate ( 'Close' ),
					click : function () {

						$j( this ).dialog ( 'destroy' );

					}

				}, {

					text  : alopeyk.wcshm.admin.fn.translate ( 'Submit' ),
					class : 'button-primary',
					click : function () {

						$j( this ).find ( '[type="submit"]' ).trigger ( 'click' );

					}

				}],

				data = {

					order : order

				};

			alopeyk.wcshm.admin.fn.showModal ( 'rate-order', alopeyk.wcshm.admin.fn.translate ( 'Rate Alopeyk Courier' ), 'create_rate_modal', data, buttons, null, function ( dialogElement ) {

				dialogElement.find ( 'input[type="radio"]' ).on ( 'change', function () {

					dialogElement.dialog ( 'option', 'position', {

						of : window,
						my : 'center',
						at : 'center',

					});

				});

			});

		},

		handleDateTimeFilters : function () {

			var lastActiveHour   = 0,
				lastActiveMinute = 0,
				schedule_dates   = alopeyk.wcshm.admin.vars.common.info.alopeyk.wcshm.scope.schedule_dates.dates,
				schedule_steps   = alopeyk.wcshm.admin.vars.common.info.alopeyk.wcshm.scope.schedule_dates.steps,

				shipDateDropdown = $j( alopeyk.wcshm.admin.vars.forms.dateDropdownElement ).filter ( function () {

					return ! $j( this ).data ( 'alopeyk-initiated' );

				}).data ( 'alopeyk-initiated', true ),

				shipHourDropdown = $j( alopeyk.wcshm.admin.vars.forms.hourDropdownElement ).filter ( function () {

					return ! $j( this ).data ( 'alopeyk-initiated' );

				}).data ( 'alopeyk-initiated', true ),

				shipMinuteDropdown = $j( alopeyk.wcshm.admin.vars.forms.minuteDropdownElement ).filter ( function () {

					return ! $j( this ).data ( 'alopeyk-initiated' );

				}).data ( 'alopeyk-initiated', true );


			if ( shipDateDropdown.length && shipHourDropdown.length && shipMinuteDropdown.length && schedule_dates && Object.keys ( schedule_dates ).length ) {


				shipDateDropdown.children().remove().end().on ( 'change', function () {

					shipHourDropdown.children().remove();

					var selected_date = $j( this ).find ( ':selected' ),
						initial_hour = parseInt ( selected_date.data ( 'initial_hour' ) ),
						initial_minute = parseInt ( selected_date.data ( 'initial_minute' ) );

					for ( var h = initial_hour; h < 24; h++ ) {

						var hour = alopeyk.wcshm.admin.fn.pad ( h, 2, '0' );

						$j( '<option>' )
						.attr ( 'value', hour )
						.attr ( 'selected', lastActiveHour == h )
						.text ( hour )
						.appendTo ( shipHourDropdown );

					}

					shipHourDropdown.trigger ( 'change' );

				});

				schedule_steps = Math.max ( 1, parseInt ( schedule_steps ) );

				shipHourDropdown.children().remove().end().on ( 'change', function () {

					shipMinuteDropdown.children().remove();

					var selected_date = shipDateDropdown.find ( ':selected' ),
						initial_hour = parseInt ( selected_date.data ( 'initial_hour' ) ),
						selected_hour = parseInt ( $j( this ).find ( ':selected' ).val() ),
						initial_minute = parseInt ( selected_date.data ( 'initial_minute' ) ),
						initial_minute_limit = initial_hour == selected_hour ? initial_minute : 0;

					lastActiveHour = selected_hour;

					for ( var m = initial_minute_limit; m < 60; m += schedule_steps ) {

						var minute = alopeyk.wcshm.admin.fn.pad ( m, 2, '0' );

						$j( '<option>' )
						.attr ( 'value', minute )
						.attr ( 'selected', m == lastActiveMinute )
						.text ( minute )
						.appendTo ( shipMinuteDropdown );

					}

					shipMinuteDropdown.trigger ( 'change' );

				});

				shipMinuteDropdown.children().remove().end().on ( 'change', function () {

					lastActiveMinute = parseInt ( $j( this ).find ( ':selected' ).val() );

				});

				for ( var date in schedule_dates ) {

					if ( schedule_dates.hasOwnProperty ( date ) ) {

						$j( '<option>' )
						.attr ( 'value', date )
						.text ( schedule_dates[ date ].label )
						.appendTo ( shipDateDropdown )
						.data ( 'initial_hour', schedule_dates[ date ].initial_hour )
						.data ( 'initial_minute', schedule_dates[ date ].initial_minute );

					}

				}

				shipDateDropdown.trigger ( 'change' );
				shipHourDropdown.trigger ( 'change' );

			}

		},

		pad : function ( input, width, char ) {

			char = char || '0';
			input = input + '';

			return input.length >= width ? input : new Array ( width - input.length + 1 ).join ( char ) + input;

		},

		handleCheckOrderForm : function () {

			$j( document ).on ( 'submit', '.' + alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.forms.createOrderFormClass ), function ( e ) {

				e.preventDefault();

				var buttons = [{

					text  : alopeyk.wcshm.admin.fn.translate ( 'Cancel' ),
					click : function () {

						$j( this ).dialog ( 'destroy' );

					}

				}, {

					text  : alopeyk.wcshm.admin.fn.translate ( 'Submit' ),
					class : 'button-primary',
					click : function () {

						var buttons = [{

								text  : alopeyk.wcshm.admin.fn.translate ( 'Track Order' ),
								click : function () {

									var tracking_url = alopeyk.wcshm.admin.fn.decodeToHtml ( $j( this ).data ( 'response' ).tracking_url );
									alopeyk.wcshm.admin.fn.openTab ( tracking_url );

								}

							}, {

								text  : alopeyk.wcshm.admin.fn.translate ( 'View Order' ),
								class : 'button-primary',
								click : function () {

									var view_url = alopeyk.wcshm.admin.fn.decodeToHtml ( $j( this ).data ( 'response' ).edit_url );
									alopeyk.wcshm.admin.fn.openTab ( view_url );

								}

							}],

							dialogElement = $j( this );

						dialogElement.dialog ( 'destroy' );
						alopeyk.wcshm.admin.fn.showModal ( 'submit-order', alopeyk.wcshm.admin.fn.translate ( 'Alopeyk Order' ), 'submit_order_modal', dialogElement.data ( 'response' ), buttons, null, function () {

							var orderCreateModal = $j( '#' + alopeyk.wcshm.admin.fn.addPrefix ( 'modal-create-order' ) );

							if ( orderCreateModal.length ) {
								orderCreateModal.dialog ( 'destroy' );
							}

							$j( document ).trigger ( 'awcshm:order:create' );

						});

					}

				}],

				checkOrderForm = $j( this ),
				data = checkOrderForm.serialize(),
				dataObject = alopeyk.wcshm.admin.fn.getUrlVars ( '#?' + data );

				if ( dataObject.ship_now == 'false' && dataObject.ship_date && dataObject.ship_date.length && dataObject.ship_hour && dataObject.ship_hour.length && dataObject.ship_minute && dataObject.ship_minute.length && alopeyk.wcshm.admin.vars.common.time && alopeyk.wcshm.admin.vars.common.info.time ) {

					var timeDiff = new Date ( parseInt ( ( '' + alopeyk.wcshm.admin.vars.common.info.time ).replace ( /-/g, '/' ) ) ) - new Date ( parseInt ( ( '' + alopeyk.wcshm.admin.vars.common.time ).replace ( /-/g, '/' ) ) ),
						shipDate = ( new Date ( dataObject.ship_date + ' ' + dataObject.ship_hour + ':' + dataObject.ship_minute + ':00' ) ).getTime() + timeDiff;

					if ( shipDate > Date.now() ) {

						alopeyk.wcshm.admin.fn.showModal ( 'check-order', alopeyk.wcshm.admin.fn.translate ( 'Alopeyk Order' ), 'check_order_modal', data, buttons );

					} else {

						var confirm_buttons = [{

							text  : alopeyk.wcshm.admin.fn.translate ( 'No' ),
							click : function () {

								$j( this ).dialog ( 'destroy' );

							}

						}, {

							text  : alopeyk.wcshm.admin.fn.translate ( 'Yes' ),
							class : 'button-primary',
							click : function () {

								$j( alopeyk.wcshm.admin.vars.forms.shipNowTogglerElement ).val ( [ 'true' ] );
								data = checkOrderForm.serialize();
								$j( this ).dialog ( 'destroy' );

								alopeyk.wcshm.admin.fn.showModal ( 'check-order', alopeyk.wcshm.admin.fn.translate ( 'Alopeyk Order' ), 'check_order_modal', data, buttons );

							}

						}];

						alopeyk.wcshm.admin.fn.showModal ( 'check-order-time', alopeyk.wcshm.admin.fn.translate ( 'Alopeyk Order' ), null, null, confirm_buttons, alopeyk.wcshm.admin.vars.common.info.alopeyk.wcshm.scope.schedule_dates.error );

					}

				} else {

					alopeyk.wcshm.admin.fn.showModal ( 'check-order', alopeyk.wcshm.admin.fn.translate ( 'Alopeyk Order' ), 'check_order_modal', data, buttons );

				}

			});

		},

		handleAddCouponForm : function () {

			$j( document ).on ( 'submit', '.' + alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.forms.addCouponFormClass ), function ( e ) {

				e.preventDefault();

				var data = $j( this ).serialize();
				alopeyk.wcshm.admin.fn.showModal ( 'add-coupon', alopeyk.wcshm.admin.fn.translate ( 'Alopeyk Coupon' ), 'add_coupon_modal', data, null, null, function () {

					var couponModal     = $j( '#' + alopeyk.wcshm.admin.fn.addPrefix ( 'modal-create-coupon' ) ),
						creditModal     = $j( '#' + alopeyk.wcshm.admin.fn.addPrefix ( 'modal-create-credit' ) ),
						orderCheckModal = $j( '#' + alopeyk.wcshm.admin.fn.addPrefix ( 'modal-check-order'   ) );

					if ( couponModal.length ) {
						couponModal.dialog ( 'destroy' );
					}

					if ( creditModal.length ) {
						creditModal.dialog ( 'destroy' );
					}

					if ( orderCheckModal.length ) {
						orderCheckModal.dialog ( 'destroy' );
					}

					$j( document ).trigger ( 'awcshm:credit:add' );

				});

			});

		},

		handleCancelOrderForm : function () {

			$j( document ).on ( 'submit', '.' + alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.forms.cancelOrderFormClass ), function ( e ) {

				e.preventDefault();

				var data = $j( this ).serialize();
				alopeyk.wcshm.admin.fn.showModal ( 'cancel-order-result', alopeyk.wcshm.admin.fn.translate ( 'Cancel Order' ), 'cancel_result_modal', data, null, null, function () {

					var CouponModal = $j( '#' + alopeyk.wcshm.admin.fn.addPrefix ( 'modal-cancel-order' ) );

					if ( CouponModal.length ) {
						CouponModal.dialog ( 'destroy' );
					}

					$j( document ).trigger ( 'awcshm:order:cancel' );

				});

			});

		},

		handleRateOrderForm : function () {

			$j( document ).on ( 'submit', '.' + alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.forms.rateOrderFormClass ), function ( e ) {

				e.preventDefault();

				var data = $j( this ).serialize();
				alopeyk.wcshm.admin.fn.showModal ( 'rate-order-result', alopeyk.wcshm.admin.fn.translate ( 'Rate Alopeyk Courier' ), 'rate_result_modal', data, null, null, function () {

					var CouponModal = $j( '#' + alopeyk.wcshm.admin.fn.addPrefix ( 'modal-rate-order' ) );

					if ( CouponModal.length ) {
						CouponModal.dialog ( 'destroy' );
					}

					$j( document ).trigger ( 'awcshm:order:finish' );

				});

			});

		},

		setupCreditModalTrigger : function () {

			$j( document ).on ( 'click', '.' + alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.modals.creditModalTogglerClass ), function ( e ) {

				e.preventDefault();

				var element = $j( this ),
					amount  = element.data ( alopeyk.wcshm.admin.vars.modals.creditModalAmountDataAttr );

				alopeyk.wcshm.admin.fn.createCreditModal ( amount );

			});

		},

		setupOrderModalTrigger : function () {

			$j( document ).on ( 'click', '.' + alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.modals.orderModalTogglerClass ), function ( e ) {

				e.preventDefault();

				var element     = $j( this ),
					type        = element.data ( alopeyk.wcshm.admin.vars.modals.orderModalTypeDataAttr ),
					orders      = element.data ( alopeyk.wcshm.admin.vars.modals.orderModalOrdersDataAttr ),
					description = element.data ( alopeyk.wcshm.admin.vars.modals.orderModalDescriptionDataAttr );

				orders = typeof orders !== 'undefined' ? orders + '' : null;
				orders = orders && orders.length ? orders.split ( alopeyk.wcshm.admin.vars.modals.orderModalOrdersDelimiter ) : null;
				alopeyk.wcshm.admin.fn.createOrderModal ( orders, type, description );

			});

		},

		setupCouponModalTrigger : function () {

			$j( document ).on ( 'click', '.' + alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.modals.couponModalTogglerClass ), function ( e ) {

				e.preventDefault();
				alopeyk.wcshm.admin.fn.createCouponModal ();

			});

		},

		setupCancelModalTrigger : function () {

			$j( document ).on ( 'click', '.' + alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.modals.cancelModalTogglerClass ), function ( e ) {

				e.preventDefault();
				alopeyk.wcshm.admin.fn.createCancelModal ( $j( this ).data ( alopeyk.wcshm.admin.vars.modals.cancelModalOrderDataAttr ) );

			});

		},

		setupRateModalTrigger : function () {

			$j( document ).on ( 'click', '.' + alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.modals.rateModalTogglerClass ), function ( e ) {

				e.preventDefault();
				alopeyk.wcshm.admin.fn.createRateModal ( $j( this ).data ( alopeyk.wcshm.admin.vars.modals.rateModalOrderDataAttr ) );

			});

		},

		handleAmountButtons : function () {

			$j( document ).on ( 'click', '.' + alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.forms.creditButtonElementClass ), function () {

				var button = $j( this ),
					amount = button.data ( alopeyk.wcshm.admin.vars.forms.creditButtonAmountDataAttr ),
					target = $j( button.data ( alopeyk.wcshm.admin.vars.forms.creditButtonTargetDataAttr ) );

				target = target && target.length ? target : $j( '.' + alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.forms.priceInputsClass ) );

				if ( target.length ) {
					target.val ( amount ? amount : '' );
				}

			});

		},

		handlePriceInputs : function () {

			var priceInputsSelector = '.' + alopeyk.wcshm.admin.fn.addPrefix ( alopeyk.wcshm.admin.vars.forms.priceInputsClass );

			$j( document ).on ( 'keydown', priceInputsSelector, function ( e ) {
				-1 !== $j.inArray ( e.keyCode, [ 46, 8, 9, 27, 13, 110, 190 ] ) || ( /65|67|86|88/.test ( e.keyCode ) && ( e.ctrlKey === true || e.metaKey === true ) ) && ( ! 0 === e.ctrlKey || ! 0 === e.metaKey ) || 35 <= e.keyCode && 40 >= e.keyCode || ( e.shiftKey || 48 > e.keyCode || 57 < e.keyCode ) && ( 96 > e.keyCode || 105 < e.keyCode ) && e.preventDefault();
			});

			$j( document ).on ( 'change paste keyup input propertychange', priceInputsSelector, function ( e ) {

				var input      = $j( this ),
					inputValue = input.val();

				input.val ( inputValue.replace ( /\D/g, '' ) );

			}).trigger ( 'change' );

		},

		handlePageUpdates : function () {

			var dynamic_parts = [],
				refresh_interval = parseInt ( alopeyk.wcshm.admin.vars.common.info.refresh_interval ),
				dynamic_parts_selectors = alopeyk.wcshm.admin.vars.common.info.dynamic_parts;

			if ( dynamic_parts_selectors && dynamic_parts_selectors.length ) {

				$j.each ( dynamic_parts_selectors, function ( index, value ) {

					if ( $j( value ).length ) {
						dynamic_parts.push ( value );
					}

				});

				if ( dynamic_parts.length ) {

					var load_data = function () {

							if ( alopeyk.wcshm.admin.vars.common.pageUpdateConnection ) {

								alopeyk.wcshm.admin.vars.common.pageUpdateConnection.abort();

							}

							alopeyk.wcshm.admin.vars.common.pageUpdateConnection = $j.ajax ( window.location.href ).done ( function ( html ) {

								var response = $j( html );

								$j.each ( dynamic_parts, function ( index, selector ) {

									var part = $j( selector ),
										content = response.find ( selector ).html();

									if ( part.html() != content ) {

										part.html ( content );
										
									}

								});

							});

						},
						pageUpdateTimeout = setInterval ( load_data, ( refresh_interval ? refresh_interval : 10 ) * 1000 );

					$j( document ).on ( 'awcshm:order:create awcshm:order:cancel awcshm:order:finish awcshm:credit:add', load_data );

				}

			}

		},

		handleChatToggler : function () {

			$j( document ).on ( 'change', alopeyk.wcshm.admin.vars.chat.togglerInput, function () {

				if ( $j( this ).prop ( 'checked' ) ) {

					var hasScroll = $j( document ).height() > $j( window ).height();

					$j( 'html, body' ).stop().animate ( { scrollTop : 0 }, 250, function () {

						$j( 'body' ).scrollTop ( 0 ).css ( 'overflow', 'hidden' ).parent().css ( 'overflow-y', hasScroll ? 'scroll' : '' );

					});

				} else {

					$j( 'body' ).css ( 'overflow', '' ).parent().css ( 'overflow-y', '' );

				}

			});

			$j( alopeyk.wcshm.admin.vars.chat.togglerInput ).trigger ( 'change' );

		},

		init : function () {

			alopeyk.wcshm.admin.fn.handlePageUpdates();
			alopeyk.wcshm.admin.fn.handleChatToggler();
			alopeyk.wcshm.admin.fn.handlePriceInputs();
			alopeyk.wcshm.admin.fn.handleAmountButtons();
			alopeyk.wcshm.admin.fn.handleSettingFields();
			alopeyk.wcshm.admin.fn.handleBulkAction();
			alopeyk.wcshm.admin.fn.handleDateTimeFilters();
			alopeyk.wcshm.admin.fn.handleCheckOrderForm();
			alopeyk.wcshm.admin.fn.handleCancelOrderForm();
			alopeyk.wcshm.admin.fn.handleRateOrderForm();
			alopeyk.wcshm.admin.fn.handleAddCouponForm();
			alopeyk.wcshm.admin.fn.setupCreditModalTrigger();
			alopeyk.wcshm.admin.fn.setupOrderModalTrigger();
			alopeyk.wcshm.admin.fn.setupCancelModalTrigger();
			alopeyk.wcshm.admin.fn.setupCouponModalTrigger();
			alopeyk.wcshm.admin.fn.setupRateModalTrigger();
			alopeyk.wcshm.admin.fn.handleBelowHeadingElements();

		},

	};



	/*=========================================
	=            Calling Functions            =
	=========================================*/

	/**
	 *
	 * Registered Events
	 *
	 **/

	$j( document ).on ({

		'ready' : function ( e ) {

			alopeyk.wcshm.admin.fn.init();

		}

	});

	$j( window ).on ({

		'load' : function ( e ) {

			alopeyk.wcshm.admin.fn.initMaps();

		},

	});

})( jQuery );
