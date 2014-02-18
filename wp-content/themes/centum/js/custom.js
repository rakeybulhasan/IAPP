/*-----------------------------------------------------------------------------------
/*
/* Custom JS
/*
-----------------------------------------------------------------------------------*/

/* Start Document */

(function($){
	$(document).ready(function(){

        $('body').removeClass('no-js').addClass('js');

        $("#navigation li").hover(
         function () {
            $(this).has('ul').addClass("hover");
            $(this).find('ul:first').css({
               visibility: "visible",
               display: "none"
           }).stop(true, true).slideDown("fast");
        },
        function () {
            $(this).removeClass("hover");
            $(this).find('ul:first').css({
               visibility: "visible",
               display: "block"
           }).stop(true, true).slideUp("fast");
        }
        );

        $("select.selectnav").change(function() {
         window.location = $(this).find("option:selected").val();
     });

        /*----------------------------------------------------*/
/*	Image Overlay
/*----------------------------------------------------*/

$('.picture a').hover(function () {
	$(this).find('.image-overlay-zoom, .image-overlay-link').stop().fadeTo('fast', 1);
},
    function () {
	$(this).find('.image-overlay-zoom, .image-overlay-link').stop().fadeTo('fast', 0);
});

$('.picture').hover(function () {
	$(this).find('.image-overlay-zoom, .image-overlay-link').stop().fadeTo('fast', 1);
},
    function () {
	$(this).find('.image-overlay-zoom, .image-overlay-link').stop().fadeTo('fast', 0);
});

$(window).load(function() {
    $('.flexslider').flexslider({ animation: "slide" , controlNav: true, directionNav: false,smoothHeight: true, slideshowSpeed: 7000, animationSpeed: 600,start: function(slider) {
     $('.flexslider').removeClass('loadingflex');
 }  });
});
/*----------------------------------------------------*/
/*	Back To Top Button
/*----------------------------------------------------*/

$('#scroll-top-top a').click(function(){
	$('html, body').animate({scrollTop:0}, 300);
	return false;
});


/*----------------------------------------------------*/
/*	Accordion
/*----------------------------------------------------*/

var $container = $('.acc-container'),
$trigger   = $('.acc-trigger');

$container.hide();
$trigger.first().addClass('active').next().show();

var fullWidth = $container.outerWidth(true);
$trigger.css('width', fullWidth);
$container.css('width', fullWidth);

$trigger.on('click', function(e) {
	if( $(this).next().is(':hidden') ) {
		$trigger.removeClass('active').next().slideUp(300);
		$(this).toggleClass('active').next().slideDown(300);
	}
	e.preventDefault();
});

		// Resize
		$(window).on('resize', function() {
			fullWidth = $container.outerWidth(true)
			$trigger.css('width', $trigger.parent().width() );
			$container.css('width', $container.parent().width() );
		});


        /*----------------------------------------------------*/
/*  Accordion
/*----------------------------------------------------*/

$(".toggle-container").hide();
$(".toggle-trigger").click(function(){
    $(this).toggleClass("active").next().slideToggle("normal");
        return false; //Prevent the browser jump to the link anchor
    });


/*----------------------------------------------------*/
/*	Tabs
/*----------------------------------------------------*/



var $tabsNav    = $('.tabs-nav'),
$tabsNavLis = $tabsNav.children('li'),
$tabContent = $('.tab-content');

$tabsNav.each(function() {
	var $this = $(this);

	$this.next().children('.tab-content').stop(true,true).hide()
	.first().show();

	$this.children('li').first().addClass('active').stop(true,true).show();
});

$tabsNavLis.on('click', function(e) {
	var $this = $(this);

	$this.siblings().removeClass('active').end()
	.addClass('active');

	$this.parent().next().children('.tab-content').stop(true,true).hide()
	.siblings( $this.find('a').attr('href') ).fadeIn();

	e.preventDefault();
});



$('.testimonials-carousel').carousel({
    namespace: "mr-rotato",
    slider : '.carousel',
    slide : '.testimonial'
})
/*----------------------------------------------------*/
/*	Contact Form
/*----------------------------------------------------*/

var animateSpeed=300;
var emailReg = /^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;

	// Validating

	function validateName(name) {
		if (name.val()=='*') {name.addClass('validation-error',animateSpeed); return false;}
		else {name.removeClass('validation-error',animateSpeed); return true;}
	}

	function validateEmail(email,regex) {
		if (!regex.test(email.val())) {email.addClass('validation-error',animateSpeed); return false;}
		else {email.removeClass('validation-error',animateSpeed); return true;}
	}

	function validateMessage(message) {
		if (message.val()=='') {message.addClass('validation-error',animateSpeed); return false;}
		else {message.removeClass('validation-error',animateSpeed); return true;}
	}

	$('#send1').click(function() {

		var result=true;

		var name = $('input[name=name]');
		var email = $('input[name=email]');
		var message = $('textarea[name=message]');

		// Validate
		if(!validateName(name)) result=false;
		if(!validateEmail(email,emailReg)) result=false;
		if(!validateMessage(message)) result=false;

		if(result==false) {return false; }

		// Data
		var data = 'visitor_name=' + name.val() + '&visitor_email=' + email.val() + '&message='  + encodeURIComponent(message.val());

		// Disable fields
		$('.text').attr('disabled','true');

		// Loading icon
		$('.loading').show();

		// Start jQuery
		$.ajax({

			// PHP file that processes the data and send mail
			url: "",

			// GET method is used
			type: "POST",

			// Pass the data
			data: data,

			//Do not cache the page
			//cache: false,

			// Success
			success: function (html) {

				if (html==1) {

					// Loading icon
					$('.loading').fadeOut('slow');

					//show the success message
					$('.success-message').slideDown('slow');

					// Disable send button
					$('#send1').attr('disabled',true);
                    return;

				}

				else {
					$('.loading').fadeOut('slow')
					alert('Sorry, unexpected error. Please try again later.');
				}
			}
		});

		return false;

	});

$('input[name=name]').blur(function(){validateName($(this));});
$('input[name=email]').blur(function(){validateEmail($(this),emailReg); });
$('textarea[name=message]').blur(function(){validateMessage($(this)); });




/*----------------------------------------------------*/
/*	Isotope Portfolio Filter
/*----------------------------------------------------*/
$(window).load(function(){
	$('#portfolio-wrapper').isotope({
      itemSelector : '.portfolio-item',
      layoutMode : 'fitRows'
  });

});
$('#filters a').click(function(e){
  e.preventDefault();

  var selector = $(this).attr('data-option-value');
  $('#portfolio-wrapper').isotope({ filter: selector });

  $(this).parents('ul').find('a').removeClass('selected');
  $(this).addClass('selected');
});



$('a.close').click(function(e){
	e.preventDefault();
	$(this).parent().fadeOut();

});

$('.tooltips').tooltip({
  selector: "a[rel=tooltip]"
})
/*----------------------------------------------------*/
/*	Fancybox
/*----------------------------------------------------*/


$('[rel=image]').fancybox({
	type        : 'image',
	openEffect  : 'fade',
	closeEffect	: 'fade',
	nextEffect  : 'fade',
	prevEffect  : 'fade',
	helpers     : {
		title   : {
			type : 'inside'
		}
	}
});

$('a[rel=image-gallery]').fancybox({
    type        : 'image',
    openEffect  : 'fade',
    closeEffect : 'fade',
    nextEffect  : 'fade',
    prevEffect  : 'fade',
	helpers     : {
		title   : {
			type : 'inside'
		}

	}
});

$(window).load(function(){
    $('.products').isotope({
        itemSelector : '.isotope-item',
        layoutMode : 'fitRows'
    });
});

$(".video-cont").fitVids();


/* End Document */

});

})(this.jQuery);



