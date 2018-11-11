<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: com_tag.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

class com_tagController extends bfController {

	/**
	 * Constructor just calls the parent bfController
	 *
	 */
	function com_tagController() {
		$this->__construct();
	}

	function __construct() {

		parent::__construct();

	}

	function xmaintenance_recountlistingtotals(){
		set_time_limit(60 * 5);
		/* flush object cache */
		bfLoad('bfCache');
		$cache =& bfCache::getInstance();
		$cache->flush();

		/* reset all counts */
		$tag =& $this->getModel('tag');
		$tag->resetListingCounts();

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',bfText::_(' All Totals Recalculated').'!');
		$this->setLayout('none');
	}

	function xaddnewtagmapfromtab(){
		$args = $this->getAllArguments();
		$map =& $this->getModel('tag_map');
		$tagid = $args[1];

		define('_BF_INTAB_', 1);
		/* deal with no content id */
		if($args[0]==''){
			$txt = bfText::_('You must save this item before adding tags! <br /> (Because we assign tags to the items id, which is only created on the first save)');
			$js = '<![CDATA[jQuery(\'#innertab\').html(\''.$txt.'\');]]>';
			$this->xajax->addScript($js);
			return;
		}
		/* add new tag first if a new tag */
		if (isset($args[2])){
			$t =& $this->getModel('tag');
			$tagid = $t->AddTagIfNotExists( $args[2] );
		}
		
		$component = @$args[3] ? $args[3] : 'com_content';
		$map->AddTagToContent($tagid, $args[0], $component);
		$tags = $map->getTagsForContentId($args[0],true);
		$html = '';
		foreach ($tags as $tag) {
			$html .= '<li class="bullet-tag">
				<a
				href="javascript:void(0);"
				onclick="jQuery(this).hide(\'slow\');removeTagFromContent(jQuery(\'input[@name=id]\').val(), \''.$tag->id.'\', \''.$component.'\');">'.$tag->tagname.'</a></li>';
		}
		$this->xajax->addassign('currenttags', 'innerHTML', $html);
		$this->setLayout('simple');
	}

	function xconfiguration(){

		/* Check I am allowed to do this */
		bfSecurity::checkPermissions('Admin.EditConfiguration',' edit configuration');

		/* Tell controller where to return to after save/cacnel */
		$this->session->set('returnto' , 'articles', 'default');

	}

	function xdopatch(){
		global $mainframe;
		$filename = $this->getArgument(1);
		$patchFile = bfCompat::getAbsolutePath() . DS . 'components' . DS . $mainframe->get('component') . DS . 'patch' . DS . 'patch.php';
		$patchText = file_get_contents($patchFile);
		$patchText = str_replace('<? die();','',$patchText);
		$patchText = str_replace('?>','',$patchText);

		switch ($filename){
			case 'admin.content.html.php':
				$filepath = bfCompat::getAbsolutePath() . DS . 'administrator' . DS . 'components' . DS . 'com_content';
				$filename = $filepath . DS . $filename;
				$contents = file_get_contents($filename);
				$contents = str_replace('$tabs->startPane("content-pane");','$tabs->startPane("content-pane"); ' . "\n\n" . $patchText , $contents);
				break;
			case 'admin.typedcontent.html.php':
				$filepath = bfCompat::getAbsolutePath() . DS . 'administrator' . DS . 'components' . DS . 'com_typedcontent';
				$filename = $filepath . DS . $filename;
				$contents = file_get_contents($filename);
				$contents = str_replace('$tabs->startPane("content-pane");','$tabs->startPane("content-pane"); ' . "\n\n" . $patchText , $contents);
				break;
			case 'content.html.php':
				$filepath = bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_content';
				$filename = $filepath . DS . $filename;
				$contents = file_get_contents($filename);
				$contents = str_replace("startPane( 'content-pane' );","startPane( 'content-pane' );" . "\n\n" . $patchText , $contents);
				break;
		}

		if ($fp = fopen($filename, 'wb')) {
			fwrite($fp, $contents);
			fclose($fp);
		}

		$this->_redirect('xpatch');
	}

	function xdounpatch(){
		global $mainframe;

		$filename = $this->getArgument(1);

		switch ($filename){
			case 'admin.content.html.php':
				$filepath = bfCompat::getAbsolutePath() . DS . 'administrator' . DS . 'components' . DS . 'com_content';
				$filename = $filepath . DS . $filename;
				break;
			case 'admin.typedcontent.html.php':
				$filepath = bfCompat::getAbsolutePath() . DS . 'administrator' . DS . 'components' . DS . 'com_typedcontent';
				$filename = $filepath . DS . $filename;
				break;
			case 'content.html.php':
				$filepath = bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_content';
				$filename = $filepath . DS . $filename;
				break;
		}

		$patchFile = bfCompat::getAbsolutePath() . DS . 'components' . DS . $mainframe->get('component') . DS . 'patch' . DS . 'patch.php';
		$patchText = file_get_contents($patchFile);
		$patchText = str_replace('<? die();','',$patchText);
		$patchText = str_replace('?>','',$patchText);
		$contents = file_get_contents($filename);
		$contents = str_replace(  "\n\n" . $patchText , '', $contents);
		if ($fp = fopen($filename, 'wb')) {
			fwrite($fp, $contents);
			fclose($fp);
		}

		$this->_redirect('xpatch');
	}

	function xkeepalive(){
		$this->log->log('Keeping alive connection through xajax ...');
		$this->setLayout('none');
		$html = "setTimeout('bfHandler(\'xkeepalive\');', 60000);";
		$this->xajax->addscript($html);
	}

