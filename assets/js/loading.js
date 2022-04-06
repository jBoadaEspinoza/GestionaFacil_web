function show_loadingOverlay(element,rgba){
	$(element).LoadingOverlay("show", {
	    background  : "rgba("+rgba[0]+", "+rgba[1]+", "+rgba[2]+", "+rgba[4]+")",
	    image       : "",
	    fontawesome : "fas fa-circle-notch fa-spin",
	});
	$.LoadingOverlay("show", {
		background  : "rgba("+rgba[0]+", "+rgba[1]+", "+rgba[2]+", "+rgba[3]+")",
		image       : "",
	});
}
function hide_loadingOverlay(element){
	$(element).LoadingOverlay("hide",true);
	$.LoadingOverlay('hide');
}