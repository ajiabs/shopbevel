jQuery(document).ready(function(){
	
	jQuery('.my-account input[type="checkbox"]').ezMark();
	jQuery('.my-account input[type="radio"]').ezMark();
	
	/* login step one*/
	jQuery("#login-window-step-one-container").fancybox({
		'padding'			: 0,
		'transitionIn'		: 'fade',
		'titleShow'			:  false,
		'showCloseButton'	: false,
		'hideOnOverlayClick': true,
		'transitionOut'		: 'none'
	});

	/* login step two
	jQuery("#login-window-step-two-container").fancybox({
		'padding'			: 0,
		'transitionIn'		: 'fade',
		'titleShow'			:  false,
		'showCloseButton'	: false,
		'hideOnOverlayClick': true,
		'transitionOut'		: 'none'
	});
	*/
	
	/* login step Three*/
	jQuery("#login-window-step-three-container").fancybox({
		'padding'			: 0,
		'transitionIn'		: 'fade',
		'titleShow'			:  false,
		'showCloseButton'	: false,
		'hideOnOverlayClick': true,
		'transitionOut'		: 'none'
	});
	
	 ajaxCartOptions = {
		success:addToCartResult,
		error:addToCartResult,
		dataType:'json'
	
	}; 
	$j("#product_addtocart_form, .single-click-add").ajaxForm(ajaxCartOptions);

});


function getUrl(url)
{
	return site_url + url;
}

function addToCartResult(data)
{
	$j("#cart-cnt").html(data.cart_cnt);
	if(data.hasOwnProperty("emptied"))
	{
		$j("#item-list").html('');
		jQuery.fancybox({
			'padding'			: 0,
			'transitionIn'		: 'fade',
			'titleShow'			:  false,
			'showCloseButton'	: true,
			'hideOnOverlayClick': false,
			'overlayColor'      : '#f2f2f2',
			'transitionOut'		: 'none',
			'href'				: '#error-message-output'
		});
	}
	$j("#item-list").html(data.close_html);
	$j("#item-list").append(data.item_html);
	if(data.hasOwnProperty("action_html"))
	{
		$j("#item-list").append(data.action_html);
	}

	if(data.hasOwnProperty("time_left"))
	{
		//Modify for time zone
		target = new Date(data.time_left);
		target.setHours(target.getHours() - (target.getTimezoneOffset()/60));
		$j('#cart-time').countdown({until: target, format: 'MS', compact : true, onExpiry:expireCallback});
	}
	
	if(data.hasOwnProperty("emptied"))
	{
		$j("#item-list").append('<p class="cart-empty">You have no items in your shopping bag.</p>');
	}
	else
	{
		scrollToArea("#cartHeader");
		Enterprise.TopCart.showCart(6);
	}	
}

function scrollToArea(elementObj)
{
	var duration  = 1000;
	$j('html, body').animate({scrollTop : $j(elementObj).offset().top, easing: 'easeInOutQuint'}, duration);
}

function expireCallback()
{
	//window.location.reload();
	$j.post(getUrl("bevellogin/cart/expiredCart"),addToCartResult,  "json");
}

jQuery(window).load(function() {	
	/* Product Grid Fadein Effect */	
	jQuery('.product-grid-slider').cycle({                           
	       timeout:400,
	       delay:  -10000, 
	       fx: 'fade',
	       speed:  800
	 }).cycle("pause").hover(function() {
	       jQuery(this).cycle('resume');
	       
	 },function(){
	 	jQuery(this).cycle(0, 'fade').cycle('pause');
	 });
});


jQuery(document).ready(function(){

	jQuery('.category-products .products-grid li').each(function(){
		if(jQuery(this).find(".sold-out").length > 0 ) {
			jQuery(this).find('img').css({'opacity':'0.3'})
			jQuery(this).find('.sold-out  span').css({'border-color':'#d9d9d9', 'color':'#d9d9d9'})
			
		} else {
			
		}
	});	
	
	jQuery('.category-products .products-grid li .actions').hover(function() {
		jQuery('.sold-out', this).hide();
		jQuery('.join-waitlist', this).show()
		
	} , function () {
		jQuery('.sold-out', this).show();
		jQuery('.join-waitlist', this).hide()
	})
	
	
	jQuery('.category-products .products-grid li').hover(function() {
		jQuery('.left-stock', this).hide();
		jQuery('.add-product', this).show();
		
	} , function () {
		jQuery('.left-stock', this).show();
		jQuery('.add-product', this).hide();
	})
	

});