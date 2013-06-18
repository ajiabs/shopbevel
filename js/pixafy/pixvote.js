
	//init publish and subscription methodology
	initPubSub();

	$j(function()
	{
		$j("body").on("click", ".view-comments-display", function( e )
		{
			var self = $j(this);

			var container = self.parents( "#comment-area-" + self.data("design-id") ).find( '.comments-list' );

			//show the loader while we process the request
			showLoader( container );

			$j.post(
				getUrl(VOTE_FRONTNAME + "/comment/getcomments"),
				{id:self.data("design-id")},
				buildCommentList,
				"json"
			);

			e.preventDefault();

		});

		$j("body").on("click", ".vote-btn.logged-in.new-vote", function()
		{
			// show vote success message and disable vote button
			// update vote count
			var button = $j( this ),
				alert = button.siblings( '.following-alert' ),
				countContainer = button.siblings( '.vote-count' ),
				count = countContainer.children( 'span' ),
				newCount = parseInt( count.text(), 10 );
				newCount++;

				// fade out the count container and the button
				countContainer.fadeOut( 300 );
				button.fadeOut( 300, function() {
					// remove the vote-btn class to disable and set the inactive class
					button.removeClass( 'vote-btn first-vote' ).addClass( 'grey' );
					// fade in the success message
					alert.fadeIn( 300, function() {
						// pause for 1.5 seconds, then fade out alert
						setTimeout(function() {
							alert.fadeOut( 300, function() {
								// upon completion fade in button, update vote count
								// and fade in vote count container
								button.fadeIn( 300 );
								count.text( newCount );
								countContainer.fadeIn( 300 );

								// a bleet, a bleet, a bleet, that's all folks!!
							});
						}, 1500);
					});
				});


			if(!VOTE_UPDATE_OPERATING)
			{
				//update vote counter
				VOTE_UPDATE_OPERATING = true;
				var data = {total_vote_count: $j('.counter').data('stop')};
				data.total_vote_count++;
				updateCounter(data);
			}

			$j.post(
				getUrl(VOTE_FRONTNAME + "/index/addVote"),
				{id:$j(this).data("design-id")},
				voteResult,
				'json'
			);
		});

		$j("body").on("click", ".comments-login", showLogin);


		$j("body").on("click", ".vote-comment-btn", function()
		{
			//get all the elements we need and the comment and package them
			var btn = $j( this );
			var designId = btn.attr( 'data-design-id' );
			var btns = $j( '.btn-' + designId );
			var inputs = $j( '.input-' + designId );
			var commentLists = $j( '.list-' + designId );

			var obj = {
				designId : designId,
				btns : btns,
				inputs : inputs,
				commentLists : commentLists
			};

			$j.publish( 'comment/bundle/packaged', obj );
		});

	}); //end doc ready


	/* set subscriptions */
	$j.subscribe( 'comment/bundle/packaged', function( e, obj ) {
		prepCommentForSubmit( obj );
	});

	$j.subscribe( 'comment/bundle/prepped', function( e, obj ) {
		submitComment( obj );
	});

	$j.subscribe( 'comment/returned', function( e, obj ) {
		prepCommentList( obj );
	});

	$j.subscribe( 'comment/prepped', function( e, obj ) {
		showCommentList( obj );
	});

	$j.subscribe ( 'update/counts', function( e, obj ) {
		updateCommentCount( obj );
	});

	function prepCommentForSubmit( obj ) {

		//boolean for error handling
		var hasComment = false;

		$j.each( obj.inputs, function() {
			//cache this
			var self = $j( this );

			//get the value of the input
			var val = self.val();

			//init our comment text var
			var commentText = '';

			if ( ( val !== '' ) && ( val !== 'Add a comment...' ) ) {
				commentText = val;

				//clear the value in the comment field
				self.val( '' );

				obj.commentText = commentText;

				showLoaders( obj );

				hasComment = true;
			}
		});

		if ( !hasComment ) {
			window.alert( 'Please add a comment before submitting.' );
		}
	}

	function submitComment( obj ) {

		$j.each( obj.btns, function() {
			$j( this ).removeClass( 'vote-comment-btn' );
		});

		$j.post(
			getUrl(VOTE_FRONTNAME + "/comment/addcomment"),
			{
				'comment':
				{
					'design_id' : obj.designId,
					'comment' : obj.commentText
				}
			},
			addCommentResult,
			'json'
		);
	}

	//success passes back id, comment, image, comment count
	function addCommentResult( data )
	{
		var container = {};

		if(data.hasOwnProperty("error"))
		{
			//cache the container for our comments
			container = $j( "#comment-area-" + data.id ).find( '.comments-list' );

			//if error, show the end user the error of their ways
			var listItem = $j('<li/>', {
				'class'	: 'comments-list-item clear-box',
				text	: data.error
			}).appendTo( container );
			showComments( container );
		}
		else
		{
			//get all the elements we need and package them
			var designId = data.id;
			var btns = $j( '.btn-' + designId );
			var inputs = $j( '.input-' + designId );
			var commentLists = $j( '.list-' + designId );

			var obj = {
				designId : designId,
				btns : btns,
				inputs : inputs,
				commentLists : commentLists,
				data : data //add data to the object
			};

			$j.publish( 'comment/returned', obj );
		}
	}

	function prepCommentList( obj ) {
		$j.each( obj.commentLists, function() {
			buildCommentListItem( obj.data, this );
		});

		$j.publish( 'comment/prepped' , obj );
		$j.publish( 'update/counts', obj );
	}

	function buildCommentListItem( item, container ) {

		//create our list item and append it to our ul
		var listItem = $j('<li/>', {
			'class'	: 'comments-list-item clear-box'
		}).appendTo( container );

		//create our image container div and append it to the list item
		var imageContainer = $j('<div/>', {
			'class'	: 'comment-image'
		}).appendTo(listItem);

		//create our image and append it to the image container
		var image = $j('<img/>', {
			src : item.image
		}).appendTo( imageContainer );

		//create our comment container and append it to the list item
		var commentContainer = $j('<div/>', {
			'class'	: 'comment-text'
		}).appendTo( listItem );

		//create our author span and append it to the comment container
		var author = $j('<span/>', {
			'class'	: 'author',
			text	: item.author + ' '
		}).appendTo( commentContainer );

		//create our comment span and append it to the comment container
		var comment = $j('<span/>', {
			'class'	: 'text',
			text	: item.comment
		}).appendTo( commentContainer );
	}

	function buildCommentList( data ) {

		var container = {};

		//cache the container for our comments
		container = $j( '.comments-list.see-all' );

		container.empty();

		$j.each(data.comments, function() {
			buildCommentListItem( this, container);
		});

		showComments( container );

		container.jScrollPane();
	}

	function showCommentList( obj ) {
		//remove loaders from comment list parents
		$j.each( obj.commentLists, function() {
			removeLoader( this );
		});

		//show the containers
		$j.each( obj.commentLists, function() {
			showComments( this );
		});

		//add class back to submit button
		$j.each( obj.btns, function() {
			$j( this ).addClass( 'vote-comment-btn' );
		});
	}

	function showComments( el ) {
		$j( el ).fadeIn( 300 );
	}

	function updateCommentCount( obj ) {
		var countBoxes = $j( '.count-box-' + obj.designId );
		var count = obj.data.count;

		$j.each( countBoxes, function() {
			$j( this ).text( count );
		});
	}

	function showLoaders( obj ) {

		$j.each( obj.commentLists, function() {
			var self = $j( this );
			var parent = self.parent();

			self.hide();
			self.empty();

			$j('<div>', {
				'class' : 'comments-loader'
			}).prependTo( parent );
		});

		$j.publish( 'comment/bundle/prepped', obj );
	}

	function showLoader( container ) {

		var self = $j( this );
		var parent = self.parent();

		self.hide();
		self.empty();

		$j('<div>', {
			'class' : 'comments-loader'
		}).prependTo( parent );

	}

	function removeLoader( container ) {
		$j( container ).siblings( '.comments-loader' ).remove();
	}

	//sucess returns id,vote_count
	function voteResult(data)
	{
		var container = {};

		if(data.hasOwnProperty("error"))
		{
			// var message = '<?php echo $this->getLayout()->createBlock("cms/block")->setBlockId("submit-vote-login")->toHtml() ?>';
			// $j( '.comment-input' ).on( 'focus', function() {
			// 	showLogin( message );
			// });
		}
		else
		{
            $j(document).trigger('shopBevel_designVoted', data.id);
			if(data.hasOwnProperty("store_credit"))
			{
				$j("#credit-amt").html(data.store_credit);
			}
			//get our vote count container and update the value by one
			var voteCountContainer =	$j('p#vote-' + data.id);
			voteCountContainer.text( data.vote_count );
			//checkForVotes();
			$j('.counter').queue(function(next){
				if(!VOTE_UPDATE_OPERATING)
				{
					VOTE_UPDATE_OPERATING = true;
					updateCounter(data);
				}
				next();
			});
		}
	}


	function shareVoteResult(data)
	{
		var container = {};

		if(data.hasOwnProperty("error"))
		{
			// var message = '<?php echo $this->getLayout()->createBlock("cms/block")->setBlockId("submit-vote-login")->toHtml() ?>';
			// $j( '.comment-input' ).on( 'focus', function() {
			// 	showLogin( message );
			// });
		}
		else
		{
			ajaxVoteUpdate(data);
			//$j.post(getUrl(VOTE_FRONTNAME + "/index/addCredit"));
			// dismiss the current modal
			$j( '.pixafy-modal-dismiss' ).trigger( 'click' );

			// show thank you for sharing modal
			trackPopup($j("#popup_thanks_subject").html(), $j("#popup_thanks_version").html());
			trackSuccess($j("#popup_share_subject").html());
			setTimeout(function() {
				$j( '#share-double-thanks-trigger' ).trigger( 'click' );
			}, 1000);

		}
	}

	function ajaxVoteUpdate(data)
	{
		$j('.counter').queue(function(next){
			if(!VOTE_UPDATE_OPERATING)
			{
				VOTE_UPDATE_OPERATING = true;
				updateCounter(data);
			}
			next();
		});
	}

	function trackPopup(trackSubject, trackVersion)
	{
		$j.post(
					getUrl(VOTE_FRONTNAME + "/index/track"),
					{
						subject:trackSubject,
						version:trackVersion
					}
				);
	}

	function trackSuccess(trackSubject)
	{
		$j.post(
					getUrl(VOTE_FRONTNAME + "/index/trackSuccess"),
					{
						subject:trackSubject
					}
				);
	}

	function setVoteItems( items ) {

		$j.each( items, function() {
			var self =$j( this );
			if ( self.hasClass( 'no-right-margin' ) ) {
				self.removeClass('no-right-margin');
			}
		});

		$j( '#vote-item-collection' ).columnsUpdate( {}, items );
	}


	//Function to update pagination vars after ajax call
	function updatePaginationVars()
	{
		OPERATING = false;
	}

	//Function for top section next/prev button
	$j(function()
	{
		$j("body").on("click", "#next-btn", function(){
			if(FEATURED_PAGE < FEATURED_TOTAL)
			{
				var container = $j( '.vote-display-container' );
				container.css( 'height', 432 + 'px' ).children().fadeOut( 300 );
				$j('<div>', {
					'class' : 'featured-loader'
				}).appendTo(container);
				featuredPaginate("next");
			}
		});
		$j("body").on("click", "#prev-btn", function(){
			if(FEATURED_PAGE > 1)
			{
				var container = $j( '.vote-display-container' );
				container.css( 'height', 432 + 'px' ).children().fadeOut( 300 );
				$j('<div>', {
					'class' : 'featured-loader'
				}).appendTo(container);
				featuredPaginate("prev");
			}
		});
	});

	function featuredPaginate(direction)
	{
		switch(direction)
		{
			case "next":
			FEATURED_PAGE++;
			break;
			case "prev":
			FEATURED_PAGE--;
			break;
			default:
			break;
		}
		$j.post(FEATURED_PAGEINATION_URL, {page:FEATURED_PAGE}, featuredPaginateResult, 'json');
	}

	function featuredPaginateResult(data)
	{
		$j(".vote-display-container").html(data.html).css('height', 'auto');
	}

	//Function for pagination init
	function setPaginationVars(perPage, startingPage, total, url)
	{
		PER_PAGE = perPage;
		PAGE = startingPage;
		TOTAL = total;
		PAGINATION_URL = url;
	}

	function checkScrollBottom()
	{
		//var target = window;
		var target = ".footer-container";
		var curHeight =  $j(window).scrollTop() + $j(window).height();
		return curHeight >= $j(target).height() || $j(target).height() - curHeight == 1;
	}


	//Function to get next set of designs
	function ajaxPaginate()
	{
		OPERATING = true;
		PAGE++;
		$j.post(PAGINATION_URL, {page:PAGE}, ajaxPaginateResult, 'json');
	}

	//Process result from pagination call
	function ajaxPaginateResult(data)
	{
		var items = $j(data.html).find(".product-designer");
		$j.each( items, function() {

			var self =	$j( this ),
				id =	self.find( '.vote-info' )
							.children( 'button' )
							.attr( 'data-design-id' ),
				image =	self.find( '.prdt-img' )
							.children( 'img' )
							.first(),
				link =	self.find( '.vote-item-share' )
							.attr( 'data-url' ),
				name =	self.find( '.vote-dsgnr-desc' )
							.find( '.txt-dsgnr-name' )
							.text(),
				src =	'';

				if ( image.length > 0 ) {
					src = image.attr( 'src' );
				}

				VOTE_ITEM_INFO[ id ] = {
					name: name,
					url: link,
					image: src
				};
		});
		setVoteItems( items );
		updatePaginationVars();
	}

	//Function to update pagination vars after ajax call
	function updatePaginationVars()
	{
		OPERATING = false;
	}


