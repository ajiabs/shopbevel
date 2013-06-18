/**
 * Pixafy jQuery contentSlider
 * Author: Kurtis Kemple
 * Email: kkemple@pixafy.com
 * Desc: A simple content slider with fade or slide transitions, lightweight and has a few options
 */

;(function( $, window, document, undefined ) {
	'use strict';

	var Slider = function( el, opts) {
		this.init( el, opts );
		return this;
	};

	Slider.prototype = {
		init: function( el, opts ) {
			//cache the el and wrap in jquery
			this.element = $( el );

			// check to see if the element has position set,
			// if not add it so we can contain elements like the nav buttons
			if ( this.element.css( 'position' ) !== 'fixed' ||
				 this.element.css( 'position' ) !== 'absolute' ||
				 this.element.css( 'position' ) !== 'relative' ) {
					this.element.css( 'position', 'relative' );
			}

			//create default settings
			this.defaults = {
				duration: 3000, // 3 seconds
				speed: 400,
				transition: 'slide',
				navClass: '',
				infinite: true,
				reverse: false,
				useControls: true,
				auto: true,
				pauseOnHover: false,
				hideOverflow: true,
				beforeSlide: function() {},
				afterSlide: function() {}
			};

			//extend the default settings with end use settings
			this.options = $.extend( {}, this.defaults, opts || {} );

			//init our timer id, this will be used to control our interval functions
			this.timerId = 0;

			//init our no slides flag
			this.noSlides = false;

			//init our busy flag
			this.isBusy = false;

			//build our slider
			this.build();
		},

		build: function() {

			//cache this
			var self = this;

			//store any information we may need about the slider element
			this.slider = {
				width: this.element.width(),
				height: this.element.height(),
				slides: this.element.children().css({
					'float' : 'left',
					'margin' : 0,
					'width' : this.element.width() + 'px'
				}).remove()
			};

			//if no elements to slide return false and alert the end user
			if ( this.slider.slides.length === 0 ) {
				window.console.log( 'Error no elements found to slide!! jQuery content slider.' );
				this.noSlides = true;
				return false;
			}

			//create a container for our elements and set it up for a fade system
			this.sliderContainer = $( '<div/>', {
				'class' : 'content-slider-container' //add an id in case we need a hook
			})
				.css({
					'width' : this.slider.width + 'px', //set the width to the width of the children elments combined
					'height' : this.slider.height, //set height to match the slider height
					'position' : 'relative'
				})
				.attr( 'data-slider-id', this.sliderId )
				.appendTo( this.element )
				.append( this.slider.slides );

			if ( this.options.hideOverflow ) {
				this.sliderContainer.css( 'overflow', 'hidden' );
			}

			//prep our slides
			$.each(this.slider.slides, function() {
				$( this ).css({
					'position' : 'absolute',
					'top' : 0,
					'left' : self.slider.width,
					'display' : 'none'
				});
			});

			//show the first slide
			this.slider.slides.first()
				.addClass( 'first-item active' )
				.css( 'left', 0 ).show();

			//if there is only one element then return false and alert the user
			if ( this.slider.slides.length === 1 ) {
				window.console.log( 'Error: only one element found to slide!! Add another!! jQuery content slider.' );
				this.noSlides = true;
				return false;
			}

			if ( !this.noSlides ) {
				this.slider.slides.last()
					.addClass( 'last-item' );


				//build our controls if needed
				if ( this.options.useControls ) {
					this.buildControls();
				}

				this.events();

				if ( this.options.auto ) {
					this.timerId = window.setInterval( function() {
						//check to see if the slide needs to run in reverse
						switch ( self.options.reverse ) {
							case true:
								self.unsift.call( self );
							break;

							case false:
								self.sift.call( self );
							break;
						}
					}, this.options.duration );
				}
			}
		},

		sift: function() {

			this.isBusy = true;

			//call our before slide callback
			this.options.beforeSlide.call( this );

			switch ( this.options.transition.toLowerCase() ) {
				case 'slide':
					this.enterSlide();
				break;
				case 'fade':
					this.enterFade();
				break;
				default:
					return 'Available transitions are "fade" and "slide".';
			}
		},

		unsift: function() {

			this.isBusy = true;

			//call our before slide callback
			this.options.beforeSlide.call( this );

			switch ( this.options.transition.toLowerCase() ) {
				case 'slide':
					this.exitSlide();
				break;
				case 'fade':
					this.exitFade();
				break;
				default:
					return 'Available transitions are "fade" and "slide".';
			}
		},

		enterSlide: function() {

			//set variable we will need through the function
			var slides = this.slider.slides;
			var item;
			var nextItem;


			//get our currently active slide
			$.each( slides, function() {
				var currentItem = $( this );
				if ( currentItem.hasClass('active') ) {
					item = currentItem;
				}
			});


			//get our next item to animate, if this is the last slide item, get the first
			if ( item.hasClass( 'last-item' ) ) {
				//also make the item visible and add the active class
				nextItem = $( this.slider.slides ).first()
					.show()
					.addClass('active');

				//if infinite is false clear the timer and stop back on the first slide
				if ( !this.options.infinite) {
					clearInterval( this.timerId );
				}
			} else {
				//also make the item visible and add the active class
				nextItem = item.next()
								.show()
								.addClass('active');
			}

			//cache this
			var self = this;

			//animate our next slide in to view
			nextItem.stop().animate({

				'left' : 0 + 'px'

			}, self.options.speed, function() {

				//call our callback for after the content has transitioned
				self.options.afterSlide.call( self );

			});

			//animate the current slide out for the new slide
			item.stop().animate({

				'left' : -self.slider.width + 'px'

			}, self.options.speed, function() {

				//once animation is done hide the elem and repostion for next time it is called
				$( this ).hide()
						 .css( 'left', self.slider.width + 'px' );

				self.isBusy = false;

			}).removeClass('active'); //remove the active class

		},

		enterFade: function( index ) {

			//set variable we will need through the function
			var slides = this.slider.slides;
			var item;
			var nextItem;


			//get our currently active slide
			$.each( slides, function() {
				var currentItem = $( this );
				if ( currentItem.hasClass('active') ) {
					item = currentItem;
				}
			});


			//get our next item to animate, if this is the last slide item, get the first
			if ( item.hasClass( 'last-item' ) ) {

				//also make the item visible and add the active class
				nextItem = $( this.slider.slides ).first().css({
								'left' : 0 + 'px'
							}).addClass('active');

				//if infinite is false clear the timer and stop back on the first slide
				if ( !this.options.infinite) {
					clearInterval( this.timerId );
				}

			} else {

				//also make the item visible and add the active class
				nextItem =	item.next()
								.css({
									'left' : 0 + 'px'
								})
								.addClass('active');
			}

			//cache this
			var self = this;

			nextItem.stop()
					.fadeIn( self.options.speed, function() {

						//call our callback for after the content has transitioned
						self.options.afterSlide.call( self );
						self.isBusy = false;

					});

			item.stop()
				.fadeOut( self.options.speed )
				.removeClass('active'); //remove the active class
		},

		exitSlide: function() {

			//set variable we will need through the function
			var slides = this.slider.slides;
			var item;
			var nextItem;


			//get our currently active slide
			$.each( slides, function() {
				var currentItem = $( this );
				if ( currentItem.hasClass( 'active' ) ) {
					item = currentItem;
				}
			});

			//get our next item to animate, if this is the last slide item, get the first
			if ( item.hasClass( 'first-item' ) ) {

				//also make the item visible and add the active class
				//get the item in to position as well
				nextItem = $( this.slider.slides ).last()
								.css({
									'left' : -this.slider.width + 'px'
								})
								.show()
								.addClass('active');

				//if infinite is false clear the timer and stop back on the first slide
				if ( !this.options.infinite) {
					clearInterval( this.timerId );
				}

			} else {

				//also make the item visible and add the active class
				//get the item in to position as well
				nextItem = item.prev()
				.css({
					'left' : -this.slider.width + 'px'
				})
				.show()
				.addClass('active');
			}

			//cache this
			var self = this;

			//animate our next slide in to view
			nextItem.stop().animate({
				'left' : 0 + 'px'
			}, self.options.speed, function() {

				//call our callback for after the content has transitioned
				self.options.afterSlide.call( self );

			});

			//animate the current slide out for the new slide
			item.stop().animate({
				'left' : self.slider.width + 'px'
			}, self.options.speed, function() {

				//once animation is done hide the elem and repostion for next time it is called
				$( this ).hide().css( 'left', self.slider.width + 'px' );
				self.isBusy = false;

			}).removeClass('active'); //remove the active class

		},

		exitFade: function( index ) {

			//cache this
			var self = this;

			//set variable we will need through the function
			var slides = this.slider.slides;
			var item;
			var nextItem;


			//get our currently active slide
			$.each( slides, function() {
				var currentItem = $( this );
				if ( currentItem.hasClass('active') ) {
					item = currentItem;
				}
			});


			//get our next item to animate, if this is the last slide item, get the first
			if ( item.hasClass( 'first-item') ) {

				//also make the item visible and add the active class
				nextItem = $( this.slider.slides ).last()
								.css({
									'left' : 0 + 'px'
								})
								.addClass('active');

				//if infinite is false clear the timer and stop back on the first slide
				if ( !this.options.infinite) {
					clearInterval( this.timerId );
				}

			} else {

				//also make the item visible and add the active class
				nextItem = item.prev()
								.css({
									'left' : 0 + 'px'
								})
								.addClass('active');

			}

			nextItem.stop().fadeIn( self.options.speed, function() {

				//call our callback for after the content has transitioned
				self.options.afterSlide.call( self );
				self.isBusy = false;

			});

			item.stop().fadeOut( self.options.speed )
				.removeClass('active');
		},

		buildControls: function() {
			var self = this;

			this.navPrev = $( '<a/>', {
				href : '#',
				'class' : 'content-slider-prev-btn ' + self.options.navClass
			}).appendTo( this.sliderContainer );

			this.navNext = $( '<a/>', {
				href : '#',
				'class' : 'content-slider-next-btn ' + self.options.navClass
			}).appendTo( this.sliderContainer );
		},

		events: function() {
			var self = this;

			$( window ).resize(function() {
				//self.resize();
			});

			if ( this.options.useControls ) {


				/* controls events */
				this.navNext.on( 'click', function( event ) {
					if( !self.isBusy ) {
						//clear our timer
						window.clearInterval( self.timerId );

						self.sift.call( self );

						//check to see if auto is set
						if ( self.options.auto ) {
							//reset our timer and sift to next item
							self.timerId = window.setInterval( function() {
								//check to see if the slide needs to run in reverse
								switch ( self.options.reverse ) {
									case true:
										self.unsift.call( self );
									break;

									case false:
										self.sift.call( self );
									break;
								}
							}, self.options.duration );
						}
					}

					event.preventDefault();
					//event.stopPropagation();
				});

				this.navPrev.on( 'click', function( event ) {

					//make sure the event is firing from within this slider
					if ( !self.isBusy ) {
						//clear our timer
						window.clearInterval( self.timerId );

						self.unsift.call( self );

						//check to see if auto is set
						if ( self.options.auto ) {
							self.timerId = window.setInterval( function() {
								//check to see if the slide needs to run in reverse
								switch ( self.options.reverse ) {
									case true:
										self.unsift.call( self );
									break;

									case false:
										self.sift.call( self );
									break;
								}
							}, self.options.duration );
						}
					}

					event.preventDefault();
					//event.stopPropagation();
				});

			}

			if ( this.options.pauseOnHover ) {

				/* hover events */
				$( this.element ).on( 'mouseenter', function( event ) {

					//if set to auto and pauseOnHover is true
					if( self.options.pauseOnHover ) {
						window.clearInterval( self.timerId );
					}

					event.preventDefault();
					//event.stopPropagation();
				});

				$( this.element ).on( 'mouseleave', function( event ) {

					//if set to auto and pauseOnHover is true
					if( self.options.pauseOnHover ) {
						if ( self.options.auto ) {
							//clear our timer
							window.clearInterval( self.timerId );

							self.timerId = window.setInterval( function() {
								//check to see if the slide needs to run in reverse
								switch ( self.options.reverse ) {
									case true:
										self.unsift.call( self );
									break;

									case false:
										self.sift.call( self );
									break;
								}
							}, self.options.duration );
						}
					}

					event.preventDefault();
					//event.stopPropagation();
				});
			}
		},

		resize: function() {
			var width = this.element.width();
			var height = this.element.height();

			this.sliderContainer.css({
				'width' : width + 'px',
				'height' : height + 'px'
			});

			$.each( this.slider.slides, function() {
				$( this ).css({
					'width' : width + 'px',
					'height' : height + 'px'
				});
			});
		}
	};

	$.fn.contentSlider = function( opts ) {

		//return our object and init it
		return this.each(function() {
			return new Slider( this, opts );
		});
	};

})( jQuery, window, document );