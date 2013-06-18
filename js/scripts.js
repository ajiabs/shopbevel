var IS_DESIGNER;
var POPUP_SHOWN = false;
var POPUP_COOKIE_ID = "popup_count"
var POPUP_COUNT;
var POPUP_ID = 0;
var DESIGN_ID = 0;
var CHECKOUT_URL = getUrl("checkout/onepage");
var POPUP_COOKIE_EXPIRATION = 365;
var REDIRECT_DESIGN = false;
jQuery(document).ready(function(){

    POPUP_COUNT = parseInt(getCookie(POPUP_COOKIE_ID));
    if(isNaN(POPUP_COUNT))
    {
        POPUP_COUNT = 0;
    }

    $j(".works").find("a").click(function(){
        highlghtNav($j(this));
    });

    //jQuery('.my-account input[type="checkbox"]').ezMark();
    //jQuery('.my-account input[type="radio"]').ezMark();

    /* login step one
     jQuery("#login-window-step-one-container").fancybox({
     'padding'			: 0,
     'transitionIn'		: 'fade',
     'titleShow'			:  false,
     'showCloseButton'	: false,
     'hideOnOverlayClick': true,
     'transitionOut'		: 'none'
     });

     /* login step Three
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
     */

    $j(".fb-login-btn").click(function(){
        FB.login(function(response) {
            if (response.authResponse) {
                FB.api('/me', function(response) {
                    var params = {};
                    params['login'] = {};
                    params['login']['fb-id'] = response.id;
                    params['login']['design-id'] = DESIGN_ID;
                    $j.post(
                        $j("#login-form").attr("action"),
                        params,
                        function(data)
                        {
                            //alert(data.error);
                            if(data.error == undefined) {
                                window.location.href = data.redirect_url;
                            } else {
                                $('#login-error').text(data.error).show();
                            }

                        },
                        'json'
                    );
                });
            }
        });
    });

    $j(".btn-fb-signup, .btn-signup-facebook, .btn-rua-designer").click(function(){
        //added class btn-rua-designer for facebook connect as designer
        if($j(this).data("is-designer"))
        {
            IS_DESIGNER = $j(this).data("is-designer");
        }
        else
        {
            IS_DESIGNER = 0;
        }
        FB.login(fbSignup, {scope:"email"});
    });
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

function checkPagePosition() {
    var winTop = 0;
    winTop = jQuery(window).scrollTop(), bodyHt = jQuery(document).height(), vpHt = jQuery(window).height();


    if(winTop < contentTop[0]){
        jQuery('.top-main-link').removeClass('current');
        jQuery('.header-arrow').stop().animate({'left': '37px'});
    }

    if((winTop >= contentTop[0]) && (winTop < contentTop[1])){
        jQuery('.top-main-link').removeClass('current');
        jQuery('.top-main-link.link-shop').addClass('current');
        jQuery('.header-arrow').stop().animate({'left': '193px'});
    }

    if((winTop >= contentTop[1]) && (winTop < contentTop[2])){
        jQuery('.top-main-link').removeClass('current');
        jQuery('.top-main-link.link-vote').addClass('current');
        jQuery('.header-arrow').stop().animate({'left': '325px'});
    }

    if((winTop >= contentTop[2]) && (winTop < contentTop[3])){
        jQuery('.top-main-link').removeClass('current');
        jQuery('.top-main-link.link-meet').addClass('current');
        jQuery('.header-arrow').stop().animate({'left': '470px'});
    }
}

function customSelectsChangeText(ele) {
    console.log('chng txt');
    var selectedText = jQuery(ele).find('option:selected').text();
    jQuery(ele).parent().find('.ipt-bvl').text(selectedText);
    console.log('chng txt-' + selectedText);
}

function customSelectsInitializeText(){
    jQuery('.select-wrapper').each(function(){
        var currentText = jQuery(this).find('select option:selected').text();
        jQuery(this).find('.ipt-bvl').text(currentText);
    });
}

function equalHeight(group) {
    var tallest = 0;

    group.each(function() {
        var thisHeight = jQuery(this).height();
        if(thisHeight > tallest) { tallest = thisHeight; }
    });
    group.css({'min-height': tallest});
}

function inputclear(thisfield, defaulttext) {
    if (thisfield.value == defaulttext) {
        thisfield.value = "";
    }
}

function inputrecall(thisfield, defaulttext) {
    if (thisfield.value == "") {
        thisfield.value = defaulttext;
    }
}

function iOSScroll() {
    if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
        // only for versions less than 5 that don't support css fixed position
        if (ver[0] < 5) {
            document.getElementById('header-top').style.webkitTransform = "translate3d(0, " + window.pageYOffset + "px, 0)";
        }
    }
}

function iOSversion() {
    if (/iP(hone|od|ad)/.test(navigator.platform)) {
        // supports iOS 2.0 and later: <http://bit.ly/TJjs1V>
        var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
        return [parseInt(v[1], 10), parseInt(v[2], 10), parseInt(v[3] || 0, 10)];
    }
}

function showFeedback() {
    jQuery('.overlay-box').hide();
    jQuery('.overlay-container').fadeIn(850);
    jQuery('.feedback-box').fadeIn(850);
}

function showLogin( str ) {
    // check if on vote page for proper overlay order
    if(jQuery('.cms-vote')) {
        jQuery('.cms-vote .header-container').css({'z-index': '9997'});
    }

    jQuery('.overlay-box').hide();
    jQuery('.sign-in-box .overlay').hide();

    //set a parameter that allows for custom messages in the login form
    var loginBox  = jQuery('.sign-in-box');
    var loginTitle = loginBox.children( '.overlay-cntnt' ).children( '.overlay-sign-in' ).children( '.overlay-title' );

    //check to see if the parameter is set
    var newTitle = '';

    if ( ( str !== undefined ) && ( str !== '<p></p>' ) && ( str !== '' ) ) {
        newTitle = str;

        //check to see if newTitle has a p tag, in case it came from a static block
        if( newTitle.indexOf( '<p>' ) !== -1 ) {
            //if it does create an object so we can strip the text from it
            newTitle = jQuery( str );
            loginTitle.text( newTitle.text() );
        } else {
            //if not then just append
            loginTitle.text( newTitle );
        }

    }

    jQuery('.overlay-container').fadeIn(850);
    loginBox.fadeIn(850);
    jQuery('.sign-in-box .overlay-sign-in').fadeIn(50);
}

function showSignUp( str ) {
    // check if on vote page for proper overlay order
    if(jQuery('.cms-vote')) {
        jQuery('.cms-vote .header-container').css({ 'z-index': '9997' });
    }

    jQuery('.overlay-box').hide();
    jQuery('.sign-in-box .overlay').hide();

    //set a parameter that allows for custom messages in the login form
    var signInBox = jQuery( '.sign-in-box' );
    var signInTitle = signInBox.children( '.overlay-cntnt' ).children( '.overlay-signup-social' ).children( '.overlay-title' );

    //check to see if the parameter is set
    var newTitle = '';

    if ( ( str !== undefined ) && ( str !== '<p></p>' ) && ( str !== '' ) ) {
        newTitle = str;

        //check to see if newTitle has a p tag, in case it came from a static block
        if( newTitle.indexOf( '<p>' ) !== -1 ) {
            //if it does create an object so we can strip the text from it
            newTitle = jQuery( str );
            signInTitle.text( newTitle.text() );
        } else {
            //if not then just append
            signInTitle.text( newTitle );
        }

    }

    jQuery('.overlay-container').fadeIn(850);
    jQuery('.sign-in-box').fadeIn(850);
    jQuery('.sign-in-box .overlay-signup-social').fadeIn(50);
}

function showVoting() {
    // check if on vote page for proper overlay order
    if(jQuery('.cms-vote')) {
        jQuery('.cms-vote .header-container').css({'z-index': '9999'});
    }

    jQuery('.overlay-box').hide();
    jQuery('.overlay-container').fadeIn(850);
    jQuery('.vote-box').fadeIn(850);
}

jQuery(window).load(function() {
    /* Product Grid Fadein Effect */
    jQuery('.product-grid-slider').cycle({
        fx: 'fade', speed: 250, timeout: 550

    }).cycle("pause").hover(function() {
            jQuery(this).cycle('resume');
        },function(){
            jQuery(this).cycle(0, 'fade').cycle('pause');
        });

    /* @collections-page - level related product boxes */
    equalHeight(jQuery('.products-grid .prdt-info'));
    equalHeight(jQuery('.products-grid .prdt-info .prdt-title'));

    /* @product-page - level related product boxes */
    equalHeight(jQuery('.trunkshow-items .prdt-info'));
});


jQuery(document).ready(function(){
    jQuery('.category-products .products-grid li, .ed-block').each(function(){
        if(jQuery(this).find(".sold-out").length > 0 ) {
            jQuery(this).find('img').css({'opacity':'0.3'})
            jQuery(this).find('.sold-out  span').css({'border-color':'#d9d9d9', 'color':'#d9d9d9'})

        } else {

        }
    });

    jQuery('.category-products .products-grid li .actions, .ed-block').hover(function() {
        jQuery('.sold-out', this).hide();
        jQuery('.join-waitlist', this).show()

    } , function () {
        jQuery('.sold-out', this).show();
        jQuery('.join-waitlist', this).hide();
    });


    jQuery('.category-products .products-grid li, .ed-block').hover(function() {
        jQuery('.left-stock', this).hide();
        jQuery('.add-product', this).show();

    } , function () {
        jQuery('.left-stock', this).show();
        jQuery('.add-product', this).hide();
    });

    /* @global-header - close overlay container */
    jQuery('.overlay-box .btn-close').on('click', function(){
        jQuery('.overlay-container, .overlay-box').fadeOut(850);
    });

    /* @global-header - switch between register-login boxes */
    jQuery('.txt-already-with-us .btn-switch').on('click', function(){
        jQuery('.overlay-signup-social, .overlay-signup-own').fadeOut(350, function(){
            jQuery('.overlay-cntnt .overlay-sign-in').delay(500).fadeIn(850);
        });
    });

    jQuery('.txt-your-email .btn-switch').on('click', function(){
        jQuery('.overlay-signup-social').fadeOut(350, function(){
            jQuery('.overlay-cntnt .overlay-signup-own').delay(500).fadeIn(850);
        });
    });

    jQuery('.txt-not-with-us .btn-switch').on('click', function(){
        jQuery('.overlay-sign-in').fadeOut(350, function(){
            jQuery('.overlay-signup-social').delay(500).fadeIn(850);
        });
    });

    /* @global - select dropdown custom */
    customSelectsInitializeText();

    jQuery('body').on('change', '.select-wrapper select', function() {
        customSelectsChangeText(this);
    });

    /* @home-new - removing right margin on designer boxes */
    jQuery('.product-designer:nth-child(4n)').addClass('no-right-margin');

    /* @home-new - removing bottom margin on designer testimonial in meet us section */
    jQuery('.designer-display:last-child').css({'margin-bottom': 0});


    equalHeight(jQuery('.meet-block'));

    if($j.browser.msie && $j.browser.version < 10)
    {
        //$j("form input:text, form input:password").clickclear();
    }

    $j(".like-products").click(function(){
        $j.post(
            getUrl("productlikes/index/like"),
            {id : $j(this).data("productid")},
            likeProductResult,
            "json"
        );
    });

    $j('.designer-slider').contentSlider({
        transition: 'slide',
        auto: false,
        navClass: 'od-slider-nav'
    });

    $j('.customer-slider').contentSlider({
        transition: 'slide',
        auto: false,
        navClass: 'cm-slider-nav'
    });

    $j('.hero-slider').contentSlider({
        transition: 'slide',
        useControls: false,
        pauseOnHover: true,
        auto: true,
        duration: 4000
    });

    // $j('.hero-slider').find( 'product-slider' ).contentSlider({
    // 	transition: 'slide',
    // 	useControls: true,
    // 	pauseOnHover: true,
    // 	auto: false,
    // 	duration: 4000
    // });


    $j( '.comment-image' ).fillHeight();


    $j( '.top-seller-items-container' ).columns({
        columns: 4,
        duration: 400
    });

    $j( '.best-seller-items-container' ).columns({
        columns: 4,
        duration: 400
    });

    $j( '.challenges-wrapper' ).columns({
        columns: 2,
        duration: 400,
        className: ['pixafyColumn left', 'pixafyColumn right']
    });

    $j( '.form-submission-section' ).find( '.form-wrapper' ).columns({
        columns: 2,
        duration: 400
    });

    $j( '.form-item.images' ).find( '.image-form-wrapper' ).columns({
        columns: 4,
        duration: 400,
        className: ['image-item left', 'image-item', 'image-item', 'image-item right']
    }).css( 'overflow', 'visible' );

    $j( '.challenge-main-section' ).columns({
        columns: 2,
        duration: 400,
        className: ['pixafyColumn left', 'pixafyColumn right']
    });

    $j( '.recent-entries-slider' ).find( '.slide-item' ).columns({
        columns: 4,
        duration: 400,
        onComplete: function() {
            $( this ).find( '.product-designer' ).removeClass( 'no-right-margin' );
        }
    });

    $j( '.recent-entries-slider' ).contentSlider({
        auto: false,
        useControls: true,
        speed: 400,
        duration: 3000
    }).css( 'overflow', 'visible' );

    $j( '.hiw-wrapper' ).columns({
        columns: 2,
        duration: 0,
        className: ['pixafyColumn left', 'pixafyColumn right']
    });



    $j( '.about-os-section' ).find( '.info-wrapper' ).columns({
        columns: 2,
        duration: 400,
        className: ['pixafyColumn left', 'pixafyColumn right']
    });

    $j( '.about-op-section' ).columns({
        columns: 2,
        duration: 400,
        className: ['pixafyColumn left', 'pixafyColumn right']
    });

    $j( '.about-opr-section' ).columns({
        columns: 2,
        duration: 400,
        className: ['pixafyColumn left', 'pixafyColumn right']
    });

    $j( '.about-opr-section' ).find( '.info-left' ).columns({
        columns: 2,
        duration: 400,
        className: ['pixafyColumn left', 'pixafyColumn right']
    });

    $j('.cms-vote').find('.section-cntnt').columns({
        columns: 4,
        duration: 400
    });

    $j('#main-vote-collection').columns({
        columns: 3,
        duration: 400
    });

    $j('.pd-tab#tab1').find( '.slider-item' ).columns({
        columns: 4,
        duration: 400
    });

    $j('.pd-tab#tab2').columns({
        columns: 3,
        duration: 400
    });

    $j( '.profile-presentation-wrapper' ).tabs({
        eventType: 'click',
        speed: 700,
        index: 0
    });

    $j( '#vote-item-collection' ).columns({
        columns: 4,
        duration: 400
    });

    $j( '.my-items-slider' ).contentSlider({
        auto: false,
        useControls: true,
        navClass: 'my-items-nav',
        speed: 400,
        duration: 3000
    }).css( 'overflow', 'visible' );

    $j( '.my-items-slider' ).find( '.content-slider-container' ).css( 'overflow', 'visible' );

    $j( '.product-designer' ).removeClass( 'no-right-margin' );

    //$j( '.my-items-slider' ).verticalAlign( 'center' );


    $j( '.home-new' ).find( '.product-designer' ).removeClass( 'no-right-margin' );
    $j( '.profile-presentation-wrapper' ).find( '.product-designer' ).removeClass( 'no-right-margin' );

    /* js for accordian section on submit 2 page */
    (function( $ ) {
        var accordianSections = $('.pixafy-accordian-group').find('.pixafy-accordian-content').hide();

        $('body').on('click', '.pixafy-accordian-trigger', function () {
            var $this = $(this),
                $target = $this.next('.pixafy-accordian-content'),
                id = $this.parents('.pixafy-accordian-group').attr('data-accordian-id');

            if (!$target.hasClass('active')) {
                accordianSections.each(function () {
                    if ($(this).parents('.pixafy-accordian-group').attr('data-accordian-id') === id) {
                        if($(this).hasClass('active')) {
                            $(this).removeClass('active').stop().slideUp(400, function() {
                                $('body').trigger('pixafy.accordian.sectionClosed');
                            });
                        }
                    }
                });
                $target.addClass('active').stop().slideDown(400, function() {
                    $('body').trigger('pixafy.accordian.sectionOpened');
                });
            } else {
                $target.removeClass('active').stop().slideUp(400, function() {
                    $('body').trigger('pixafy.accordian.sectionClosed');
                });
            }
        });
    })( jQuery );

    /**
     * design submission checkbox JS (challenges)
     * author kkemple@pixafy.com
     */
    (function( $ ) {
        $('body').on('click', '.check-style', function() {
            var self = $( this );
            var checkbox = self.children( 'input[type="checkbox"]' );

            if( $( checkbox ).prop('checked') ) {
                $( checkbox ).prop( 'checked', false );
                $( self ).css({
                    'background-position': '0 0'
                });
            } else {
                $( checkbox ).prop( 'checked', true );
                $( self ).css({
                    'background-position': '0 -21px'
                });
            }
        });
    })( jQuery );

    /* set char counts for our design submission form */
    $j( '#entry-materials' ).charCounter( '#materials-counter' );
    $j( '#entry-desc' ).charCounter( '#story-counter' );

    $j( '#profile-story' ).charCounter( '#profile-story-counter' );
    $j( '#profile-specialties' ).charCounter( '#profile-specialties-counter' );

    /* set scrolling for about-us page sections */
    $j( '.need-help' ).scrollToSection({
        section: '#faq-section',
        offset: -200,
        duration: 0
    });

    $j( '#om-link' ).scrollToSection({
        section: '#our-model',
        offset: -280,
        duration: 0
    });

    $j( '#os-link' ).scrollToSection({
        section: '#our-story',
        offset: -180,
        duration: 0
    });

    $j( '#od-link' ).scrollToSection({
        section: '#our-designers',
        offset: -200,
        duration: 0
    });

    $j( '#op-link' ).scrollToSection({
        section: '#our-products',
        offset: -200,
        duration: 0
    });

    $j( '#opr-link' ).scrollToSection({
        section: '#our-prices',
        offset: -200,
        duration: 0
    });

    $j( '.faq-page-wrapper' ).tabs({
        eventType: 'click',
        speed: 700,
        index: 0
    });

    $j( '.cms-terms' ).find( '.header-sub-nav' ).hide();
    $j( '.cms-privacy' ).find( '.header-sub-nav' ).hide();
    $j( '.cms-jobs' ).find( '.header-sub-nav' ).hide();
    $j( '.cms-faq' ).find( '.header-sub-nav' ).hide();

    $j( '#faq-section' ).find( '.pixafy-accordian-trigger' ).scrollToSection({
        section: '#faq-section',
        offset: -200,
        duration: 0
    });

    if ( is_ie( 9 ) ) {
        $j( '.comment-input' ).placeholderText({
            color: '#aaaaaa'
        });
    }

    $j( '.gallery-images' ).on( 'click', function( e ) {
        var self = $j( this );
        var imageId = self.attr( 'data-image-id' );

        var activeItem = $j( '.gallery-images-big.active' );
        var itemToShow = $j( '#big-image-' + imageId );

        activeItem.fadeOut( 300 );
        activeItem.removeClass( 'active' );

        itemToShow.fadeIn( 300 );
        itemToShow.addClass( 'active' );
    });

    $j( '.check-style.email' ).on( 'click', function() {
        toggleImageRule();
    });

    $j( '.pixafy-tooltip.about-price' ).on( 'mouseenter', function() {
        togglePriceTooltip();
    });

    $j( '.pixafy-tooltip.about-price' ).on( 'mouseleave', function() {
        togglePriceTooltip();
    });

    $j('.press-section .press_label').fancybox({
        helpers: {
            scrolling : false,
            overlay: {
                locked: false
            }
        }
    });
}); /* end document.ready */

function togglePriceTooltip() {
    var initTop = -10;
    var initRight = -32;

    var tooltip = $j( '.tooltip-container' );
    var tWidth = tooltip.width();
    var tHeight = tooltip.height();

    if( tooltip.css( 'display' ) === 'block' ) {
        tooltip.fadeOut( 300, function() {
            $j( this ).css({
                top: initTop,
                right: initRight
            });
        });
    } else {
        tooltip.css({
            top: initTop - tHeight,
            right: initRight - ( tWidth / 2 )
        })
            .fadeIn( 300 );
    }
}

function hidePriceTooltip() {
    var tooltip = $j( '.tooltip-container' );
    tooltip.fadeOut( 300 );
}

function toggleImageRule() {
    var rule = $j( '.form-item.images' ).find( '.rule' );

    if ( rule.css( 'display' ) === 'inline' ) {
        rule.css( 'display', 'none' );
    } else {
        rule.css( 'display', 'inline' );
    }
}

(function( $ ) {
    'use strict';

    $.fn.charCounter = function( countHolder ) {
        var self = $( this );
        self.countHolder = $( countHolder );
        self.count = 0;

        self.init = function( el ) {
            self.elToWatch = $( el );
            self.events();
        },

            self.events = function() {
                $( self.elToWatch ).on('keyup', function() {
                    var etw = $( this );
                    var str = etw.val();
                    self.count = str.length;
                    self.updateCount( self.count );
                });
            },

            self.updateCount = function() {
                self.countHolder.text( self.count );
            };

        return this.each(function() {
            self.init( this );
        });
    };
})( jQuery, window, document, undefined );


function likeProductResult(data)
{
    if(data.hasOwnProperty("error"))
    {
        alert(data.error);
    }
    else
    {
        $j("#like-product-" + data.id).parent().removeClass("not-liked").prepend('<span class="icon-likes"></span>');
        $j("#like-product-" + data.id).replaceWith("<span>" + data.like_count + "<span>");
    }
}

function fbSignup(response) {
    // user accepted
    if (response.authResponse) {
        FB.api('/me',{fields: "id,email,first_name,last_name,gender"}, function(response) {
            // check for error
            if (response.error != undefined) {
                alert("Facebook error: " + response.error);
            }
            // process success
            else
            {
                var params = {};
                params["fb-id"] = response.id;
                params["email"] = response.email;
                params["first-name"] = response.first_name;
                params["last-name"] = response.last_name;
                params["is-designer"] = IS_DESIGNER;
                params["design-id"] = DESIGN_ID;
                $j.post(
                    getUrl('bevellogin/ajax/register_user/'),
                    params,
                    ajax_register_result,
                    'json'
                );
            }
        });
    }
    // user cancelled/closed
    else {
        //  alert("User cancelled");
    }
}

function ajax_register(formElement)
{
    var params = {};
    jQuery("[name *='register']", jQuery(formElement)).each(function()
    {
        var elmentName = jQuery(this).attr("name");
        var start = elmentName.indexOf("[") + 1;
        var end = elmentName.indexOf("]");
        name = elmentName.substring(start, end);
        if(jQuery("#field-is-designer", formElement).size() && name != "is-designer")
        {
            name = name.replace("-designer","");
        }
        params[name] = jQuery(this).val();
    })

    jQuery.post(
        getUrl('bevellogin/ajax/register_user/'),
        params,
        ajax_register_result,
        'json'
    );
}
function ajax_register_result(data)
{
    if(data.hasOwnProperty("error"))
    {
        alert(data.error);
    }
    else
    {
        var checkoutUrl = getUrl("checkout/onepage");
        var submitUrl = getUrl("submit-design");
        var reloadPage = window.location.href.indexOf(checkoutUrl) != -1;
        if($j(".overlay-container").is(":visible"))
        {
            var successMsg = "<p style='color:green'>Thanks for signing up!</p>";
            var welcomeMsg = "<p style='color:green'>Welcome back!</p>";
            if($j(".account-create").is(":visible"))
            {
                $j("#form-validate").hide().before(successMsg);
            }
            else
            {
                if(data.hasOwnProperty("logged_in"))
                {
                    $j(".txt-your-email").after(welcomeMsg);
                }
                else
                {
                    $j(".txt-your-email").after(successMsg);
                }
            }
            $j('.overlay-container, .overlay-box').delay(1000).fadeOut(850, function()
            {
                POPUP_COUNT = POPUP_LIMIT
                if(window.location.href.indexOf(submitUrl) == -1)
                {
                    if(data.hasOwnProperty('redirect_designer_url'))
                    {
                        window.location = data.redirect_designer_url;
                    }
                    else if(data.hasOwnProperty('redirect_url'))
                    {
                        window.location = data.redirect_url;
                    }
                    else if(reloadPage)
                    {
                        window.location = checkoutUrl;
                    }
                    else
                    {
                        window.location = getUrl("shop");
                    }
                }
                else
                {
                    //Reset image files
                    $j(".entry-images").val(function()
                    {
                        $j(this).replaceWith($j(this).val('').clone(true));
                    })

                }
            });
        }
        else
        {
            if(data.hasOwnProperty("refresh"))
            {
                if(reloadPage)
                {
                    window.location = checkoutUrl;
                }
                else
                {
                    window.location = getUrl("shop");
                }
            }
            else
            {
                jQuery(".frm-wrap").hide();
                jQuery(".success-wrap").show();
            }
        }
    }
}

function check_defaults(the_field_value)
{
    var defaults = ['first name','last name','password', 'gender'];
    return $j.inArray(the_field_value.toLowerCase(), defaults) == -1;
}

function showOverlay()
{
    if(!$j(".overlay-container").is(":visible"))
    {
        var textNotFound = -1;
        if((POPUP_SHOWN && window.location.href.indexOf(getUrl("shop")) != textNotFound)
            || window.location.href.indexOf(CHECKOUT_URL) != textNotFound)
        {
            clearTimeout(POPUP_ID);
        }
        else
        {
            if(POPUP_COUNT == POPUP_LIMIT)
            {
                clearTimeout(POPUP_ID);
            }
            else
            {
                showSignUp();
                POPUP_COUNT++;
                setCookie(POPUP_COOKIE_ID,POPUP_COUNT,POPUP_COOKIE_EXPIRATION, '/');
                POPUP_SHOWN = true;
                setPopupInterval();
            }
        }
    }
}

function setPopupInterval()
{
    if(POPUP_ID)
    {
        clearTimeout(POPUP_ID);
    }
    POPUP_ID = setInterval(showOverlay, POPUP_DISPLAY_TIME * 1000);
}

function setVoteCheckInterval()
{
    setInterval(checkForVotes, VOTE_UPDATE_FREQUENCY * 1000);
}

$j.fn.fillHeight = function () {

    return this.each(function() {

        /* cache this and this' parent */
        var	self = $j(this),
            selfParent = $j(self.parent());

        self.css({
            'height' : selfParent.outerHeight() + 10 + 'px'
        });
    });
};

$j.fn.verticalAlign = function( str ) {
    this.init = function() {
        var parent = this.parent();
        var pHeight = parent.height();
        var sHeight = this.height();
        this.space = pHeight - sHeight;

        this.align();
    },

        this.align = function() {
            switch ( str.toLowerCase() ) {
                case 'top':
                    this.css( 'margin-top', 0 + 'px' );
                    break;

                case 'center':
                    this.css( 'margin-top', this.space / 2 );
                    break;

                case 'bottom':
                    this.css( 'margin-top', this.space + 'px' );
                    break;
            }
        },

        this.resize = function() {
            $j( window ).resize(function() {
                this.align();
            });
        };

    var self = this;
    return this.each(function() {
        self.init();
    });
};

/**
 * @scrollToSection - jQuery Plugin
 * Author: Kurtis Kemple
 * Date: Feb 11th, 2013
 * Desc: set element as a link that will make page scroll
 * to designated section on click
 */
;(function( $ ) {
    'use strict';

    $.fn.scrollToSection = function( opts ) {

        this.init = function( el, opts ) {

            this.anchor = $( this );

            this.defaults = {
                section: 'body',
                offset: 0,
                duration: 400,
                complete: function() {}
            };

            this.options = $.extend( {}, this.defaults, opts || {} );

            this.element = $( this.options.section );

            if( this.exists( this.element ) ) {
                this.events();
            } else {
                window.console.log('Invalid Selector, function: scrollToSection');
                return false;
            }
        },

            this.scroll = function() {
                this.topPos = this.element.offset().top;

                var finalPos = this.topPos + this.options.offset;

                var self = this;

                $( 'html, body' ).animate({
                    scrollTop: finalPos
                }, self.options.duration, function() {
                    self.options.complete.call( self );
                });
            },

            this.exists = function( el ) {
                if ( el.length > 0 ) {
                    return true;
                } else {
                    return false;
                }
            },

            this.events = function() {
                var self = this;

                this.anchor.on( 'click', function( e ) {
                    self.scroll();

                    if ( e.preventDefault ) {
                        e.preventDefault();
                    } else {
                        return false;
                    }
                });
            };

        var self = this;
        return this.each(function() {
            self.init( this, opts );
        });
    };
})( jQuery, window, document, undefined );

function shareFb(elementObj)
{
    var image;
    var itemName = $j("meta[property='og:title']").attr("content");;
    if(elementObj.data("image"))
    {
        image = elementObj.data("image");
    }
    else
    {
        image = $j("meta[property='og:image']").attr("content");
    }

    if(elementObj.data("name"))
    {
        itemName += elementObj.data("name");
    }

    FB.ui(
        {
            method: 'feed',
            name: itemName,
            link: elementObj.data("url"),
            picture: image,
            caption: itemName,
            description: $j("meta[property='og:description']").attr("content")
        },
        function(response) {
            if (response && response.post_id) {
            } else {
            }
        }
    );
}

$j(function(){
    $j(".share-link").click(function(event){
        event.preventDefault();
        if($j(this).data('fb'))
        {
            shareFb($j(this));
        }
        else
        {
            window.open($j(this).attr("href"),'share-window','height=400,width=400');
        }
    })
})

function designerSignup()
{
    showSignUp();
    //$j("#email-signup").click();
    $j('.overlay-signup-social').hide();
    $j('.overlay-cntnt .overlay-signup-own').show()
    $j("[name='is-designer']").prop("checked",true);
    $j(".account-create").find("form").append("<input type='hidden' name='redirect-designer' id='redirect-designer' value='1'/>");
}

function highlghtNav(el)
{
    el.parent().siblings().removeClass("active")
    el.parent().addClass("active");
}


/**
 * validation for the design submit form on submit-design page,
 * and all forms on the designer profile page
 */

function validateDesignSubmissionForm() {
    var borderColor = '#b1b1b1';
    var title = $j( '#entry-name' );
    var price = $j( '#entry-price' );
    var desc = $j( '#entry-desc' );
    var item = $j( '#entry-item' );
    var challenge = $j( '#entry-challenge' );
    var color = $j( '#entry-color' );
    var terms = $j( '#entry-terms' );
    var confirm = $j( '#entry-confirm' );
    var materials = $j( '#entry-materials' );
    var submitEmail = $j( '#entry-email-images');
    var sources = $j( '#entry-sources' );
    var school = $j( '#entry-school' );
    var submitByEmail = false;

    if ( submitEmail.is( ':checked' ) ) {
        submitByEmail = true;
    }

    var errors = [];

    /* 3 word check for title */
    var titleVal = title.val();
    titleVal = $j.trim(titleVal);
    var arr = titleVal.split( ' ' );

    var titleError = {};
    var titleParent = $j( title ).parent( '.form-item' );

    if ( arr.length > 3 ) {

        titleParent.find( 'p.design-form-error' ).remove();

        titleError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'No more than 3 words allowed, please adjust.'
        });

        title.css( 'border-color', 'red' );
        titleParent.append( titleError );

        errors.push( 'title' );

    } else if ( title.val() === '' ) {

        titleParent.find( 'p.design-form-error' ).remove();

        titleError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Please enter a title three words or less.'
        });

        title.css( 'border-color', 'red' );
        titleParent.append( titleError );

        errors.push( 'title' );

    } else {

        titleParent.find( 'p.design-form-error' ).remove();

        title.css( 'border-color', borderColor );

        if ( $j.inArray( 'title', errors ) ) {
            errors = $j.grep( errors, function( value ) {
                return 'title' != value;
            });
        }
    }

    /* price check */

    var priceError = {};
    var priceParent = $j( price ).parent( '.form-item' );

    if ( price.val() === '' ) {
        priceParent.find( 'p.design-form-error' ).remove();

        priceError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Please enter a price.'
        });

        price.css( 'border-color', 'red' );
        priceParent.append( priceError );

        errors.push( 'price' );

    } else if ( !$j.isNumeric( price.val() ) ) {
        priceParent.find( 'p.design-form-error' ).remove();

        priceError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Please enter a valid price.'
        });

        price.css( 'border-color', 'red' );
        priceParent.append( priceError );

        errors.push( 'price' );

    } else {
        priceParent.find( 'p.design-form-error' ).remove();

        price.css( 'border-color', borderColor );

        if ( $j.inArray( 'price', errors ) ) {
            errors = $j.grep(errors, function( value ) {
                return 'price' != value;
            });
        }
    }

    /* check story char length */
    var descError = {};
    var descParent = $j( desc ).parent( '.form-item' );

    if ( desc.val().length > 130 ) {
        descParent.find( 'p.design-form-error' ).remove();

        descError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Maximum 130 characters.'
        });

        desc.css( 'border-color', 'red' );
        descParent.append( descError );

        errors.push( 'desc' );
    } else if ( desc.val() === '' ) {
        descParent.find( 'p.design-form-error' ).remove();

        descError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Please enter a description.'
        });

        desc.css( 'border-color', 'red' );
        descParent.append( descError );

        errors.push( 'desc' );
    } else {
        descParent.find( 'p.design-form-error' ).remove();

        desc.css( 'border-color', borderColor );

        if ( $j.inArray( 'desc', errors ) ) {
            errors = $j.grep( errors, function( value ) {
                return 'desc' != value;
            });
        }
    }

    /* materials max char check */
    var materialsError = {};
    var materialsParent = $j( materials ).parent( '.form-item' );

    if ( materials.val().length > 130 ) {
        materialsParent.find( 'p.design-form-error' ).remove();

        materialsError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Maximum 130 characters.'
        });

        materials.css( 'border-color', 'red' );
        materialsParent.append( materialsError );

        errors.push( 'materials' );
    } else if ( materials.val() === '' ) {
        materialsParent.find( 'p.design-form-error' ).remove();

        materialsError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Please enter a materials description.'
        });

        materials.css( 'border-color', 'red' );
        materialsParent.append( materialsError );

        errors.push( 'materials' );
    } else {
        materialsParent.find( 'p.design-form-error' ).remove();

        materials.css( 'border-color', borderColor );

        if ( $j.inArray( 'materials', errors ) ) {
            errors = $j.grep(errors, function( value ) {
                return 'materials' != value;
            });
        }
    }

    /* item selection check */
    var itemError = {};
    var itemParent = $j( item ).parents( '.form-item' );

    if ( item.find( 'option:selected' ).text() === 'Choose...' ) {
        itemParent.find( 'p.design-form-error' ).remove();

        itemError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Please choose a valid option.'
        });

        item.parent().css( 'border-color', 'red' );
        itemParent.append( itemError );

        errors.push( 'item' );
    } else {
        itemParent.find( 'p.design-form-error' ).remove();

        item.parent().css( 'border-color', borderColor );

        if ( $j.inArray( 'item', errors ) ) {
            errors = $j.grep( errors, function( value ) {
                return 'item' != value;
            });
        }
    }

    /* item selection check */

    var challengeError = {};
    var challengeParent = $j( challenge ).parents( '.form-item' );

    if ( challenge.find( 'option:selected' ).text() === 'Choose...' ) {
        challengeParent.find( 'p.design-form-error' ).remove();

        challengeError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Please choose a valid option.'
        });

        challenge.parent().css( 'border-color', 'red' );
        challengeParent.append( challengeError );

        errors.push( 'challenge' );
    } else {
        challengeParent.find( 'p.design-form-error' ).remove();

        challenge.parent().css( 'border-color', borderColor );

        if ( $j.inArray( 'challenge', errors ) ) {
            errors = $j.grep( errors, function( value ) {
                return 'challenge' != value;
            });
        }
    }

    /* item selection check */

    var schoolError = {};
    var schoolParent = $j( school ).parents( '.form-item' );

    if ( challenge.find( 'option:selected' ).attr( 'data-is-school' ) === 'true' ) {
        if ( school.val() === 'School Name' || school.val() === '' ) {
            schoolParent.find( 'p.design-form-error' ).remove();

            schoolError = $j( '<p/>', {
                'class' : 'design-form-error',
                text : 'Please enter your school name.'
            });

            school.css( 'border-color', 'red' );
            schoolParent.append( schoolError );

            errors.push( 'school' );
        } else {
            schoolParent.find( 'p.design-form-error' ).remove();

            school.css( 'border-color', borderColor );

            if ( $j.inArray( 'school', errors ) ) {
                errors = $j.grep( errors, function( value ) {
                    return 'school' != value;
                });
            }
        }
    }

    /* color option */
    var colorError = {};
    var colorParent = $j( color ).parents( '.form-item' );

    if ( color.find( 'option:selected' ).text() === 'Choose...' ) {
        colorParent.find( 'p.design-form-error' ).remove();

        colorError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Please choose a valid option.'
        });

        color.parent().css( 'border-color', 'red' );
        colorParent.append( colorError );

        errors.push( 'color' );
    } else {
        colorParent.find( 'p.design-form-error' ).remove();

        color.parent().css( 'border-color', borderColor );

        if ( $j.inArray( 'color', errors ) ) {
            errors = $j.grep( errors, function( value ) {
                return 'color' != value;
            });
        }
    }

    /* color option */
    var sourcesError = {};
    var sourcesParent = $j( sources ).parents( '.form-item' );

    if ( sources.find( 'option:selected' ).text() === 'Choose...' ) {
        sourcesParent.find( 'p.design-form-error' ).remove();

        sourcesError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Please choose a valid option.'
        });

        sources.parent().css( 'border-color', 'red' );
        sourcesParent.append( sourcesError );

        errors.push( 'sources' );
    } else {
        sourcesParent.find( 'p.design-form-error' ).remove();

        sources.parent().css( 'border-color', borderColor );

        if ( $j.inArray( 'sources', errors ) ) {
            errors = $j.grep( errors, function( value ) {
                return 'sources' != value;
            });
        }
    }


    /* terms selection check */

    var termsError = {};
    var termsParent = $j( terms ).parents( '.form-item' );

    if( $( terms ).prop( 'checked' ) ) {

        termsParent.find( 'p.design-form-error' ).remove();

        terms.parent().css( 'border-color', borderColor );

        if ( $j.inArray( 'terms', errors ) ) {
            erros = $j.grep( errors, function( value ) {
                return 'terms' != value;
            });
        }
    } else {
        termsParent.find( 'p.design-form-error' ).remove();

        termsError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Please check the box(es).'
        });

        terms.parent().css( 'border-color', 'red' );
        termsParent.append( termsError );

        errors.push( 'terms' );
    }

    /* confirm selection check */

    var confirmError = {};
    var confirmParent = $j( confirm ).parents( '.form-item' );

    if( $( confirm ).prop( 'checked' ) ) {

        confirmParent.find( 'p.design-form-error' ).remove();

        confirm.parent().css( 'border-color', borderColor );

        if ( $j.inArray( 'confirm', errors ) ) {
            errors = $j.grep(errors, function( value ) {
                return 'confirm' != value;
            });
        }
    } else {
        confirmParent.find( 'p.design-form-error' ).remove();

        confirmError = $j( '<p/>', {
            'class' : 'design-form-error',
            text : 'Please check the box(es).'
        });

        confirm.parent().css( 'border-color', 'red' );
        confirmParent.append( confirmError );

        errors.push( 'confirm' );
    }


    /* check that an image is uploaded */
    if( !submitByEmail ) {
        var thumbContainer = $j( '.thumb-holder' ).first();
        var thumbs = $j( '.thumb-holder' ).find( 'img' );

        var thumbsError = {};
        var thumbsParent = $j( thumbContainer ).parents( '.form-item' );

        if ( thumbs.length === 0 ) {

            thumbsParent.find( 'p.design-form-error' ).remove();

            thumbsError = $j( '<p/>', {
                'class' : 'design-form-error',
                text : 'Please upload an image.'
            });

            thumbContainer.css( 'border-color', 'red' );
            thumbsParent.append( thumbsError );

            errors.push( 'thumbs' );

        } else {
            thumbsParent.find( 'p.design-form-error' ).remove();

            thumbContainer.css( 'border-color', borderColor );

            if ( $j.inArray( 'thumbs', errors ) ) {
                errors = $j.grep( errors, function( value ) {
                    return 'thumbs' != value;
                });
            }
        }
    }


    if ( errors.length > 0 ) {
        return false;
    } else {
        return true;
    }

}