/*global jQuery */
/*!
* FitVids 1.0
*
* Copyright 2011, Chris Coyier - http://css-tricks.com + Dave Rupert - http://daverupert.com
* Credit to Thierry Koblentz - http://www.alistapart.com/articles/creating-intrinsic-ratios-for-video/
* Released under the WTFPL license - http://sam.zoy.org/wtfpl/
*
* Date: Thu Sept 01 18:00:00 2011 -0500
*/


(function( $ ){

  "use strict";

  $.fn.fitVids = function( options ) {
    var settings = {
      customSelector: null
    };

    if(!document.getElementById('fit-vids-style')) {

      var div = document.createElement('div'),
          ref = document.getElementsByTagName('base')[0] || document.getElementsByTagName('script')[0],
          cssStyles = '&shy;<style>.fluid-width-video-wrapper{width:100%;position:relative;padding:0;}.fluid-width-video-wrapper iframe,.fluid-width-video-wrapper object,.fluid-width-video-wrapper embed {position:absolute;top:0;left:0;width:100%;height:100%;}</style>';

      div.className = 'fit-vids-style';
      div.id = 'fit-vids-style';
      div.style.display = 'none';
      div.innerHTML = cssStyles;

      ref.parentNode.insertBefore(div,ref);

    }

    if ( options ) {
      $.extend( settings, options );
    }

    return this.each(function(){
      var selectors = [
        "iframe[src*='player.vimeo.com']",
        "iframe[src*='youtube.com']",
        "iframe[src*='youtube-nocookie.com']",
        "iframe[src*='kickstarter.com'][src*='video.html']",
        "object",
        "embed"
      ];

      if (settings.customSelector) {
        selectors.push(settings.customSelector);
      }

      var $allVideos = $(this).find(selectors.join(','));
      $allVideos = $allVideos.not("object object"); // SwfObj conflict patch

      $allVideos.each(function(){
        var $this = $(this);
        if (this.tagName.toLowerCase() === 'embed' && $this.parent('object').length || $this.parent('.fluid-width-video-wrapper').length) { return; }
        var height = ( this.tagName.toLowerCase() === 'object' || ($this.attr('height') && !isNaN(parseInt($this.attr('height'), 10))) ) ? parseInt($this.attr('height'), 10) : $this.height(),
            width = !isNaN(parseInt($this.attr('width'), 10)) ? parseInt($this.attr('width'), 10) : $this.width(),
            aspectRatio = height / width;
        if(!$this.attr('id')){
          var videoID = 'fitvid' + Math.floor(Math.random()*999999);
          $this.attr('id', videoID);
        }
        $this.wrap('<div class="fluid-width-video-wrapper"></div>').parent('.fluid-width-video-wrapper').css('padding-top', (aspectRatio * 100)+"%");
        $this.removeAttr('height').removeAttr('width');
      });
    });
  };
// Works with either jQuery or Zepto
})( window.jQuery || window.Zepto );