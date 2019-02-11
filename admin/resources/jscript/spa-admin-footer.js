/* ---------------------------------
 Simple:Press - Version 5.0
 Forum Admin Javascript loaded in footer after page loads

 $LastChangedDate: 2019-01-30 16:40:00 -0600 (Wed, 30 Jan 2019) $
 $Rev: 14157 $
 ------------------------------------ */

(function(spj, $, undefined) {
	// private properties

	// public properties

	// public methods
	$(document).ready(function() {
		activateAccordion();
		setupTooltips();
		highlightMenu();

		/* trigger event for items waiting on admin form loaded */
		$('#sfmaincontainer').trigger('adminformloaded');
	});

	// private methods
	function activateAccordion() {
		$("#sfadminmenu").accordion({
			heightStyle: 'content',
			collapsible: true,
			active: parseInt(sp_admin_footer_vars.panel)
		});
	}

	function setupTooltips() {
		if (sp_platform_vars.device == 'desktop' && sp_platform_vars.tooltips == true) {
			$(document).tooltip({
				tooltipClass: "ttip",
				position: {
					my: "left+20 top",
					at: "left bottom+10"
				},
				track: false
			});
		}
	}

	function highlightMenu() {
		$('.wp-submenu li').removeClass('current');
		$('.wp-submenu li').find('a:contains(' + sp_admin_footer_vars.panel_name + ')').parent().addClass('current');
	}
	
	jQuery(document).on("click", 'input[name="SP_license_activate"]', function (e) {
		e.preventDefault();
		var s = jQuery(this).parents("form").find('input[name="sp_sample_license_key"]').val(),
			n = jQuery(this).parents("form").find(".sp_item_name").val(),
			a = jQuery(this).parents("form").find('input[name="sp_itemn"]').val(),
			i = jQuery(this).parents("form").find('input[name="sp_item_id"]').val();
		return jQuery.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: {
				action: "license-check",
				licence_key: s,
				item_name: n,
				sp_item: a,
				sp_itemn_id: i,
				sp_action: "activate_license"
			},
			timeout: 7e5,
			success: function (e) {
				e.message && "" != e.message && (t("#sfmsgspot").fadeIn(), t("#sfmsgspot").html(e.message), t("#sfmsgspot").fadeOut(6e3)), setTimeout(function () {
					t("#acclicensing").click()
				}, 4e3)
			},
			error: function (e, s, n) {
				"timeout" === s && (t("#sfmsgspot").html("Something Went Wrong Please Try Again!"), t("#sfmsgspot").fadeOut(6e3))
			},
			beforeSend: function () {
				t("#sfmsgspot").show(), t("#sfmsgspot").html(sp_platform_vars.pWait)
			},
			complete: function () {}
		}), !1
	});
	
	jQuery(document).on("click", 'input[name="SP_license_deactivate"]', function (e) {
		e.preventDefault();
		var s = jQuery(this).parents("form").find('input[name="sp_sample_license_key"]').val(),
			n = jQuery(this).parents("form").find(".sp_item_name").val(),
			a = jQuery(this).parents("form").find('input[name="sp_itemn"]').val(),
			i = jQuery(this).parents("form").find('input[name="sp_item_id"]').val();
		return jQuery.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: {
				action: "license-check",
				licence_key: s,
				item_name: n,
				sp_item: a,
				sp_itemn_id: i,
				sp_action: "deactivate_license"
			},
			timeout: 7e5,
			success: function (e) {
				e.message && "" != e.message && (t("#sfmsgspot").fadeIn(), t("#sfmsgspot").html(e.message), t("#sfmsgspot").fadeOut(6e3)), setTimeout(function () {
					t("#acclicensing").click()
				}, 4e3)
			},
			error: function (e, s, n) {
				"timeout" === s && (t("#sfmsgspot").html("Something Went Wrong Please Try Again!"), t("#sfmsgspot").fadeOut(6e3))
			},
			beforeSend: function () {
				t("#sfmsgspot").show(), t("#sfmsgspot").html(sp_platform_vars.pWait)
			},
			complete: function () {}
		}), !1
	});
	
	jQuery(document).on("click", 'input[name="save_store_url"]', function (e) {
		e.preventDefault();
		var s = jQuery(this).parents("form").find('input[name="sp_sample_store_url"]').val();
		return jQuery.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: {
				action: "license-check",
				sp_sample_store_url: s,
				sp_action: "save_store_url"
			},
			timeout: 7e5,
			success: function (e) {
				e.message && "" != e.message && (t("#sfmsgspot").fadeIn(), t("#sfmsgspot").html(e.message), t("#sfmsgspot").fadeOut(6e3)), setTimeout(function () {
					t("#acclicensing").click()
				}, 4e3)
			},
			error: function (e, s, n) {
				"timeout" === s && (t("#sfmsgspot").html("Something Went Wrong Please Try Again!"), t("#sfmsgspot").fadeOut(6e3))
			},
			beforeSend: function () {
				t("#sfmsgspot").show(), t("#sfmsgspot").html(sp_platform_vars.pWait)
			},
			complete: function () {}
		}), !1
	});
	
}(window.spj = window.spj || {}, jQuery));