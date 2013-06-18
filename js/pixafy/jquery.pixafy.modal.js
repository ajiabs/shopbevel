/*
 *
 * @PixafyModal - jQuery Plugin
 * Author: Kurtis Kemple
 * Date: Feb 6th, 2013
 * Desc: A modal widget, has options for fade or slide transitions
 *       For usage see the wiki, https://wiki.pixafy.com/index.php/FrontEnd/PixafyWidgets/Modal
 *
 */


;(function( $, window, document, undefined ) {
	'use strict';

	var Modal = {

		/* set our initialization code */
		init: function( el, opts ) {
			var self = this;

			this.element = $( el );

			/* store "this" in a variable so we don't lose track of our reference to the plugin object */
			this.elementParent = this.element.parent();

			/* boolean to hook on to, to see if this instance is active */
			var isActive = false;

			/* set the default variables for the modal */
			this.defaults = {
				onload: this.element.attr('data-modal-onload') || 'false',
				overlayDismiss: this.element.attr('data-modal-overlay-dismiss') || 'true',
				title: this.element.attr('data-modal-title') || 'none',
				transition: this.element.attr('data-modal-transition') || 'slide',
				modalEntrance: this.element.attr('data-modal-entrance') || 'top',
				modalExit: this.element.attr('data-modal-exit') || this.element.attr('data-modal-entrance') || 'top',
				classNames: this.element.attr('data-modal-classNames') || '',
				modalID: this.element.attr('data-modal-ID') || '',
				position: this.element.attr('data-modal-position') || 'fixed',
				keepOverlay: this.element.attr('data-modal-keep-overlay') || 'false',
				noOverlay: this.element.attr('data-modal-no-overlay') || 'false',
				loaded: function() {},
				enter: function() {},
				exit: function() {},
				repositioned: function() {}
			};

			/* combine default options with user options */
			this.options = $.extend( {}, this.defaults, opts || {} );

			/* create our wrapper and add a modal body and our actual element to it */
			this.modalOuter = $( '<div/>', {
				'class' : 'pixafy-modal-wrapper ' + self.options.classNames,
				id: self.options.modalID
			}).appendTo( 'body' );

			/* check to see if there is a title, if so add it to the modal */
			if ( this.options.title !== 'none' ) {
				this.modalHeader = $( '<div/>', {
					'class' : 'pixafy-modal-header'
				}).appendTo( this.modalOuter );

				$('<h2/>', {
					text: self.options.title
				}).appendTo( this.modalHeader );
			}

			/* add the modal body */
			this.modalBody = $( '<div/>', {
				'class' : 'pixafy-modal-body'
			}).appendTo( this.modalOuter );

			/* add the actual modal content to the modal */
			this.element.appendTo( this.modalBody );

			/* initialize our events for our modal */
			this.events();

			/*
			 * call the callback for content loaded from options and custom pixafy event,
			 * so the options callback will be scoped to this instance of a modal,
			 * but the pixafy custom event code will run no matter what modal is active
			 */
			this.options.loaded.call( this );
			$( document ).trigger( 'pixafy.modal.contentLoaded' );
		},

		events: function() {
			var self = this;

			$( window ).smartresize(function() {
				self.reposition();
			});

			$( 'body' ).on( 'click', '.pixafy-modal-dismiss', function(e) {

				e.preventDefault();

				/* if this modal is active then exit */
				if( self.isActive ) {
					self.exit();
				}
			});


			/* check to make sure overlay dismiss in enabled */
			if ( this.options.overlayDismiss.toLowerCase() === 'true' ) {
				$( 'body' ).on( 'click', '.pixafy-modal-overlay', function(e) {
					/* check to see if this modal is active */
					if(self.isActive) {
						e.stopPropagation();
						self.exit();
					}
				});
			}


			$( 'body' ).on( 'click', '.pixafy-modal-trigger',  function( e ) {
				e.preventDefault();
				/* make sure that this is the right modal to fire */
				var target = $( this );
				if ( target.attr( 'data-modal-container' ) === self.element.attr( 'id' ) ) {

					/* make sure another modal is not already open */
					if ( $( '.pixafy-modal-conainer' ).css( 'display' ) !== 'block' ) {
						self.enter();
					}
				}
			});

			/* if onload is true fire modal immediately */
			if ( this.options.onload.toLowerCase() === 'true' ) {
				this.enter();
			}
		},

		/* called when we want our modal to make its entrance */
		enter: function() {

			switch ( this.options.transition.toLowerCase() ) {
				case 'slide':
					switch ( this.options.modalEntrance.toLowerCase() ) {
						case 'top':
							this.makeEntrance('top');
							break;
						case 'bottom':
							this.makeEntrance('bottom');
							break;
						case 'left':
							this.makeEntrance('left');
							break;
						case 'right':
							this.makeEntrance('right');
							break;
						default:
							return 'error, no entrance set';
					}
					break;

				case 'fade':
					this.enterFade();
					break;

				default:
					return 'error, no transition set';
			}

			/* set active to true, used for dismissing the modal */
			this.isActive = true;
		},

		makeEntrance: function( str ) {

			/* make the HTML from the page visible */
			this.element.css({ 'display' : 'block' });

			this.modalOuter.css({
				'display' : 'block',
				'position' : this.options.position
			});

			/* set the initial positioning for the modal, make it visible */
			switch ( str ) {
				case 'top':
					this.modalOuter.css({ 'top' : -2000 });
					break;
				case 'bottom':
					this.modalOuter.css({ 'top' : $( window ).height() + 2000 });
					break;
				case 'left':
					/* set the initial positioning for the modal, make it visible */
					this.modalOuter.css({ 'left' : -2000 });
					break;
				case 'right':
					this.modalOuter.css({ 'left' : $(window).width() + 2000 });
					break;
				default:
					return 'error, invalid entrance variable, accepted values "right, left, top, bottom"';
			}

			/* now that all elements have a display of block, get the height and width of the modal to set the position */

			/* check for window.innerHeight and window.innerWidth in case we are dealing with iPad or iPhone, otherwise we will
			 * not have a true dimesion
			 */
			var wH = window.innerHeight ? window.innerHeight : $(window).height();
			var wW = window.innerWidth ? window.innerWidth : $(window).width();
			var top = ( wH - this.modalOuter.height() ) / 2;
			var left = ( wW - this.modalOuter.width() ) / 2;

			/* set the final top positioning of the modal */
			switch ( str ) {
				case 'top':
					this.modalOuter.css({ 'left' : left });
					break;
				case 'bottom':
					this.modalOuter.css({ 'left' : left });
					break;
				case 'left':
					this.modalOuter.css({ 'top' : top });
					break;
				case 'right':
					this.modalOuter.css({ 'top' : top });
					break;
				default:
					return 'error, invalid entrance variable, accepted values "right, left, top, bottom"';
			}

			var self = this;

			/* add the overlay to the DOM, fade it in, once completed animate the modal in to view */
			if ( this.options.noOverlay.toLowerCase() === 'false' ) {
				$( '<div/>', {
					'class' : 'pixafy-modal-overlay'
				}).appendTo( 'body' ).fadeIn(300, function() {
					switch ( str ) {
						case 'top':
							self.modalOuter.animate({
								'top' : top
							}, 500);
						break;

						case 'bottom':
							self.modalOuter.animate({
								'top' : top
							}, 500);
						break;

						case 'left':
							self.modalOuter.animate({
								'left' : left
							}, 500);
						break;

						case 'right':
							self.modalOuter.animate({
								'left' : left
							}, 500);
						break;

						default:
							return 'error, invalid entrance variable, accepted values: "right, left, top, bottom"';
					}
				});
			} else {
				switch ( str ) {
					case 'top':
						self.modalOuter.animate({
							'top' : top
						}, 500);
					break;

					case 'bottom':
						self.modalOuter.animate({
							'top' : top
						}, 500);
					break;

					case 'left':
						self.modalOuter.animate({
							'left' : left
						}, 500);
					break;

					case 'right':
						self.modalOuter.animate({
							'left' : left
						}, 500);
					break;

					default:
						return 'error, invalid entrance variable, accepted values: "right, left, top, bottom"';
				}
			}

			self.options.enter.call( this );
			$( document ).trigger('pixafy.modal.enter');

		},

		enterFade: function() {
			var self = this;

			/* make the HTML from the page visible */
			this.element.css({'display' : 'block'});

			/* now that all elements have a display of block, get the height and width of the modal to set the position */
			var top = ( $( window ).height() - this.modalOuter.height() ) / 2;
			var left = ( $( window ).width() - this.modalOuter.width() ) / 2;

			/* set the initial positioning for the modal */
			this.modalOuter.css({
				'position' : 'fixed',
				'left' : left,
				'top' : top
			});

			/* add the overlay to the DOM, fade it in, once completed fade the modal in to view */
			if ( this.options.noOverlay.toLowerCase() === 'false' ) {
				$( '<div/>', {
					'class' : 'pixafy-modal-overlay'
				}).appendTo( 'body' ).fadeIn( 300, function() {
					self.modalOuter.fadeIn( 500, function() {

						/* fire the enter callbacks */
						self.options.enter.call( self );
						$( 'body' ).trigger( 'pixafy.modal.enter' );
					});
				});
			} else {
				self.modalOuter.fadeIn( 500, function() {

						/* fire the enter callbacks */
						self.options.enter.call( self );
						$( 'body' ).trigger( 'pixafy.modal.enter' );
					});
			}
		},

		exit: function() {
			var self = this;

			this.isActive = false;

			switch ( this.options.transition.toLowerCase() ) {
				case 'slide':
					switch ( this.options.modalExit.toLowerCase() ) {
						case 'top':
							this.modalOuter.animate({
								'top' : -2000
							}, 1000, function() {
								self.remove();
							});
							break;
						case 'bottom':
							this.modalOuter.animate({
								'top' : $( window ).height() + 2000
							}, 1000, function() {
								self.remove();
							});
							break;
						case 'left':
							this.modalOuter.animate({
								'left' : -2000
							}, 1000, function() {
								self.remove();
							});
							break;
						case 'right':
							this.modalOuter.animate({
								'left' : $( window ).width() + 2000
							}, 1000, function() {
								self.remove();
							});
							break;
						default:
							return 'error, no entrance set';
					}
					break;

				case 'fade':
					this.modalOuter.fadeOut(500, function() {
						self.remove();
					});
					break;

				default:
					return 'error, no transition set';
			}

		},

		remove: function() {
			var self = this;

			/* fade out overlay, then remove the modal and put the container back into its parent */
			if ( this.options.keepOverlay.toLowerCase() === 'false' ) {
				$( '.pixafy-modal-overlay' ).fadeOut(300, function() {
					$( '.pixafy-modal-overlay' ).remove();
					self.modalOuter.css({ 'display' : 'none' });

					/* fire the exit callback functions */
					self.options.exit.call( self );
					$( document ).trigger( 'pixafy.modal.exit' );
				});
			} else {
				self.modalOuter.css({ 'display' : 'none' });

				/* fire the exit callback functions */
				self.options.exit.call( self );
				$( document ).trigger( 'pixafy.modal.exit' );
			}
		},

		reposition: function() {
			var self = this;

			/* store window in a jQuery object as we will use it a lot during repositioning */
			var $window = $( window );
			var wH = window.innerHeight ? window.innerHeight : $window.height();
			var wW = window.innerWidth ? window.innerWidth : $window.width();

			/* make sure that this modal is active */
			if ( this.isActive ) {

				/* do the initial reposition of the modal */
				this.modalOuter.dequeue().stop().animate({
					'left' : ( wW - self.modalOuter.width() ) / 2,
					'top' : ( wH - self.modalOuter.height() ) / 2
				},150, function() {

					/* fire the repositioned callback */
					self.options.repositioned.call( self );
					$( document ).trigger( 'pixafy.modal.resized' );
				});
			}
		}
	};

	$.fn.pixafyModal = function( opts ) {

		var obj = {};

		//if a modern browser use Object.create which prototypes our methods for us,
		//else just instantiate Modal
		if ( Object.create ) {
			obj = Object.create( Modal );
		} else {
			obj = Modal;
		}

		return this.each(function() {
			obj.init( this, opts );
		});
	};


	(function(sr){

		// debouncing function from John Hann
		// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
		var debounce = function (func, threshold, execAsap) {
		var timeout;

		return function debounced () {
			var obj = this, args = arguments;
			function delayed () {
				if (!execAsap)
				func.apply(obj, args);
				timeout = null;
			}

			if (timeout)
				clearTimeout(timeout);
			else if (execAsap)
				func.apply(obj, args);

				timeout = setTimeout(delayed, threshold || 100);
			};
		};

		// smartresize
		$.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

	})('smartresize');
})( jQuery, window, document );