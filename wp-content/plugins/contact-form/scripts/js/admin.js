(function ($) {
$(function () {

var _bound = false;

function _check_generic_from_enabled ($chk) {
	var $parent = $chk.parents(".cw_generic_from_container");
	var $target = $parent.find(".cw_generic_from_options");
	if ($chk.is(":checked")) {
		$target.show();
	} else {
		$target.hide();
	}
}

function _show_captcha_keys () {
	var $me = $(this);
	var $target = $me.parents(".cw_captcha").find(".cw_captcha_keys");
	if ($me.is(":checked")) $target.show();
	else $target.hide();
}

function _init_generic_forms () {
	$('.cw_generic_from_container :checkbox.cw-contact_form_generic_from').each(function () {
		var $me = $(this);
		_check_generic_from_enabled($me);
	});	
	$(".cw_captcha :checkbox").each(_show_captcha_keys);
}

function init () {
	$(document).ajaxComplete(_init_generic_forms);
	$('.cw_generic_from_container :checkbox.cw-contact_form_generic_from').live('click', function () { 
		_check_generic_from_enabled($(this)); 
	});
	$(".cw_captcha :checkbox").live('click', _show_captcha_keys);
	_init_generic_forms();
	_bound = true;
}

if (!_bound) init();

});
})(jQuery);