//Function for pagination init
	function setPaginationVars(perPage, startingPage, total, url)
	{
		PER_PAGE = perPage;
		PAGE = startingPage;
		TOTAL = total;
		PAGINATION_URL = url;
	}

	function checkScrollBottom()
	{
		//var target = window;
		var target = ".footer-container";
		var curHeight =  $j(window).scrollTop() + $j(window).height();
		return curHeight >= $j(target).height() || $j(target).height() - curHeight == 1;
	}

	//Function to get next set of designs
	function ajaxPaginate()
	{
		OPERATING = true;
		PAGE++;
		$j.post(PAGINATION_URL, {page:PAGE}, ajaxPaginateResult, 'json');
	}

	function initPubSub() {
		/**
		 * Create an observer pattern for jQuery
		 * allows you to alias trigger with publish,
		 * on with subscribe,
		 * and off with unsubcsribe
		 * Useful for custom events
		 *
		 *USAGE:
		 * $(el).subscribe('customEvent', function() {
		 *       do something when customEvent is published
		 *
		 *       No longer need to subscribe to custom event
		 *       $(el).unsubscribe('customEvent');
		 * });
		 *
		 * Custom event is fired
		 * $.publish('customEvent');
		 */

		var o = $j( {} );
		$j.each({
			trigger: 'publish',
			on: 'subscribe',
			off: 'unsubscribe'
		}, function( key, val ){
			jQuery[ val ] = function() {
				o[ key ].apply( o, arguments );
			};
		});
	}

	/* set up functionality for sharing a vote on facebook */
	(function( $ ) {
		$(function() {

			/* instanitate share modals */
			$( '#share-double-modal' ).pixafyModal({
				transition: 'fade',
				keepOverlay: 'true',
				enter: function() {
					$( '.pixafy-modal-overlay' ).addClass( 'share-overlay' );
				},
				exit: function() {
					// shows store credit with an attention drawing animation
					setTimeout(function() {
						bounceCredit();
					}, 500);
					$( '.pixafy-modal-overlay' ).removeClass( 'share-overlay' );
				}
			});

			$( '#share-double-thanks-modal' ).pixafyModal({
				transition: 'fade',
				noOverlay: 'true'
			});

			$( '#share-double-no-thanks-modal' ).pixafyModal({
				transition: 'fade',
				noOverlay: 'true'
			});

			/* set up our event handlers */
			$( 'body' ).on( 'click', '.vote-btn.first-vote.logged-in', function() {
				/*
				 * when an item is voted pass the item id to the share modal so
				 * we can share the right information
				 */
				var self = $( this );
				var shareModal = $( '#share-double-modal' );
				var id = self.attr( 'data-design-id' );

					shareModal.attr( 'data-elem', id );

					shareModal.find( '.profile-pic' )
						.attr( 'src', VOTE_ITEM_INFO[ id ].designer_image );

					shareModal.find( '.profile-name' )
						.text( VOTE_ITEM_INFO[ id ].designer_name );

					shareModal.find( '.profile-loc' )
						.text( VOTE_ITEM_INFO[ id ].designer_loc );


					//ajax post to create tracking record
					//j("#popup_share_subject").html()
					trackPopup($("#popup_share_subject").html(), $("#popup_share_version").html());
					
					$( '#share-double-trigger' ).trigger( 'click' );
			});

			$( 'body' ).on( 'click', '.pixafy-modal-overlay.share-overlay', function() {
				trackPopup($("#popup_no_thanks_subject").html(), $("#popup_no_thanks_version").html());
				setTimeout(function() {
					$( '#share-double-no-thanks-trigger' ).trigger( 'click' );
				}, 1000);
			});

			$( '#no-share-btn' ).on( 'click', function() {
				$( '.pixafy-modal-dismiss' ).trigger( 'click' );
				trackPopup($("#popup_no_thanks_subject").html(), $("#popup_no_thanks_version").html());
				setTimeout(function() {
					$( '#share-double-no-thanks-trigger' ).trigger( 'click' );
				}, 1000);
			});

			$( 'body' ).on( 'click', '.fb-share-double-btn', function( e ) {
				/*
				 * when the fb share button is clicked prep our FB.ui call with
				 * the proper share info
				 */
				shareVote();
				e.preventDefault();
			});
		}); //end doc ready

		function shareVote() {
			var	modal = $( '#share-double-modal' ),
				id = modal.attr( 'data-elem' ),
				image = VOTE_ITEM_INFO[ id ].image,
				link = VOTE_ITEM_INFO[ id ].url,
				name = VOTE_ITEM_INFO[ id ].item_name;

			FB.ui({
				method: 'feed',
				name: name,
				link: link,
				picture: image,
				caption: 'I voted to produce this: Vote too!',
				description: $j( 'meta[property="og:description"]' ).attr( 'content' )
			},
			function( response ) {
				if ( response && response.post_id ) {
					$j.post(
						getUrl(VOTE_FRONTNAME + "/index/addVote"),
						{"id":id, "shareType":"fb"},
						shareVoteResult,
						'json'
					);

				} else {
					// ?? need direction on error handling ??
				}
			});
		}

		function bounceCredit() {
			// get the store credit container and show it
			var credit = $( '.account-container' ).find( '.store-credit' );

			if ( credit.css( 'display' ) !== 'block' || 'inline' ) {
				credit.fadeIn( 300 );

				// once it is visible, bounce it!
				(function bounce( index, direction ) {
					index++;

					if( index !== 5 ) {
						switch ( direction ) {
							case 'up':
								credit.animate({
									'bottom' : 27 + 'px'
								},300, function() {
									bounce( index, 'down' );
								});
							break;

							case 'down':
								credit.animate({
									'bottom' : 18 + 'px'
								},300, function() {
									bounce( index, 'up' );
								});
							break;
						}
					}
				})( 0, 'up' );
			}
		}
	})( jQuery );

