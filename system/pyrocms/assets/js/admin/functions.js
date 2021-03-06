/**
 * Pyro object
 * 
 * The Pyro object is the foundation of all PyroUI enhancements
 */
var pyro = {};

jQuery(function($) {

	/**
	 * This initializes all JS goodness
	 */
	pyro.init = function() {
		$("#main-nav li ul").hide();
		$("#main-nav li a.current").parent().find("ul").toggle();
		$("#main-nav li a.current:not(.no-submenu)").addClass("bottom-border");

		$("#main-nav li a.top-link").click(function () {
			if($(this).hasClass("no-submenu"))
			{
				return false;
			}
			$(this).parent().siblings().find("ul").slideUp("normal");
			$(this).parent().siblings().find("a").removeClass("bottom-border");
			$(this).next().slideToggle("normal");
			$(this).toggleClass("bottom-border");
			return false;
		});

		$("#main-nav li a.no-submenu").click(function () {
			window.location.href = $(this).attr("href");
			return false;
		});

		// Add the close link to all boxes with the closable class
		$(".closable").append('<a href="#" class="close">close</a>');

		// Close the notifications when the close link is clicked
		$("a.close").live('click', function () {
			$(this).fadeTo(200, 0); // This is a hack so that the close link fades out in IE
			$(this).parent().fadeTo(200, 0);
			$(this).parent().slideUp(400);
			return false;
		});

		// Fade in the notifications
		$(".notification").fadeIn("slow");


		// Check all checkboxes in table
		$(".check-all").live('click', function () {
			$(this).parents("table").find("tbody input[type='checkbox']").each(function () {
				if($(".check-all").is(":checked") && !$(this).is(':checked'))
				{
					$(this).click();
				}
				else if(!$(".check-all").is(":checked") && $(this).is(':checked'))
				{
					$(this).click();
				}
			});
			$.uniform.update();
		});

		// Table zerbra striping
		$("tbody tr:nth-child(even)").livequery(function () {
			$(this).addClass("alt");
		});

		$('.tabs').livequery(function () {
			$(this).tabs();
		});
		$('#tabs').livequery(function () {
			$(this).tabs({
				// This allows for the Back button to work.
				select: function(event, ui) {
					parent.location.hash = ui.tab.hash;
				},
				load: function(event, ui) {
					confirm_links();
					confirm_buttons();
				}
			});
		});
		$("select, input[type=checkbox], input[type=radio], input[type=file], input[type=submit], a.button, button, textarea").livequery(function () {
			$(this).uniform();
		});
	}

	$(document).ready(function() {
		pyro.init();
	});
});