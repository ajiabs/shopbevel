/**
 * jQuery tabs plugin
 * Author: Kurtis Kemple
 * Email: kkemple@pixafy.com
 * Desc: Lightweight easy tab plugin with click and hover event types
 */

;(function( $, window, document ) {
	'use strict';

	$.fn.tabs = function( opts ) {
		this.init = function( el, opts ) {

			//store our container element
			this.element = $( el );

			//store our nav items and tabs
			this.navItems = this.element.find( '.pixafy-tab-trigger' );
			this.tabs = this.element.find( '.pixafy-tab-container' );

			/**
			 * do our initial error handling,
			 * check for at least two nav items,
			 * check for at least two tabs,
			 * make sure we have an even number of tabs and nav items
			 */
			if ( this.navItems.length < 2 ) {
				window.console.log( 'Error: There are not enough navigation items to make tabbing possible, please add more. ' +
							this.element.attr( 'class' ) +
							' ' +
							this.element.id );
				return false;
			} else if ( this.tabs.length < 2 ) {
				window.console.log( 'Error: There are not enough tab items to make tabbing possible, please add more. ' +
							this.element.attr( 'class' ) +
							' ' +
							this.element.id );
				return false;
			} else if ( this.navItems.length !== this.tabs.length ) {
				window.console.log( 'Error: There are not an even number of tabs and nav items, please correct. ' +
							'tabs: ' + this.tabs.length +
							' ' +
							'navItems: ' + this.navItems.length );
				return false;
			}


			//set our defaults
			this.defaults = {
				eventType: 'click',
				speed : 0,
				index : 0,
				tabClass : 'tab',
				navClass : 'nav'
			};


			//compile final options from options from defaults and end user options
			this.options = $.extend( this.defaults, opts || {} );

			//init our timerId we will need for later
			this.timerId = 0;

			//init our busy flag
			this.busy = false;

			//build our tab section
			this.build();

			//assign our events
			this.events();
		},

		this.build = function() {

			//cache this
			var self = this;

			//hide all tabs but the first
			this.tabs.hide().addClass( this.options.tabClass );
			this.tabs.eq( this.options.index ).show().addClass( 'active' );

			this.navItems.addClass( this.options.tabClass );
			this.navItems.eq( this.options.index ).addClass( 'active' );
		},

		this.sift = function( id ) {

			//cache this
			var self = this;

			//remove the hashtag from the id parameter
			//id = id.replace( '#', '');
			var tabToHide;
			var tabToShow;
			var isActive = false;

			$.each( this.tabs, function() {
				var tab = $( this );
				if ( (tab.hasClass( 'active' ) ) && ( tab.attr( 'id' ) === id ) ) {

					isActive = true;

				} else if ( tab.hasClass( 'active' ) ) {

					tabToHide = tab;

				} else if ( tab.attr( 'id' ) === id ) {

					tabToShow = tab;
				}
			});

			if( isActive ) {
				return false;
			} else {
				this.busy = true;

				tabToHide.removeClass( 'active' ).fadeOut( self.options.speed / 2, function() {
					tabToShow.addClass( 'active' ).fadeIn( self.options.speed / 2, function() {
						self.busy = false;
					});
				});
			}
		},

		this.events = function() {
			//cache this
			var self = this;

			switch ( this.options.eventType.toLowerCase() ) {
				case 'click':
					$( this.navItems ).on( 'click', function( e ) {
						if( !self.busy ) {

							//cache the current nav item
							var navItem = $( this );

							//get the id of the item to show
							var id = navItem.attr( 'data-tab-container' );


							//update the current active nav item
							self.navItems.removeClass( 'active' );
							navItem.addClass( 'active' );

							//show new tab
							self.sift( id );

							e.preventDefault();
						}
					});
				break;
				case 'hover':
					$( this.navItems ).on( 'mouseenter', function( e ) {


						//cache the current nav item
						var navItem = $( this );

						if( !self.busy ) {

							//get the id of the item to show
							var id = navItem.attr( 'href' );


							//update the current active nav item
							self.navItems.removeClass( 'active' );
							navItem.addClass( 'active' );

							//show new tab
							self.sift( id );


						} else {
							self.timerId = setTimeout(function() {
								//get the id of the item to show
								var id = navItem.attr( 'href' );


								//update the current active nav item
								self.navItems.removeClass( 'active' );
								navItem.addClass( 'active' );

								//show new tab
								self.sift( id );
							}, self.options.speed );
						}

						e.preventDefault( self.timerId );
					});

					$( this.navItems ).on( 'mouseleave', function( e ) {
						window.clearTimeout(self.timerId );
					});
				break;
				default:
					window.console.log( 'Error: only acceptable event types are "click" and "hover", please correct. ' +
								'tabs: ' + this.tabs.length +
								' ' +
								'navItems: ' + this.navItems.length );
					return false;
			}

		};

		var self = this;
		return this.each(function() {
			self.init( self, opts );
		});
	};
})( jQuery, window, document, undefined );