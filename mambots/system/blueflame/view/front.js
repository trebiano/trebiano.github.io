function bf_fixTips(){
	var spans = jQuery('div.tool-tip');
	for (var n = 0; n < spans.length; n++){
		spans[n].style.visibility = 'hidden';
	}
	var newArray = [];
	allTipElements = jQuery('.hasTip');
	for(i=0;i < allTipElements.length; i++) {
		var dual=allTipElements[i].title.split('::');
		if (dual[1]) {
			newArray.push(allTipElements[i]);
		}
	}
	var myTips = new Tips(newArray, 
	{
		maxTitleChars: 50,
		maxOpacity: 0.9,
		showDelay: 400,
		hideDelay: 400
	});
}
/* set the xajax hideloading message */
function hideLoadingMessage(){
	jQuery('div#loading').hide('slow');
}
function showLoadingMessage(){
	jQuery('div#loading').show('fast');
}