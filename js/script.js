jQuery(function() {
	jQuery('a[href*="#"]:not([href="#"])').click(function() {
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
			var target = jQuery(this.hash);
			target = target.length ? target : jQuery('[name=' + this.hash.slice(1) +']');
			if (target.length) {
				jQuery('html, body').animate({
					scrollTop: target.offset().top
				}, 1000);
				return false;
			}
		}
	});
});

function scrollToElement(elem)
{
	jQuery('html, body').animate({
		scrollTop: jQuery(elem).offset().top
	}, 800);
}

function doSearch(term)
{
	var terms = jQuery(".lettergroup li .degreename").map(function () { return this.innerText;}).get();
	jQuery( "#searchdegreeinput" ).autocomplete({
		source: terms,
		open: function (e, ui) {
			jQuery("body").addClass("searching");
			var autocomplete = jQuery("#ui-id-2");
			var newTop = parseInt(autocomplete.css("top"))+24;
			var newLeft = parseInt(autocomplete.css("left"))+10;
			autocomplete.css("top", newTop);
			autocomplete.css("left", newLeft);
		},
		close: function( event, ui ) { jQuery("body").removeClass("searching"); },
		select: function( event, ui ) {
			jQuery("body").removeClass("searching");
			var text = ui.item.value.replace(/(\r\n|\n|\r)/gm,"");
			var degrees = jQuery(".degreename a:contains('"+text+"')"); if(degrees) scrollToElement(degrees); }

	}).data("ui-autocomplete")._renderItem = customItemRenderer;
}

function customItemRenderer( ul, item ) {
	var newText = String(item.value).replace(
		new RegExp(this.term, "gi"),
		"<span class='ui-state-highlight'>$&</span>");

	return jQuery("<li></li>")
		.data("item.autocomplete", item)
		.append("<a>" + newText + "</a>")
		.appendTo(ul);
}

jQuery(document).ready(function(){
	if(window.location.href.indexOf("wp-admin") < 0)
	{
		jQuery("a img").parent().addClass("anchorimage");
		var searchinput = "<input type='text' name='searchdegrees' id='searchdegreeinput' placeholder='Search Degrees A-Z'>";
		jQuery(".searchdegreeswrapper").html(searchinput);
		jQuery(".footer").closest("section").addClass("lastsectionwithfooter");
		jQuery("body").append("<div class='overlay'></div>");
		jQuery("#searchdegreeinput").on('change blur keyup', function(){ doSearch(jQuery("#searchdegreeinput").val()); });
		jQuery(".degreelist .pagination a").on("click", function(){ jQuery(".degreelist .pagination a.active").removeClass("active"); jQuery(this).addClass("active"); });
	}
});