/**
 * function for clearing the submission form on submit 2 page (submit design)
 */

function clearDesignSubmissionForm() {
    var title = $j( '#entry-name' );
    var price = $j( '#entry-price' );
    var desc = $j( '#entry-desc' );
    var item = $j( '#entry-item' );
    var challenge = $j( '#entry-challenge' );
    var color = $j( '#entry-color' );
    var terms = $j( '#entry-terms' );
    var confirm = $j( '#entry-confirm' );
    var materials = $j( '#entry-materials' );
    var images = $j( '.entry-images' );
    var thumbs = $j( '.thumb-holder' );

    title.val( '' );
    price.val( '' );

    desc.val( '' );
    desc.siblings( '.char-count-container' ).text( '0' );

    materials.val( '' );
    materials.siblings( '.char-count-container' ).text( '0' );

    item.find( 'option:selected' ).removeProp( 'selected' );
    item.find( 'option' ).first().prop( 'selected', 'selected' );

    challenge.find( 'option:selected' ).removeProp( 'selected' );
    challenge.find( 'option' ).first().prop( 'selected', 'selected' );

    color.find( 'option:selected' ).removeProp( 'selected' );
    color.find( 'option' ).first().prop( 'selected', 'selected' );

    terms.removeProp( 'checked' );
    terms.parent().css( 'background-position', '0 0' );

    confirm.removeProp( 'checked' );
    confirm.parent().css( 'background-position', '0 0' );

    $j.each( images, function() {
        var imageInput = $j( this );
        imageInput.val( '' );
    });

    $j.each( thumbs, function( index ) {
        var thumbContainer = $j( this );
        var image = thumbContainer.find( 'img' );

        if ( image.length > 0 ) {

            image.remove();

            $j( '<p/>', {
                text : 'Image ' + (index + 1)
            }).appendTo( thumbContainer );
        }
    });
}

