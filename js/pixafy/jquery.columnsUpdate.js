;(function( $, window, document ) {
	'use strict';


	$.fn.columnsUpdate = function( opts, items ) {
		this.init = function( opts, items ) {
			this.defaults = {
				startIndex: undefined,
				duration: 400,
				limit: 999
			};

			this.options = $.extend( {}, this.defaults, opts );

			this.items = items;

			this.prep();
		},

		this.prep = function() {
			this.columns = this.children();

			if ( this.options.startIndex === undefined ) {
				this.counts = [];

				var self = this;

				$.each( this.columns, function() {
					var children = $( this ).children();
					self.counts.push( children.length );
				});

				var i = 0;
				var len = this.counts.length;
				var min = this.counts[ 0 ];

				for ( ; i < len; ) {
					if ( this.counts[ i ] < min ) {
						min = this.counts[ i ];
						this.options.startIndex = i;
					}
					i++;
				}

				if ( this.options.startIndex === undefined ) {
					this.options.startIndex = 0;
				}
			}

			this.fill();
		},

		this.fill = function() {
			var i = 0;
			var c = this.options.startIndex;
			var len = this.items.length;
			var reset = this.columns.length;

			for ( ; i < len; ) {
				var item = $( this.items[ i ] );
				var column = $( this.columns[ c ] );

				item.appendTo( column ).fadeIn( this.options.duration );

				i++;
				c++;

				if ( c === reset ) {
					c = 0;
				}
			}
		};

		var self = this;
		return this.each(function() {
			self.init( opts, items );
		});
	};
})( jQuery, window, document, undefined );