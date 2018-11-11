var bf_tag = {
	bfHandler: function(){
		return 'bf_com_bftag_Handler';
	},
	bf_showAddTagDiv: function(id){
		jQuery('#tagaddform'+id).show('slow');
	},
	bf_hideAddTagDiv: function(id){
		jQuery('#tagaddform'+id).hide('slow');
	},
	bf_addTag: function(id){
		var tagname 	= jQuery('#tagname'+id).val();
		var content_id 	= jQuery('#content_id'+id).val();
		var scope	 	= jQuery('#scope'+id).val();
		var cap	 	= jQuery('#cap').val();
		var args = new Array ('xpublic_addnewtag',content_id,tagname,scope,cap);
		var handler = this.bfHandler();
		/* xajax 0.5 */
		//return 	xajax.call(handler,{parameters:args});
		/* xajax 0.2.4 */
		return 	xajax.call(handler,args);
	}
}