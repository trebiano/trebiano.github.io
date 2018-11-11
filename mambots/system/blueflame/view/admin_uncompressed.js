jQuery.noConflict();
var addLoadingFunction = true;

/* for Mozilla */
if (document.addEventListener) {
	document.addEventListener("DOMContentLoaded", init, false);
}

/* for other browsers */
window.onload = init;

/**
* Highlights the selected submenu, kills TinyMCE and then calls the xajax function
*/
function selectSubmenuItem(item, submenu){
	killTinyMCE();
	bfHandler(submenu);
}

/**
* unloads all instances of TinyMCE
*/
function killTinyMCE(){
	/* Handle tinyMCE, unload editor items */
	if ( typeof tinyMCE != "undefined" && tinyMCE.idCounter > 0) {
		tinyMCE.triggerSave();

		for (n in tinyMCE.instances) {
			inst = tinyMCE.instances[n];
			if (!tinyMCE.isInstance(inst)){
				continue;
			}

			inst.switchSettings();
			editor_id = inst.editorId;
			tinyMCE.removeMCEControl(editor_id);
		}
		tinyMCE.idCounter = 0;
	}
}

/**
* changes the class name of a menu item to give the appearance of being clicked
*/
function highlightSubMenuItem(item){
	return true;
}

/**
* Wrapper function for toggles
*/
function bfToggleHandler(task, id, next, model){
	var handler = getbfHandler();
	var args = [task, id, next, model];

	/* xajax 0.5 */
	//return 	xajax.call(handler,{parameters:args});
	/* xajax 0.2.4 */
	return 	xajax.call(handler,args);
}

/**
* Used by all the toolbar buttons
* replicates what joomla does with submitbutton function
* but allows us to route control through xajax
*/
function submitToXAJAX (task){

	//	jQuery('div.col85').fadeOut('fast');

	/* Handle tinyMCE, unload editor items */
	killTinyMCE();

	var args = [];
	args[0] = 'x'+task;
	args[1] = xajax.getFormValues('adminForm');
	var handler = getbfHandler();

	/* xajax 0.5 */
	//return 	xajax.call(handler,{parameters:args});
	/* xajax 0.2.4 */
	return 	xajax.call(handler,args);
}

/**
* Disable the use of enter in form inputboxes - need to route through xajax and not hard form submits
*/
function handleEnter (field, event) {
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if (keyCode == 13) {
		return false;
	} else{
		return true;
	}
}

/**
* Used for switching tabs in config
* @author Phil Taylor
*/
function showConfigTab(tab, tabtitle) {
	element = document.getElementById('config-document');
	elements = element.getElementsByTagName('DIV');
	for (i=0; i < elements.length; i++) {
		elements[i].style.display = "none";
	}

	document.getElementById('page-'+tab.toLowerCase()).style.display = "block";
}

function xindex() {
	xtra = "";
	returnto = xajax.$('returnto').value;
	var l = xindex.arguments.length;
	for (i=0; i < l; i++ ) {
		xtra+=",'"+xindex.arguments[i] + "'";
	}
	if (xtra==="")  {
		bfHandler('x'+returnto);
	} else {
		//		var handler = getbfHandler();
		eval("xajax_"+getbfHandler()+"('x"+returnto+"'" + xtra + ");");
	}

}


/**
* This function is needed for tinyMCE Editor to allow inserting of Images
* with the dynamic joomla image manager popup
*/
function jInsertEditorText( text ) {
	tinyMCE.execCommand('mceInsertContent',false,text);
}

/**
* This function is needed for tinyMCE Editor to allow inserting of
* readmore split
* @todo - First check a readmore doesnt exist
*/
function insertReadmore() {
	//	var content = $getContent
	//	if (content.match(/<hr id=\"system-readmore\" \/>/)) {
	//		alert('Already Exists');
	//		return false;
	//	} else {
	jInsertEditorText('<hr id=\"system-readmore\" />');
	//	}
}

/**
* Ordering of content
*/
function orderItem(item, task){

	/** guess which way to order
	*  The numbers appear the wrong way round - but they ARE CORRECT
	*  really strange I know :-)
	*/
	if (task=='orderup') {
		var dir = '-1';
	} else {
		var dir = '1';
	}

	bfToggleHandler('xorderitem', item, dir);
}

/* set the xajax hideloading message */
function hideLoadingMessage(){
	jQuery('div#loading').hide('slow');
}

/**
* Set up our loading div - has to be delayed as we need to wait for xAJAX to load first
*/
function init() {

	// quit if this function has already been called
	if (arguments.callee.done) {
		return;
	}

	// flag this function so we don't do the same thing twice
	arguments.callee.done = true;


	/** Set the xajax loading message */
	/* xajax 0.5 */
	//	if (typeof(xajax)!='undefined'){
	//		xajax.callback.global.onRequest = function() {}
	//		xajax.callback.global.onResponseDelay = function() {
	//			jQuery('div#loading').show('fast');
	//		}
	//		xajax.callback.global.onExpiration = function() {}
	//		xajax.callback.global.beforeResponseProcessing = function() {}
	//		xajax.callback.global.onFailure = function() {}
	//		xajax.callback.global.onRedirect = function() {}
	//		xajax.callback.global.onSuccess = function() {
	//			hideLoadingMessage();
	//		}
	//		xajax.callback.global.onComplete = function() {
	//			hideLoadingMessage();
	//		}
	//	}
	if ( typeof xajax != "undefined"){
		if ( addLoadingFunction == true){
			xajax.loadingFunction = function(){ jQuery('div#loading').show('fast'); };
		}
		// xajax.doneLoadingFunction = function(){ jQuery('div#loading').hide('slow'); };
	}

	/** hide loading message on first page load - can cause a JS error if timeout below
	*  is reached before the page has loaded fully - need to find a better work around
	*/
	hideLoadingMessage();
}

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

function hideLoadingModalTB(){
	jQuery("#TB_imageOff").unbind("click");
	jQuery("#TB_overlay").unbind("click");
	jQuery("#TB_closeWindowButton").unbind("click");
	jQuery("#TB_window").fadeOut("fast",function(){jQuery('#TB_window,#TB_overlay,#TB_HideSelect').remove();});
	jQuery("#TB_load").remove();
	if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
		jQuery("body","html").css({height: "auto", width: "auto"});
		jQuery("html").css("overflow","");
	}
}

function showLoadingModelTB(){
	if (typeof document.body.style.maxHeight === "undefined") {//if IE 6
		jQuery("body","html").css({height: "100%", width: "100%"});
		jQuery("html").css("overflow","hidden");
		if (document.getElementById("TB_HideSelect") === null) {//iframe to hide select elements in ie6
			jQuery("body").append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div><div id='TB_window'></div>");
			jQuery("#TB_overlay").click(tb_remove);
		}
	}else{//all others
		if(document.getElementById("TB_overlay") === null){
			jQuery("body").append("<div id='TB_overlay'></div><div id='TB_window'>");
		}
	}
	jQuery('#TB_load').show();//show loader
}