/* name form on designer profile page */


function getInternetExplorerVersion() {
    var rv = -1; // Return value assumes failure.

    if (navigator.appName == 'Microsoft Internet Explorer') {
        var ua = navigator.userAgent;
        var re  = new RegExp( "MSIE ([0-9]{1,}[\.0-9]{0,})" );

        if (re.exec(ua) !== null) {
            rv = parseFloat( RegExp.$1 );
        }
    }

    return rv;
}

function is_ie( ver_or_less ) {
    var isIE = false;
    var ver = getInternetExplorerVersion();

    if ( ver > -1 && ( (ver_or_less && ver <= ver_or_less) || !ver_or_less) ) {
        isIE = true;
    }

    return isIE;
}

//plugin for setting and removing placeholder text
;(function( $ ) {
    'use strict';

    // add placeholder check to jQuery support
    jQuery.support.placeholder = (function(){
        var i = document.createElement('input');
        return 'placeholder' in i;
    })();

    var Placeholder = {
        init: function( el, opts ) {

            // store our input element
            this.element = $( el );

            if(this.element === undefined ) debugger;

            // set our defaults
            this.defaults = {
                color: '#aaaaaa',
                text: this.element.attr( 'placeholder' ) || ''
            };

            // extend our defaults with user options
            this.options = $.extend( {}, this.defaults, opts || {} );

            // store our original and placeholder colors
            this.oColor = this.element.css( 'color' );
            this.phColor = this.options.color;

            this.prepElement();
        },

        prepElement: function() {
            this.element.css( 'color', this.phColor );
            this.element.val( this.options.text );

            this.events();
        },

        events: function() {
            var oColor = this.oColor;
            var phColor = this.phColor;
            var element = this.element;
            var placeholder = this.options.text;

            element.on( 'focus', function() {
                var self = $( this );
                if ( self.val() === placeholder ) {
                    self.val( '' );
                    self.css( 'color', oColor );
                }
            });

            // on element blur
            element.on( 'blur', function() {
                var self = $( this );
                if ( self.val() === '' ) {
                    self.css( 'color', phColor );
                    self.val( placeholder );
                }
            });

            element.parents( 'form' ).submit(function() {
                if ( element.val() === placeholder ) {
                    element.val( '' );
                }
            });
        }
    };

    $.fn.placeholderText = function( opts ) {
        if ( !$.support.placeholder ) {
            //init our object we will return
            var obj = {};

            //if a modern browser use Object.create which prototypes our methods for us,
            //else just instantiate Placeholder
            if( Object.create ) {
                obj = Object.create( Placeholder );
            } else {
                obj = Placeholder;
            }

            return this.each(function() {
                obj.init( this, opts );
            });
        } else {
            return this;
        }
    };
})( jQuery, window, document, undefined );


function checkForVotes()
{
    if(!VOTE_UPDATE_OPERATING)
    {
        VOTE_UPDATE_OPERATING = true;
        $j.get(getUrl(VOTE_FRONTNAME + "/index/getVotes"),updateCounter,'json');
    }
}

function updateCounter(data)
{
    var counterElement = $j('.counter');
    if(counterElement.data('stop') < data.total_vote_count)
    {
        var start = counterElement.data('stop');
        var stop = data.total_vote_count;
        if(!counterElement.data('direction'))
        {
            counterElement.attr('data-direction', 'up');
        }
        counterElement.data('start', start);
        counterElement.attr('data-start', start);

        counterElement.data('stop', stop);
        counterElement.attr('data-stop', stop);
        counterElement.counter('play');
        counterElement.on('counterStop', function()
        {
            VOTE_UPDATE_OPERATING = false;
        });

    }
    else
    {
        VOTE_UPDATE_OPERATING = false;
    }

}