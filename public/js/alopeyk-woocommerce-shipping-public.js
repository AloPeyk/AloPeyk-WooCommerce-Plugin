(function ( $j ) {

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
			destinationAddressInput                    : '#destination_address, #_shipping_address_location',
			destinationNumberInput                     : '#destination_number, #_shipping_address_number',
			destinationUnitInput                       : '#destination_unit, #_shipping_address_unit',
			billingCountry                             : '#billing_country',
			shippingCountry                            : '#shipping_country',
			billingCity                                : '#billing_city',
			shippingCity                               : '#shipping_city',
			billingState                               : '#billing_state',
			shippingState                              : '#shipping_state',
			woocommerceCheckoutClass                   : '.woocommerce-checkout',
			wcAddAlopeyk                               : 'awcshm-map-visible',
			shipToDifferentAddressInput                : 'input[name="ship_to_different_address"]',
			editAddressButton                          : 'a.edit_address:eq(1)',
			destinationLocatorMapId                    : 'destination-locator-map',
			destinationLocatorMapClass                 : 'map-canvas destination-locator-map',
			destinationLocatorCtaClass                 : 'destination-locator-cta',
			mapMarkerIconClass                         : 'map-marker-icon',
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
			maxZoom                                    : 17,
			defaultCenter                              : {
				lat : 35.6996468,
				lng : 51.3377773
			},

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
		
		translate : function ( term ) {

			var translation = alopeyk.wcshm.public.vars.common.info.translations[ term ];
			return translation ? translation : term;

		},

		injectScript : function ( src, callback ) {

			if ( ! $j( '[src="' + src + '"]' ).length ) {

				var s, t;

				s        = document.createElement ( 'script' );
				s.type   = 'text/javascript';
				s.async  = true;
				s.src    = src;
				s.onload = callback ? callback : false;
				t        = document.getElementsByTagName ( 'script' )[ 0 ];

				t.parentNode.insertBefore ( s, t );

			}

		},
		
		injectStylesheet : function ( href ) {

			if ( ! $j( '[href="' + href + '"]' ).length ) {

				var s, t;

				s       = document.createElement ( 'link' );
				s.type  = 'text/css';
				s.rel   = "stylesheet";
				s.href  = href;
				t       = document.getElementsByTagName ( 'link' )[ 0 ];

				t.parentNode.insertBefore ( s, t );

			}

		},

		loadCedarMaps : function () {

			window[ 'alopeykHandleMapsPublic' ] = alopeyk.wcshm.public.fn.handleMaps;

			if ( typeof alopeykHandleMapsPublic === 'function' ) {

				if ( typeof window.L != 'undefined' ) {

					window.cedarMapIsLoading = false;
					alopeyk.wcshm.public.fn.handleMaps();

				} else if ( window.cedarMapIsLoading ) {

					alopeyk.wcshm.public.vars.loadingMapInterval = setInterval ( function () {

						if ( window.L ) {

							window.cedarMapIsLoading = false;
							clearInterval ( alopeyk.wcshm.public.vars.loadingMapInterval );
							alopeyk.wcshm.public.fn.handleMaps();

						}

					}, 500 );

				} else {

					window.cedarMapIsLoading = true;
					alopeyk.wcshm.public.fn.injectScript ( alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.leaflet.js, alopeykHandleMapsPublic );
					alopeyk.wcshm.public.fn.injectStylesheet ( alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.leaflet.css );

				}

			}

		},

		handleMaps : function () {

			$j( document ).trigger ( 'alopeyk:public:map:loaded' );

		},

		initMaps : function () {

			$j( document ).on ( 'alopeyk:public:map:loaded', function () {

				var destinationLatInput         = $j( alopeyk.wcshm.public.vars.maps.destinationLatInput ),
					destinationLngInput         = $j( alopeyk.wcshm.public.vars.maps.destinationLngInput ),
					destinationAddressInput     = $j( alopeyk.wcshm.public.vars.maps.destinationAddressInput ),
					destinationNumberInput      = $j( alopeyk.wcshm.public.vars.maps.destinationNumberInput ),
					destinationUnitInput        = $j( alopeyk.wcshm.public.vars.maps.destinationUnitInput ),
					shipToDifferentAddressInput = $j( alopeyk.wcshm.public.vars.maps.shipToDifferentAddressInput ),
					editAddressButton           = $j( alopeyk.wcshm.public.vars.maps.editAddressButton ),
					billingCountry              = alopeyk.wcshm.public.vars.maps.billingCountry,
					shippingCountry             = alopeyk.wcshm.public.vars.maps.shippingCountry,
					billingCity                 = alopeyk.wcshm.public.vars.maps.billingCity,
					shippingCity                = alopeyk.wcshm.public.vars.maps.shippingCity,
					billingState                = alopeyk.wcshm.public.vars.maps.billingState,
					shippingState               = alopeyk.wcshm.public.vars.maps.shippingState,
					woocommerceCheckoutClass    = alopeyk.wcshm.public.vars.maps.woocommerceCheckoutClass,
					wcAddAlopeyk                = alopeyk.wcshm.public.vars.maps.wcAddAlopeyk;

				if ( destinationLatInput.length && destinationLngInput.length && destinationAddressInput.length ) {
					alopeyk.wcshm.public.fn.initDestinationLocator( destinationLatInput, destinationLngInput, destinationAddressInput, destinationNumberInput, destinationUnitInput, shipToDifferentAddressInput, editAddressButton, billingCountry, shippingCountry, billingCity, shippingCity, billingState, shippingState, woocommerceCheckoutClass, wcAddAlopeyk );
				}

			});

			alopeyk.wcshm.public.fn.loadCedarMaps();

		},

		setCurrentGeolocation : function ( forceRelocation ) {

			var destinationMap      = alopeyk.wcshm.public.vars.common.destinationMap,
				map                 = destinationMap ? destinationMap.mapObject : null,
				destinationLatInput = $j( alopeyk.wcshm.public.vars.maps.destinationLatInput ),
				destinationLngInput = $j( alopeyk.wcshm.public.vars.maps.destinationLngInput );

			if ( navigator.geolocation && map && destinationLatInput.length && destinationLngInput.length && ( forceRelocation || ( ! destinationLatInput.val().length && ! destinationLngInput.val().length ) || ( destinationLatInput.val() == alopeyk.wcshm.public.vars.maps.defaultCenter.lat && destinationngInput.val() == alopeyk.wcshm.public.vars.maps.defaultCenter.lng ) ) ) {

				navigator.geolocation.getCurrentPosition ( function ( position ) {

					if ( alopeyk.wcshm.public.vars.maps.fetchAddressConnection ) {
						alopeyk.wcshm.public.vars.maps.fetchAddressConnection.abort();
					}

					map.setView ({

						lat : position.coords.latitude,
						lng : position.coords.longitude

					});

					alopeyk.wcshm.public.vars.maps.defaultCenter.lat = position.coords.latitude;
					alopeyk.wcshm.public.vars.maps.defaultCenter.lng = position.coords.longitude;

					destinationLatInput.val ( position.coords.latitude );
					destinationLngInput.val ( position.coords.longitude ).trigger ( 'change' );

				});

			}

		},

		initDestinationLocator : function ( destinationLatInput, destinationLngInput, destinationAddressInput, destinationNumberInput, destinationUnitInput, shipToDifferentAddressInput, editAddressButton, billingCountry, shippingCountry, billingCity, shippingCity, billingState, shippingState, woocommerceCheckoutClass, wcAddAlopeyk ) {

            var markerMoved = false;
    var allowFormSubmission = false;
			var initialize = function () {

				var mapMarkerImageUrl  = alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.marker,

					mapCanvas = $j( '<div/>' ).attr ({

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
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorInputWrapperClass )

					}),

					autoCompleteList = $j( '<ul/>' ).attr ({

						id    : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorAutocompleteResultsClass ),
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorAutocompleteResultsClass )

					}),

					mapContainer = $j( '<div/>' ).attr ({

						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.mapContainerClass ),

					}),

					destinationLocatorCta = $j( '<button/>' ).attr ({

						type  : 'button',
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorCtaClass ),

					}),

					markerImage = $j( '<img/>' ).attr ({

						src   : mapMarkerImageUrl,
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.mapMarkerIconClass )

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
				.append( destinationLocatorCta )
				.prepend( markerImage );

				var mapOptions = {

						zoom    : alopeyk.wcshm.public.vars.maps.defaultZoom,
						maxZoom : alopeyk.wcshm.public.vars.maps.maxZoom,
						center  : [

							destinationLatInput.val().length ? parseFloat( destinationLatInput.val() ) : alopeyk.wcshm.public.vars.maps.defaultCenter.lat,
							destinationLngInput.val().length ? parseFloat( destinationLngInput.val() ) : alopeyk.wcshm.public.vars.maps.defaultCenter.lng

						],
						zoomControl     : false,
						gestureHandling : false,
						gestureHandlingText: {

							touch: alopeyk.wcshm.public.fn.translate ( 'Use two fingers to move the map' ),
							scroll: alopeyk.wcshm.public.fn.translate ( 'Use ctrl + scroll to zoom the map' ),
							scrollMac: alopeyk.wcshm.public.fn.translate ( 'Use ⌘ + scroll to zoom the map' ),
							
						}

					},

					map = L.map( mapCanvas.get ( 0 ), mapOptions),

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
							lat          : map.getCenter().lat,
							lng          : map.getCenter().lng

						}, function ( response ) {

              destinationAutocompleteInput.on('mousedown touchstart', function(event) {     
                if ($j(this).val() !== '') {
                    $j(this).val(''); 
                }
            });


							if ( response ) {

								autoCompleteList.empty();
								destinationAutocompleteInput.val ( response.data.address );
								$j(document).trigger("update_checkout");

								if ( $j( '.' + wcAddAlopeyk ).length ) {

									var address = response.success ? response.data.address : '';
									destinationAddressInput.val ( address );

								}
								destinationAddressInput.trigger ( 'change' );

							}

						}).always ( function () {

							destinationAutocompleteInputWrapper.removeClass ( alopeyk.wcshm.public.vars.common.loadingClass );

						});

					},

					saveData = function ( update ) {

						if ( alopeyk.wcshm.public.vars.updateShippingMethod ) {

							alopeyk.wcshm.public.vars.updateShippingMethod.abort();

						}

						destinationAddressInput.parents ( 'form' )
						.find ( 'button, input[type="button"], input[type="submit"]' )
						.filter ( function () {

							return ! $j( this ).is ( ':disabled' );

						})
						.prop ( 'disabled', true )
						.data ( 'alopeyk-disable', true );

						alopeyk.wcshm.public.vars.updateShippingMethod = $j.post ( alopeyk.wcshm.public.vars.common.info.ajaxOptions.url, {

							nonce          : alopeyk.wcshm.public.vars.common.info.ajaxOptions.nonce,
							action         : alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.id,
							request        : 'set_session',
							authenticate   : true,
							lat            : map.getCenter().lat,
							lng            : map.getCenter().lng,
							address        : destinationAddressInput.val(),
							number         : destinationNumberInput.val(),
							unit           : destinationUnitInput.val(),
							payment_method : $j( paymentMethodInputSelector + ':checked' ).val()

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

					preLoadCities = function () {

						var selectedCountryType = billingCountry;

						if ( shipToDifferentAddressInput.prop ( 'checked' ) ) {

							selectedCountryType = shippingCountry;

						}
						
						if ( $j( selectedCountryType ).val() == 'IR' ) {

							$j( woocommerceCheckoutClass ).addClass ( wcAddAlopeyk );
							fetchAddressFromLocation();

						}

					},
					cityProvinceValue = function ( type, cityProvince ) {

						var valueOne = $j( type + ' option[value="' + cityProvince + '"]' ).val(),
							valueTwo = $j( type + ' option' ).filter(function () { return $j(this).html() == cityProvince; }).val();

						if ( valueOne && valueOne.length > 0  ) {

							return valueOne;

						} else if ( valueTwo && valueTwo.length > 0  ) {

							return valueTwo;

						} else {

							return '';

						}

					},
					changeMapLatlng = function ( type ) {

						var city_type    = '#' + type + '_city',
							country_type = '#' + type + '_country',
							state_type   = '#' + type + '_state';

						if ( $j( country_type ).val () == 'IR' ) {

							var selectedCity   = $j( city_type ).find ( 'option:selected' ),
								selectedState  = $j( state_type ).find ( 'option:selected' ),
								cedarmapSatate = null,
								latlng         = null;

							for (var i = 0; i < cedarmap_data.provinces.length; i++) {
								if( cedarmap_data.provinces[i].name == selectedState.text() ) {
									cedarmapSatate = cedarmap_data.provinces[i].name_en.toLowerCase();
									latlng = cedarmap_data.provinces[i].location.center;
                  latlng = latlng.split (',');
									break;
								}
							}

							if ( selectedCity.text().length > 0 && cedarmapSatate ) {

								var keys = Object.keys(cedarmap_data.cities[cedarmapSatate]);
								for (var i = 0; i < keys.length; i++) {
									if( cedarmap_data.cities[cedarmapSatate][keys[i]].name == selectedCity.text() ) {
										latlng = cedarmap_data.cities[cedarmapSatate][keys[i]].location.center;
										latlng = latlng.split (',');
										break;
									}
								}

							}

							if ( latlng && latlng.length > 0 ) {

								destinationLatInput.val ( latlng[0] );
								destinationLngInput.val ( latlng[1] ).trigger ( 'change' );

							}

						} else {

							$j( woocommerceCheckoutClass ).removeClass ( wcAddAlopeyk );

						}

					},

					paymentMethodInputSelector = alopeyk.wcshm.public.vars.common.paymentMethodInput;
					preLoadCities();

				$j( document ).on ( 'change', billingCity, function ( event ) {

					if ( ! shipToDifferentAddressInput.prop ( 'checked' ) ) {

						changeMapLatlng ( 'billing' );

					}

				}).on ( 'change', shippingCity, function ( event ) {

					if ( shipToDifferentAddressInput.prop ( 'checked' ) ) {

						changeMapLatlng ( 'shipping' );

					}

				}).on ({ 'updated_checkout' : function ( e ) {

                  $j.post ( alopeyk.wcshm.public.vars.common.info.ajaxOptions.url, {
        
                    nonce        : alopeyk.wcshm.public.vars.common.info.ajaxOptions.nonce,
                    action       : alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.id,
                    request      : 'check_shipping_rates'
        
                  }, function ( response ) {
        
                    if ( response ) {
                      //console.log(response.data.showMap);
                      if (response.data && response.data.showMap) {
                        $j( woocommerceCheckoutClass ).addClass ( wcAddAlopeyk );
                      } else {
                        $j( woocommerceCheckoutClass ).removeClass ( wcAddAlopeyk );
                      }
                    }
        
                  });
        
                }});

				L.control.zoom ({
					position : 'bottomleft'
				})
				.addTo ( map );

				L.tileLayer( alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.api_url.replace ( '{{TOKEN}}', alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.api_key ) ).addTo ( map );

				alopeyk.wcshm.public.vars.common.destinationMap = {

					mapObject : map

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
				
                    map.on('dragend', function () {
                        markerMoved = true;
                       //console.log("Marker moved!"); 
                    });
                
                    $j(document).on('click', '#place_order', function (e) {
                        if (!markerMoved) {
                            e.preventDefault(); 
                    
                            var popup = $j('<div>', {
                                id: 'awcshm-map-popup',
                                class: 'awcshm-map-popup'
                            }).append(
                                $j('<div>', {
                                    class: 'awcshm-map-popup-content'
                                }).append(
                                    $j('<p>').text('لطفاً موقعیت مکانی خود را روی نقشه مشخص نمایید.'),
                                    $j('<button>', {
                                        id: 'close-popup',
                                        text: 'متوجه شدم'
                                    })
                                )
                            );
                    
                            $j('body').append(popup);
                    
                            $j('#awcshm-map-popup').fadeIn();
                    
                            $j('html, body').animate({
                                scrollTop: $j('.awcshm-map-container').offset().top - 100
                            }, 1000, function () {  
                                $j('.awcshm-map-container').addClass('awcshm-map-highlight');
                    
                                setTimeout(function () {
                                    $j('.awcshm-map-container').removeClass('awcshm-map-highlight');
                                }, 5000); 
                            });
                    
                            $j(document).on('click', '#close-popup', function () {
                                $j('#awcshm-map-popup').fadeOut(function () {
                                    $j(this).remove(); 
                                });
                            });
                    
                            $j(document).on('click', '#map-popup', function (e) {
                                if (e.target === this) {
                                    $j('#awcshm-map-popup').fadeOut(function () {
                                        $j(this).remove(); 
                                    });
                                }
                            });
                    
                            return false; 
                        }
                    });


				map.on ( 'move dragend zoomend', function () {

					destinationLatInput.val ( map.getCenter().lat );
					destinationLngInput.val ( map.getCenter().lng ).trigger ( 'change' );

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
							map.setView ( location );
							fetchAddressFromLocation();

						}, alopeyk.wcshm.public.vars.maps.positionKeyupDelay );

					}

				});

				shipToDifferentAddressInput.on ( 'change', function () {

					var checked = $j( this ).prop ( 'checked' );

					if ( ! checked ) {

						changeMapLatlng ( 'billing' );

					} else {

						changeMapLatlng ( 'shipping' );

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
									lat          : map.getCenter().lat,
									lng          : map.getCenter().lng

								}, function ( response ) {

									if ( response && response.success && response.data.length ) {

										destinationAutocompleteInput.focus();
										
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
														map.setView ( location );
														map.setZoom ( alopeyk.wcshm.public.vars.maps.defaultZoom );
														destinationLatInput.val ( location.lat );
														destinationLngInput.val ( location.lng );
														alopeyk.wcshm.public.vars.maps.autocompleteInputValue = address;

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

		init : function () {},

	};



	/*=========================================
	=            Calling Functions            =
	=========================================*/

	/**
	 *
	 * Registered Events
	 *
	 **/

	jQuery(function(){
		alopeyk.wcshm.public.fn.init();
	});

	$j( window ).on ({
		'load' : function ( e ) {
			alopeyk.wcshm.public.fn.initMaps();
		},
	});

})( jQuery );

var cedarmap_data = {
  provinces: [
    {
      name: "آذربایجان شرقی",
      name_en: "East Azerbaijan",
      location: {
        bb: {
          ne: "39.426533399999997,48.342766099999999",
          sw: "36.746356300000002,45.084802000000003",
        },
        center: "38.0143859921995,46.710746318798797",
      },
    },
    {
      name: "آذربایجان غربی",
      name_en: "West Azerbaijan",
      location: {
        bb: {
          ne: "39.771215499999997,47.396033099999997",
          sw: "35.968154400000003,44.038775100000002",
        },
        center: "37.656842714424499,45.3430836072963",
      },
    },
    {
      name: "اردبیل",
      name_en: "Ardabil",
      location: {
        bb: {
          ne: "39.705692599999999,48.922960500000002",
          sw: "37.102777099999997,47.292544599999999",
        },
        center: "38.438869919145603,48.081257014834598",
      },
    },
    {
      name: "اصفهان",
      name_en: "Esfahan",
      location: {
        bb: {
          ne: "34.504502899999999,55.494326899999997",
          sw: "30.701300400000001,49.641263000000002",
        },
        center: "33.1246981363172,52.500008368196397",
      },
    },
    {
      name: "ایلام",
      name_en: "Ilam",
      location: {
        bb: {
          ne: "34.038021899999997,48.044158799999998",
          sw: "32.043997900000001,45.679287799999997",
        },
        center: "33.120932964951699,46.919897726810099",
      },
    },
    {
      name: "البرز",
      name_en: "Alborz",
      location: {
        bb: {
          ne: "36.342407399999999,51.461217900000001",
          sw: "35.544852599999999,50.163162",
        },
        center: "35.967560584248801,50.805230553272303",
      },
    },
    {
      name: "بوشهر",
      name_en: "Boushehr",
      location: {
        bb: {
          ne: "30.2876592,52.935341999999999",
          sw: "27.2950397,50.105617000000002",
        },
        center: "28.8249915574197,51.392994160418603",
      },
    },
    {
      name: "تهران",
      name_en: "Tehran",
      location: {
        bb: {
          ne: "36.134299599999999,53.155692700000003",
          sw: "34.861731800000001,50.332803499999997",
        },
        center: "35.6996468,51.3377773",
      },
    },
    {
      name: "چهارمحال و بختیاری",
      name_en: "Khaharmahal And Bakhtiyari",
      location: {
        bb: {
          ne: "32.809510099999997,51.434142100000003",
          sw: "31.150817199999999,49.501517700000001",
        },
        center: "32.040083983047602,50.642669823490998",
      },
    },
    {
      name: "خراسان جنوبی",
      name_en: "South Khorasan",
      location: {
        bb: {
          ne: "35.0943404,60.933204699999997",
          sw: "30.516734,55.381466600000003",
        },
        center: "32.8340446498731,58.373304067453603",
      },
    },
    {
      name: "خراسان رضوی",
      name_en: "Khorasan Razavi",
      location: {
        bb: {
          ne: "37.7008261,61.268053399999999",
          sw: "33.460252199999999,56.227891499999998",
        },
        center: "35.548274553368202,58.938014369481898",
      },
    },
    {
      name: "خراسان شمالی",
      name_en: "North Khorasan",
      location: {
        bb: {
          ne: "38.286606300000003,58.424430200000003",
          sw: "36.576115999999999,55.903365100000002",
        },
        center: "37.408853882971201,57.134679963284803",
      },
    },
    {
      name: "خوزستان",
      name_en: "Khouzestan",
      location: {
        bb: {
          ne: "32.994282300000002,50.552046400000002",
          sw: "29.882294600000002,47.666704099999997",
        },
        center: "31.438509901595399,49.012108321559602",
      },
    },
    {
      name: "یزد",
      name_en: "Yazd",
      location: {
        bb: {
          ne: "33.362711300000001,56.6559308",
          sw: "29.595357700000001,52.8028081",
        },
        center: "31.806780554950699,54.609748576743797",
      },
    },
    {
      name: "زنجان",
      name_en: "Zanjan",
      location: {
        bb: {
          ne: "37.252113600000001,49.436378900000001",
          sw: "35.548088200000002,47.1736687",
        },
        center: "36.497404312174503,48.383017686490803",
      },
    },
    {
      name: "سیستان و بلوچستان",
      name_en: "Sistan And Balouchestan",
      location: {
        bb: {
          ne: "31.478568599999999,63.321837299999999",
          sw: "25.0641499,58.829422000000001",
        },
        center: "27.919708340887698,60.724740168955698",
      },
    },
    {
      name: "سمنان",
      name_en: "Semnan",
      location: {
        bb: {
          ne: "37.328185900000001,57.056779800000001",
          sw: "34.239121699999998,51.832748899999999",
        },
        center: "35.424796420725798,54.672255062842297",
      },
    },
    {
      name: "فارس",
      name_en: "Fars",
      location: {
        bb: {
          ne: "31.670557599999999,55.578491200000002",
          sw: "27.048347199999998,50.602467900000001",
        },
        center: "29.166758984327199,53.259180850692701",
      },
    },
    {
      name: "قزوین",
      name_en: "Qazvin",
      location: {
        bb: {
          ne: "36.8171228,50.8572287",
          sw: "35.405836299999997,48.725732800000003",
        },
        center: "36.089875351724601,49.7665805921052",
      },
    },
    {
      name: "قم",
      name_en: "Qom",
      location: {
        bb: {
          ne: "35.182934500000002,51.964952599999997",
          sw: "34.149622399999998,50.086584799999997",
        },
        center: "34.695388533144502,51.0264227073176",
      },
    },
    {
      name: "کردستان",
      name_en: "Kordestan",
      location: {
        bb: {
          ne: "36.462460999999998,48.249693499999999",
          sw: "34.738477400000001,45.554956099999998",
        },
        center: "35.683279003202102,46.983574956856401",
      },
    },
    {
      name: "کرمان",
      name_en: "Kerman",
      location: {
        bb: {
          ne: "31.962196200000001,59.571862799999998",
          sw: "26.483251500000001,54.340259400000001",
        },
        center: "29.614529285139501,57.296329377407297",
      },
    },
    {
      name: "کرمانشاه",
      name_en: "Kermanshah",
      location: {
        bb: {
          ne: "35.281867800000001,48.1046111",
          sw: "33.685353300000003,45.403356700000003",
        },
        center: "34.432759101946502,46.684986279967397",
      },
    },
    {
      name: "کهکیلویه و بویراحمد",
      name_en: "Kohgiluye And Boyerahmad",
      location: {
        bb: {
          ne: "31.481598699999999,51.890602299999998",
          sw: "29.9279872,49.889684600000002",
        },
        center: "30.770646199538099,50.835256949480502",
      },
    },
    {
      name: "گیلان",
      name_en: "Gilan",
      location: {
        bb: {
          ne: "38.453352199999998,50.603814499999999",
          sw: "36.562787899999996,48.569740099999997",
        },
        center: "37.253546522638899,49.490358847636003",
      },
    },
    {
      name: "گلستان",
      name_en: "Golestan",
      location: {
        bb: {
          ne: "38.124448600000001,56.313564100000001",
          sw: "36.495617000000003,53.858131899999997",
        },
        center: "37.318828503073,55.090715716345002",
      },
    },
    {
      name: "لرستان",
      name_en: "Lorestan",
      location: {
        bb: {
          ne: "34.377642799999997,50.019260299999999",
          sw: "32.651518099999997,46.8384924",
        },
        center: "33.462384940443897,48.435406934996202",
      },
    },
    {
      name: "مازندران",
      name_en: "Mazandaran",
      location: {
        bb: {
          ne: "36.961835600000001,54.132152099999999",
          sw: "35.762649699999997,50.351557499999998",
        },
        center: "36.377236320151802,52.377763352020601",
      },
    },
    {
      name: "مرکزی",
      name_en: "Markazi",
      location: {
        bb: {
          ne: "35.571534100000001,51.050832300000003",
          sw: "33.383440800000002,48.949050499999998",
        },
        center: "34.489226608330902,49.957566383054797",
      },
    },
    {
      name: "هرمزگان",
      name_en: "Hormozgan",
      location: {
        bb: {
          ne: "28.880206999999999,59.264794199999997",
          sw: "25.4104633,52.736308200000003",
        },
        center: "27.028709386044898,56.4970765417346",
      },
    },
    {
      name: "همدان",
      name_en: "Hamedan",
      location: {
        bb: {
          ne: "35.733034600000003,49.470902199999998",
          sw: "34.000752400000003,47.795561399999997",
        },
        center: "34.885229291427102,48.610567155438503",
      },
    },
  ],
  cities: {
    "east azerbaijan": {
      "khoda afarin": {
        name: "خداآفرین",
        name_en: "Khoda Afarin",
        location: {
          bb: {
            ne: "39.426533399999997,47.511330800000003",
            sw: "38.737085,46.461328799999997",
          },
          center: "39.065951716058301,46.948854454997402",
        },
      },
      bonab: {
        name: "بناب",
        name_en: "Bonab",
        location: {
          bb: {
            ne: "37.535220000000002,46.199440899999999",
            sw: "37.1660836,45.751148999999998",
          },
          center: "37.325653243349599,45.9954672681015",
        },
      },
      jolfa: {
        name: "جلفا",
        name_en: "Jolfa",
        location: {
          bb: {
            ne: "38.991503999999999,46.524853999999998",
            sw: "38.646925299999999,45.283975599999998",
          },
          center: "38.836714845772001,45.868874609859702",
        },
      },
      sarab: {
        name: "سراب",
        name_en: "Sarab",
        location: {
          bb: {
            ne: "38.249112599999997,47.937519299999998",
            sw: "37.740192,46.997453700000001",
          },
          center: "37.977900752252602,47.467499171174197",
        },
      },
      malekan: {
        name: "ملکان",
        name_en: "Malekan",
        location: {
          bb: {
            ne: "37.283659499999999,46.442270999999998",
            sw: "36.939694799999998,45.910789899999997",
          },
          center: "37.127159946308097,46.202758168407698",
        },
      },
      miyaneh: {
        name: "میانه",
        name_en: "Miyaneh",
        location: {
          bb: {
            ne: "37.892368900000001,48.342766099999999",
            sw: "37.075451700000002,47.208670900000001",
          },
          center: "37.466516778055201,47.723731220472601",
        },
      },
      shabestar: {
        name: "شبستر",
        name_en: "Shabestar",
        location: {
          bb: {
            ne: "38.481963,46.344925400000001",
            sw: "37.998194099999999,45.084802000000003",
          },
          center: "38.241792582939397,45.746394032726698",
        },
      },
      "bostan abad": {
        name: "بستان آباد",
        name_en: "Bostan Abad",
        location: {
          bb: {
            ne: "38.095091600000003,47.244316699999999",
            sw: "37.539656299999997,46.461235899999998",
          },
          center: "37.803203308495299,46.813094973560801",
        },
      },
      charuymaq: {
        name: "چاراویماق",
        name_en: "Charuymaq",
        location: {
          bb: {
            ne: "37.365501999999999,47.632638",
            sw: "36.746356300000002,46.652243499999997",
          },
          center: "37.061818846939097,47.112573760843802",
        },
      },
      ajabshir: {
        name: "عجب شیر",
        name_en: "Ajabshir",
        location: {
          bb: {
            ne: "37.702602300000002,46.331822299999999",
            sw: "37.374924999999998,45.7058885",
          },
          center: "37.547770355693203,46.025646750912003",
        },
      },
      haris: {
        name: "هریس",
        name_en: "Haris",
        location: {
          bb: {
            ne: "38.404369600000003,47.380702800000002",
            sw: "38.053643399999999,46.373531300000003",
          },
          center: "38.227694815489201,46.823401591984997",
        },
      },
      varzaqan: {
        name: "ورزقان",
        name_en: "Varzaqan",
        location: {
          bb: {
            ne: "38.794930700000002,46.8692007",
            sw: "38.3714941,46.025773899999997",
          },
          center: "38.600535756514397,46.452456302897602",
        },
      },
      maragheh: {
        name: "مراغه",
        name_en: "Maragheh",
        location: {
          bb: {
            ne: "37.736606299999998,46.721280999999998",
            sw: "37.015703600000002,46.1337683",
          },
          center: "37.3610269272775,46.4211561198304",
        },
      },
      kaleybar: {
        name: "کلیبر",
        name_en: "Kaleybar",
        location: {
          bb: {
            ne: "39.287144400000003,47.526145200000002",
            sw: "38.611746099999998,46.683494799999998",
          },
          center: "38.937591803823501,47.138108595950797",
        },
      },
      azarshahr: {
        name: "آذرشهر",
        name_en: "Azarshahr",
        location: {
          bb: {
            ne: "37.894367899999999,46.163066200000003",
            sw: "37.563904800000003,45.657770200000002",
          },
          center: "37.727929035453499,45.898248291359501",
        },
      },
      oskou: {
        name: "اسکو",
        name_en: "Oskou",
        location: {
          bb: {
            ne: "38.0433947,46.347815900000001",
            sw: "37.681635499999999,45.406767899999998",
          },
          center: "37.868206846002998,45.866718676854397",
        },
      },
      tabriz: {
        name: "تبریز",
        name_en: "Tabriz",
        location: {
          bb: {
            ne: "38.485131600000003,46.670341200000003",
            sw: "37.718916499999999,45.855426700000002",
          },
          center: "38.0651720158152,46.289841518480799",
        },
      },
      ahar: {
        name: "اهر",
        name_en: "Ahar",
        location: {
          bb: {
            ne: "39.083245900000001,47.6043029",
            sw: "38.298621400000002,46.743225799999998",
          },
          center: "38.620143735814302,47.213684563100301",
        },
      },
      marand: {
        name: "مرند",
        name_en: "Marand",
        location: {
          bb: {
            ne: "38.880144199999997,46.199561799999998",
            sw: "38.291122999999999,45.2024556",
          },
          center: "38.5578299346177,45.659079785653901",
        },
      },
      hashtroud: {
        name: "هشترود",
        name_en: "Hashtroud",
        location: {
          bb: {
            ne: "37.641285500000002,47.317837500000003",
            sw: "37.147683000000001,46.466689000000002",
          },
          center: "37.4241905484513,46.904764668147997",
        },
      },
    },
    "west azerbaijan": {
      piranshahr: {
        name: "پیرانشهر",
        name_en: "Piranshahr",
        location: {
          bb: {
            ne: "36.932398800000001,45.561512700000002",
            sw: "36.404397699999997,44.836644100000001",
          },
          center: "36.681302208623997,45.225220127853198",
        },
      },
      chaldoran: {
        name: "چالدران",
        name_en: "Chaldoran",
        location: {
          bb: {
            ne: "39.405530599999999,44.792827199999998",
            sw: "38.785448000000002,44.038775100000002",
          },
          center: "39.092399769097703,44.372436336021302",
        },
      },
      "shahin dezh": {
        name: "شاهین دژ",
        name_en: "Shahin Dezh",
        location: {
          bb: {
            ne: "36.934842500000002,46.988076100000001",
            sw: "36.395288399999998,46.242159299999997",
          },
          center: "36.677431203746899,46.644376640256603",
        },
      },
      makou: {
        name: "ماکو",
        name_en: "Makou",
        location: {
          bb: {
            ne: "39.771215499999997,44.880271399999998",
            sw: "39.135004899999998,44.286476700000001",
          },
          center: "39.444196433896799,44.589334584843797",
        },
      },
      oroumiyeh: {
        name: "ارومیه",
        name_en: "Oroumiyeh",
        location: {
          bb: {
            ne: "38.158959299999999,45.431850900000001",
            sw: "37.1154589,44.395717300000001",
          },
          center: "37.617095864462598,44.927999617726897",
        },
      },
      oshnavieh: {
        name: "اشنویه",
        name_en: "Oshnavieh",
        location: {
          bb: {
            ne: "37.282554699999999,45.262628100000001",
            sw: "36.890445399999997,44.748040500000002",
          },
          center: "37.069007880897203,45.0588687068146",
        },
      },
      miyandoab: {
        name: "میاندوآب",
        name_en: "Miyandoab",
        location: {
          bb: {
            ne: "37.293213999999999,46.894998600000001",
            sw: "36.787262400000003,45.609630799999998",
          },
          center: "37.011326011647299,46.1957519189301",
        },
      },
      showt: {
        name: "شوط",
        name_en: "Showt",
        location: {
          bb: {
            ne: "39.357557499999999,45.057524600000001",
            sw: "38.945799800000003,44.646078600000003",
          },
          center: "39.138037998997497,44.837953723881803",
        },
      },
      sardasht: {
        name: "سردشت",
        name_en: "Sardasht",
        location: {
          bb: {
            ne: "36.476326999999998,45.7021072",
            sw: "35.968154499999997,45.237242199999997",
          },
          center: "36.230435545706399,45.460908031167499",
        },
      },
      salmas: {
        name: "سلماس",
        name_en: "Salmas",
        location: {
          bb: {
            ne: "38.369647399999998,45.104297600000002",
            sw: "37.845559899999998,44.221807599999998",
          },
          center: "38.1391244651861,44.6550261384767",
        },
      },
      mahabad: {
        name: "مهاباد",
        name_en: "Mahabad",
        location: {
          bb: {
            ne: "37.043543,46.0638814",
            sw: "36.271771200000003,45.438863499999997",
          },
          center: "36.661737000872797,45.736529809146901",
        },
      },
      naqadeh: {
        name: "نقده",
        name_en: "Naqadeh",
        location: {
          bb: {
            ne: "37.166482500000001,45.696129499999998",
            sw: "36.753554800000003,45.235087200000002",
          },
          center: "36.9776088804648,45.446706646584197",
        },
      },
      boukan: {
        name: "بوکان",
        name_en: "Boukan",
        location: {
          bb: {
            ne: "36.865880199999999,46.505541700000002",
            sw: "36.216079000000001,45.696918599999997",
          },
          center: "36.5566706597142,46.141461604489201",
        },
      },
      takab: {
        name: "تکاب",
        name_en: "Takab",
        location: {
          bb: {
            ne: "36.837046999999998,47.396033099999997",
            sw: "36.243562099999998,46.807920799999998",
          },
          center: "36.505685749248798,47.124048250151503",
        },
      },
      khoy: {
        name: "خوی",
        name_en: "Khoy",
        location: {
          bb: {
            ne: "38.976030299999998,45.344419600000002",
            sw: "38.315762900000003,44.251814699999997",
          },
          center: "38.604780865370699,44.761679566682801",
        },
      },
      poldasht: {
        name: "پلدشت",
        name_en: "Poldasht",
        location: {
          bb: {
            ne: "39.598380900000002,45.461093099999999",
            sw: "38.935654399999997,44.769050300000004",
          },
          center: "39.1999620472792,45.091304615656803",
        },
      },
      chaypareh: {
        name: "چایپاره",
        name_en: "Chaypareh",
        location: {
          bb: {
            ne: "39.007247800000002,45.361183699999998",
            sw: "38.745073099999999,44.669425099999998",
          },
          center: "38.879924485067498,45.024799076389598",
        },
      },
    },
    ardabil: {
      nir: {
        name: "نیر",
        name_en: "Nir",
        location: {
          bb: {
            ne: "38.1894578,48.3713129",
            sw: "37.8007852,47.824701599999997",
          },
          center: "37.991017645728803,48.106707062500902",
        },
      },
      "pars abad": {
        name: "پارس آباد",
        name_en: "Pars Abad",
        location: {
          bb: {
            ne: "39.705692599999999,48.171275700000002",
            sw: "39.209664799999999,47.3628231",
          },
          center: "39.474634762111101,47.736211643833997",
        },
      },
      germi: {
        name: "گرمی",
        name_en: "Germi",
        location: {
          bb: {
            ne: "39.224483399999997,48.336581799999998",
            sw: "38.827797699999998,47.499795599999999",
          },
          center: "39.032294155278898,47.8789947121832",
        },
      },
      "bileh savar": {
        name: "بیله سوار",
        name_en: "Bileh Savar",
        location: {
          bb: {
            ne: "39.5625918,48.367623999999999",
            sw: "39.122205999999998,47.616638999999999",
          },
          center: "39.340009561713998,47.972991986007401",
        },
      },
      khalkhal: {
        name: "خلخال",
        name_en: "Khalkhal",
        location: {
          bb: {
            ne: "37.872598500000002,48.922960500000002",
            sw: "37.102777099999997,48.146104700000002",
          },
          center: "37.426862092314501,48.541842556187298",
        },
      },
      ardabil: {
        name: "اردبیل",
        name_en: "Ardabil",
        location: {
          bb: {
            ne: "38.641713199999998,48.687978800000003",
            sw: "37.929040499999999,47.846085000000002",
          },
          center: "38.262754492568298,48.271164550641402",
        },
      },
      "meshgin shahr": {
        name: "مشگین شهر",
        name_en: "Meshgin Shahr",
        location: {
          bb: {
            ne: "38.899902599999997,48.292127899999997",
            sw: "38.182593199999999,47.292544700000001",
          },
          center: "38.558067065603701,47.765259022815599",
        },
      },
      "sar ein": {
        name: "سرعین",
        name_en: "Sar Ein",
        location: {
          bb: {
            ne: "38.263464300000003,48.247802999999998",
            sw: "38.098211900000003,47.7801714",
          },
          center: "38.193239443767503,48.009060268367399",
        },
      },
      namin: {
        name: "نمین",
        name_en: "Namin",
        location: {
          bb: {
            ne: "38.613711199999997,48.689357299999998",
            sw: "38.159837000000003,48.279520900000001",
          },
          center: "38.390404086296599,48.467106504346702",
        },
      },
      kowsar: {
        name: "کوثر",
        name_en: "Kowsar",
        location: {
          bb: {
            ne: "37.974331599999999,48.582568000000002",
            sw: "37.454951199999996,48.019606000000003",
          },
          center: "37.715790600099197,48.274627846576102",
        },
      },
    },
    esfahan: {
      "tiran-o karvan": {
        name: "تیران و کرون",
        name_en: "Tiran-o Karvan",
        location: {
          bb: {
            ne: "33.0553332,51.242797000000003",
            sw: "32.507945100000001,50.547681500000003",
          },
          center: "32.783228945745897,50.959171098910801",
        },
      },
      kashan: {
        name: "کاشان",
        name_en: "Kashan",
        location: {
          bb: {
            ne: "34.4443451,51.815725399999998",
            sw: "33.495893899999999,50.913410499999998",
          },
          center: "33.920791125878999,51.2783012639322",
        },
      },
      faridan: {
        name: "فریدن",
        name_en: "Faridan",
        location: {
          bb: {
            ne: "33.172325499999999,50.633969499999999",
            sw: "32.7423863,50.186650200000003",
          },
          center: "32.974687303821803,50.401418242378803",
        },
      },
      barkhar: {
        name: "برخوار",
        name_en: "Barkhar",
        location: {
          bb: {
            ne: "33.312897599999999,52.071999400000003",
            sw: "32.743020100000003,51.385646399999999",
          },
          center: "33.0201867517141,51.753178410434799",
        },
      },
      golpaygan: {
        name: "گلپایگان",
        name_en: "Golpaygan",
        location: {
          bb: {
            ne: "33.652636999999999,50.767189700000003",
            sw: "33.235170799999999,49.947540799999999",
          },
          center: "33.447081253186703,50.386485034047098",
        },
      },
      "najaf abad": {
        name: "نجف آباد",
        name_en: "Najaf Abad",
        location: {
          bb: {
            ne: "33.316725599999998,51.4931646",
            sw: "32.490079299999998,50.602635499999998",
          },
          center: "32.960074735137603,51.054819701180698",
        },
      },
      natanz: {
        name: "نطنز",
        name_en: "Natanz",
        location: {
          bb: {
            ne: "33.966981099999998,52.340206299999998",
            sw: "33.201752399999997,51.4188227",
          },
          center: "33.547779608511703,51.8767112546645",
        },
      },
      dehaqan: {
        name: "دهاقان",
        name_en: "Dehaqan",
        location: {
          bb: {
            ne: "32.256166399999998,51.801124000000002",
            sw: "31.754936799999999,51.376656699999998",
          },
          center: "31.979446699433101,51.615193227458498",
        },
      },
      khansar: {
        name: "خوانسار",
        name_en: "Khansar",
        location: {
          bb: {
            ne: "33.415792199999999,50.697843800000001",
            sw: "33.075717099999999,50.022455100000002",
          },
          center: "33.249765377630901,50.381405013707599",
        },
      },
      "fereydoun shahr": {
        name: "فریدونشهر",
        name_en: "Fereydoun Shahr",
        location: {
          bb: {
            ne: "33.0808252,50.324692200000001",
            sw: "32.614712400000002,49.641263000000002",
          },
          center: "32.852278098668002,50.002045918578403",
        },
      },
      "khour-o biyabanak": {
        name: "خور و بیابانک",
        name_en: "Khour-o Biyabanak",
        location: {
          bb: {
            ne: "34.249682800000002,55.494326899999997",
            sw: "33.093586500000001,54.1705325",
          },
          center: "33.794565191224699,54.915801560344804",
        },
      },
      samirom: {
        name: "سمیرم",
        name_en: "Samirom",
        location: {
          bb: {
            ne: "31.852073799999999,51.9691592",
            sw: "30.701300400000001,51.2584175",
          },
          center: "31.281193318735301,51.600143610843297",
        },
      },
      "shahin shahr va meymeh": {
        name: "شاهین شهر و میمه",
        name_en: "Shahin Shahr va Meymeh",
        location: {
          bb: {
            ne: "33.730113500000002,51.6547853",
            sw: "32.732666700000003,50.584355000000002",
          },
          center: "33.298980065356503,51.154412439793603",
        },
      },
      chadegan: {
        name: "چادگان",
        name_en: "Chadegan",
        location: {
          bb: {
            ne: "32.963443599999998,50.853928500000002",
            sw: "32.536534699999997,50.217296900000001",
          },
          center: "32.748860405240997,50.533093524766699",
        },
      },
      "buin va miandasht ": {
        name: "بوئین و میاندشت",
        name_en: "Buin va Miandasht ",
        location: {
          bb: {
            ne: "33.359484999999999,50.328739300000002",
            sw: "32.971796699999999,49.868134599999998",
          },
          center: "33.156538510010101,50.089558958258003",
        },
      },
      ardestan: {
        name: "اردستان",
        name_en: "Ardestan",
        location: {
          bb: {
            ne: "34.429142599999999,53.219484299999998",
            sw: "32.876055100000002,51.824191999999996",
          },
          center: "33.5889821214504,52.612799657163599",
        },
      },
      mobarakeh: {
        name: "مبارکه",
        name_en: "Mobarakeh",
        location: {
          bb: {
            ne: "32.477780699999997,51.823724300000002",
            sw: "32.065182900000003,51.221030499999998",
          },
          center: "32.276749688704797,51.510262350781197",
        },
      },
      falavarjan: {
        name: "فلاورجان",
        name_en: "Falavarjan",
        location: {
          bb: {
            ne: "32.618487700000003,51.681355799999999",
            sw: "32.422863300000003,51.419789799999997",
          },
          center: "32.514583492853603,51.536890419094398",
        },
      },
      lenjan: {
        name: "لنجان",
        name_en: "Lenjan",
        location: {
          bb: {
            ne: "32.5618683,51.470016899999997",
            sw: "32.186384799999999,50.967977900000001",
          },
          center: "32.395558171042602,51.223215774986201",
        },
      },
      "aran-o bidgol": {
        name: "آران و بیدگل",
        name_en: "Aran-o Bidgol",
        location: {
          bb: {
            ne: "34.504502899999999,52.425609399999999",
            sw: "33.7892291,51.297368300000002",
          },
          center: "34.171535409024003,51.872504746664603",
        },
      },
      isfahan: {
        name: "اصفهان",
        name_en: "Isfahan",
        location: {
          bb: {
            ne: "33.011699100000001,53.2038212",
            sw: "31.490936300000001,51.5253224",
          },
          center: "32.245853541835302,52.442471460304603",
        },
      },
      "khomeini shahr": {
        name: "خمینی شهر",
        name_en: "Khomeini Shahr",
        location: {
          bb: {
            ne: "32.768999999999998,51.590344600000002",
            sw: "32.590293299999999,51.4132003",
          },
          center: "32.682338195372402,51.5109296469385",
        },
      },
      naein: {
        name: "نائین",
        name_en: "Naein",
        location: {
          bb: {
            ne: "34.247942700000003,54.769245900000001",
            sw: "32.512198099999999,52.601509499999999",
          },
          center: "33.402169237942402,53.690845682321502",
        },
      },
      shahreza: {
        name: "شهرضا",
        name_en: "Shahreza",
        location: {
          bb: {
            ne: "32.391520700000001,52.209249300000003",
            sw: "31.388838499999999,51.6162603",
          },
          center: "31.864236690253499,51.880253307095799",
        },
      },
    },
    ilam: {
      ilam: {
        name: "ایلام",
        name_en: "Ilam",
        location: {
          bb: {
            ne: "33.865271800000002,46.8583949",
            sw: "33.360623799999999,45.679287899999999",
          },
          center: "33.644944236426603,46.186987180411897",
        },
      },
      ivan: {
        name: "ایوان",
        name_en: "Ivan",
        location: {
          bb: {
            ne: "34.038021800000003,46.453357699999998",
            sw: "33.684680100000001,45.836831199999999",
          },
          center: "33.884385569833398,46.166001941890599",
        },
      },
      malekshahi: {
        name: "ملکشاهی",
        name_en: "Malekshahi",
        location: {
          bb: {
            ne: "33.514976900000001,46.884083099999998",
            sw: "33.067231100000001,46.271855199999997",
          },
          center: "33.304566163923802,46.5870662256551",
        },
      },
      mehran: {
        name: "مهران",
        name_en: "Mehran",
        location: {
          bb: {
            ne: "33.6301737,46.681060600000002",
            sw: "32.895912199999998,45.8637303",
          },
          center: "33.245720239337999,46.275936856390203",
        },
      },
      dehloran: {
        name: "دهلران",
        name_en: "Dehloran",
        location: {
          bb: {
            ne: "33.320043099999999,48.044158699999997",
            sw: "32.043997900000001,46.514735299999998",
          },
          center: "32.659172524462903,47.282628753848698",
        },
      },
      abdanan: {
        name: "آبدانان",
        name_en: "Abdanan",
        location: {
          bb: {
            ne: "33.191347999999998,48.009564500000003",
            sw: "32.5136593,47.033580700000002",
          },
          center: "32.8463530812435,47.544894912103899",
        },
      },
      badreh: {
        name: "بدره",
        name_en: "Badreh",
        location: {
          bb: {
            ne: "33.490359400000003,47.2476536",
            sw: "33.143692199999997,46.790031200000001",
          },
          center: "33.312724457928297,47.027354423318002",
        },
      },
      "dareh shahr": {
        name: "دره شهر",
        name_en: "Dareh Shahr",
        location: {
          bb: {
            ne: "33.253120600000003,48.002682",
            sw: "32.793007799999998,47.208128700000003",
          },
          center: "33.022604610757099,47.573442371510801",
        },
      },
      sirvan: {
        name: "سیروان",
        name_en: "Sirvan",
        location: {
          bb: {
            ne: "33.813277399999997,47.027402299999999",
            sw: "33.379284300000002,46.415663899999998",
          },
          center: "33.606481622697999,46.675523255911799",
        },
      },
      chardavol: {
        name: "چرداول",
        name_en: "Chardavol",
        location: {
          bb: {
            ne: "33.999180099999997,47.516209500000002",
            sw: "33.584146400000002,46.285996400000002",
          },
          center: "33.771481079854702,46.926913647945199",
        },
      },
    },
    alborz: {
      taleghan: {
        name: "طالقان",
        name_en: "Taleghan",
        location: {
          bb: {
            ne: "36.342407299999998,51.176857200000001",
            sw: "36.098112899999997,50.414407199999999",
          },
          center: "36.211676826520304,50.771017212619",
        },
      },
      karaj: {
        name: "کرج",
        name_en: "Karaj",
        location: {
          bb: {
            ne: "36.181361299999999,51.463854400000002",
            sw: "35.693006199999999,50.739850500000003",
          },
          center: "35.956953799581797,51.148765663114197",
        },
      },
      savojbolaq: {
        name: "ساوجبلاغ",
        name_en: "Savojbolaq",
        location: {
          bb: {
            ne: "36.1137528,51.091299100000001",
            sw: "35.7502864,50.562236400000003",
          },
          center: "35.964908479000997,50.8104903503814",
        },
      },
      fardis: {
        name: "فردیس",
        name_en: "Fardis",
        location: {
          bb: {
            ne: "35.7823797,51.066954899999999",
            sw: "35.689648699999999,50.899057300000003",
          },
          center: "35.739729287489197,50.975218169757099",
        },
      },
      eshtehard: {
        name: "اشتهارد",
        name_en: "Eshtehard",
        location: {
          bb: {
            ne: "35.857324400000003,50.749668999999997",
            sw: "35.544852599999999,50.163162",
          },
          center: "35.708510894737799,50.421491386851002",
        },
      },
      "nazar abad": {
        name: "نظرآباد",
        name_en: "Nazar Abad",
        location: {
          bb: {
            ne: "36.038860100000001,50.674843099999997",
            sw: "35.759948199999997,50.332061699999997",
          },
          center: "35.894043486907201,50.514063991288602",
        },
      },
    },
    boushehr: {
      deyr: {
        name: "دیر",
        name_en: "Deyr",
        location: {
          bb: {
            ne: "28.2569731,52.085402799999997",
            sw: "27.8251843,51.264976799999999",
          },
          center: "28.022791124424099,51.681356784303098",
        },
      },
      jam: {
        name: "جم",
        name_en: "Jam",
        location: {
          bb: {
            ne: "28.173125500000001,52.538378000000002",
            sw: "27.6725295,51.864625500000002",
          },
          center: "27.937497456417901,52.237176416938397",
        },
      },
      kangan: {
        name: "کنگان",
        name_en: "Kangan",
        location: {
          bb: {
            ne: "27.9424274,52.525462900000001",
            sw: "27.6030309,51.993704999999999",
          },
          center: "27.761650603069199,52.239013689082697",
        },
      },
      boushehr: {
        name: "بوشهر",
        name_en: "Boushehr",
        location: {
          bb: {
            ne: "29.308177100000002,51.209754500000003",
            sw: "28.8121969,50.694569600000001",
          },
          center: "29.077126199175002,50.959454132513997",
        },
      },
      asalouyeh: {
        name: "عسلویه",
        name_en: "Asalouyeh",
        location: {
          bb: {
            ne: "27.7036759,52.935341999999999",
            sw: "27.2724303,52.473406699999998",
          },
          center: "27.439339654134901,52.7017617976609",
        },
      },
      tangestan: {
        name: "تنگستان",
        name_en: "Tangestan",
        location: {
          bb: {
            ne: "29.1529892,51.630810699999998",
            sw: "28.26989,51.005173900000003",
          },
          center: "28.811004574522201,51.251332280306997",
        },
      },
      dashtestan: {
        name: "دشتستان",
        name_en: "Dashtestan",
        location: {
          bb: {
            ne: "29.7809591,51.988769099999999",
            sw: "28.661419500000001,50.752397199999997",
          },
          center: "29.242416726222999,51.349519506359002",
        },
      },
      deylam: {
        name: "دیلم",
        name_en: "Deylam",
        location: {
          bb: {
            ne: "30.2876592,50.662391900000003",
            sw: "29.743972299999999,50.105617000000002",
          },
          center: "30.0179856285489,50.342360172892199",
        },
      },
      dashti: {
        name: "دشتی",
        name_en: "Dashti",
        location: {
          bb: {
            ne: "28.784172600000002,52.139127600000002",
            sw: "28.1106181,51.142652599999998",
          },
          center: "28.438948010643699,51.625849170583599",
        },
      },
      genaveh: {
        name: "گناوه",
        name_en: "Genaveh",
        location: {
          bb: {
            ne: "29.9158112,50.852413499999997",
            sw: "29.175899300000001,50.318761000000002",
          },
          center: "29.622207083573201,50.629522109390599",
        },
      },
      "om-ol karam": {
        name: "ام الکرم",
        name_en: "Om-ol Karam",
        location: {
          bb: {
            ne: "27.828625500000001,51.475742500000003",
            sw: "27.818988000000001,51.467068300000001",
          },
          center: "27.823673423521399,51.471377723215902",
        },
      },
      kharkou: {
        name: "خارکو",
        name_en: "Kharkou",
        location: {
          bb: {
            ne: "29.344375899999999,50.359257900000003",
            sw: "29.2904093,50.327213",
          },
          center: "29.3173831863309,50.342929525602301",
        },
      },
      khark: {
        name: "خارک",
        name_en: "Khark",
        location: {
          bb: {
            ne: "29.273426000000001,50.337198700000002",
            sw: "29.205231699999999,50.280222199999997",
          },
          center: "29.240495733327101,50.311499126757198",
        },
      },
      gorm: {
        name: "گرم",
        name_en: "Gorm",
        location: {
          bb: {
            ne: "27.9164478,51.455891700000002",
            sw: "27.906672799999999,51.440359299999997",
          },
          center: "27.911862854915402,51.448555781439303",
        },
      },
      tahmadou: {
        name: "تهمادو",
        name_en: "Tahmadou",
        location: {
          bb: {
            ne: "27.840482600000001,51.5694625",
            sw: "27.8333145,51.550052000000001",
          },
          center: "27.8364653716643,51.559273727569",
        },
      },
      nakhilou: {
        name: "نخیلو",
        name_en: "Nakhilou",
        location: {
          bb: {
            ne: "27.918664400000001,51.463419899999998",
            sw: "27.8439373,51.405278899999999",
          },
          center: "27.873138669650199,51.442132744035497",
        },
      },
    },
    tehran: {
      baharestan: {
        name: "بهارستان",
        name_en: "Baharestan",
        location: {
          bb: {
            ne: "35.5955905,51.2174701",
            sw: "35.4703053,51.123974699999998",
          },
          center: "35.530142049660398,51.170719628268301",
        },
      },
      rey: {
        name: "ری",
        name_en: "Rey",
        location: {
          bb: {
            ne: "35.622419200000003,51.690171900000003",
            sw: "35.1105132,50.847383700000002",
          },
          center: "35.340559873537998,51.271330475803701",
        },
      },
      damavand: {
        name: "دماوند",
        name_en: "Damavand",
        location: {
          bb: {
            ne: "35.861183500000003,52.554132899999999",
            sw: "35.373353199999997,51.802747799999999",
          },
          center: "35.623303618478801,52.171697502060297",
        },
      },
      firouzkouh: {
        name: "فیروزکوه",
        name_en: "Firouzkouh",
        location: {
          bb: {
            ne: "35.939216399999999,53.1569346",
            sw: "35.332128500000003,52.220626500000002",
          },
          center: "35.700701426783198,52.663967844726301",
        },
      },
      qods: {
        name: "قدس",
        name_en: "Qods",
        location: {
          bb: {
            ne: "35.7425067,51.197470299999999",
            sw: "35.662363300000003,51.020417600000002",
          },
          center: "35.704055028149597,51.113207935451904",
        },
      },
      varamin: {
        name: "ورامین",
        name_en: "Varamin",
        location: {
          bb: {
            ne: "35.413464599999998,52.044811099999997",
            sw: "34.865228799999997,51.453510700000002",
          },
          center: "35.132762109249697,51.756833399441803",
        },
      },
      pardis: {
        name: "پردیس",
        name_en: "Pardis",
        location: {
          bb: {
            ne: "35.7888527,51.890616399999999",
            sw: "35.554567800000001,51.676250699999997",
          },
          center: "35.694426007256503,51.788043747405098",
        },
      },
      eslamshahr: {
        name: "اسلامشهر",
        name_en: "Eslamshahr",
        location: {
          bb: {
            ne: "35.681998499999999,51.355358899999999",
            sw: "35.474737400000002,51.176338100000002",
          },
          center: "35.579806137070101,51.2489892635082",
        },
      },
      "robat karim": {
        name: "رباط کریم",
        name_en: "Robat Karim",
        location: {
          bb: {
            ne: "35.565002999999997,51.2104936",
            sw: "35.4101924,50.896840300000001",
          },
          center: "35.496103483906502,51.0523681639446",
        },
      },
      shemiranat: {
        name: "شمیرانات",
        name_en: "Shemiranat",
        location: {
          bb: {
            ne: "36.135402300000003,51.879005999999997",
            sw: "35.768701399999998,51.339333400000001",
          },
          center: "35.9200812112175,51.613635464881298",
        },
      },
      pishva: {
        name: "پیشوا",
        name_en: "Pishva",
        location: {
          bb: {
            ne: "35.3907357,51.885440099999997",
            sw: "35.273854399999998,51.645269300000002",
          },
          center: "35.323173718952603,51.750812661909499",
        },
      },
      qarchak: {
        name: "قرچک",
        name_en: "Qarchak",
        location: {
          bb: {
            ne: "35.467909800000001,51.638674799999997",
            sw: "35.371135000000002,51.495977699999997",
          },
          center: "35.416716870328202,51.570559752533299",
        },
      },
      tehran: {
        name: "تهران",
        name_en: "tehran",
        location: {
          bb: {
            ne: "35.939095600000002,51.788907000000002",
            sw: "35.521485599999998,51.0890007",
          },
          center: "35.7277591635414,51.405716863609499",
        },
      },
      malard: {
        name: "ملارد",
        name_en: "Malard",
        location: {
          bb: {
            ne: "35.725994499999999,51.001012600000003",
            sw: "35.480838599999998,50.332803499999997",
          },
          center: "35.611875433279401,50.694477157165799",
        },
      },
      pakdasht: {
        name: "پاکدشت",
        name_en: "Pakdasht",
        location: {
          bb: {
            ne: "35.587335699999997,51.944835699999999",
            sw: "35.294193399999997,51.580954300000002",
          },
          center: "35.450804198552198,51.779827285413397",
        },
      },
      manjilabad: {
        name: "منجیل آباد",
        name_en: "Manjilabad",
        location: {
          bb: {
            ne: "35.547687400000001,51.065311399999999",
            sw: "35.539424799999999,51.054936599999998",
          },
          center: "35.543649900515298,51.060308117374902",
        },
      },
      shahriyar: {
        name: "شهریار",
        name_en: "Shahriyar",
        location: {
          bb: {
            ne: "35.733689300000002,51.231202600000003",
            sw: "35.479195300000001,50.833741099999997",
          },
          center: "35.608861357848198,51.0309475784792",
        },
      },
    },
    "khaharmahal and bakhtiyari": {
      ben: {
        name: "بن",
        name_en: "Ben",
        location: {
          bb: {
            ne: "32.717840299999999,50.8502194",
            sw: "32.437496600000003,50.416023199999998",
          },
          center: "32.581970255932902,50.648227358864503",
        },
      },
      "shahr-e kord": {
        name: "شهرکرد",
        name_en: "Shahr-e Kord",
        location: {
          bb: {
            ne: "32.566047699999999,51.195875899999997",
            sw: "32.020440100000002,50.353640900000002",
          },
          center: "32.305174815057498,50.799623275635199",
        },
      },
      kiar: {
        name: "کیار",
        name_en: "Kiar",
        location: {
          bb: {
            ne: "32.193398899999998,51.022086100000003",
            sw: "31.670054700000001,50.397211900000002",
          },
          center: "31.9273356718695,50.768590043982499",
        },
      },
      boroujen: {
        name: "بروجن",
        name_en: "Boroujen",
        location: {
          bb: {
            ne: "32.220348999999999,51.434142100000003",
            sw: "31.492254800000001,50.775034099999999",
          },
          center: "31.865516570898599,51.152129143407798",
        },
      },
      lordegan: {
        name: "لردگان",
        name_en: "Lordegan",
        location: {
          bb: {
            ne: "31.758458699999998,51.338542099999998",
            sw: "31.150817199999999,50.2934834",
          },
          center: "31.461044685006499,50.887381099187202",
        },
      },
      saman: {
        name: "سامان",
        name_en: "Saman",
        location: {
          bb: {
            ne: "32.709175700000003,51.005159300000003",
            sw: "32.389673999999999,50.777360000000002",
          },
          center: "32.5483546070937,50.8892466867254",
        },
      },
      farsan: {
        name: "فارسان",
        name_en: "Farsan",
        location: {
          bb: {
            ne: "32.383278199999999,50.766070399999997",
            sw: "32.056939499999999,50.350387400000002",
          },
          center: "32.227116119773498,50.563075223719601",
        },
      },
      ardal: {
        name: "اردل",
        name_en: "Ardal",
        location: {
          bb: {
            ne: "32.234285800000002,50.739979900000002",
            sw: "31.591088899999999,50.148191699999998",
          },
          center: "31.909204777380399,50.457780007202501",
        },
      },
      kouhrang: {
        name: "کوهرنگ",
        name_en: "Kouhrang",
        location: {
          bb: {
            ne: "32.809510099999997,50.440043299999999",
            sw: "31.998780799999999,49.501517800000002",
          },
          center: "32.420566210046204,50.035396464677802",
        },
      },
    },
    "south khorasan": {
      khosf: {
        name: "خوسف",
        name_en: "Khosf",
        location: {
          bb: {
            ne: "33.215407200000001,59.373454799999998",
            sw: "31.441665100000002,57.995608699999998",
          },
          center: "32.303969684006397,58.684125506032601",
        },
      },
      birjand: {
        name: "بیرجند",
        name_en: "Birjand",
        location: {
          bb: {
            ne: "33.502242299999999,59.696927100000003",
            sw: "32.650828799999999,58.658537699999997",
          },
          center: "33.0558009629233,59.2687583916248",
        },
      },
      qaenat: {
        name: "قائنات",
        name_en: "Qaenat",
        location: {
          bb: {
            ne: "34.149214399999998,59.899520500000001",
            sw: "33.160431600000003,58.626193200000003",
          },
          center: "33.726227180328202,59.1528678830487",
        },
      },
      zirkouh: {
        name: "زیرکوه",
        name_en: "Zirkouh",
        location: {
          bb: {
            ne: "34.0055488,60.9332049",
            sw: "33.077198299999999,59.382476500000003",
          },
          center: "33.554741011338599,60.150139106070903",
        },
      },
      darmian: {
        name: "درمیان",
        name_en: "Darmian",
        location: {
          bb: {
            ne: "33.340446200000002,60.767631299999998",
            sw: "32.554909600000002,59.484030400000002",
          },
          center: "32.948374119900798,60.125295374239002",
        },
      },
      tabas: {
        name: "طبس",
        name_en: "Tabas",
        location: {
          bb: {
            ne: "35.0943404,58.350374000000002",
            sw: "31.680605,55.381466600000003",
          },
          center: "33.306436604321497,56.820601035511601",
        },
      },
      sarbisheh: {
        name: "سربیشه",
        name_en: "Sarbisheh",
        location: {
          bb: {
            ne: "32.916603799999997,60.873614699999997",
            sw: "32.016447499999998,59.222053000000002",
          },
          center: "32.441198443441799,60.067697571687297",
        },
      },
      sarayan: {
        name: "سرایان",
        name_en: "Sarayan",
        location: {
          bb: {
            ne: "34.109583399999998,58.866008200000003",
            sw: "32.572614899999998,57.671463099999997",
          },
          center: "33.365383774543901,58.242378016329297",
        },
      },
      nehbandan: {
        name: "نهبندان",
        name_en: "Nehbandan",
        location: {
          bb: {
            ne: "32.336703200000002,60.859778599999999",
            sw: "30.516734,58.423341299999997",
          },
          center: "31.4966454065286,59.746179159421402",
        },
      },
    },
    "khorasan razavi": {
      bardaskan: {
        name: "بردسکن",
        name_en: "Bardaskan",
        location: {
          bb: {
            ne: "35.586403300000001,58.249481899999999",
            sw: "34.680922000000002,56.227891499999998",
          },
          center: "35.1737909133264,57.362987051179303",
        },
      },
      sabzevar: {
        name: "سبزوار",
        name_en: "Sabzevar",
        location: {
          bb: {
            ne: "36.4347633,58.2915177",
            sw: "35.465968500000002,56.714013199999997",
          },
          center: "35.908464862079803,57.5585122773021",
        },
      },
      bakharz: {
        name: "باخرز",
        name_en: "Bakharz",
        location: {
          bb: {
            ne: "35.323150099999999,60.672451100000004",
            sw: "34.777358399999997,60.0021208",
          },
          center: "35.037761668299801,60.296704751537199",
        },
      },
      ferdows: {
        name: "فردوس",
        name_en: "Ferdows",
        location: {
          bb: {
            ne: "34.347073000000002,58.495540699999999",
            sw: "33.579973000000003,57.669592000000002",
          },
          center: "33.994085377097598,58.016230974412899",
        },
      },
      roshtkhar: {
        name: "رشتخوار",
        name_en: "Roshtkhar",
        location: {
          bb: {
            ne: "35.213164300000003,59.919220000000003",
            sw: "34.4001679,58.943837299999998",
          },
          center: "34.829884418383699,59.396246600741797",
        },
      },
      "torbate jaam": {
        name: "تربت جام",
        name_en: "Torbate Jaam",
        location: {
          bb: {
            ne: "35.996798800000001,61.268053700000003",
            sw: "34.903144099999999,60.006836800000002",
          },
          center: "35.475826531673,60.739891259293302",
        },
      },
      bajestan: {
        name: "بجستان",
        name_en: "Bajestan",
        location: {
          bb: {
            ne: "34.9198594,58.618889500000002",
            sw: "34.2309828,57.557809599999999",
          },
          center: "34.603074783054197,58.090700697721402",
        },
      },
      binaloud: {
        name: "بینالود",
        name_en: "Binaloud",
        location: {
          bb: {
            ne: "36.457069699999998,59.581230599999998",
            sw: "36.104043400000002,59.062767399999998",
          },
          center: "36.2851811923436,59.315368069677596",
        },
      },
      "torbate heydariyeh": {
        name: "تربت حیدریه",
        name_en: "Torbate Heydariyeh",
        location: {
          bb: {
            ne: "35.851935300000001,59.512942700000004",
            sw: "34.955582499999998,58.686322099999998",
          },
          center: "35.469812119810797,59.119102166119802",
        },
      },
      joqatay: {
        name: "جغتای",
        name_en: "Joqatay",
        location: {
          bb: {
            ne: "36.8683446,57.296102099999999",
            sw: "36.425801499999999,56.52722",
          },
          center: "36.682459051098903,56.989143355948599",
        },
      },
      fariman: {
        name: "فریمان",
        name_en: "Fariman",
        location: {
          bb: {
            ne: "36.018065,60.583976200000002",
            sw: "35.4352929,59.487825299999997",
          },
          center: "35.694132166614096,59.881092649965701",
        },
      },
      kalat: {
        name: "کلات",
        name_en: "Kalat",
        location: {
          bb: {
            ne: "37.283067000000003,60.444423700000002",
            sw: "36.409162500000001,59.344809900000001",
          },
          center: "36.830447133298598,59.917753393247096",
        },
      },
      sarakhs: {
        name: "سرخس",
        name_en: "Sarakhs",
        location: {
          bb: {
            ne: "36.633137599999998,61.221174400000002",
            sw: "35.891959399999998,60.204288200000001",
          },
          center: "36.291366780659502,60.781043901638",
        },
      },
      khaaf: {
        name: "خواف",
        name_en: "Khaaf",
        location: {
          bb: {
            ne: "35.043269500000001,60.903208599999999",
            sw: "33.854452199999997,59.387972900000001",
          },
          center: "34.412030362579699,60.033074675210401",
        },
      },
      boshruyeh: {
        name: "بشرویه",
        name_en: "Boshruyeh",
        location: {
          bb: {
            ne: "34.6204003,57.8064082",
            sw: "33.460252199999999,57.0084479",
          },
          center: "34.023402680239101,57.427295824087302",
        },
      },
      "khalil abad": {
        name: "خلیل آباد",
        name_en: "Khalil Abad",
        location: {
          bb: {
            ne: "35.460153300000002,58.429524299999997",
            sw: "34.883904899999997,57.9913156",
          },
          center: "35.168304465116599,58.187898390801898",
        },
      },
      mahvelat: {
        name: "مه ولات",
        name_en: "Mahvelat",
        location: {
          bb: {
            ne: "35.348019899999997,59.152857400000002",
            sw: "34.758496899999997,58.205194300000002",
          },
          center: "35.017906429996401,58.729962360574198",
        },
      },
      gonabaad: {
        name: "گناباد",
        name_en: "Gonabaad",
        location: {
          bb: {
            ne: "34.800171900000002,59.539810000000003",
            sw: "34.019387299999998,58.293047199999997",
          },
          center: "34.376083260899797,58.855232349121799",
        },
      },
      zaveh: {
        name: "زاوه",
        name_en: "Zaveh",
        location: {
          bb: {
            ne: "35.515200800000002,60.139702200000002",
            sw: "35.012741900000002,59.317487300000003",
          },
          center: "35.295292624292401,59.719490213506504",
        },
      },
      neyshabour: {
        name: "نیشابور",
        name_en: "Neyshabour",
        location: {
          bb: {
            ne: "36.966961099999999,59.315649399999998",
            sw: "35.574633200000001,58.201674799999999",
          },
          center: "36.1676948249427,58.676098797692902",
        },
      },
      jowayin: {
        name: "جوین",
        name_en: "Jowayin",
        location: {
          bb: {
            ne: "36.8352735,57.929234299999997",
            sw: "36.395663200000001,57.2200007",
          },
          center: "36.601214503965998,57.478642279867799",
        },
      },
      kashmar: {
        name: "کاشمر",
        name_en: "Kashmar",
        location: {
          bb: {
            ne: "35.731841000000003,58.812769500000002",
            sw: "35.094427899999999,58.16075",
          },
          center: "35.417569857721602,58.493625559066601",
        },
      },
      firouzeh: {
        name: "فیروزه",
        name_en: "Firouzeh",
        location: {
          bb: {
            ne: "36.608665600000002,58.675749600000003",
            sw: "36.077370000000002,58.214576600000001",
          },
          center: "36.315712979364498,58.423232035305503",
        },
      },
      khoushab: {
        name: "خوشاب",
        name_en: "Khoushab",
        location: {
          bb: {
            ne: "36.892489900000001,58.275385200000002",
            sw: "36.245460799999996,57.600237100000001",
          },
          center: "36.490302608976201,58.006803775994001",
        },
      },
      dargaz: {
        name: "درگز",
        name_en: "Dargaz",
        location: {
          bb: {
            ne: "37.7008261,59.4655512",
            sw: "36.9133779,58.481921399999997",
          },
          center: "37.354871044257898,59.021611854607201",
        },
      },
      qouchan: {
        name: "قوچان",
        name_en: "Qouchan",
        location: {
          bb: {
            ne: "37.666992800000003,59.090281300000001",
            sw: "36.6220657,58.160095099999999",
          },
          center: "37.117722784637003,58.588017862266597",
        },
      },
      mashhad: {
        name: "مشهد",
        name_en: "Mashhad",
        location: {
          bb: {
            ne: "36.974725100000001,60.602090799999999",
            sw: "35.704324399999997,59.172753399999998",
          },
          center: "36.250003270393499,59.814294542264399",
        },
      },
      chenaran: {
        name: "چناران",
        name_en: "Chenaran",
        location: {
          bb: {
            ne: "37.047121599999997,59.402400200000002",
            sw: "36.271660699999998,58.657740099999998",
          },
          center: "36.688997835075,59.051455644396398",
        },
      },
      taybad: {
        name: "تایباد",
        name_en: "Taybad",
        location: {
          bb: {
            ne: "35.106779500000002,61.064144300000002",
            sw: "34.448097699999998,60.274015499999997",
          },
          center: "34.762479213629099,60.699498788014097",
        },
      },
      davarzan: {
        name: "داورزن",
        name_en: "Davarzan",
        location: {
          bb: {
            ne: "36.556890899999999,57.4646756",
            sw: "36.066133700000002,56.696916000000002",
          },
          center: "36.288335544574402,57.070741233743",
        },
      },
    },
    "north khorasan": {
      bojnourd: {
        name: "بجنورد",
        name_en: "Bojnourd",
        location: {
          bb: {
            ne: "37.984553499999997,57.725223300000003",
            sw: "37.220222800000002,56.950914599999997",
          },
          center: "37.555706056036399,57.342857082282499",
        },
      },
      "raz and jargalan": {
        name: "رازوجرگلان",
        name_en: "Raz and Jargalan",
        location: {
          bb: {
            ne: "38.286606399999997,57.385959999999997",
            sw: "37.8550319,56.3245328",
          },
          center: "38.096800263832399,56.907391840404202",
        },
      },
      farouj: {
        name: "فاروج",
        name_en: "Farouj",
        location: {
          bb: {
            ne: "37.682911900000001,58.424430200000003",
            sw: "36.860968700000001,57.859142800000001",
          },
          center: "37.192559134094701,58.1678600981325",
        },
      },
      "maneh and samalqan": {
        name: "مانه و سملقان",
        name_en: "Maneh and Samalqan",
        location: {
          bb: {
            ne: "38.143025799999997,57.282798200000002",
            sw: "37.296796499999999,55.9916828",
          },
          center: "37.675024833032197,56.588141517873296",
        },
      },
      garmeh: {
        name: "گرمه",
        name_en: "Garmeh",
        location: {
          bb: {
            ne: "37.376369699999998,56.621943299999998",
            sw: "36.824329499999997,55.903365100000002",
          },
          center: "37.151735757543797,56.235998575189797",
        },
      },
      jajrom: {
        name: "جاجرم",
        name_en: "Jajrom",
        location: {
          bb: {
            ne: "37.395787499999997,57.035867000000003",
            sw: "36.658817200000001,56.234475400000001",
          },
          center: "37.029112844875499,56.666729676972402",
        },
      },
      esfarayen: {
        name: "اسفراین",
        name_en: "Esfarayen",
        location: {
          bb: {
            ne: "37.281981600000002,58.130947200000001",
            sw: "36.576115999999999,56.971858400000002",
          },
          center: "36.9548006060338,57.554586947895601",
        },
      },
      shirvan: {
        name: "شیروان",
        name_en: "Shirvan",
        location: {
          bb: {
            ne: "37.923643300000002,58.286811399999998",
            sw: "37.091377199999997,57.463024300000001",
          },
          center: "37.544249241038401,57.891903153633002",
        },
      },
    },
    khouzestan: {
      behbahan: {
        name: "بهبهان",
        name_en: "Behbahan",
        location: {
          bb: {
            ne: "30.905404900000001,50.552046400000002",
            sw: "30.164213400000001,49.789206399999998",
          },
          center: "30.5275438951406,50.209192496603997",
        },
      },
      "bandar-e mahshahr": {
        name: "بندر ماهشهر",
        name_en: "Bandar-e Mahshahr",
        location: {
          bb: {
            ne: "30.9319582,49.5409583",
            sw: "30.133602400000001,48.912267399999998",
          },
          center: "30.512611759626999,49.211629552821201",
        },
      },
      ahwaz: {
        name: "اهواز",
        name_en: "Ahwaz",
        location: {
          bb: {
            ne: "31.7685453,49.313996899999999",
            sw: "30.891629999999999,48.033720099999996",
          },
          center: "31.218830406250301,48.685893292158603",
        },
      },
      andika: {
        name: "اندیکا",
        name_en: "Andika",
        location: {
          bb: {
            ne: "32.646729100000002,49.875025600000001",
            sw: "31.9859103,49.2365584",
          },
          center: "32.303958731006198,49.537512729307899",
        },
      },
      khoramshahr: {
        name: "خرمشهر",
        name_en: "Khoramshahr",
        location: {
          bb: {
            ne: "30.944772199999999,48.472120599999997",
            sw: "30.315635400000001,48.0328971",
          },
          center: "30.685274512205002,48.2139464205097",
        },
      },
      "dashte azadegan": {
        name: "دشت آزادگان",
        name_en: "Dashte Azadegan",
        location: {
          bb: {
            ne: "32.065112599999999,48.463070199999997",
            sw: "31.401170199999999,47.714558699999998",
          },
          center: "31.724525334247399,48.065205734443097",
        },
      },
      ramshir: {
        name: "رامشیر",
        name_en: "Ramshir",
        location: {
          bb: {
            ne: "31.259250300000001,49.6278498",
            sw: "30.634132699999999,49.170251999999998",
          },
          center: "30.943820473427699,49.394276410060201",
        },
      },
      shoushtar: {
        name: "شوشتر",
        name_en: "Shoushtar",
        location: {
          bb: {
            ne: "32.1397403,49.206824300000001",
            sw: "31.605331799999998,48.564040499999997",
          },
          center: "31.884207497460299,48.877717281119203",
        },
      },
      "masjed soleiman": {
        name: "مسجد سلیمان",
        name_en: "Masjed Soleiman",
        location: {
          bb: {
            ne: "32.309676000000003,49.709532299999999",
            sw: "31.691292399999998,48.958157900000003",
          },
          center: "31.948901931639099,49.312552722301298",
        },
      },
      aqajari: {
        name: "آغاجاری",
        name_en: "Aqajari",
        location: {
          bb: {
            ne: "30.884068500000001,50.014832400000003",
            sw: "30.6836074,49.826688699999998",
          },
          center: "30.7936959831002,49.924732670904802",
        },
      },
      karoun: {
        name: "کارون",
        name_en: "Karoun",
        location: {
          bb: {
            ne: "31.268647399999999,48.904180400000001",
            sw: "30.889789400000002,48.364219300000002",
          },
          center: "31.070583154279401,48.626695860210297",
        },
      },
      ramhormoz: {
        name: "رامهرمز",
        name_en: "Ramhormoz",
        location: {
          bb: {
            ne: "31.460227100000001,49.990742400000002",
            sw: "30.9459175,49.269842500000003",
          },
          center: "31.215329091919902,49.662139243905798",
        },
      },
      shoush: {
        name: "شوش",
        name_en: "Shoush",
        location: {
          bb: {
            ne: "32.507855300000003,48.666226399999999",
            sw: "31.672372200000002,47.666704199999998",
          },
          center: "32.030760093532798,48.222735819274",
        },
      },
      bawi: {
        name: "باوی",
        name_en: "Bawi",
        location: {
          bb: {
            ne: "31.707409200000001,49.255546699999996",
            sw: "31.345550800000002,48.682761800000002",
          },
          center: "31.514961752992399,48.969290734608002",
        },
      },
      izeh: {
        name: "ایذه",
        name_en: "Izeh",
        location: {
          bb: {
            ne: "32.347571199999997,50.4425454",
            sw: "31.442050200000001,49.552083400000001",
          },
          center: "31.873249540387,49.971573376317401",
        },
      },
      baghmalek: {
        name: "باغ ملک",
        name_en: "Baghmalek",
        location: {
          bb: {
            ne: "31.726208499999998,50.299364599999997",
            sw: "31.206992799999998,49.477311399999998",
          },
          center: "31.484101523894001,49.904433591057703",
        },
      },
      abadan: {
        name: "آبادان",
        name_en: "Abadan",
        location: {
          bb: {
            ne: "30.505610600000001,48.946881900000001",
            sw: "29.882294699999999,48.197121799999998",
          },
          center: "30.181592919101799,48.611484755674198",
        },
      },
      howeyzeh: {
        name: "هویزه",
        name_en: "Howeyzeh",
        location: {
          bb: {
            ne: "31.6736176,48.3182756",
            sw: "30.897151999999998,47.683421799999998",
          },
          center: "31.275860093020899,47.913290590593803",
        },
      },
      haftgel: {
        name: "هفتگل",
        name_en: "Haftgel",
        location: {
          bb: {
            ne: "31.7207817,49.711825300000001",
            sw: "31.343949899999998,49.148117200000002",
          },
          center: "31.535380017619001,49.403813412668804",
        },
      },
      hamidiyeh: {
        name: "حمیدیه",
        name_en: "Hamidiyeh",
        location: {
          bb: {
            ne: "31.713282400000001,48.611583600000003",
            sw: "31.295568100000001,48.266407999999998",
          },
          center: "31.4906820382256,48.4492396925395",
        },
      },
      andimeshk: {
        name: "اندیمشک",
        name_en: "Andimeshk",
        location: {
          bb: {
            ne: "32.994282300000002,48.781257699999998",
            sw: "32.274035699999999,47.903027999999999",
          },
          center: "32.696074031726603,48.3289613387009",
        },
      },
      omidiyeh: {
        name: "امیدیه",
        name_en: "Omidiyeh",
        location: {
          bb: {
            ne: "31.1317296,50.076447700000003",
            sw: "30.442112300000002,49.320100099999998",
          },
          center: "30.769336541793699,49.708318375315997",
        },
      },
      dezful: {
        name: "دزفول",
        name_en: "Dezful",
        location: {
          bb: {
            ne: "32.966725699999998,49.565688799999997",
            sw: "31.996987699999998,48.279967999999997",
          },
          center: "32.572739632257097,48.840230290512302",
        },
      },
      shadegan: {
        name: "شادگان",
        name_en: "Shadegan",
        location: {
          bb: {
            ne: "30.9843598,49.040636200000002",
            sw: "30.293850200000001,48.324575699999997",
          },
          center: "30.643021567326201,48.681160262900697",
        },
      },
      laali: {
        name: "لالی",
        name_en: "Laali",
        location: {
          bb: {
            ne: "32.607247100000002,49.427446500000002",
            sw: "32.185780700000002,48.913498599999997",
          },
          center: "32.425089078599598,49.172400552039001",
        },
      },
      hendijan: {
        name: "هندیجان",
        name_en: "Hendijan",
        location: {
          bb: {
            ne: "30.598898599999998,50.2087729",
            sw: "30.010107300000001,49.475898399999998",
          },
          center: "30.311199006151401,49.735430008286599",
        },
      },
      gotvand: {
        name: "گتوند",
        name_en: "Gotvand",
        location: {
          bb: {
            ne: "32.439065499999998,49.024972499999997",
            sw: "32.076847100000002,48.571461300000003",
          },
          center: "32.236280847900801,48.818556048970201",
        },
      },
    },
    yazd: {
      bahabad: {
        name: "بهاباد",
        name_en: "Bahabad",
        location: {
          bb: {
            ne: "32.479118900000003,56.6559308",
            sw: "31.548750999999999,55.504782499999997",
          },
          center: "32.011812601356702,56.143799977328797",
        },
      },
      mehriz: {
        name: "مهریز",
        name_en: "Mehriz",
        location: {
          bb: {
            ne: "31.755988200000001,55.278092100000002",
            sw: "31.005061300000001,54.125152700000001",
          },
          center: "31.346708640439399,54.644753219239902",
        },
      },
      taft: {
        name: "تفت",
        name_en: "Taft",
        location: {
          bb: {
            ne: "31.886145899999999,54.305177899999997",
            sw: "31.0009333,53.2038212",
          },
          center: "31.4544159879411,53.814270168926001",
        },
      },
      khatam: {
        name: "خاتم",
        name_en: "Khatam",
        location: {
          bb: {
            ne: "31.013367200000001,54.565239599999998",
            sw: "29.595357700000001,53.836931900000003",
          },
          center: "30.364588567966301,54.237098194862597",
        },
      },
      meybod: {
        name: "میبد",
        name_en: "Meybod",
        location: {
          bb: {
            ne: "32.3194442,54.420460499999997",
            sw: "31.593025099999998,53.007540300000002",
          },
          center: "32.010006140649502,53.527279691193897",
        },
      },
      yazd: {
        name: "یزد",
        name_en: "Yazd",
        location: {
          bb: {
            ne: "32.222904399999997,54.846845000000002",
            sw: "31.656897399999998,54.145925200000001",
          },
          center: "31.933558616223099,54.517363433342801",
        },
      },
      "abar kouh": {
        name: "ابرکوه",
        name_en: "Abar Kouh",
        location: {
          bb: {
            ne: "31.5957203,54.002648100000002",
            sw: "30.658297600000001,52.8028081",
          },
          center: "31.093275958155399,53.383017792997599",
        },
      },
      ashkezar: {
        name: "اشکذر",
        name_en: "Ashkezar",
        location: {
          bb: {
            ne: "32.213243499999997,54.440623799999997",
            sw: "31.758129,53.692245300000003",
          },
          center: "31.990862080673701,54.043176362606303",
        },
      },
      bafq: {
        name: "بافق",
        name_en: "Bafq",
        location: {
          bb: {
            ne: "32.178696000000002,56.064157899999998",
            sw: "31.113684599999999,54.7195702",
          },
          center: "31.668010354273999,55.400603451107102",
        },
      },
      ardakan: {
        name: "اردکان",
        name_en: "Ardakan",
        location: {
          bb: {
            ne: "33.362711300000001,56.306925800000002",
            sw: "32.036486799999999,53.014371799999999",
          },
          center: "32.605173488115902,54.767440695799998",
        },
      },
    },
    zanjan: {
      soltaniyeh: {
        name: "سلطانیه",
        name_en: "Soltaniyeh",
        location: {
          bb: {
            ne: "36.711626799999998,49.168662400000002",
            sw: "36.230492699999999,48.557012399999998",
          },
          center: "36.440894073296199,48.8916250874883",
        },
      },
      abhar: {
        name: "ابهر",
        name_en: "Abhar",
        location: {
          bb: {
            ne: "36.387293999999997,49.436378900000001",
            sw: "35.873404700000002,48.902631499999998",
          },
          center: "36.0990284759093,49.185497050396897",
        },
      },
      khodabandeh: {
        name: "خدابنده",
        name_en: "Khodabandeh",
        location: {
          bb: {
            ne: "36.426446200000001,48.953327399999999",
            sw: "35.548088200000002,47.858204800000003",
          },
          center: "35.989234111622402,48.482848819485802",
        },
      },
      "khoram dareh": {
        name: "خرمدره",
        name_en: "Khoram Dareh",
        location: {
          bb: {
            ne: "36.430462599999998,49.306003500000003",
            sw: "36.1542429,48.940178099999997",
          },
          center: "36.268915152789397,49.151321776987899",
        },
      },
      tarom: {
        name: "طارم",
        name_en: "Tarom",
        location: {
          bb: {
            ne: "37.1826127,49.279947700000001",
            sw: "36.667019799999998,48.481941499999998",
          },
          center: "36.946804469797797,48.879455918657499",
        },
      },
      "mah neshan": {
        name: "ماه نشان",
        name_en: "Mah Neshan",
        location: {
          bb: {
            ne: "36.959311499999998,47.9714186",
            sw: "36.327863600000001,47.1736687",
          },
          center: "36.646275299798802,47.582069968328597",
        },
      },
      zanjan: {
        name: "زنجان",
        name_en: "Zanjan",
        location: {
          bb: {
            ne: "37.252113600000001,48.917771600000002",
            sw: "36.452245099999999,47.427909800000002",
          },
          center: "36.846427917326302,48.219714104043902",
        },
      },
      ijrud: {
        name: "ایجرود",
        name_en: "Ijrud",
        location: {
          bb: {
            ne: "36.565506499999998,48.578094399999998",
            sw: "36.078560299999999,47.874462700000002",
          },
          center: "36.344694377045101,48.198654893551897",
        },
      },
    },
    "sistan and balouchestan": {
      mirjaveh: {
        name: "میرجاوه",
        name_en: "Mirjaveh",
        location: {
          bb: {
            ne: "29.3842447,62.0496494",
            sw: "28.330978200000001,60.709075599999998",
          },
          center: "28.846667771706901,61.327855018478402",
        },
      },
      sarbaz: {
        name: "سرباز",
        name_en: "Sarbaz",
        location: {
          bb: {
            ne: "27.065123499999999,62.316500300000001",
            sw: "25.812604100000001,60.619768899999997",
          },
          center: "26.449638885826499,61.409259703883102",
        },
      },
      fanouj: {
        name: "فنوج",
        name_en: "Fanouj",
        location: {
          bb: {
            ne: "27.0613952,59.980650199999999",
            sw: "26.379260200000001,58.993721299999997",
          },
          center: "26.712299667088601,59.542230487156097",
        },
      },
      "qasr-e qand": {
        name: "قصرقند",
        name_en: "Qasr-e Qand",
        location: {
          bb: {
            ne: "26.526983000000001,61.308173799999999",
            sw: "25.702794300000001,60.328845800000003",
          },
          center: "26.075804984808499,60.831160830915699",
        },
      },
      konarak: {
        name: "کنارک",
        name_en: "Konarak",
        location: {
          bb: {
            ne: "25.992252700000002,60.778146399999997",
            sw: "25.284391500000002,58.9956703",
          },
          center: "25.665164530294401,59.902319682662402",
        },
      },
      "nik shahr": {
        name: "نیک شهر",
        name_en: "Nik Shahr",
        location: {
          bb: {
            ne: "27.015333800000001,60.895156700000001",
            sw: "25.885869799999998,58.843446100000001",
          },
          center: "26.393384605760399,59.913804992818797",
        },
      },
      zabol: {
        name: "زابل",
        name_en: "Zabol",
        location: {
          bb: {
            ne: "31.478568599999999,61.741443599999997",
            sw: "30.0966916,59.9354455",
          },
          center: "30.8361667810819,60.920794110369201",
        },
      },
      zehak: {
        name: "زهک",
        name_en: "Zehak",
        location: {
          bb: {
            ne: "30.982593600000001,61.811597900000002",
            sw: "30.6092762,61.502500900000001",
          },
          center: "30.8189628088615,61.640587842208298",
        },
      },
      khash: {
        name: "خاش",
        name_en: "Khash",
        location: {
          bb: {
            ne: "28.900387299999998,62.781752500000003",
            sw: "27.455990100000001,60.1683618",
          },
          center: "28.184775032892301,61.252649587318501",
        },
      },
      iranshahr: {
        name: "ایرانشهر",
        name_en: "Iranshahr",
        location: {
          bb: {
            ne: "28.733315099999999,61.228502900000002",
            sw: "26.7632078,58.9555164",
          },
          center: "27.7347454246441,60.1381478349509",
        },
      },
      zahedan: {
        name: "زاهدان",
        name_en: "Zahedan",
        location: {
          bb: {
            ne: "30.768008900000002,61.319243499999999",
            sw: "28.547291900000001,59.422242400000002",
          },
          center: "29.634617973889199,60.191154579029501",
        },
      },
      "sib-o souran": {
        name: "سیب و سوران",
        name_en: "Sib-o Souran",
        location: {
          bb: {
            ne: "27.900460500000001,62.514185900000001",
            sw: "26.5401712,61.183059100000001",
          },
          center: "27.316869117876202,61.871376508073403",
        },
      },
      dalgan: {
        name: "دلگان",
        name_en: "Dalgan",
        location: {
          bb: {
            ne: "28.060803,59.948585199999997",
            sw: "26.760166900000002,58.829435400000001",
          },
          center: "27.4151156707639,59.306903973694801",
        },
      },
      saravan: {
        name: "سراوان",
        name_en: "Saravan",
        location: {
          bb: {
            ne: "28.088901799999999,63.321851299999999",
            sw: "26.5765779,61.728422799999997",
          },
          center: "27.352164229296299,62.560721551672302",
        },
      },
      mehrestan: {
        name: "مهرستان",
        name_en: "Mehrestan",
        location: {
          bb: {
            ne: "27.553854399999999,62.404078800000001",
            sw: "26.504916399999999,61.093206600000002",
          },
          center: "26.976934241888198,61.656656378596097",
        },
      },
      hirmand: {
        name: "هیرمند",
        name_en: "Hirmand",
        location: {
          bb: {
            ne: "31.406970699999999,61.845474000000003",
            sw: "30.9322996,61.453218300000003",
          },
          center: "31.199382065379901,61.666507931112001",
        },
      },
    },
    semnan: {
      garmsar: {
        name: "گرمسار",
        name_en: "Garmsar",
        location: {
          bb: {
            ne: "35.576775699999999,52.524629099999999",
            sw: "34.397059800000001,51.832748899999999",
          },
          center: "34.945266266288598,52.174272718629403",
        },
      },
      shahmirzad: {
        name: "شهمیرزاد",
        name_en: "Shahmirzad",
        location: {
          bb: {
            ne: "36.176768299999999,53.849789000000001",
            sw: "35.7585628,53.015569900000003",
          },
          center: "35.945468667962103,53.425921046571098",
        },
      },
      sorkheh: {
        name: "سرخه",
        name_en: "Sorkheh",
        location: {
          bb: {
            ne: "35.714237799999999,53.528458899999997",
            sw: "34.232343399999998,52.793620599999997",
          },
          center: "34.912600480939098,53.1577600544842",
        },
      },
      mayamey: {
        name: "میامی",
        name_en: "Mayamey",
        location: {
          bb: {
            ne: "37.328185900000001,56.861859099999997",
            sw: "36.142588199999999,55.308608700000001",
          },
          center: "36.6223419733697,56.055874817112098",
        },
      },
      shahroud: {
        name: "شاهرود",
        name_en: "Shahroud",
        location: {
          bb: {
            ne: "36.902492000000002,57.056779800000001",
            sw: "34.239121699999998,54.568702999999999",
          },
          center: "35.439028519146802,55.6094937665194",
        },
      },
      aradan: {
        name: "آرادان",
        name_en: "Aradan",
        location: {
          bb: {
            ne: "35.557358000000001,52.871257100000001",
            sw: "34.280106400000001,52.365989300000003",
          },
          center: "34.901161128949603,52.640757268024601",
        },
      },
      damqan: {
        name: "دامغان",
        name_en: "Damqan",
        location: {
          bb: {
            ne: "36.545490700000002,54.807972100000001",
            sw: "34.245165999999998,53.702826999999999",
          },
          center: "35.560273683141602,54.349847443680702",
        },
      },
      semnan: {
        name: "سمنان",
        name_en: "Semnan",
        location: {
          bb: {
            ne: "35.979284399999997,54.189659800000001",
            sw: "34.237971700000003,53.329439000000001",
          },
          center: "35.044867357269702,53.805583791065203",
        },
      },
      "mehdi shahr": {
        name: "مهدی شهر",
        name_en: "Mehdi Shahr",
        location: {
          bb: {
            ne: "35.874724399999998,53.592863399999999",
            sw: "35.5842429,53.066449300000002",
          },
          center: "35.738835727378003,53.325290036694497",
        },
      },
    },
    fars: {
      bavanat: {
        name: "بوانات",
        name_en: "Bavanat",
        location: {
          bb: {
            ne: "30.7474107,54.106344700000001",
            sw: "29.786736900000001,53.271935200000001",
          },
          center: "30.303325945181498,53.665996049646502",
        },
      },
      firouzabad: {
        name: "فیروزآباد",
        name_en: "Firouzabad",
        location: {
          bb: {
            ne: "29.258121500000001,52.963849500000002",
            sw: "28.469799800000001,52.111449399999998",
          },
          center: "28.8981268690818,52.561470662992598",
        },
      },
      mamasani: {
        name: "ممسنی",
        name_en: "Mamasani",
        location: {
          bb: {
            ne: "30.4469387,52.124343699999997",
            sw: "29.697106300000002,50.602467900000001",
          },
          center: "30.0114456782986,51.375779450473203",
        },
      },
      neyriz: {
        name: "نی ریز",
        name_en: "Neyriz",
        location: {
          bb: {
            ne: "29.909283500000001,55.281008499999999",
            sw: "28.708322800000001,53.408851599999998",
          },
          center: "29.336844316291302,54.4303075024403",
        },
      },
      sepidan: {
        name: "سپیدان",
        name_en: "Sepidan",
        location: {
          bb: {
            ne: "30.589634100000001,52.607393999999999",
            sw: "29.8157596,51.661173099999999",
          },
          center: "30.172469338815201,52.124283101082803",
        },
      },
      larestan: {
        name: "لارستان",
        name_en: "Larestan",
        location: {
          bb: {
            ne: "28.364422099999999,55.578491200000002",
            sw: "27.305920400000002,53.200635400000003",
          },
          center: "27.816949506577,54.377126365232499",
        },
      },
      ghirokarzin: {
        name: "قیروکارزین",
        name_en: "Ghirokarzin",
        location: {
          bb: {
            ne: "28.615948100000001,53.524251200000002",
            sw: "28.041864700000001,52.440810599999999",
          },
          center: "28.343248569459099,52.908633060405997",
        },
      },
      kazeroun: {
        name: "کازرون",
        name_en: "Kazeroun",
        location: {
          bb: {
            ne: "29.917396799999999,52.141823899999999",
            sw: "29.096357099999999,51.270052499999998",
          },
          center: "29.521002690940399,51.713112446213302",
        },
      },
      kavar: {
        name: "کوار",
        name_en: "Kavar",
        location: {
          bb: {
            ne: "29.384643199999999,53.038304699999998",
            sw: "29.0420245,52.449623899999999",
          },
          center: "29.220425525649699,52.7170327553848",
        },
      },
      kharameh: {
        name: "خرامه",
        name_en: "Kharameh",
        location: {
          bb: {
            ne: "29.7027596,53.5912887",
            sw: "29.329488600000001,52.9644762",
          },
          center: "29.501304059933801,53.263290242913897",
        },
      },
      pasargad: {
        name: "پاسارگاد",
        name_en: "Pasargad",
        location: {
          bb: {
            ne: "30.348997900000001,53.574726900000002",
            sw: "29.931124700000002,52.8019617",
          },
          center: "30.135686743051501,53.1612514682122",
        },
      },
      khonj: {
        name: "خنج",
        name_en: "Khonj",
        location: {
          bb: {
            ne: "28.286058700000002,53.755509199999999",
            sw: "27.670268100000001,52.397779999999997",
          },
          center: "27.968506727487998,53.064581037583402",
        },
      },
      estahban: {
        name: "استهبان",
        name_en: "Estahban",
        location: {
          bb: {
            ne: "29.512124,54.481785100000003",
            sw: "28.830416199999998,53.567160899999998",
          },
          center: "29.145083483199599,54.004161419409897",
        },
      },
      rostam: {
        name: "رستم",
        name_en: "Rostam",
        location: {
          bb: {
            ne: "30.658770400000002,51.594980100000001",
            sw: "30.178068,51.137659499999998",
          },
          center: "30.399510753616202,51.370217021797998",
        },
      },
      arsanjan: {
        name: "ارسنجان",
        name_en: "Arsanjan",
        location: {
          bb: {
            ne: "30.020172800000001,53.739028400000002",
            sw: "29.647880000000001,53.1239366",
          },
          center: "29.832754663131698,53.387496865527503",
        },
      },
      zarindasht: {
        name: "زرین دشت",
        name_en: "Zarindasht",
        location: {
          bb: {
            ne: "28.6444528,54.907052299999997",
            sw: "27.954979600000001,54.0056303",
          },
          center: "28.289923186221301,54.474210596859002",
        },
      },
      sarvestan: {
        name: "سروستان",
        name_en: "Sarvestan",
        location: {
          bb: {
            ne: "29.450978299999999,53.467339799999998",
            sw: "29.015267099999999,52.7350159",
          },
          center: "29.233611194752701,53.114751947738299",
        },
      },
      farashband: {
        name: "فراشبند",
        name_en: "Farashband",
        location: {
          bb: {
            ne: "29.191892899999999,52.536073600000002",
            sw: "28.019000999999999,51.794001000000002",
          },
          center: "28.623876301180999,52.208198801693001",
        },
      },
      fasa: {
        name: "فسا",
        name_en: "Fasa",
        location: {
          bb: {
            ne: "29.425582599999998,54.261216500000003",
            sw: "28.5046669,53.3403584",
          },
          center: "28.948811000890998,53.785483467217503",
        },
      },
      eqlid: {
        name: "اقلید",
        name_en: "Eqlid",
        location: {
          bb: {
            ne: "31.213161599999999,52.920180700000003",
            sw: "30.234889500000001,51.786393799999999",
          },
          center: "30.722268236512701,52.397899653701302",
        },
      },
      darab: {
        name: "داراب",
        name_en: "Darab",
        location: {
          bb: {
            ne: "28.942720000000001,55.451984899999999",
            sw: "28.0272614,54.101162100000003",
          },
          center: "28.529856308129499,54.897567619616602",
        },
      },
      lamerd: {
        name: "لامرد",
        name_en: "Lamerd",
        location: {
          bb: {
            ne: "27.894496700000001,54.013416599999999",
            sw: "27.048347100000001,52.615861500000001",
          },
          center: "27.405362468950901,53.3469599478234",
        },
      },
      abadeh: {
        name: "آباده",
        name_en: "Abadeh",
        location: {
          bb: {
            ne: "31.670557599999999,53.245821499999998",
            sw: "30.774695900000001,51.934122600000002",
          },
          center: "31.2607128111047,52.510135867500999",
        },
      },
      jahrom: {
        name: "جهرم",
        name_en: "Jahrom",
        location: {
          bb: {
            ne: "29.130681899999999,54.0478065",
            sw: "28.292861500000001,52.7611408",
          },
          center: "28.6968517734763,53.352628101086303",
        },
      },
      khorambid: {
        name: "خرم بید",
        name_en: "Khorambid",
        location: {
          bb: {
            ne: "30.895460100000001,53.4486019",
            sw: "30.2166207,52.821934800000001",
          },
          center: "30.568698946500799,53.134929584937503",
        },
      },
      marvdasht: {
        name: "مرودشت",
        name_en: "Marvdasht",
        location: {
          bb: {
            ne: "30.6233298,53.240716300000003",
            sw: "29.688393900000001,51.972854900000002",
          },
          center: "30.100719156821999,52.645749027939303",
        },
      },
      mohr: {
        name: "مهر",
        name_en: "Mohr",
        location: {
          bb: {
            ne: "28.030220199999999,53.233556800000002",
            sw: "27.331794899999998,52.352244300000002",
          },
          center: "27.633588018384,52.7642227163443",
        },
      },
      gerash: {
        name: "گراش",
        name_en: "Gerash",
        location: {
          bb: {
            ne: "27.809973100000001,54.242910100000003",
            sw: "27.435241900000001,53.180948200000003",
          },
          center: "27.650105169606402,53.702959055496898",
        },
      },
      shiraz: {
        name: "شیراز",
        name_en: "Shiraz",
        location: {
          bb: {
            ne: "29.949648799999999,53.096184200000003",
            sw: "29.191849300000001,51.803992100000002",
          },
          center: "29.600667074212399,52.433358300601398",
        },
      },
    },
    qazvin: {
      abyek: {
        name: "آبیک",
        name_en: "Abyek",
        location: {
          bb: {
            ne: "36.316495199999999,50.679401900000002",
            sw: "35.845232000000003,50.070406599999998",
          },
          center: "36.086655379696303,50.347274927700703",
        },
      },
      alborz: {
        name: "البرز",
        name_en: "Alborz",
        location: {
          bb: {
            ne: "36.386814399999999,50.427939600000002",
            sw: "36.049106799999997,49.932750499999997",
          },
          center: "36.214187046511803,50.1590287879675",
        },
      },
      qazvin: {
        name: "قزوین",
        name_en: "Qazvin",
        location: {
          bb: {
            ne: "36.8171228,50.8572287",
            sw: "36.143929499999999,48.969499300000003",
          },
          center: "36.475101692709302,49.857744614729803",
        },
      },
      "boein zahra": {
        name: "بوئین زهرا",
        name_en: "Boein Zahra",
        location: {
          bb: {
            ne: "36.176490800000003,50.332061699999997",
            sw: "35.396477900000001,48.725732800000003",
          },
          center: "35.734892058368303,49.6103195732491",
        },
      },
      takestan: {
        name: "تاکستان",
        name_en: "Takestan",
        location: {
          bb: {
            ne: "36.360127599999998,49.882300399999998",
            sw: "35.671895399999997,49.141210800000003",
          },
          center: "36.012772549747197,49.542880122893799",
        },
      },
    },
    qom: {
      qom: {
        name: "قم",
        name_en: "Qom",
        location: {
          bb: {
            ne: "35.184050300000003,51.964952599999997",
            sw: "34.149622399999998,50.086584799999997",
          },
          center: "34.696198004299497,51.027694221296201",
        },
      },
    },
    kordestan: {
      sanandaj: {
        name: "سنندج",
        name_en: "Sanandaj",
        location: {
          bb: {
            ne: "35.651195100000002,47.3299102",
            sw: "35.049075100000003,46.4087593",
          },
          center: "35.353373768704301,46.887422096136198",
        },
      },
      marivan: {
        name: "مریوان",
        name_en: "Marivan",
        location: {
          bb: {
            ne: "35.817246900000001,46.766667900000002",
            sw: "35.356374000000002,45.976744699999998",
          },
          center: "35.572223325966497,46.354618333465503",
        },
      },
      "diwan dareh": {
        name: "دیواندره",
        name_en: "Diwan Dareh",
        location: {
          bb: {
            ne: "36.352558299999998,47.493245199999997",
            sw: "35.570711299999999,46.521095099999997",
          },
          center: "35.925915126408803,46.975725215880601",
        },
      },
      dehgolan: {
        name: "دهگلان",
        name_en: "Dehgolan",
        location: {
          bb: {
            ne: "35.686031399999997,47.6164597",
            sw: "35.016500000000001,47.109888300000001",
          },
          center: "35.336257474879197,47.357060309279298",
        },
      },
      kamyaran: {
        name: "کامیاران",
        name_en: "Kamyaran",
        location: {
          bb: {
            ne: "35.170331599999997,47.337613099999999",
            sw: "34.738477400000001,46.502353300000003",
          },
          center: "34.955923791614303,46.914433938352502",
        },
      },
      "sarv abad": {
        name: "سروآباد",
        name_en: "Sarv Abad",
        location: {
          bb: {
            ne: "35.405533200000001,46.722266900000001",
            sw: "35.028975199999998,46.076510800000001",
          },
          center: "35.230208013939802,46.400547405426003",
        },
      },
      qorveh: {
        name: "قروه",
        name_en: "Qorveh",
        location: {
          bb: {
            ne: "35.5786236,48.1934282",
            sw: "34.9239642,47.420735399999998",
          },
          center: "35.229385110257297,47.826159269743201",
        },
      },
      bijar: {
        name: "بیجار",
        name_en: "Bijar",
        location: {
          bb: {
            ne: "36.435137900000001,48.249693499999999",
            sw: "35.495328700000002,47.083648699999998",
          },
          center: "35.944518576422901,47.6468133181124",
        },
      },
      saqez: {
        name: "سقز",
        name_en: "Saqez",
        location: {
          bb: {
            ne: "36.462461099999999,46.9050449",
            sw: "35.779221,45.842354200000003",
          },
          center: "36.156413938858996,46.3785628724318",
        },
      },
      baneh: {
        name: "بانه",
        name_en: "Baneh",
        location: {
          bb: {
            ne: "36.235278600000001,46.1953958",
            sw: "35.800366599999997,45.554956099999998",
          },
          center: "35.996036451755998,45.848957569773603",
        },
      },
    },
    kerman: {
      arzuiyeh: {
        name: "ارزوئیه",
        name_en: "Arzuiyeh",
        location: {
          bb: {
            ne: "28.811287799999999,57.0829345",
            sw: "28.130088000000001,56.034866600000001",
          },
          center: "28.430591361301701,56.532237074259299",
        },
      },
      "roudbar-e jonoub": {
        name: "رودبار جنوب",
        name_en: "Roudbar-e Jonoub",
        location: {
          bb: {
            ne: "28.248152000000001,58.979387099999997",
            sw: "27.476962199999999,57.807446400000003",
          },
          center: "27.8942229444845,58.453041046170704",
        },
      },
      kahnouj: {
        name: "کهنوج",
        name_en: "Kahnouj",
        location: {
          bb: {
            ne: "28.239198300000002,57.921392400000002",
            sw: "27.475163200000001,57.192674099999998",
          },
          center: "27.8466964658774,57.636365591866401",
        },
      },
      reygan: {
        name: "ریگان",
        name_en: "Reygan",
        location: {
          bb: {
            ne: "28.860483899999998,59.562342800000003",
            sw: "28.081176800000001,58.366010199999998",
          },
          center: "28.5048692094309,58.944895958070703",
        },
      },
      "shahr-e babak": {
        name: "شهربابک",
        name_en: "Shahr-e Babak",
        location: {
          bb: {
            ne: "31.070420500000001,55.834535199999998",
            sw: "29.449929000000001,54.340259400000001",
          },
          center: "30.238324173740899,54.989345967021301",
        },
      },
      jiroft: {
        name: "جیرفت",
        name_en: "Jiroft",
        location: {
          bb: {
            ne: "29.362388899999999,58.211551999999998",
            sw: "28.103457800000001,56.895207800000001",
          },
          center: "28.778532269532999,57.494950657940798",
        },
      },
      rabor: {
        name: "رابر",
        name_en: "Rabor",
        location: {
          bb: {
            ne: "29.4563314,57.272199100000002",
            sw: "28.892310800000001,56.742636500000003",
          },
          center: "29.234072364188201,56.988245975092802",
        },
      },
      bam: {
        name: "بم",
        name_en: "Bam",
        location: {
          bb: {
            ne: "29.627055299999999,58.623561799999997",
            sw: "28.5333462,57.700186299999999",
          },
          center: "29.115836224659901,58.198511746674598",
        },
      },
      bardsir: {
        name: "بردسیر",
        name_en: "Bardsir",
        location: {
          bb: {
            ne: "30.1972855,57.314723600000001",
            sw: "29.386711999999999,55.998769799999998",
          },
          center: "29.7737627255148,56.693154712397003",
        },
      },
      "anbar abad": {
        name: "عنبرآباد",
        name_en: "Anbar Abad",
        location: {
          bb: {
            ne: "28.759172100000001,58.638048499999996",
            sw: "28.130929900000002,57.757701699999998",
          },
          center: "28.404973434320802,58.144205887530703",
        },
      },
      anar: {
        name: "انار",
        name_en: "Anar",
        location: {
          bb: {
            ne: "31.110711899999998,55.694910399999998",
            sw: "30.543514800000001,54.944035499999998",
          },
          center: "30.8339730903892,55.290924322653098",
        },
      },
      ravar: {
        name: "راور",
        name_en: "Ravar",
        location: {
          bb: {
            ne: "31.962196200000001,57.755136899999997",
            sw: "30.5614268,56.430699500000003",
          },
          center: "31.321673840179901,57.152348921574799",
        },
      },
      zarand: {
        name: "زرند",
        name_en: "Zarand",
        location: {
          bb: {
            ne: "31.286374500000001,56.961896400000001",
            sw: "30.550184399999999,55.579827600000002",
          },
          center: "30.941134402652299,56.288113304433999",
        },
      },
      "qal-e ganj": {
        name: "قلعه گنج",
        name_en: "Qal-e Ganj",
        location: {
          bb: {
            ne: "27.883708899999998,59.038499999999999",
            sw: "26.483251599999999,57.743138399999999",
          },
          center: "27.1696340355412,58.399100096913202",
        },
      },
      faryab: {
        name: "فاریاب",
        name_en: "Faryab",
        location: {
          bb: {
            ne: "28.485565000000001,57.708306100000001",
            sw: "27.897956400000002,56.960855100000003",
          },
          center: "28.160982278608799,57.250666880699399",
        },
      },
      narmashir: {
        name: "نرماشیر",
        name_en: "Narmashir",
        location: {
          bb: {
            ne: "29.607054900000001,59.087434299999998",
            sw: "28.845379300000001,58.406925100000002",
          },
          center: "29.260180234623199,58.754135652932298",
        },
      },
      fahraj: {
        name: "فهرج",
        name_en: "Fahraj",
        location: {
          bb: {
            ne: "29.6067438,59.571876199999998",
            sw: "28.7272979,58.7153116",
          },
          center: "29.096302964498101,59.178671154865398",
        },
      },
      rafsanjan: {
        name: "رفسنجان",
        name_en: "Rafsanjan",
        location: {
          bb: {
            ne: "31.233201099999999,56.689382299999998",
            sw: "29.907900999999999,55.264350899999997",
          },
          center: "30.541989616477299,55.895431815315497",
        },
      },
      kerman: {
        name: "کرمان",
        name_en: "Kerman",
        location: {
          bb: {
            ne: "31.743186099999999,59.470191900000003",
            sw: "29.274304099999998,56.251758000000002",
          },
          center: "30.347065411888501,58.130382251833701",
        },
      },
      sirjan: {
        name: "سیرجان",
        name_en: "Sirjan",
        location: {
          bb: {
            ne: "30.012165700000001,56.454472000000003",
            sw: "28.6917115,54.948435799999999",
          },
          center: "29.371436189713901,55.716388873097699",
        },
      },
      kuhbanan: {
        name: "کوهبنان",
        name_en: "Kuhbanan",
        location: {
          bb: {
            ne: "31.7113406,56.582627899999999",
            sw: "31.007166600000001,55.758836100000003",
          },
          center: "31.3822868168591,56.282772849725497",
        },
      },
      manoujan: {
        name: "منوجان",
        name_en: "Manoujan",
        location: {
          bb: {
            ne: "27.8061443,58.132895099999999",
            sw: "26.9349159,57.223185899999997",
          },
          center: "27.3254126631548,57.671212436529203",
        },
      },
      baft: {
        name: "بافت",
        name_en: "Baft",
        location: {
          bb: {
            ne: "29.577840999999999,56.978734199999998",
            sw: "28.5333459,56.038505700000002",
          },
          center: "29.012717343721501,56.491276943010398",
        },
      },
    },
    kermanshah: {
      ravansar: {
        name: "روانسر",
        name_en: "Ravansar",
        location: {
          bb: {
            ne: "35.008607599999998,46.825545499999997",
            sw: "34.524613700000003,46.357477500000002",
          },
          center: "34.739704394947097,46.589685061366197",
        },
      },
      "salaas babajani": {
        name: "ثلاث باباجانی",
        name_en: "Salaas Babajani",
        location: {
          bb: {
            ne: "35.0458687,46.4060828",
            sw: "34.590898000000003,45.7542799",
          },
          center: "34.788867508823003,46.051752031652399",
        },
      },
      dalahou: {
        name: "دالاهو",
        name_en: "Dalahou",
        location: {
          bb: {
            ne: "34.639686699999999,46.536765199999998",
            sw: "34.1223922,45.9086985",
          },
          center: "34.406488644813599,46.245236762885099",
        },
      },
      sahne: {
        name: "صحنه",
        name_en: "Sahne",
        location: {
          bb: {
            ne: "34.801649400000002,47.854919600000002",
            sw: "34.329641700000003,47.114216599999999",
          },
          center: "34.5705225067681,47.505780950796698",
        },
      },
      sonqor: {
        name: "سنقر",
        name_en: "Sonqor",
        location: {
          bb: {
            ne: "35.071651099999997,47.925535799999999",
            sw: "34.629637700000004,47.069733300000003",
          },
          center: "34.862578206968401,47.543425758374902",
        },
      },
      kermanshah: {
        name: "کرمانشاه",
        name_en: "Kermanshah",
        location: {
          bb: {
            ne: "34.8070381,47.514422400000001",
            sw: "33.789394199999997,46.427017800000002",
          },
          center: "34.297916224290198,47.013661502571601",
        },
      },
      "gilan-e gharb": {
        name: "گیلانغرب",
        name_en: "Gilan-e Gharb",
        location: {
          bb: {
            ne: "34.420049599999999,46.669216499999997",
            sw: "33.685353300000003,45.603754199999997",
          },
          center: "34.071398374980703,45.978162282768103",
        },
      },
      javanroud: {
        name: "جوانرود",
        name_en: "Javanroud",
        location: {
          bb: {
            ne: "35.093793499999997,46.592277000000003",
            sw: "34.628823199999999,45.920391700000003",
          },
          center: "34.852806587228599,46.283283658022199",
        },
      },
      "sarpol-e zahab": {
        name: "سرپل ذهاب",
        name_en: "Sarpol-e Zahab",
        location: {
          bb: {
            ne: "34.846110799999998,46.149093700000002",
            sw: "34.212492500000003,45.653637500000002",
          },
          center: "34.540009875880699,45.885861765595699",
        },
      },
      paveh: {
        name: "پاوه",
        name_en: "Paveh",
        location: {
          bb: {
            ne: "35.281867800000001,46.514560600000003",
            sw: "34.906801100000003,45.963077499999997",
          },
          center: "35.0631679597954,46.258111610364601",
        },
      },
      kangavar: {
        name: "کنگاور",
        name_en: "Kangavar",
        location: {
          bb: {
            ne: "34.647388800000002,48.1046111",
            sw: "34.252447799999999,47.7110293",
          },
          center: "34.477169560784397,47.916085393931702",
        },
      },
      "eslamabad gharb": {
        name: "اسلام آباد غرب",
        name_en: "Eslamabad Gharb",
        location: {
          bb: {
            ne: "34.351456900000002,46.998099099999997",
            sw: "33.7353314,46.317818600000003",
          },
          center: "34.026337430170202,46.680861156742097",
        },
      },
      "ghasre shirin": {
        name: "قصر شیرین",
        name_en: "Ghasre Shirin",
        location: {
          bb: {
            ne: "34.603944400000003,45.797807599999999",
            sw: "33.797426899999998,45.403356700000003",
          },
          center: "34.196305527759598,45.6093975738277",
        },
      },
      harsin: {
        name: "هرسین",
        name_en: "Harsin",
        location: {
          bb: {
            ne: "34.578684199999998,47.699652",
            sw: "34.103624000000003,47.243482200000003",
          },
          center: "34.3473141692293,47.455634257154003",
        },
      },
    },
    "kohgiluye and boyerahmad": {
      kohgiluyeh: {
        name: "کهگیلویه",
        name_en: "Kohgiluyeh",
        location: {
          bb: {
            ne: "31.4815988,50.785111399999998",
            sw: "30.5646266,50.106301299999998",
          },
          center: "31.059213587767601,50.484370397956702",
        },
      },
      basht: {
        name: "باشت",
        name_en: "Basht",
        location: {
          bb: {
            ne: "30.6839209,51.299427999999999",
            sw: "30.2114026,50.802785399999998",
          },
          center: "30.460740910266399,51.069474148622398",
        },
      },
      choram: {
        name: "چرام",
        name_en: "Choram",
        location: {
          bb: {
            ne: "31.020283800000001,51.207640499999997",
            sw: "30.5900894,50.607842900000001",
          },
          center: "30.772448307715599,50.872866051038002",
        },
      },
      dena: {
        name: "دنا",
        name_en: "Dena",
        location: {
          bb: {
            ne: "31.227373,51.596219599999998",
            sw: "30.779919100000001,50.948073800000003",
          },
          center: "31.016602517020502,51.264851627185699",
        },
      },
      bahmai: {
        name: "بهمئی",
        name_en: "Bahmai",
        location: {
          bb: {
            ne: "31.2684818,50.412357999999998",
            sw: "30.708674599999998,49.889684600000002",
          },
          center: "31.012760326842098,50.1229095254943",
        },
      },
      "boyer ahmad": {
        name: "بویراحمد",
        name_en: "Boyer Ahmad",
        location: {
          bb: {
            ne: "31.451661000000001,51.890602399999999",
            sw: "30.371947299999999,50.477152599999997",
          },
          center: "30.8387080644017,51.231497912247697",
        },
      },
      gachsaran: {
        name: "گچساران",
        name_en: "Gachsaran",
        location: {
          bb: {
            ne: "30.6426351,51.186173199999999",
            sw: "29.9279872,50.383218599999999",
          },
          center: "30.292223236081401,50.746657340186502",
        },
      },
      landeh: {
        name: "لنده",
        name_en: "Landeh",
        location: {
          bb: {
            ne: "31.128493299999999,50.538139200000003",
            sw: "30.830778899999999,50.164101799999997",
          },
          center: "30.9880098166769,50.355385033077702",
        },
      },
    },
    gilan: {
      tavalash: {
        name: "طوالش",
        name_en: "Tavalash",
        location: {
          bb: {
            ne: "38.295699399999997,49.063829900000002",
            sw: "37.506596000000002,48.569740099999997",
          },
          center: "37.888477161396203,48.785266208473701",
        },
      },
      fouman: {
        name: "فومن",
        name_en: "Fouman",
        location: {
          bb: {
            ne: "37.344705400000002,49.424881300000003",
            sw: "36.998081300000003,48.869852199999997",
          },
          center: "37.179258594550902,49.134484884003797",
        },
      },
      roudbar: {
        name: "رودبار",
        name_en: "Roudbar",
        location: {
          bb: {
            ne: "37.093944299999997,50.160288399999999",
            sw: "36.562787899999996,49.116226300000001",
          },
          center: "36.798154810871701,49.6185652855143",
        },
      },
      roudsar: {
        name: "رودسر",
        name_en: "Roudsar",
        location: {
          bb: {
            ne: "37.209022500000003,50.603814499999999",
            sw: "36.632282199999999,50.086397499999997",
          },
          center: "36.878251105273101,50.318761704041201",
        },
      },
      siahkal: {
        name: "سیاهکل",
        name_en: "Siahkal",
        location: {
          bb: {
            ne: "37.191842399999999,50.1299481",
            sw: "36.686082300000002,49.709085600000002",
          },
          center: "36.943135492463,49.900384158455402",
        },
      },
      rasht: {
        name: "رشت",
        name_en: "Rasht",
        location: {
          bb: {
            ne: "37.457074400000003,49.9145386",
            sw: "37.0257115,49.474693500000001",
          },
          center: "37.272569443150203,49.682052025426103",
        },
      },
      lahijan: {
        name: "لاهیجان",
        name_en: "Lahijan",
        location: {
          bb: {
            ne: "37.390491699999998,50.228177100000003",
            sw: "37.0868994,49.773422199999999",
          },
          center: "37.220033768864397,50.036120777636498",
        },
      },
      astara: {
        name: "آستارا",
        name_en: "Astara",
        location: {
          bb: {
            ne: "38.453352199999998,48.881436899999997",
            sw: "38.239992000000001,48.569752999999999",
          },
          center: "38.351623377591302,48.742389465644699",
        },
      },
      masal: {
        name: "ماسال",
        name_en: "Masal",
        location: {
          bb: {
            ne: "37.5774562,49.216413500000002",
            sw: "37.309683800000002,48.799384000000003",
          },
          center: "37.3993870967521,49.028091421330501",
        },
      },
      rezvanshahr: {
        name: "رضواشهر",
        name_en: "Rezvanshahr",
        location: {
          bb: {
            ne: "37.668640000000003,49.1975865",
            sw: "37.390629400000002,48.692252400000001",
          },
          center: "37.529807646872499,48.950024833102901",
        },
      },
      shaft: {
        name: "شفت",
        name_en: "Shaft",
        location: {
          bb: {
            ne: "37.308574999999998,49.5398493",
            sw: "36.962116299999998,49.0976602",
          },
          center: "37.086766452457802,49.374199645450297",
        },
      },
      somesara: {
        name: "صومعه سرا",
        name_en: "Somesara",
        location: {
          bb: {
            ne: "37.498677800000003,49.549983699999999",
            sw: "37.251230499999998,49.040166499999998",
          },
          center: "37.353136432695699,49.296703303239603",
        },
      },
      "astaneh ashrafiyeh": {
        name: "آستانه اشرفیه",
        name_en: "Astaneh Ashrafiyeh",
        location: {
          bb: {
            ne: "37.467646600000002,50.186060699999999",
            sw: "37.197256400000001,49.772830800000001",
          },
          center: "37.3300557215005,49.986000080754799",
        },
      },
      langeroud: {
        name: "لنگرود",
        name_en: "Langeroud",
        location: {
          bb: {
            ne: "37.296217400000003,50.276541399999999",
            sw: "36.957376199999999,49.935589800000002",
          },
          center: "37.120764226171502,50.108928317161201",
        },
      },
      "bandar anzali": {
        name: "بندر انزلی",
        name_en: "Bandar Anzali",
        location: {
          bb: {
            ne: "37.570780800000001,49.680904900000002",
            sw: "37.387150300000002,49.195661000000001",
          },
          center: "37.461486909208702,49.410916984000899",
        },
      },
      amlash: {
        name: "املش",
        name_en: "Amlash",
        location: {
          bb: {
            ne: "37.128182000000002,50.276690000000002",
            sw: "36.785650799999999,50.015126799999997",
          },
          center: "36.963525984721102,50.137627130238201",
        },
      },
    },
    golestan: {
      "gonbad kavous": {
        name: "گنبد کاووس",
        name_en: "Gonbad Kavous",
        location: {
          bb: {
            ne: "38.107040300000001,55.683181599999998",
            sw: "37.062661800000001,54.547344500000001",
          },
          center: "37.562120802923801,55.103035970077102",
        },
      },
      kolaleh: {
        name: "کلاله",
        name_en: "Kolaleh",
        location: {
          bb: {
            ne: "37.813100200000001,56.050275599999999",
            sw: "37.322684600000002,55.293808599999998",
          },
          center: "37.555023129127697,55.577328877450498",
        },
      },
      "agh ghola": {
        name: "آق قلا",
        name_en: "Agh Ghola",
        location: {
          bb: {
            ne: "37.453629300000003,54.857736299999999",
            sw: "36.911400800000003,54.218188900000001",
          },
          center: "37.163148924456102,54.527407318548498",
        },
      },
      galikash: {
        name: "گالیکش",
        name_en: "Galikash",
        location: {
          bb: {
            ne: "37.461758500000002,56.012494500000003",
            sw: "37.183078399999999,55.334367499999999",
          },
          center: "37.324040723095898,55.672765699921001",
        },
      },
      gorgan: {
        name: "گرگان",
        name_en: "Gorgan",
        location: {
          bb: {
            ne: "36.971757699999998,54.744922299999999",
            sw: "36.512805499999999,54.217473200000001",
          },
          center: "36.7510061904824,54.505414954301898",
        },
      },
      "minoo dasht": {
        name: "مینودشت",
        name_en: "Minoo Dasht",
        location: {
          bb: {
            ne: "37.275233900000003,55.712136000000001",
            sw: "37.0104981,55.230409000000002",
          },
          center: "37.135544382734899,55.4614225721118",
        },
      },
      "bandar gaz": {
        name: "بندرگز",
        name_en: "Bandar Gaz",
        location: {
          bb: {
            ne: "36.821167000000003,54.059701099999998",
            sw: "36.647242200000001,53.858131899999997",
          },
          center: "36.730358257224999,53.962979231048799",
        },
      },
      gomishan: {
        name: "گمیشان",
        name_en: "Gomishan",
        location: {
          bb: {
            ne: "37.345583900000001,54.4149058",
            sw: "36.948376099999997,53.904815300000003",
          },
          center: "37.1452788233102,54.147200864794698",
        },
      },
      "ali abad": {
        name: "علی آباد",
        name_en: "Ali Abad",
        location: {
          bb: {
            ne: "37.090773300000002,55.123598399999999",
            sw: "36.597897099999997,54.677982999999998",
          },
          center: "36.842879070673099,54.852863884225002",
        },
      },
      kordkooy: {
        name: "کردکوی",
        name_en: "Kordkooy",
        location: {
          bb: {
            ne: "36.879503900000003,54.373156700000003",
            sw: "36.495617000000003,54.012172",
          },
          center: "36.6817000023172,54.197997783606702",
        },
      },
      "azad shahr": {
        name: "آزادشهر",
        name_en: "Azad Shahr",
        location: {
          bb: {
            ne: "37.189686299999998,55.5997512",
            sw: "36.798657900000002,55.055320600000002",
          },
          center: "36.988663137956799,55.338714927770297",
        },
      },
      maravehtapeh: {
        name: "مراوه تپه",
        name_en: "Maravehtapeh",
        location: {
          bb: {
            ne: "38.124448600000001,56.313564100000001",
            sw: "37.502433400000001,55.543036200000003",
          },
          center: "37.853749182291402,55.917635367410703",
        },
      },
      ramiyan: {
        name: "رامیان",
        name_en: "Ramiyan",
        location: {
          bb: {
            ne: "37.144430700000001,55.271421699999998",
            sw: "36.785429899999997,54.904702200000003",
          },
          center: "36.955554289970998,55.098331265853702",
        },
      },
    },
    lorestan: {
      "khoram abad": {
        name: "خرم آباد",
        name_en: "Khoram Abad",
        location: {
          bb: {
            ne: "33.894415000000002,49.000734899999998",
            sw: "32.890200100000001,48.0486589",
          },
          center: "33.384778548506603,48.570693352891197",
        },
      },
      poldokhtar: {
        name: "پلدختر",
        name_en: "Poldokhtar",
        location: {
          bb: {
            ne: "33.465425000000003,48.412545000000001",
            sw: "32.651518099999997,47.471276199999998",
          },
          center: "33.126447620754597,47.975576521345701",
        },
      },
      dowreh: {
        name: "دوره",
        name_en: "Dowreh",
        location: {
          bb: {
            ne: "33.880474800000002,48.258166199999998",
            sw: "33.3312338,47.621185699999998",
          },
          center: "33.589713856966902,47.949629469108999",
        },
      },
      azna: {
        name: "ازنا",
        name_en: "Azna",
        location: {
          bb: {
            ne: "33.742377500000003,49.716387300000001",
            sw: "33.222714000000003,49.225116700000001",
          },
          center: "33.500845886997098,49.443276616366298",
        },
      },
      aligoudarz: {
        name: "الیگودرز",
        name_en: "Aligoudarz",
        location: {
          bb: {
            ne: "33.565483,50.019260299999999",
            sw: "32.724060399999999,48.828549299999999",
          },
          center: "33.1220986559509,49.416101245998803",
        },
      },
      boroujerd: {
        name: "بروجرد",
        name_en: "Boroujerd",
        location: {
          bb: {
            ne: "34.123515400000002,49.077173100000003",
            sw: "33.603362599999997,48.465931300000001",
          },
          center: "33.889816859292303,48.756186084255603",
        },
      },
      delfan: {
        name: "دلفان",
        name_en: "Delfan",
        location: {
          bb: {
            ne: "34.377642799999997,48.335126600000002",
            sw: "33.8372387,47.4240961",
          },
          center: "34.061727673111797,47.826146110232003",
        },
      },
      selseleh: {
        name: "سلسله",
        name_en: "Selseleh",
        location: {
          bb: {
            ne: "34.012794100000001,48.518155399999998",
            sw: "33.645764,47.712919999999997",
          },
          center: "33.826538524493102,48.169737613677597",
        },
      },
      doroud: {
        name: "دورود",
        name_en: "Doroud",
        location: {
          bb: {
            ne: "33.743562099999998,49.326173400000002",
            sw: "33.277884200000003,48.785317599999999",
          },
          center: "33.520120614856801,49.071813987824399",
        },
      },
      "kouh dasht": {
        name: "کوهدشت",
        name_en: "Kouh Dasht",
        location: {
          bb: {
            ne: "33.876797699999997,47.8555742",
            sw: "33.301982000000002,46.8384924",
          },
          center: "33.5442259418802,47.390153463891302",
        },
      },
      rumeshkhan: {
        name: "رومشکان",
        name_en: "Rumeshkhan",
        location: {
          bb: {
            ne: "33.3682412,47.639884600000002",
            sw: "33.146399799999998,47.184852800000002",
          },
          center: "33.272160499473003,47.411700485013803",
        },
      },
    },
    mazandaran: {
      tonekabon: {
        name: "تنکابن",
        name_en: "Tonekabon",
        location: {
          bb: {
            ne: "36.8702872,51.128914299999998",
            sw: "36.2660032,50.512060499999997",
          },
          center: "36.601908192812303,50.832944799909598",
        },
      },
      gelougah: {
        name: "گلوگاه",
        name_en: "Gelougah",
        location: {
          bb: {
            ne: "36.796430999999998,54.069554099999998",
            sw: "36.594929999999998,53.640292500000001",
          },
          center: "36.693881129381403,53.803060306897898",
        },
      },
      jouybar: {
        name: "جویبار",
        name_en: "Jouybar",
        location: {
          bb: {
            ne: "36.783754299999998,53.010798899999998",
            sw: "36.5695628,52.808939000000002",
          },
          center: "36.674338567731503,52.914224870178302",
        },
      },
      amol: {
        name: "آمل",
        name_en: "Amol",
        location: {
          bb: {
            ne: "36.626707400000001,52.5314142",
            sw: "35.763757499999997,51.732559299999998",
          },
          center: "36.113032848662499,52.244969013926003",
        },
      },
      sari: {
        name: "ساری",
        name_en: "Sari",
        location: {
          bb: {
            ne: "36.8240579,53.951687",
            sw: "35.960875999999999,52.926371199999998",
          },
          center: "36.322725417462699,53.357268581317399",
        },
      },
      "savad kouh": {
        name: "سوادکوه",
        name_en: "Savad Kouh",
        location: {
          bb: {
            ne: "36.389697300000002,53.232297299999999",
            sw: "35.829141800000002,52.605194599999997",
          },
          center: "36.104239934620203,52.922531585218202",
        },
      },
      neka: {
        name: "نکا",
        name_en: "Neka",
        location: {
          bb: {
            ne: "36.8466953,54.028780500000003",
            sw: "36.332239299999998,53.231380700000003",
          },
          center: "36.513711175250599,53.5562560170167",
        },
      },
      chalous: {
        name: "چالوس",
        name_en: "Chalous",
        location: {
          bb: {
            ne: "36.696910000000003,51.481856999999998",
            sw: "36.153262599999998,50.999178800000003",
          },
          center: "36.4027680889929,51.258192179322897",
        },
      },
      "fereydoun kenar": {
        name: "فریدونکنار",
        name_en: "Fereydoun Kenar",
        location: {
          bb: {
            ne: "36.698692999999999,52.602409799999997",
            sw: "36.577086600000001,52.470306999999998",
          },
          center: "36.6439804507663,52.528873253509303",
        },
      },
      kelardasht: {
        name: "کلاردشت",
        name_en: "Kelardasht",
        location: {
          bb: {
            ne: "36.597602100000003,51.273192999999999",
            sw: "36.236701600000004,50.916009500000001",
          },
          center: "36.427345045295802,51.079908830079901",
        },
      },
      "abas abad": {
        name: "عباس آباد",
        name_en: "Abas Abad",
        location: {
          bb: {
            ne: "36.737690100000002,51.297775600000001",
            sw: "36.533797300000003,51.033096100000002",
          },
          center: "36.646507135814502,51.159003353817901",
        },
      },
      "miyan doroud": {
        name: "میاندورود",
        name_en: "Miyan Doroud",
        location: {
          bb: {
            ne: "36.843220899999999,53.431271899999999",
            sw: "36.403288799999999,53.137435600000003",
          },
          center: "36.599675163812897,53.2584950991032",
        },
      },
      ramsar: {
        name: "رامسر",
        name_en: "Ramsar",
        location: {
          bb: {
            ne: "36.961835600000001,50.779516999999998",
            sw: "36.575437700000002,50.351557499999998",
          },
          center: "36.778313295820503,50.554182054357597",
        },
      },
      babol: {
        name: "بابل",
        name_en: "Babol",
        location: {
          bb: {
            ne: "36.617009500000002,52.798914799999999",
            sw: "35.930948600000001,52.469889299999998",
          },
          center: "36.302073690759102,52.623489133774697",
        },
      },
      "qaem shahr": {
        name: "قائمشهر",
        name_en: "Qaem Shahr",
        location: {
          bb: {
            ne: "36.573490399999997,53.048584699999999",
            sw: "36.367501500000003,52.722863199999999",
          },
          center: "36.456255204873003,52.882468826040999",
        },
      },
      noshahr: {
        name: "نوشهر",
        name_en: "Noshahr",
        location: {
          bb: {
            ne: "36.672509099999999,51.939446199999999",
            sw: "36.255097300000003,51.3182689",
          },
          center: "36.434717744866099,51.605577660966802",
        },
      },
      simorq: {
        name: "سیمرغ",
        name_en: "Simorq",
        location: {
          bb: {
            ne: "36.6422004,52.897624800000003",
            sw: "36.515017100000001,52.749775200000002",
          },
          center: "36.578829958533603,52.816436102009597",
        },
      },
      babolsar: {
        name: "بابلسر",
        name_en: "Babolsar",
        location: {
          bb: {
            ne: "36.743519999999997,52.845316500000003",
            sw: "36.5934016,52.552154399999999",
          },
          center: "36.667313538083,52.700790864547898",
        },
      },
      behshahr: {
        name: "بهشهر",
        name_en: "Behshahr",
        location: {
          bb: {
            ne: "36.943568200000001,54.132152099999999",
            sw: "36.451466199999999,53.261431899999998",
          },
          center: "36.670716689834101,53.688620606002701",
        },
      },
      "mahmoud abad": {
        name: "محمودآباد",
        name_en: "Mahmoud Abad",
        location: {
          bb: {
            ne: "36.685544800000002,52.484220899999997",
            sw: "36.5293098,52.181385300000002",
          },
          center: "36.605561838686597,52.334672930174897",
        },
      },
      nour: {
        name: "نور",
        name_en: "Nour",
        location: {
          bb: {
            ne: "36.615960299999998,52.287574999999997",
            sw: "36.022574800000001,51.326545199999998",
          },
          center: "36.2739296081633,51.893410761863798",
        },
      },
    },
    markazi: {
      arak: {
        name: "اراک",
        name_en: "Arak",
        location: {
          bb: {
            ne: "34.534503700000002,50.312171499999998",
            sw: "33.750049199999999,49.263108099999997",
          },
          center: "34.141017148430898,49.805616302799798",
        },
      },
      mahalat: {
        name: "محلات",
        name_en: "Mahalat",
        location: {
          bb: {
            ne: "34.154173999999998,50.762340999999999",
            sw: "33.626026799999998,50.151504299999999",
          },
          center: "33.882955289093204,50.450797855474697",
        },
      },
      delijan: {
        name: "دلیجان",
        name_en: "Delijan",
        location: {
          bb: {
            ne: "34.342132499999998,51.053177300000002",
            sw: "33.7005725,50.257732699999998",
          },
          center: "34.017134339843999,50.750698902715101",
        },
      },
      khondab: {
        name: "خنداب",
        name_en: "Khondab",
        location: {
          bb: {
            ne: "34.6317083,49.5394705",
            sw: "34.123463399999999,48.949050499999998",
          },
          center: "34.346399474485501,49.221518379332601",
        },
      },
      ashtiyan: {
        name: "آشتیان",
        name_en: "Ashtiyan",
        location: {
          bb: {
            ne: "34.620798100000002,50.365993699999997",
            sw: "34.256662400000003,49.755929600000002",
          },
          center: "34.431273640159702,50.052376219391299",
        },
      },
      komijan: {
        name: "کمیجان",
        name_en: "Komijan",
        location: {
          bb: {
            ne: "34.961627,49.507827599999999",
            sw: "34.524796600000002,49.105394199999999",
          },
          center: "34.7255631741263,49.306010108686699",
        },
      },
      khomein: {
        name: "خمین",
        name_en: "Khomein",
        location: {
          bb: {
            ne: "33.941443100000001,50.455080899999999",
            sw: "33.383440899999997,49.595826199999998",
          },
          center: "33.666881931046802,49.997613194449499",
        },
      },
      shazand: {
        name: "شازند",
        name_en: "Shazand",
        location: {
          bb: {
            ne: "34.2119328,49.687435299999997",
            sw: "33.595260400000001,48.9546536",
          },
          center: "33.891474511412397,49.3036415788741",
        },
      },
      tafresh: {
        name: "تفرش",
        name_en: "Tafresh",
        location: {
          bb: {
            ne: "35.058897299999998,50.166078400000004",
            sw: "34.521539599999997,49.568994000000004",
          },
          center: "34.811706325861302,49.863031067658703",
        },
      },
      saveh: {
        name: "ساوه",
        name_en: "Saveh",
        location: {
          bb: {
            ne: "35.417881000000001,50.748572600000003",
            sw: "34.748089100000001,49.261668800000002",
          },
          center: "35.074591180614497,50.032896465916998",
        },
      },
      zarandiyeh: {
        name: "زرندیه",
        name_en: "Zarandiyeh",
        location: {
          bb: {
            ne: "35.571581600000002,50.964053900000003",
            sw: "35.096177300000001,49.493926600000002",
          },
          center: "35.365187390119601,50.328824281693699",
        },
      },
      farahan: {
        name: "فراهان",
        name_en: "Farahan",
        location: {
          bb: {
            ne: "34.9548165,49.897799499999998",
            sw: "34.330385300000003,49.419190399999998",
          },
          center: "34.614764268641302,49.628263352988803",
        },
      },
    },
    hormozgan: {
      larak: {
        name: "لارک",
        name_en: "Larak",
        location: {
          bb: {
            ne: "26.888731499999999,56.4128167",
            sw: "26.8182738,56.312053900000002",
          },
          center: "26.852762184953001,56.360716569889902",
        },
      },
      qeshm: {
        name: "قشم",
        name_en: "Qeshm",
        location: {
          bb: {
            ne: "26.9952182,56.280695399999999",
            sw: "26.538111300000001,55.262024799999999",
          },
          center: "26.783832158828901,55.811451092632801",
        },
      },
      shidvar: {
        name: "شیدور",
        name_en: "Shidvar",
        location: {
          bb: {
            ne: "26.797921599999999,53.4215144",
            sw: "26.784925099999999,53.401025199999999",
          },
          center: "26.791612499173599,53.410790599520404",
        },
      },
      siri: {
        name: "سیری",
        name_en: "Siri",
        location: {
          bb: {
            ne: "25.9425624,54.578251000000002",
            sw: "25.873395599999998,54.471549199999998",
          },
          center: "25.905465606582599,54.529082123586001",
        },
      },
      "tonb kouchak": {
        name: "تنب کوچک",
        name_en: "Tonb Kouchak",
        location: {
          bb: {
            ne: "26.2488636,55.153266100000003",
            sw: "26.234325699999999,55.137504399999997",
          },
          center: "26.241540101885601,55.145514424112498",
        },
      },
      farour: {
        name: "فارور",
        name_en: "Farour",
        location: {
          bb: {
            ne: "26.317016299999999,54.540780699999999",
            sw: "26.2484778,54.484970500000003",
          },
          center: "26.286553037154,54.5151582410545",
        },
      },
      "bandar abbas": {
        name: "بندرعباس",
        name_en: "Bandar Abbas",
        location: {
          bb: {
            ne: "27.957881799999999,56.979742399999999",
            sw: "26.9979297,55.271555800000002",
          },
          center: "27.5324300535313,56.239088787957201",
        },
      },
      khamir: {
        name: "خمیر",
        name_en: "Khamir",
        location: {
          bb: {
            ne: "27.525715300000002,56.057645200000003",
            sw: "26.900324699999999,54.893221699999998",
          },
          center: "27.281502290436801,55.4963521843286",
        },
      },
      jask: {
        name: "جاسک",
        name_en: "Jask",
        location: {
          bb: {
            ne: "26.274797899999999,59.238120700000003",
            sw: "25.4104633,57.187983899999999",
          },
          center: "25.875973910927701,58.292241831094103",
        },
      },
      bashagerd: {
        name: "بشاگرد",
        name_en: "Bashagerd",
        location: {
          bb: {
            ne: "26.9417586,59.023322899999997",
            sw: "26.073237800000001,57.387049300000001",
          },
          center: "26.441939943268501,58.183034939318198",
        },
      },
      bastak: {
        name: "بستک",
        name_en: "Bastak",
        location: {
          bb: {
            ne: "27.466990200000001,55.388498499999997",
            sw: "26.912646500000001,53.828064099999999",
          },
          center: "27.194710970077999,54.488948575747202",
        },
      },
      sirik: {
        name: "سیریک",
        name_en: "Sirik",
        location: {
          bb: {
            ne: "26.967111899999999,57.527903299999998",
            sw: "26.091001899999998,56.9552312",
          },
          center: "26.490340527966602,57.229312770246501",
        },
      },
      roudan: {
        name: "رودان",
        name_en: "Roudan",
        location: {
          bb: {
            ne: "28.001337899999999,57.4750935",
            sw: "27.096889699999998,56.8473027",
          },
          center: "27.607979833036101,57.1551023387211",
        },
      },
      "haji abad": {
        name: "حاجی آباد",
        name_en: "Haji Abad",
        location: {
          bb: {
            ne: "28.880206999999999,57.008870399999999",
            sw: "27.7170819,55.244445599999999",
          },
          center: "28.2402347564901,56.007751265381899",
        },
      },
      parsian: {
        name: "پارسیان",
        name_en: "Parsian",
        location: {
          bb: {
            ne: "27.334167900000001,53.832503799999998",
            sw: "26.945173499999999,52.736308200000003",
          },
          center: "27.135284634425201,53.261813885667898",
        },
      },
      minab: {
        name: "میناب",
        name_en: "Minab",
        location: {
          bb: {
            ne: "27.453397899999999,57.815901199999999",
            sw: "26.473777800000001,56.800125800000004",
          },
          center: "26.9398793557862,57.330235764016699",
        },
      },
      "bandar-e lengeh": {
        name: "بندر لنگه",
        name_en: "Bandar-e Lengeh",
        location: {
          bb: {
            ne: "27.1324291,55.5747255",
            sw: "26.493633899999999,53.421462300000002",
          },
          center: "26.839491363789499,54.624788961891198",
        },
      },
      lavan: {
        name: "لاوان",
        name_en: "Lavan",
        location: {
          bb: {
            ne: "26.848755000000001,53.390619000000001",
            sw: "26.783101200000001,53.155924200000001",
          },
          center: "26.811450924028399,53.271563568438502",
        },
      },
      "abou mousa": {
        name: "ابوموسی",
        name_en: "Abou Mousa",
        location: {
          bb: {
            ne: "25.904714899999998,55.056760599999997",
            sw: "25.845555999999998,55.004522899999998",
          },
          center: "25.875535577366801,55.032352627127999",
        },
      },
      hendourabi: {
        name: "هندورابی",
        name_en: "Hendourabi",
        location: {
          bb: {
            ne: "26.6953703,53.672071600000002",
            sw: "26.654049100000002,53.592921799999999",
          },
          center: "26.6740586028805,53.6313674400255",
        },
      },
      hormoz: {
        name: "هرمز",
        name_en: "Hormoz",
        location: {
          bb: {
            ne: "27.1013436,56.502194699999997",
            sw: "27.030041300000001,56.419136199999997",
          },
          center: "27.061431104910898,56.4604961550735",
        },
      },
      "tonb bozorg": {
        name: "تنب بزرگ",
        name_en: "Tonb Bozorg",
        location: {
          bb: {
            ne: "26.285085599999999,55.321705000000001",
            sw: "26.2485778,55.284003800000001",
          },
          center: "26.265051419490899,55.305079766837103",
        },
      },
      kish: {
        name: "کیش",
        name_en: "Kish",
        location: {
          bb: {
            ne: "26.5776401,54.048752499999999",
            sw: "26.4948604,53.897762999999998",
          },
          center: "26.5345657578569,53.972132639206301",
        },
      },
      hengam: {
        name: "هنگام",
        name_en: "Hengam",
        location: {
          bb: {
            ne: "26.681723999999999,55.912149700000001",
            sw: "26.6110486,55.841984099999998",
          },
          center: "26.644322226841599,55.879516984336298",
        },
      },
      "farour kouchak": {
        name: "فارور کوچک",
        name_en: "Farour Kouchak",
        location: {
          bb: {
            ne: "26.125686600000002,54.448423400000003",
            sw: "26.114814299999999,54.437792100000003",
          },
          center: "26.1199656636811,54.442485764291199",
        },
      },
    },
    hamedan: {
      bahar: {
        name: "بهار",
        name_en: "Bahar",
        location: {
          bb: {
            ne: "35.161233699999997,48.688210400000003",
            sw: "34.750332899999997,48.136734500000003",
          },
          center: "34.983579934789702,48.362648545513103",
        },
      },
      "kaboudar ahang": {
        name: "کبودرآهنگ",
        name_en: "Kaboudar Ahang",
        location: {
          bb: {
            ne: "35.718671200000003,48.918855000000001",
            sw: "34.956071700000003,47.864371499999997",
          },
          center: "35.365738054665201,48.380748977847901",
        },
      },
      hamedan: {
        name: "همدان",
        name_en: "Hamedan",
        location: {
          bb: {
            ne: "35.085414100000001,49.186942199999997",
            sw: "34.581580500000001,48.351262200000001",
          },
          center: "34.824314421972197,48.799680995177297",
        },
      },
      nahavand: {
        name: "نهاوند",
        name_en: "Nahavand",
        location: {
          bb: {
            ne: "34.431840800000003,48.538993300000001",
            sw: "34.000752400000003,47.883946399999999",
          },
          center: "34.221539319391901,48.243189618959399",
        },
      },
      "asad abad": {
        name: "اسدآباد",
        name_en: "Asad Abad",
        location: {
          bb: {
            ne: "34.984865300000003,48.269670400000003",
            sw: "34.5864598,47.795561399999997",
          },
          center: "34.778978310198099,48.028469970762998",
        },
      },
      razan: {
        name: "رزن",
        name_en: "Razan",
        location: {
          bb: {
            ne: "35.733034600000003,49.434517499999998",
            sw: "35.182508599999998,48.5504915",
          },
          center: "35.432574314264102,48.973312466791697",
        },
      },
      famenin: {
        name: "فامنین",
        name_en: "Famenin",
        location: {
          bb: {
            ne: "35.244457199999999,49.470902199999998",
            sw: "34.8412668,48.832559799999999",
          },
          center: "35.090729946929102,49.186968527259701",
        },
      },
      malayer: {
        name: "ملایر",
        name_en: "Malayer",
        location: {
          bb: {
            ne: "34.672463899999997,49.174908500000001",
            sw: "34.001485600000002,48.377865999999997",
          },
          center: "34.322821463032099,48.793777956196799",
        },
      },
      touyserkan: {
        name: "تویسرکان",
        name_en: "Touyserkan",
        location: {
          bb: {
            ne: "34.765898399999998,48.585453200000003",
            sw: "34.336356299999998,48.071108799999998",
          },
          center: "34.542852998511698,48.332148097061499",
        },
      },
    },
  },
};