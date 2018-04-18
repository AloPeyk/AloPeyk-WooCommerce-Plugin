(function( $j ) {
	
	'use strict';

	var alopeyk = alopeyk || { wcshm : {} };
	alopeyk.wcshm.public = {};



	/*==========================================
	=            Defining Variables            =
	==========================================*/

	alopeyk.wcshm.public.vars = {

		common : {

			info               : window.awcshm,
			activeClass        : 'active',
			loadingClass       : 'loading',
			disabledClass      : 'disabled',
			alopeykPrefix      : 'awcshm-',
			paymentMethodInput : 'input[name="payment_method"]',

		},

		maps   : {
			
			selector                                   : '.map-canvas',
			mapContainerClass                          : 'map-container',
			destinationLatInput                        : '#destination_latitude, #_shipping_address_latitude',
			destinationLngInput                        : '#destination_longitude, #_shipping_address_longitude',
			destinationCityInput                       : '#destination_city, #_shipping_address_location_city',
			destinationAddressInput                    : '#destination_address, #_shipping_address_location',
			destinationNumberInput                     : '#destination_number, #_shipping_address_number',
			destinationUnitInput                       : '#destination_unit, #_shipping_address_unit',
			shippingAddress1Input                      : '#shipping_address_1, #_shipping_address_1',
			shippingAddress2Input                      : '#shipping_address_2, #_shipping_address_2',
			billingAddress1Input                       : '#billing_address_1, #_billing_address_1',
			billingAddress2Input                       : '#billing_address_2, #_billing_address_2',
			shipToDifferentAddressInput                : 'input[name="ship_to_different_address"]',
			editAddressButton                          : 'a.edit_address:eq(1)',
			destinationLocatorMapId                    : 'destination-locator-map',
			destinationLocatorMapClass                 : 'map-canvas destination-locator-map',
			destinationLocatorCtaClass                 : 'destination-locator-cta',
			destinationLocatorInputWrapperId           : 'destination-locator-input-wrapper',
			destinationLocatorInputWrapperClass        : 'destination-locator-input-wrapper',
			destinationLocatorAutocompleteResultsClass : 'destination-locator-autocomplete-results',
			destinationLocatorAutocompleteResultClass  : 'destination-locator-autocomplete-result',
			destinationLocatorHiddenableInput          : '.hide-parent-row',
			autocompletePlaceholderDataAttr            : 'autocomplete-placeholder',
			autocompleteKeyupTimeout                   : null,
			positionKeyupTimeout                       : null,
			autoCompleteKeyupDelay                     : 500,
			positionKeyupDelay                         : 500,
			defaultZoom                                : 15,
			defaultCenter                              : {
				lat : 35.744989,
				lng : 51.375284
			}

		},

	};



	/*==================================
	=            Prototypes            =
	==================================*/




	/*==========================================
	=            Defining Functions            =
	==========================================*/

	alopeyk.wcshm.public.fn = {

		addPrefix : function ( classes ) {
			
			var prefixedClasses = [],
				classesArray    = classes.split ( ' ' );

			for ( var i = 0; i < classesArray.length; i++ ) {
				prefixedClasses.push ( alopeyk.wcshm.public.vars.common.alopeykPrefix + classesArray[ i ] )
			}

			return prefixedClasses.join ( ' ' );

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

			window[ 'alopeykHandleMapsPublic' ] = alopeyk.wcshm.public.fn.handleMaps;

			if ( typeof alopeykHandleMapsPublic === 'function' ) {

				if ( typeof window.google != 'undefined' && window.google.maps ) {

					window.googleMapIsLoading = false;
					alopeyk.wcshm.public.fn.handleMaps();

				} else if ( window.googleMapIsLoading ) {

					alopeyk.wcshm.public.vars.loadingMapInterval = setInterval ( function () {

						if ( window.google && window.google.maps ) {

							window.googleMapIsLoading = false;
							clearInterval ( alopeyk.wcshm.public.vars.loadingMapInterval );
							alopeyk.wcshm.public.fn.handleMaps();

						}

					}, 500 );

				} else {

					window.googleMapIsLoading = true;
					alopeyk.wcshm.public.fn.injectScript('https://maps.googleapis.com/maps/api/js?key=' + alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.api_key + '&libraries=places&language=fa&region=IR&callback=alopeykHandleMapsPublic');

				}

			}

		},

		handleMaps : function () {

			$j( document ).trigger ( 'alopeyk:public:map:loaded' );

		},

		initMaps : function () {

			alopeyk.wcshm.public.fn.loadGoogleMaps();

			$j( document ).on ( 'alopeyk:public:map:loaded', function () {

				var destinationLatInput         = $j( alopeyk.wcshm.public.vars.maps.destinationLatInput ),
					destinationLngInput         = $j( alopeyk.wcshm.public.vars.maps.destinationLngInput ),
					destinationCityInput        = $j( alopeyk.wcshm.public.vars.maps.destinationCityInput ),
					destinationAddressInput     = $j( alopeyk.wcshm.public.vars.maps.destinationAddressInput ),
					destinationNumberInput      = $j( alopeyk.wcshm.public.vars.maps.destinationNumberInput ),
					destinationUnitInput        = $j( alopeyk.wcshm.public.vars.maps.destinationUnitInput ),
					shippingAddress1Input       = $j( alopeyk.wcshm.public.vars.maps.shippingAddress1Input ),
					shippingAddress2Input       = $j( alopeyk.wcshm.public.vars.maps.shippingAddress2Input ),
					billingAddress1Input        = $j( alopeyk.wcshm.public.vars.maps.billingAddress1Input ),
					billingAddress2Input        = $j( alopeyk.wcshm.public.vars.maps.billingAddress2Input ),
					shipToDifferentAddressInput = $j( alopeyk.wcshm.public.vars.maps.shipToDifferentAddressInput ),
					editAddressButton           = $j( alopeyk.wcshm.public.vars.maps.editAddressButton );

				if ( destinationLatInput.length && destinationLngInput.length && destinationCityInput.length && destinationAddressInput.length ) {
					alopeyk.wcshm.public.fn.initDestinationLocator( destinationLatInput, destinationLngInput, destinationCityInput, destinationAddressInput, destinationNumberInput, destinationUnitInput, shippingAddress1Input, shippingAddress2Input, billingAddress1Input, billingAddress2Input, shipToDifferentAddressInput, editAddressButton );
				}

			});

		},

		setCurrentGeolocation : function ( forceRelocation ) {

			var destinationMap      = alopeyk.wcshm.public.vars.common.destinationMap,
				map                 = destinationMap ? destinationMap.mapObject : null,
				marker              = destinationMap ? destinationMap.markerObject : null,
				destinationLatInput = $j( alopeyk.wcshm.public.vars.maps.destinationLatInput ),
				destinationLngInput = $j( alopeyk.wcshm.public.vars.maps.destinationLngInput );

			if ( navigator.geolocation && map && marker && destinationLatInput.length && destinationLngInput.length && ( forceRelocation || ( ! destinationLatInput.val().length && ! destinationLngInput.val().length ) || ( destinationLatInput.val() == alopeyk.wcshm.public.vars.maps.defaultCenter.lat && destinationngInput.val() == alopeyk.wcshm.public.vars.maps.defaultCenter.lng ) ) ) {
console.log('test');
				navigator.geolocation.getCurrentPosition ( function ( position ) {
console.log('test1');
					if ( alopeyk.wcshm.public.vars.maps.fetchAddressConnection ) {
						alopeyk.wcshm.public.vars.maps.fetchAddressConnection.abort();
					}

					map.setCenter ({

						lat : position.coords.latitude,
						lng : position.coords.longitude

					});

					marker.setPosition ( new google.maps.LatLng ( position.coords.latitude, position.coords.longitude ) );

					alopeyk.wcshm.public.vars.maps.defaultCenter.lat = position.coords.latitude;
					alopeyk.wcshm.public.vars.maps.defaultCenter.lng = position.coords.longitude;

					destinationLatInput.val ( position.coords.latitude );
					destinationLngInput.val ( position.coords.longitude ).trigger ( 'change' );

				});

			}

		},

		initDestinationLocator : function ( destinationLatInput, destinationLngInput, destinationCityInput, destinationAddressInput, destinationNumberInput, destinationUnitInput, shippingAddress1Input, shippingAddress2Input, billingAddress1Input, billingAddress2Input, shipToDifferentAddressInput, editAddressButton ) {

			
			var initialize = function () {

				var mapCanvas = $j('<div/>').attr ({

						id    : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorMapId ),
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorMapClass ),

					}),
					destinationAutocompleteInput = destinationAddressInput.clone().attr ({

						id             : destinationAddressInput.attr ( 'id' ) + '_autocomplete',
						class          : destinationAddressInput.attr ( 'class' ),
						placeholder    : destinationAddressInput.data ( alopeyk.wcshm.public.vars.maps.autocompletePlaceholderDataAttr ),
						name           : '',
						style          : '',
						type           : 'text',
						spellcheck     : 'false',
						autocapitalize : 'off',
						autocorrect    : 'off',
						autocomplete   : 'off'

					}).removeClass ( alopeyk.wcshm.public.vars.common.disabledClass ),
					destinationAutocompleteInputWrapper = $j( '<div>' ).attr ({

						id    : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorInputWrapperId ),
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorInputWrapperClass ),

					}),
					autoCompleteList = $j( '<ul>' ).attr ({

						id    : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorAutocompleteResultsClass ),
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorAutocompleteResultsClass ),

					}),
					mapContainer = $j( '<div/>' ).attr ({

						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.mapContainerClass ),

					}),
					destinationLocatorCta = $j( '<button/>' ).attr ({

						type  : 'button',
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorCtaClass ),

					});

				destinationAutocompleteInputWrapper
				.insertAfter ( destinationAddressInput )
				.append ( destinationAutocompleteInput )
				.append ( autoCompleteList );

				mapCanvas.insertAfter( destinationAutocompleteInputWrapper );
				mapContainer.insertAfter( mapCanvas );

				mapContainer
				.append( mapCanvas )
				.append( destinationAutocompleteInputWrapper )
				.append( destinationLocatorCta );


				var mapMarker  = alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.marker,
					mapStyles  = alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.styles,
					mapOptions = {

						zoom   : alopeyk.wcshm.public.vars.maps.defaultZoom,
						styles : mapStyles ? $j.parseJSON ( mapStyles ) : null,
						center : {

							lat : destinationLatInput.val().length ? parseFloat( destinationLatInput.val() ) : alopeyk.wcshm.public.vars.maps.defaultCenter.lat,
							lng : destinationLngInput.val().length ? parseFloat( destinationLngInput.val() ) : alopeyk.wcshm.public.vars.maps.defaultCenter.lng,

						},
						mapTypeControl    : false,
						fullscreenControl : false,
						streetViewControl : false,

					},

					map    = new google.maps.Map ( mapCanvas.get ( 0 ), mapOptions ),

					marker = new google.maps.Marker ({

								map       : map,
								draggable : true,
								position  : mapOptions.center,
								icon      : mapMarker ? mapMarker : null

							}),

					setActiveAutocompleteItem = function ( index ) {

						var results     = autoCompleteList.children(),
							activeIndex = index == results.length ? 0 : ( index < 0 ? results.length - 1 : index ),
							activeClass = alopeyk.wcshm.public.vars.common.activeClass;

						results.removeClass ( activeClass ).eq ( activeIndex ).addClass ( activeClass );

					},

					fetchAddressFromLocation = function () {

						if ( alopeyk.wcshm.public.vars.maps.fetchAddressConnection ) {
							alopeyk.wcshm.public.vars.maps.fetchAddressConnection.abort();
						}

						destinationAutocompleteInputWrapper.addClass ( alopeyk.wcshm.public.vars.common.loadingClass );
						alopeyk.wcshm.public.vars.maps.fetchAddressConnection = $j.post ( alopeyk.wcshm.public.vars.common.info.ajaxOptions.url, {

							nonce        : alopeyk.wcshm.public.vars.common.info.ajaxOptions.nonce,
							action       : alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.id,
							request      : 'get_address',
							authenticate : true,
							ask_google   : true,
							lat          : marker.getPosition().lat(),
							lng          : marker.getPosition().lng(),

						}, function ( response ) {

							if ( response ) {

								autoCompleteList.empty();
								destinationCityInput.val ( response.success && response.data.city ? response.data.city : '' );
								destinationAutocompleteInput.val ( response.data.address );

								var address = response.success ? response.data.address : '';
								destinationAddressInput.val ( address ).trigger ( 'change' );

								shippingAddress1Input.val ( address );
								shippingAddress2Input.val ( '' );

								if ( shipToDifferentAddressInput.length && ! shipToDifferentAddressInput.prop ( 'checked' ) ) {

									if ( billingAddress1Input.length )
										billingAddress1Input.val ( address );

									if ( billingAddress2Input.length )
										billingAddress2Input.val ( '' );

								}

							}

						}).always ( function () {

							destinationAutocompleteInputWrapper.removeClass ( alopeyk.wcshm.public.vars.common.loadingClass );

						});

					},

					saveData = function ( update ) {

						if ( alopeyk.wcshm.public.vars.updateShipingMethod ) {
							alopeyk.wcshm.public.vars.updateShipingMethod.abort();
						}

						destinationAddressInput.parents ( 'form' )
						.find ( 'button, input[type="button"], input[type="submit"]' )
						.filter ( function () {
							return ! $j( this ).is ( ':disabled' );
						})
						.prop ( 'disabled', true )
						.data ( 'alopeyk-disable', true );

						alopeyk.wcshm.public.vars.updateShipingMethod = $j.post ( alopeyk.wcshm.public.vars.common.info.ajaxOptions.url, {

							nonce          : alopeyk.wcshm.public.vars.common.info.ajaxOptions.nonce,
							action         : alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.id,
							request        : 'set_session',
							authenticate   : true,
							lat            : marker.getPosition().lat(),
							lng            : marker.getPosition().lng(),
							city           : destinationCityInput.val(),
							address        : destinationAddressInput.val(),
							number         : destinationNumberInput.val(),
							unit           : destinationUnitInput.val(),
							payment_method : $j( paymentMethodInputSelector + ':checked' ).val(),

						}, function ( response ) {
							
							if ( response && response.success && update ) {

								$j( 'body' ).trigger ( 'update_checkout' );

							}

							destinationAddressInput.parents ( 'form' )
							.find ( 'button, input[type="button"], input[type="submit"]' )
							.filter ( function () {
								return $j( this ).data ( 'alopeyk-disable' );
							})
							.prop ( 'disabled', false );

						});

					},

					paymentMethodInputSelector = alopeyk.wcshm.public.vars.common.paymentMethodInput;

				alopeyk.wcshm.public.vars.common.destinationMap = {

					mapObject    : map,
					markerObject : marker

				};

				if ( alopeyk.wcshm.public.vars.common.info.woocommerce.checkout ) {
					
					$j( document ).on ( 'change', paymentMethodInputSelector + ', #' + destinationAddressInput.attr ( 'id' ) , function () {
						saveData ( true );
					});

					$j.merge ( destinationNumberInput, destinationUnitInput ).on ( 'change paste keyup input propertychange', function () {

						if ( alopeyk.wcshm.public.vars.maps.addressDetailKeyupTimeout ) {
							clearTimeout(  alopeyk.wcshm.public.vars.maps.addressDetailKeyupTimeout );
						}

						alopeyk.wcshm.public.vars.maps.addressDetailKeyupTimeout = setTimeout ( function () {
							saveData ( false );
						}, alopeyk.wcshm.public.vars.maps.autoCompleteKeyupDelay );

					});

				}

				marker.addListener ( 'drag', function() {

					destinationLatInput.val ( marker.getPosition().lat() );
					destinationLngInput.val ( marker.getPosition().lng() );

				});

				marker.addListener ( 'dragend', function () {

					fetchAddressFromLocation();

				});

				$j.merge ( destinationLatInput, destinationLngInput ).on ( 'change paste keyup input propertychange', function () {

					if ( alopeyk.wcshm.public.vars.maps.latitudeValue != destinationLatInput.val() || alopeyk.wcshm.public.vars.maps.longitudeValue != destinationLngInput.val()  ) {

						alopeyk.wcshm.public.vars.maps.latitudeValue = destinationLatInput.val();
						alopeyk.wcshm.public.vars.maps.longitudeValue = destinationLngInput.val();

						if ( alopeyk.wcshm.public.vars.maps.positionKeyupTimeout ) {
							clearTimeout(  alopeyk.wcshm.public.vars.maps.positionKeyupTimeout );
						}

						alopeyk.wcshm.public.vars.maps.positionKeyupTimeout = setTimeout ( function () {

							var location = { lat : parseFloat ( destinationLatInput.val() ), lng : parseFloat ( destinationLngInput.val() ) };
							map.setCenter ( location );
							marker.setPosition ( location );
							fetchAddressFromLocation();

						}, alopeyk.wcshm.public.vars.maps.positionKeyupDelay );

					}

				});

				shipToDifferentAddressInput.on ( 'change', function () {

					var checked = $j( this ).prop ( 'checked' ),
						display = checked ? '' : 'none';

					billingAddress1Input.parent().css ( 'display', display );
					billingAddress2Input.parent().css ( 'display', display );

					if ( ! checked ) {

						billingAddress1Input.val ( shippingAddress1Input.val() );
						billingAddress2Input.val ( shippingAddress2Input.val() );

					}

				}).trigger ( 'change' );

				destinationAutocompleteInput.on ({

					'keydown' : function ( e ) {

						var activeItem  = autoCompleteList.children ( '.' + alopeyk.wcshm.public.vars.common.activeClass ),
							activeIndex = activeItem.length ? activeItem.index() : -1;

						switch ( e.which ) {

							case 13:
								e.preventDefault();
								if ( activeItem ) {
									activeItem.trigger ( 'click' );
								}
								break;
							case 27:
								destinationAutocompleteInput.blur();
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

						if ( alopeyk.wcshm.public.vars.maps.autocompleteInputValue != destinationAutocompleteInput.val()  ) {

							autoCompleteList.empty();
							alopeyk.wcshm.public.vars.maps.autocompleteInputValue = destinationAutocompleteInput.val();

							if ( alopeyk.wcshm.public.vars.maps.autocompleteKeyupTimeout ) {
								clearTimeout(  alopeyk.wcshm.public.vars.maps.autocompleteKeyupTimeout );
							}

							alopeyk.wcshm.public.vars.maps.autocompleteKeyupTimeout = setTimeout ( function () {

								if ( alopeyk.wcshm.public.vars.maps.autocompleteConnection ) {
									alopeyk.wcshm.public.vars.maps.autocompleteConnection.abort();
								}

								destinationAutocompleteInputWrapper.addClass ( alopeyk.wcshm.public.vars.common.loadingClass );
								alopeyk.wcshm.public.vars.maps.autocompleteConnection = $j.post ( alopeyk.wcshm.public.vars.common.info.ajaxOptions.url, {

									nonce        : alopeyk.wcshm.public.vars.common.info.ajaxOptions.nonce,
									action       : alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.id,
									request      : 'suggest_address',
									authenticate : true,
									input        : destinationAutocompleteInput.val(),

								}, function ( response ) {

									if ( response && response.success && response.data.length ) {

										for ( var i = 0; i < response.data.length; i++ ) {

											var itemLat      = parseFloat( response.data[i].lat ),
												itemLng      = parseFloat( response.data[i].lng ),
												itemLocation = { lat : itemLat, lng : itemLng },
												itemAddress  = response.data[i].address,
												itemCity     = response.data[i].city,

												resultItem   = $j( '<li>' )
												.addClass( alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorAutocompleteResultClass ) )
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

														destinationAutocompleteInput.blur();
														destinationAutocompleteInput.val ( address );
														destinationAddressInput.val ( address );
														destinationCityInput.val ( city );
														map.setCenter ( location );
														map.setZoom ( 17 );
														marker.setPosition ( location );
														marker.setVisible ( true );
														destinationLatInput.val ( location.lat );
														destinationLngInput.val ( location.lng );
														alopeyk.wcshm.public.vars.maps.autocompleteInputValue = address;

														shippingAddress1Input.val ( address );
														shippingAddress2Input.val ( '' );

														if ( shipToDifferentAddressInput.length && ! shipToDifferentAddressInput.prop ( 'checked' ) ) {

															if ( billingAddress1Input.length )
																billingAddress1Input.val ( address );

															if ( billingAddress2Input.length )
																billingAddress2Input.val ( '' );

														}

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

									destinationAutocompleteInputWrapper.removeClass ( alopeyk.wcshm.public.vars.common.loadingClass );

								});

							}, alopeyk.wcshm.public.vars.maps.autoCompleteKeyupDelay );

						}

					}

				});

				google.maps.event.addListenerOnce ( map, 'idle', function () {

					destinationAutocompleteInputWrapper.css ( 'position', 'absolute' );

				});

				google.maps.event.addDomListener ( window, 'resize', function () {

					if ( alopeyk.wcshm.public.vars.maps.centerTimeout )
						clearTimeout ( alopeyk.wcshm.public.vars.maps.centerTimeout );

					alopeyk.wcshm.public.vars.maps.centerTimeout = setTimeout ( function () {

						map.setCenter ( marker.getPosition() );

					}, 100 );

				});

				destinationLocatorCta.on ( 'click', function () {

					alopeyk.wcshm.public.fn.setCurrentGeolocation ( true );

				});

				alopeyk.wcshm.public.fn.setCurrentGeolocation();

			};

			if ( editAddressButton.length && editAddressButton.is ( ':visible' ) ) {

				editAddressButton.on ( 'click', function () {
					initialize();
				});

			} else {

				initialize();

			}

		},

		init : function () {

			

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

			alopeyk.wcshm.public.fn.init();

		}

	});

	$j( window ).on ({

		'load' : function ( e ) {

			alopeyk.wcshm.public.fn.initMaps();

		},

	});

})( jQuery );
