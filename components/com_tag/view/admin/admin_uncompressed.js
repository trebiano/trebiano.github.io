function getbfHandler() {
	return 'bf_com_bftag_AdminHandler';
}
function bfHandler(task, extra) {
	var handler = getbfHandler();
	var args = [task, extra];
	/* xajax 0.5 */
	//return 	xajax.call(handler,{parameters:args});
	/* xajax 0.2.4 */
	return 	xajax.call(handler,args);
}

function searchtagsfromtab(str, component){
	var handler = getbfHandler();
	xajax.call(handler,['xsearchtagsfromtab',str, component]);
}

function addTagToContent (contentid, tagid, newstring, component){
	var handler = getbfHandler();
	if (typeof (newstring) != 'undefined'){
		 extra = [contentid, tagid, newstring, component];
	} else {
		 extra = [contentid, tagid, '', component];
	}
	var args = ['xaddnewtagmapfromtab', extra];
	/* xajax 0.5 */
	//return 	xajax.call(handler,{parameters:args});
	/* xajax 0.2.4 */
	return 	xajax.call(handler,args);
}

function removeTagFromContent(contentid, tagid){
	var handler = getbfHandler();
	var extra = [contentid, tagid];
	var args = ['xremovetagmapfromtab', extra];
	/* xajax 0.5 */
	//return 	xajax.call(handler,{parameters:args});
	/* xajax 0.2.4 */
	return 	xajax.call(handler,args);
}

function saveorder() {
	elements = document.getElementsByName('cid[]');
	orderings = document.getElementsByName('order[]');
	var cids = '';
	for(i = 0; i < elements.length; i++) {
		if(elements[i].value > 0) {
			cids = cids + elements[i].value;
		}
		plus = i + 1;
		if(plus != elements.length) {
			cids = cids + '|';
		}
	}
	var ordering = '';
	for(i = 0; i < orderings.length; i++) {
		if(orderings[i].value > 0) {
			ordering = ordering + orderings[i].value;
		}
		plus = i + 1;
		if(plus != orderings.length) {
			ordering = ordering + '|';
		}
	}
	var handler = getbfHandler();
	var args = ['xsaveorder', 'cids=' + cids, 'ordering=' + ordering];
	/* xajax 0.5 */
	//return 	parent.xajax.call(handler,{parameters:args});
	/* xajax 0.2.4 */
	return 	parent.xajax.call(handler,args);
}
function checkAll_button(n) {
	for(var j = 0; j <= n; j++) {
		box = eval("document.adminForm.cb" + j);
		if(box) {
			if(box.checked === false) {
				box.checked = true;
			}
		}
		else {
			alert("You cannot change the order of items, as an item in the list is `Checked Out`");
			return;
		}
	}
}
function resetHits(model, id) {
	var handler = getbfHandler();
	var args = ['xresethits', 'model=' + model, 'id=' + id];
	/* xajax 0.5 */
	//return 	xajax.call(handler,{parameters:args});
	/* xajax 0.2.4 */
	return 	xajax.call(handler,args);
}
function removalAllTags(id, scope){
	var handler = getbfHandler();
	var args = ['xremoveallmapstocontentid', id, scope];
	/* xajax 0.5 */
	//return 	xajax.call(handler,{parameters:args});
	/* xajax 0.2.4 */
	return 	xajax.call(handler,args);
}