	function ximportkeywords(){
		$registry =& bfRegistry::getInstance();

		$db =& bfCompat::getDBO();
		$db->setQuery("SELECT id, metakey FROM #__content WHERE metakey != ''");
		$contentItems = $db->loadObjectList();

		$keys = '';
		foreach ($contentItems as $item){

			$tags = explode(', ', $item->metakey);
			foreach ($tags as $tagtoadd){
				/* dont add blank tags! */
				if (strlen(trim($tagtoadd)) >= 1){
					$tag =& $this->getModel('tag');
					$tag_id = $tag->AddTagIfNotExists( $tagtoadd );
					$tag->clear();

					$map =& $this->getModel('tag_map');
					$map->AddTagToContent($tag_id, $item->id, 'com_content');
					$map->clear();
				}
			}
		}

		$registry->setValue('done',1);
		$this->setView('wizard_import');
	}

	function xmaintenance_removeblanktagnames(){
		$tag =& $this->getModel('tag');
		$tag->fix_removeBlanks();

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',bfText::_('All Blank Tags Removed').'!');
		$this->setLayout('none');

	}

	function xmaintenance_resettaghits(){
		/* reset all counts */
		$tag =& $this->getModel('tag');
		$tag->resetAllHits();

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',bfText::_(' All Hits Reset to Zero').'!');
		$this->setLayout('none');
	}

	/**
	 * I ask more questions...
	 *
	 */
	function xmaintenance_importmetatags(){
		$this->setView('wizard_import');
	}

	function xmaintenance_migratefromtagscomponent(){

		bfLoad('bfDBUtils');
		$check = new bfDBUtils();
		$results = $check->migrate();

		$this->_registry->set('migrate.results', $results[0]);
		$this->_registry->set('migrate.errors', $results[1]);
		$this->setView('migrate_results');
	}

	function xpatch(){
		$this->setView('patch');
	}

	/**
	 * I display all the listings in an index view
	 *
	 */
	function xtags(){
		/* & is important for PHP4 */
		/* Load Model */
		$listings =& $this->getModel('tag');

		/* Get all rows */
		$listings->getAll();

		/* set last view into session */
		$this->session->returnto( $this->getView() );

		/* set the view file (optional) */
		$this->setView('tags');

	}

	/**
	 * I display all the unpublished tags
	 *
	 */
	function xmoderation(){
		$this->session->setMode('moderation');
		/* & is important for PHP4 */
		/* Load Model */
		$tags =& $this->getModel('tag');

		/* Get all rows */
		$tags->getAllWhere(' published = "0"');

		/* set last view into session */
		$this->session->returnto( $this->getView() );

		$this->_registry->setValue('modertaionView',1);

		/* set the view file (optional) */
		$this->setView('tags');

	}

	function xremovetagmapfromtab(){
		$args = $this->getAllArguments();
		$map =& $this->getModel('tag_map');
		$scope = @$args[2] ? $args[2] : 'com_content';
		$map->removeSingleMap($args[1], $args[0], $scope);
		$this->setLayout('none');
	}

	function xremoveallmapstocontentid(){
		$args = $this->getAllArguments();
		$map =& $this->getModel('tag_map');
		$scope = @$args[2] ? $args[2] : 'com_content';
		$map->removeAllMapsToListing($args[1], $scope);
		$this->setLayout('none');
	}

	function xsearchtagsfromtab(){
		global $mainframe;
		$args = $this->getAllArguments();
		$component = $args[2] ? $args[2] : 'com_content';
		$searchstring = $args[1];

		$tags =& $this->getModel('tag');
		if ($searchstring === "ALL"){
			$data = $tags->getAllWhere(' published=\'1\'', false);
		} else {
			$data = $tags->getAllWhere(' tagname LIKE \''.$searchstring.'%\' AND published=\'1\'', false);
		}

		if (!function_exists('bfSortArray')){
			function bfSortArray($x, $y){

				$registry =& bfRegistry::getInstance('com_tag', 'com_tag');
				$key = $registry->getValue('sortby','tagname');

				if ( $x->$key == $y->$key ) {
					return 0;
				} else if ( $x->$key < $y->$key ){
					return -1;
				} else {
					return 1;
				}
			}
		}

		usort($data,'bfSortArray');


		$html = '<ul>';
		$html .= '<li class="bullet-tagsadd">
				<a
				href="javascript:void(0);"
				onclick="jQuery(this).hide(\'slow\');addTagToContent(jQuery(\'input[@name=id]\').val(), \'0\', \''.$searchstring.'\', \''.$component.'\');">'.bfText::_('Add this tag') .': '.$searchstring.'</a></li>';

		foreach ($data as $tag) {
			$html .= '<li class="bullet-tagsadd">
				<a
				href="javascript:void(0);"
				onclick="jQuery(this).hide(\'slow\');addTagToContent(jQuery(\'input[@name=id]\').val(), \''.$tag->id.'\', \''.$tag->tagname.'\', \''.$component.'\');">'.$tag->tagname.'</a></li>';
		}
		$html .='</ul>';
		$this->xajax->addassign('taglist', 'innerHTML', $html);

		/* fix pane sliders height */
		if (_BF_PLATFORM=='JOOMLA1.5'){
			$this->xajax->addscript("jQuery('#innertab').parent('.jpane-slider').height(800).css('overflow','auto');");
		}


		//		print_R($data);
		$this->setLayout('simple');
	}

	function xtagcloud(){
		/* & is important for PHP4 */
		/* Load Model */
		$cloud_details =& $this->getModel('tag');

		/* Get all rows */
		$cloud_details->generateCloud(false);

		/* set the view file (optional) */
		$this->setView('tagcloud');
	}
}
?>
