<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfHTML.php 962 2007-07-04 17:08:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */


class bfHTML {

	/**
	 * Pull in the adminForm <FORM> tag.
	 *
	 */
	function preView(){
		global $mainframe;

		$isPopup = $this->_registry->getValue('isPopup');

		if (file_exists(_BF_JPATH_BASE . DS . 'components' . DS . $mainframe->get('component') . DS . 'view' . DS . 'admin' . DS .'submenu.php')){
			$hasMenu = true;
		} else ($hasMenu=false);

		if ($hasMenu===true && !$isPopup){
			echo '<div class="col15" id="bf-left-content"><div id="leftmenu">';
			include(_BF_JPATH_BASE . DS . 'components' . DS . $mainframe->get('component') . DS . 'view' . DS . 'admin' . DS .'submenu.php');
			echo '</div></div>';
		}
		require(_BF_FRONT_LIB_VIEW_DIR	. DS . 'pre_view.php');
		if ($hasMenu===true)echo '<div class="col85" id="bf-main-content">';
	}

	/**
	 *
	 * Pull in the adminForm </FORM> tag along with any hidden fields.
	 */
	function postView(){
		global $mainframe;
		if (file_exists(_BF_JPATH_BASE . DS . 'components' . DS . $mainframe->get('component') . DS . 'view' . DS . 'admin' . DS .'submenu.php')){
			$hasMenu = true;
		} else ($hasMenu=false);
		require(_BF_FRONT_LIB_VIEW_DIR	. DS . 'post_view.php');
		if ($hasMenu===true)echo '</div>';
	}


	/**
	 * Draws control panel icons
	 *
	 * @param string $text
	 * @param string $xtask
	 * @param string $image
	 * @param string $link
	 */
	function controlPanelIcon($text, $xtask, $image='templates/khepri/images/header/icon-48-article-add.png', $link='javascript:void(0);', $isXAJAXTask = true) {

		$offSite = ereg('http',$link);
		$str  = '';
		$str  .= '<div style="float:left;">';
		$str .= '   <div class="icon2" style="align: center;"><center>';
		$str .= '      <a href="'.$link.'"';
		if ($isXAJAXTask===true){
			$str .= $xtask ? ' onClick="highlightSubMenuItem($(\''.$xtask.'\'));bfHandler(\''.$xtask.'\');"' : '';
		} else {
			$str .= $xtask ? ' onClick="'.$xtask.'"' : '';
		}
		$str .= $offSite ? ' target="_blank"' : '' . '>';

		$str .= '	      <img src="'.$image.'" alt="'.$text.'" align="middle" border="0" />';

		$str .= '			<span>'.$text.'</span>';
		$str .= '	   </a>';
		$str .= '	</center></div>';
		$str .= '</div>';
		return $str;
	}

	/**
	 * Expects to be passed an array
	 *
	 * @param unknown_type $obj
	 * @return unknown
	 */
	function convertArrayToHTML($Arr, $defaultValue=null){
		/* Deal with 0 being lost as PHP thinks its nothing */

		if ($defaultValue=='0'){

			$val = $defaultValue;

		} elseif (isset($defaultValue)) {
			$val = $defaultValue;

		}else{


			/* use default from framework vars */
			$val = $Arr[4];
		}

		if (isset($Arr[7])){
			/* select list */
			$options=array();
			foreach ($Arr[7] as $k=>$v){
				$options[] = bfHTML::makeOption($k,$v);
			}
			$str = bfHTML::selectList2($options, $Arr[0], ' class="flatinputbox"','value','text',$val);
		}elseif ($Arr[3]=='yesnoradiolist'){
			$str = bfHTML::yesnoRadioList($Arr[0], ' class="inputbox"', $val);
		}elseif ($Arr[3]=='textbox'){
			$str = bfHTML::$Arr[3]($Arr[0], $val,'',' class="flatinputbox"');
		} else {
			//			textbox($name,$value,$size=null,$extra=null, $id=null){
			$str = bfHTML::$Arr[3]($Arr[0], $val,' class="flatinputbox"');
		}
		return $str;
	}

	/**
	 * use selectlist2 please
	 *
	 * @param unknown_type $name
	 * @param unknown_type $value
	 * @param unknown_type $extra
	 * @return unknown
	 */
	function selectlist($name,$value,$extra=null, $optionValues){
		return 'use selectlist2 pease';
	}

	function selectList2( &$arr, $tag_name, $tag_attribs=' class="flatinputbox"', $key, $text, $selected=NULL ) {
		// check if array
		if ( is_array( $arr ) ) {
			reset( $arr );
		}

		$html 	= "\n<select name=\"$tag_name\" id=\"$tag_name\" $tag_attribs>";
		$count 	= count( $arr );

		for ($i=0, $n=$count; $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					bfHTML::mooToolTip();
					$k2 = $obj->$key;
					if ($k == $k2) {
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " selected=\"selected\"" : '');
			}
			$html .= "\n\t<option value=\"".$k."\"$extra>" . bfText::_($t) . "</option>";
		}
		$html .= "\n</select>\n";

		return $html;
	}

	function selectbox(){
	}

	/**
	 * Generates a HTML Textbox Form Element
	 *
	 * @param string $name
	 * @param string $value
	 * @param string $extra
	 * @param string $id
	 * @return string HTML
	 */
	function textbox($name,$value,$size=null,$extra=null, $id=null){
		if ($id===null) $id = $name;
		if ($size===null) $size = 50;
		return '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" size ="'.$size.'" '.$extra.' />';
	}

	/**
	 * Generates a HTML Textbox Form Element
	 *
	 * @param string $name
	 * @param string $value
	 * @param string $cols
	 * @param string $rows
	 * @param string $id
	 * @return string HTML
	 */
	function textarea($name, $value, $cols, $rows, $id=null){
		if ($id === null) $id = null;
		return '<textarea name="'.$name.'"id="'.$id.'" cols="'.$cols.'" rows="'.$rows.'">'.$value.'</textarea>';
	}



	/**
	 * Return the html for an HTML radio list
	 *
	 * @param unknown_type $name
	 * @param unknown_type $value
	 * @param unknown_type $extra
	 * @return unknown
	 */
	//	function yesnoradiolist($name,$value,$extra=null){
	//		//		mosHTML::yesnoSelectList()
	//		$str = mosHTML::yesnoRadioList($name,$extra,$value);
	//		return $str;
	//	}

	/**
	* Generates an HTML radio list
	* @param array An array of objects
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @param string The name of the object variable for the option value
	* @param string The name of the object variable for the option text
	* @returns string HTML for the select list
	*/
	function radioList( &$arr, $tag_name, $tag_attribs, $selected=null, $key='value', $text='text' ) {
		reset( $arr );
		$html = "";
		for ($i=0, $n=count( $arr ); $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					$k2 = $obj->$key;
					if ($k == $k2) {
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " checked=\"checked\"" : '');
			}
			$html .= "\n\t<input type=\"radio\" name=\"$tag_name\" id=\"$tag_name$k\" value=\"".$k."\"$extra $tag_attribs />";
			$html .= "\n\t<label for=\"$tag_name$k\">$t</label>";
		}
		$html .= "\n";

		return $html;
	}

	/**
	* Writes a yes/no radio list
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	function yesnoRadioList( $tag_name, $tag_attribs, $selected, $yes='Yes', $no='No' ) {

		$arr = array(
		bfHTML::makeOption( '0', bfText::_($no) ),
		bfHTML::makeOption( '1', bfText::_($yes) )
		);

		return bfHTML::radioList( $arr, $tag_name, $tag_attribs, $selected );
	}

	/**
	 * Disable Enter in Form Fields
	 *
	 * @param unknown_type $onKeyPress
	 */
	function disableEnterInFormFields ($onKeyPress=0) {
		$onKP = ' onkeypress="return handleEnter(this, event)"';
		if ($onKeyPress){
			/* attach the JS to the form field */
			echo $onKP;
		}
	}

	function drawPermissionLinks($i=null, $value, $noTDs=null, $model=null) {

		/* if there is no record dont display item */
		if($i===null) return bfText::_('Save Record First');

		if ($model===null) $model = $this->session->get('lastModel','default');
		/* Change the links, next state and colours of the links */
		switch($value){
			case '0':
				$current = bfText::_('DENY');
				$next = '1';
				$color = 'red';
				break;

			case '1':
				$current = bfText::_('ALLOW');
				$next = '0';
				$color = 'green';
				break;

			default:
				$current = bfText::_('ERROR');
				$next = 'ERROR';
				$color = 'red';

				break;
		}

		$html = '';
		if (!$noTDs) $html .= '<td align="center" style="width: 60px;"><span id="xaccess'.$i.'">';
		$extra = ' title="'.bfText::_('Toggle Access').' :: '.bfText::_('Click here to toggle access permission').'"';
		$html .= '<a href="javascript:void(0);" '.$extra.' class="hasTip access-'.$color.'" style="color: '.$color.'; " onClick="bfToggleHandler(\'xtogglePermissionACL\', \''.$i.'\', \''.$next.'\',\''.$model.'\');">'.$current.'</a>';
		if (!$noTDs) $html .= '</span></td></td>';

		if ($noTDs){
			return $html;
		} else {
			echo $html;
		}

	}

	/**
	 * Draws the Access name and toggle link
	 *
	 * @param int $i The id of the row
	 * @param int $value The current access value 0/1/2
	 * @param int $noTDs Display the <TD> Tags
	 * @param string $model the name of the model
	 * @return string HTML
	 */
	function drawAccessLinks($i=null, $value, $noTDs=null, $model=null) {

		/* if there is no record dont display item */
		if($i===null) return bfText::_('Save Record First');

		if ($model===null) $model = $this->session->get('lastModel','default');
		/* Change the links, next state and colours of the links */
		switch($value){
			case '0':
				$current = bfText::_('Public');
				$next = '1';
				$color = 'green';
				break;

			case '1':
				$current = bfText::_('Registered');
				$next = '2';
				$color = 'orange';
				break;

			case '2':
				$current = bfText::_('Special');
				$next = '0';
				$color = 'red';
				break;

			default:
				$current = bfText::_('ERROR');
				$next = 'ERROR';
				$color = 'red';

				break;
		}

		$html = '';
		if (!$noTDs) $html .= '<td align="center" style="width: 60px;"><span id="xaccess'.$i.'">';
		$extra = ' title="'.bfText::_('Toggle Access').' :: '.bfText::_('Click here to toggle access permission').'"';
		$html .= '<a href="javascript:void(0);" '.$extra.' class="hasTip access-'.$color.'" style="color: '.$color.'; " onClick="bfToggleHandler(\'xtoggleAccess\', \''.$i.'\', \''.$next.'\',\''.$model.'\');">'.$current.'</a>';
		if (!$noTDs) $html .= '</span></td></td>';

		if ($noTDs){
			return $html;
		} else {
			echo $html;
		}

	}

	/**
	 *
	 * Draw the box around the checkbox for an item if it is booked out
	 * @param int $i
	 * @param int $cb_count
	 * @param int $value
	 */
	function drawIDBox($i, $cb_count, $value, $row, $xajaxLink=null){
		//echo '<td width="10"><input type="checkbox" id="cb'.$cb_count.'" name="cid[]" value="'.$value.'" onclick="'.$onClick.'" /></td>';
		//return;
		isset($row->checked_out) ? $checkdOutId = $row->checked_out : $checkdOutId = 0;

		//		echo '<td width="10">'.$i.'</td>';
		if ($checkdOutId>0){
			$user =& bfFactory::getUser();
			if ($checkdOutId != $user->get('id')){
				$u = new bfUser($checkdOutId);
				echo '<td width="10">'.mosToolTip(bfText::_('Checked out by ').$u->get('username'),null,null,'../../../administrator/images/checked_out.png',null,null,0).'</td>';
			} elseif ($checkdOutId == $user->get('id')){
				echo '<td style="border:1px solid red" width="10"><input type="checkbox" id="cb'.$cb_count.'" name="cid[]" value="'.$value.'" onclick="isChecked(this.checked);" /></td>';
			} else{
				echo '<td width="10"><input type="checkbox" id="cb'.$cb_count.'" name="cid[]" value="'.$value.'" onclick="isChecked(this.checked);" /></td>';
			}
		} else {
			if ($xajaxLink){
				$xajaxLink = str_replace('{ID}',$row->id,$xajaxLink);
				$onClick = $xajaxLink;
			} else {
				$onClick = 'isChecked(this.checked);';
			}
			echo '<td width="10"><input type="checkbox" id="cb'.$cb_count.'" name="cid[]" value="'.$value.'" onclick="'.$onClick.'" /></td>';
		}
	}

	/**
	 * Draw the table for a tab index.
	 *
	 * @param unknown_type $model
	 * @param unknown_type $showFilter
	 * @param unknown_type $view
	 */
	function drawIndexTable($model, $showFilter=false, $view){
		global $mainframe;
		/* Get our registry */
		$registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));

		/* Get our index fields */
		$index_fields = $registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.indexFields.'.$view);

		/* Get our session */
		$session =& bfSession::getInstance();

		/* Get our log */
		$log =& bfLog::getInstance();

		/* Show filter box, go and reset */
		if ($showFilter===true){
			bfHTML::filter();
		}

		/* Start Table */
		echo '<table class="bfadminlist"><thead><tr>';

		if ($registry->getValue('orderingview',0)==0){
			$limit = $session->get('limit');
		} else {
			$limit = 9999999999999;
		}

		$cb_count=0; // Check box count
		//$total=sizeof($fortune['rows']);
		$total=$session->get('rowcount');
		$page=max($session->get('page'),1);

		$log->log("view/$view.php limit=$limit");

		if (!isset( $model['rows'][0])) {
			echo bfText::_('No matching entries were found');
		} else {
			$row = $model['rows'][0];
			foreach($index_fields as $key=>$value ) {
				if (is_integer($key)) $key=$value;


				if ($key == "id") {
					//	echo '<th width="10">#</th>';
					echo bfHTML::tickAllTH($limit);
				} elseif ($key=='ordering'){
					echo bfHTML::orderByTH($key);
					bfHTML::saveorderButton( $model['rows'] ) ;
				} else {
					echo bfHTML::orderByTH($key, $value);
				}
			}
		}
		echo '</tr></thead>';

		/* Pagination */
		$firstitem=(($page - 1) * $limit + 1);
		$lastitem=min( $firstitem + $limit -1, $total );

		$limitstart = $firstitem;

		if (isset($index_fields)){
			if (in_array('ordering',$index_fields)){
				$colspan = count($index_fields)+2;
			}else{
				$colspan = count($index_fields)+1;
			}
		}else {
			$colspan =2;
		}

		$firstitem = 1;
		$iptr=0;
		$k = 0;
		$i=$firstitem;
		if (@is_array($model['rows'])){
			if (count($model['rows'])){

				foreach ($model['rows'] as $row ) {
					echo '<tr class="row'.$k.'">';

					$id=$row->id;
					foreach($index_fields as $key=>$value ) {
						if (is_integer($key)) $key=$value;
						$value = $row->$key;

						switch ($key){

							case 'id':
								bfHTML::drawIDBox($i, $cb_count, $value, $row);
								break;
							case 'hits':
								$session =& bfSession::getInstance($mainframe->get('component'));
								$m = $session->get('lastModel','default');
								echo bfHTML::drawHitsLinks($m,$row->id,$row->hits,true);
								break;

							case 'published':
							case 'enabled':
								bfHTML::publishInformationTD($id, $value,null,null,$key);
								break;

								//							case 'enabled':
								//								bfHTML::enabledInformationTD($id, $value);
								//								break;

							case 'access':
								if ($view=='permission'){
									/* com_tackle */
									bfHTML::drawPermissionLinks($row->id, $value);
								} else {
									bfHTML::drawAccessLinks($row->id, $value);
								}
								break;

							case 'ordering':
								$n =count($model['rows']);
								bfHTML::drawOrderingLinks($row, $i, $n, $limitstart, $total);
								break;

							default:
								$path = bfCompat::getLiveSite() . '/'.bfCompat::mambotsfoldername().'/system/blueflame/view/images/';
								$img = bfHTML::allOrPart($value);
								$align = ' style="text-align: left;" ';
								if ( $key =='ip'){
									$img = "<abbrev class=\"hasTip\" title=\"".bfText::_('IP Address')." :: ".$value."\"><img src=\""
									.$path.'ip.gif'."\" align=\"absmiddle\" />&nbsp;"
									.'</abbrev>';
									$align = ' style="text-align: center;" ';
								}


								if ( $key =='date'){
									if (!defined('_DATE_FORMAT_LC')) DEFINE('_DATE_FORMAT_LC',"%H:%M:%S");
									$img = "<abbrev class=\"hasTip\" title=\"".bfText::_('Date')." :: ".$value."\"><img src=\""
									.$path.'date.gif'."\" align=\"absmiddle\" />&nbsp;"
									. mosFormatDate($value, _DATE_FORMAT_LC).'</abbrev>';
									$align = ' style="text-align: center;" ';
								}
								if ( $key =='email'){
									$img = "<abbrev class=\"hasTip\" title=\"".bfText::_('Email Address')." :: ".$value."\">
									<a href=\"mailto:".stripslashes($value)."\"><img src=\""
									.$path.'email_link.gif'."\" align=\"absmiddle\" /></a>&nbsp;"
									.'</abbrev>';
									$align = ' style="text-align: center;" ';
								}

								if ( $key =='browser'){
									$parts = explode(' (', $value);
									$img = "<abbrev class=\"hasTip\" title=\"".bfText::_('Browser')." :: ".$value."\">".
									trim($parts[0]) . "</abbrev>" ;
								}

								if ( $key =='name')
								$img = "<img src=\"".$path.'user_green.gif'."\" align=\"absmiddle\" />&nbsp;".bfHTML::allOrPart($value);

								if ( ($view == 'category' OR $view =='categories') && ($key=='title' OR $key =='name')) {
									if (isset($row->category_path)){
										$preview = '<br /><small>' .$row->category_path.'</small>';
									} else {
										$preview ='';
									}
									$img = "<img src=\"".$path.'folder.gif'."\" align=\"absmiddle\" />&nbsp;".bfHTML::allOrPart($value).$preview;

								}
								if ( ($view == 'acronym' OR $view =='acronyms') && ($key=='short' OR $key =='name')) {
									$img = "<img src=\"".$path.'bullet-acronym.png'."\" align=\"absmiddle\" />&nbsp;".bfHTML::allOrPart($value);

								}

								if ( ($view == 'articles' OR $view =='content') && ($key=='title' OR $key =='name'))
								$img = "<img src=\"".$path.'page.gif'."\" align=\"absmiddle\" />&nbsp;".bfHTML::allOrPart($value);

								if ( ($view == 'layouts' OR $view =='templates') && ($key=='title' OR $key =='name'))
								$img = "<img src=\"".$path.'bullet-layout.gif'."\" align=\"absmiddle\" />&nbsp;".bfHTML::allOrPart($value)
								.'<br /><small>'.@$row->desc.'</small>';

								if ( ($view == 'comment' OR $view =='comments') && ($key=='comment'))
								$img = "<img src=\"".$path.'comment.gif'."\" align=\"absmiddle\" />&nbsp;".bfHTML::allOrPart($value);

								$img = '<span class="title">'.$img.'</span>';

								if ($registry->getValue('orderingview',0)==0){
									echo "<td".$align." onclick=\"bfHandler('xedit', '".$row->id."');\">";
								} else {
									echo "<td".$align.">";
								}
								echo $img . '</td>';
								break;
						}
					}
					echo '</tr>';
					$cb_count++;
					$i++;
					$firstitem = 0;
					$k = 1 - $k;
				}
			}
		}

		echo '</table>';

		if ($registry->getValue('orderingview',0)==0){
			echo bfHTML::pagination($page,$limit,$total);
		}
	}

	/**
	 * Draw the table for a tab index.
	 *
	 * @param unknown_type $model
	 * @param unknown_type $showFilter
	 * @param unknown_type $view
	 */
	function drawPopupIndexTable($model, $showFilter=false, $view, $xajaxLink='javascript:alert(\'NOT SET\');'){
		global $mainframe;
		/* Get our registry */
		$registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));

		/* Get our index fields */
		$index_fields = $registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.indexFields.'.$view);

		/* Get our session */
		$session =& bfSession::getInstance();

		/* Get our log */
		$log =& bfLog::getInstance();

		/* Show filter box, go and reset */
		if ($showFilter===true){
			bfHTML::filter();
		}

		/* Start Table */
		echo '<table class="bfadminlist"><thead><tr>';

		$limit = $session->get('limit');

		$cb_count=0; // Check box count
		//$total=sizeof($fortune['rows']);
		$total=$session->get('rowcount');
		$page=max($session->get('page'),1);

		$log->log("view/$view.php limit=$limit");

		if (!isset( $model['rows'][0])) {
			echo bfText::_('No matching entries were found');
		} else {
			$row = $model['rows'][0];
			foreach($index_fields as $key=>$value ) {
				if (is_integer($key)) $key=$value;


				if ($key == "id") {
					//	echo '<th width="10">#</th>';
					echo bfHTML::tickAllTH($limit);
				} elseif ($key=='ordering'){
					echo bfHTML::orderByTH($key);
					bfHTML::saveorderButton( $model['rows'] ) ;
				} else {
					echo bfHTML::orderByTH($key, $value);
				}
			}
		}
		echo '</tr></thead>';

		/* Pagination */
		$firstitem=(($page - 1) * $limit + 1);
		$lastitem=min( $firstitem + $limit -1, $total );

		$limitstart = $firstitem;

		if (isset($index_fields)){
			if (in_array('ordering',$index_fields)){
				$colspan = count($index_fields)+2;
			}else{
				$colspan = count($index_fields)+1;
			}
		}else {
			$colspan =2;
		}

		$firstitem = 1;
		$iptr=0;
		$k = 0;
		$i=$firstitem;
		if (@is_array($model['rows'])){
			if (count($model['rows'])){

				foreach ($model['rows'] as $row ) {
					echo '<tr class="row'.$k.'" id="row'.$row->id.'">';

					$id=$row->id;
					foreach($index_fields as $key=>$value ) {
						if (is_integer($key)) $key=$value;
						$value = $row->$key;

						switch ($key){

							case 'id':
								bfHTML::drawIDBox($i, $cb_count, $value, $row, $xajaxLink);
								break;
							case 'hits':
								$session =& bfSession::getInstance($mainframe->get('component'));
								$m = $session->get('lastModel','default');
								echo bfHTML::drawHitsLinks($m,$row->id,$row->hits,true);
								break;

							case 'published':
							case 'enabled':
								bfHTML::publishInformationTD($id, $value,null,null,$key);
								break;

								//							case 'enabled':
								//								bfHTML::enabledInformationTD($id, $value);
								//								break;

							case 'access':
								if ($view=='permission'){
									/* com_tackle */
									bfHTML::drawPermissionLinks($row->id, $value);
								} else {
									bfHTML::drawAccessLinks($row->id, $value);
								}
								break;

							case 'ordering':
								$n =count($model['rows']);
								bfHTML::drawOrderingLinks($row, $i, $n, $limitstart, $total);
								break;

							default:
								$path = bfCompat::getLiveSite() . '/'.bfCompat::mambotsfoldername().'/system/blueflame/view/images/';
								$img = bfHTML::allOrPart($value);
								$align = ' style="text-align: left;" ';
								if ( $key =='ip'){
									$img = "<abbrev class=\"hasTip\" title=\"".bfText::_('IP Address')." :: ".$value."\"><img src=\""
									.$path.'ip.gif'."\" align=\"absmiddle\" />&nbsp;"
									.'</abbrev>';
									$align = ' style="text-align: center;" ';
								}


								if ( $key =='date'){
									if (!defined('_DATE_FORMAT_LC')) DEFINE('_DATE_FORMAT_LC',"%H:%M:%S");
									$img = "<abbrev class=\"hasTip\" title=\"".bfText::_('Date')." :: ".$value."\"><img src=\""
									.$path.'date.gif'."\" align=\"absmiddle\" />&nbsp;"
									. mosFormatDate($value, _DATE_FORMAT_LC).'</abbrev>';
									$align = ' style="text-align: center;" ';
								}
								if ( $key =='email'){
									$img = "<abbrev class=\"hasTip\" title=\"".bfText::_('Email Address')." :: ".$value."\">
									<a href=\"mailto:".stripslashes($value)."\"><img src=\""
									.$path.'email_link.gif'."\" align=\"absmiddle\" /></a>&nbsp;"
									.'</abbrev>';
									$align = ' style="text-align: center;" ';
								}

								if ( $key =='browser'){
									$parts = explode(' (', $value);
									$img = "<abbrev class=\"hasTip\" title=\"".bfText::_('Browser')." :: ".$value."\">".
									trim($parts[0]) . "</abbrev>" ;
								}

								if ( $key =='name')
								$img = "<img src=\"".$path.'user_green.gif'."\" align=\"absmiddle\" />&nbsp;".bfHTML::allOrPart($value);

								if ( ($view == 'category' OR $view =='categories') && ($key=='title' OR $key =='name')) {
									if (isset($row->category_path)){
										$preview = '<br /><small>' .$row->category_path.'</small>';
									} else {
										$preview ='';
									}
									$img = "<img src=\"".$path.'folder.gif'."\" align=\"absmiddle\" />&nbsp;".bfHTML::allOrPart($value).$preview;

								}
								if ( ($view == 'acronym' OR $view =='acronyms') && ($key=='short' OR $key =='name')) {
									$img = "<img src=\"".$path.'bullet-acronym.png'."\" align=\"absmiddle\" />&nbsp;".bfHTML::allOrPart($value);

								}

								if ( ($view == 'articles' OR $view =='content') && ($key=='title' OR $key =='name'))
								$img = "<img src=\"".$path.'page.gif'."\" align=\"absmiddle\" />&nbsp;".bfHTML::allOrPart($value);

								if ( ($view == 'layouts' OR $view =='templates') && ($key=='title' OR $key =='name'))
								$img = "<img src=\"".$path.'bullet-layout.gif'."\" align=\"absmiddle\" />&nbsp;".bfHTML::allOrPart($value)
								.'<br /><small>'.@$row->desc.'</small>';

								if ( ($view == 'comment' OR $view =='comments') && ($key=='comment'))
								$img = "<img src=\"".$path.'comment.gif'."\" align=\"absmiddle\" />&nbsp;".bfHTML::allOrPart($value);

								$img = '<span class="title">'.$img.'</span>';
								echo "<td".$align." onclick=\"bfHandler('xedit', '".$row->id."');\">"
								.$img.'</td>';
								break;
						}
					}
					echo '</tr>';
					$cb_count++;
					$i++;
					$firstitem = 0;
					$k = 1 - $k;
				}
			}
		}

		echo '</table>';
		echo bfHTML::pagination($page,$limit,$total);
	}

	function drawValidationsIndexTable($rows){

	}

	function drawOrderingLinks($row, $i, $n, $limitstart, $total){
		//		echo 'limitstart =' . $limitstart;
		//		echo 'total =' . $total;


		$bfsession =& bfSession::getInstance();
		/* if we are NOT sorting by ordering then DISABLE ordering */
		$disabled = $bfsession->get('filter_order')=='ordering' ?  '' : '"disabled=disabled"';
		$enabled = $bfsession->get('filter_order')=='ordering' ?  true : false;
		?>
		<td class="order" width="120" colspan="2">
			<span><?php echo bfHTML::orderUpIcon( $i, true, 'orderup', bfText::_('Move Up'), $enabled, $limitstart, $row->id); ?></span>
			<span><?php echo bfHTML::orderDownIcon( $i, $n, true, 'orderdown', bfText::_('Move Down'), $enabled , $limitstart, $total, $row->id); ?></span>
			<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />
		</td>
		<?php
	}

	/**
	 * Return the icon to move an item UP
	 *
	 * @access public
	 * @param int $i The row index
	 * @param boolean $condition True to show the icon
	 * @param string $task The task to fire
	 * @param string $alt The image alternate text string
	 * @param int $limitstart The limit start
	 * @param int $total The total number of rows for this view
	 * @return string Either the icon to move an item up or a space
	 */
	function orderUpIcon($i, $condition = true, $task = 'orderup', $alt = 'Move Up', $enabled = true, $limitstart, $rowid)
	{
		$alt = bfText::_($alt);

		$html = '&nbsp;';
		if (($i > 0 && ($i + $limitstart  -2 > 0)) && $condition)
		//		if ($i > 0 )
		{
			if($enabled) {
				$html  = '<a href="javascript:void(0);" onclick="return orderItem(\''.$rowid.'\',\''.$task.'\')" title="'.$alt.'">';
				$html .= '   <img src="images/uparrow.png" width="12" height="12" border="0" alt="'.$alt.'" />';
				$html .= '</a>';
			} else {
				$html  = '<img src="images/uparrow0.png" width="12" height="12" border="0" alt="'.$alt.'" />';
			}
		}

		return $html;
	}

	/**
	 * Return the icon to move an item DOWN
	 *
	 * @access public
	 * @param int $i The row index
	 * @param int $n The number of items in the list
	 * @param boolean $condition True to show the icon
	 * @param string $task The task to fire
	 * @param string $alt The image alternate text string
	 * @param int $limitstart The limit start
	 * @param int $total The total number of rows for this view
	 * @return string Either the icon to move an item down or a space
	 */
	function orderDownIcon($i, $n, $condition = true, $task = 'orderdown', $alt = 'Move Down', $enabled = true, $limitstart, $total, $rowid)
	{
		$alt = bfText::_($alt);

		$html = '&nbsp;';
		if ($i < $total  && $i < ($limitstart + $n ) && $condition)	{
			if($enabled) {
				$html  = '<a href="javascript:void(0);" onclick="return orderItem(\''.$rowid.'\',\''.$task.'\')" title="'.$alt.'">';
				$html .= '  <img src="images/downarrow.png" width="12" height="12" border="0" alt="'.$alt.'" />';
				$html .= '</a>';
			} else {
				$html = '<img src="images/downarrow0.png" width="12" height="12" border="0" alt="'.$alt.'" />';
			}
		}

		return $html;
	}

	/**
 	 * filter: Output the filter search text field.
 	 *
 	 */
	function filter() {

		$session =& bfSession::getInstance();
		$filter = $session->get('filter');
		?>
		<span class="bullet-filter" style="float: right;margin-bottom:5px;">
			<?php echo bfText::_('Filter'); ?>:
			<input type="text" name="filter" id="filter" <?php bfHTML::disableEnterInFormFields(1); ?>
			  value="<?php echo $filter;?>" class="text_area hasTip"
			  onchange="javascript:xindex('page=1','filter='+document.adminForm.filter.value);"
			   title="<?php echo bfText::_('Textbox') . '::' . bfText::_('Filter by content, enter the word or phrase to filter by'); ?>"/>

			<button type="button" class="flatbutton hasTip" title="<?php echo bfText::_('Go') . '::' . bfText::_('Click to apply filter'); ?>" onclick="javascript:xindex('filter=' + document.adminForm.filter.value );"><?php echo bfText::_('Go'); ?></button>
			<button type="reset" class="flatbutton hasTip" title="<?php echo bfText::_('Reset') . '::' . bfText::_('Click to reset filter'); ?>"onclick="javascript:xindex('page=1','filter=');"><?php echo bfText::_('Reset'); ?></button>
		</span>
		<div style="clear:both;"></div>
		<?php






















































































































































































































































































































































































































































































































































































































































































































































	}

	/**
	 * I output a checkbox which can be clicked to tick all the boxes
	 *
	 * @param int $number_of_boxes_to_tick
	 */
	function tickAllTH( $number_of_boxes_to_tick ) {
		?>
		<th width="10">
		  <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $number_of_boxes_to_tick;?>);" />
		</th>
		<?php
	}

	/**
	 * I output the table header for each column so it can be clicked to
	 * alter the sort order.
	 *
	 * @param string $key
	 * @param string $display_string
	 */
	function orderByTH( $key, $display_string='' ) {
		global $mainframe;
		$c = $mainframe->get('component');
		$registry =& bfRegistry::getInstance($c,$c);


		if ($display_string=='') {
			$display_string=ucwords($key);
		} else {
			$display_string=ucwords($display_string);
		}

		$session=&bfSession::getInstance();
		$order_by=$session->get('filter_order','');
		$direction=$session->get('filter_order_Dir','DESC');

		if ($registry->getValue('orderingview',0)==1){
			$order_by='ordering';
		}



		/* Set the width of special columns	 */
		$width ='';
		switch ($key){
			case "email":
			case "access":
			case "published":
				$width = ' width="80"';
				break;
			default:
				break;
		}

		/* Set the images to display if we have selected to order by this column */
		$imgPath = bfCompat::getCfg('live_site') . '/'._PLUGIN_DIR_NAME.'/system/blueflame/view/images/';
		switch ($direction){
			case "DESC":
				$image = $imgPath . 'sort_desc.png';
				$img = '<img src="'.$image.'" border="0" align="absmiddle" />';
				$toggle = 'ASC';
				break;
			case "ASC":
				$image = $imgPath . 'sort_asc.png';
				$img = '<img src="'.$image.'" border="0" align="absmiddle" />';
				$toggle = 'DESC';
				break;
			default:
				$img =' ';
				break;
		}

		/* this is our ordering col which we only allow to be sorted one way */
		if ( $key == 'ordering') {

			/* No image if we are not sorting by ordering */
			if ($key === 'ordering' && $key == $order_by) {
				$imgPath = bfCompat::getCfg('live_site') . '/images/M_images/';
				$img = $imgPath . 'sort_asc.png';
				$img = '<img src="'.$img.'" border="0" align="absmiddle" />';
			} else { $img = ''; }
		 	?>
		  <th<?php echo $width; ?>><a href="javascript:xindex('filter_order=ordering','filter_order_Dir=ASC');"
		  title="Order by <?php echo $key;?>"><?php echo bfText::_($display_string);?> <?php echo $img; ?></a></th>
		<?php
		} else
		/* If we are ordering by this column */
		if ( $key == $order_by) {
		?>
		<th<?php echo $width; ?>><a class="hasTip" href="javascript:xindex('filter_order=<?php echo $key;?>','filter_order_Dir=<?php echo $toggle;?>','');"
		  title="<?php echo bfText::_($display_string);?>::Order by <?php echo $key;?> <?php echo $toggle;?>"><?php echo bfText::_($display_string);?></a> <?php echo $img; ?></th>
		<?php

		/* else we are not ordering by this column */
		 } else { ?>
		  <th<?php echo $width; ?>><a class="hasTip" href="javascript:xindex('filter_order=<?php echo $key;?>','filter_order_Dir=ASC');"
		  title="<?php echo bfText::_($display_string);?>::Order by <?php echo $key;?> <?php echo bfText::_('Ascending');?>"><?php echo bfText::_($display_string);?></a></th>
		<?php

		 }
	}

	/**
	 * I output a div with an anchor and img for publishing
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	function publishInformationDiv( $id=null, $field = 'published' ) {

		/* if there is no record dont display item */
		if($id===null) return bfText::_('Save Record First');

		$session =& bfSession::getInstance();

		if ($field == 'published'){
			$xajaxFunction = 'xtoggleunpublish';
		} elseif  ($field == 'enabled'){
			$xajaxFunction = 'xtoggleenable';
		}

		$model = $session->get('lastModel','default');
		$img = bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/view/images/flag_green.gif';
		$extra = ' class="hasTip" title="'.bfText::_('Item Is Published').' :: '.bfText::_('Click here to toggle published state').'"';
		$ret = "<a {$extra} href=\"javascript:void(0);\"	onclick=\"return bfToggleHandler('$xajaxFunction',$id,'','$model');\">
   			<img src=\"".$img."\"  border=\"0\" alt=\"".bfText::_("Published")."\" /></a>";
		return $ret ;
	}




	/**
	 * I output a div with an anchor and img for publishing
	 *
	 * @param int $id
	 * @return string HTML
	 */
	function unPublishInformationDiv( $id=null, $field = 'published' ) {

		/* if there is no record dont display item */
		if($id===null) return bfText::_('Save Record First');


		$session = bfSession::getInstance();

		if ($field == 'published'){
			$xajaxFunction = 'xtogglepublish';
		} elseif  ($field == 'enabled'){
			$xajaxFunction = 'xtoggleenable';
		}

		$model = $this->session->get('lastModel','default');
		$img = bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/view/images/flag_red.gif';
		$extra = ' class="hasTip" title="'.bfText::_('Item Is Unpublished').' :: '.bfText::_('Click here to toggle published state').'"';
		$ret = "<a {$extra} href=\"javascript:void(0);\"
	onclick=\"return bfToggleHandler('$xajaxFunction',$id,'','$model');\">
   <img src=\"".$img."\" width=\"16\" height=\"16\" border=\"0\"
	alt=\"".bfText::_("Unpublished")."\" /></a>";
		return( $ret );
	}

	/**
	 *	I output a td containing a div containing
	 *	the published/unpublished icon with an onclick
	 * 	to the relevant xajax to switch the published state.
	 *
	 *  Note the commented out code for overlib which is incompatible with
	 *  xajax image clicks.
	 *
	 * @param unknown_type $id
	 * @param unknown_type $published
	 * @param unknown_type $start
	 * @param unknown_type $end
	 */
	function publishInformationTD($id,$published,$start="2006-01-01 00:00:00", $end="No Expiry", $field='published') {
		?>

		<td align="center" style="width: 60px;">

		<div id="pub<?php echo $id;?>">
		<?php
		/* @TODO@ need to add expired informationdiv for published bu expired item */
		if ($published == 1) {
			echo bfHTML::publishInformationDiv($id, $field);
		} else {
			echo bfHTML::unpublishInformationDiv($id, $field);
		}
		?>
		</div>
		</td>

		<?php
	}

	function sssenabledInformationTD($id,$published,$start="2006-01-01 00:00:00", $end="No Expiry", $field='published') {
		?>

		<td align="center" style="width: 60px;">

		<div id="pub<?php echo $id;?>">
		<?php
		/* @TODO@ need to add expired informationdiv for published bu expired item */
		if ($published == 1) {
			echo bfHTML::enabledInformationDiv($id, $field);
		} else {
			echo bfHTML::enabledInformationDiv($id, $field);
		}
		?>
		</div>
		</td>

		<?php
	}

	/**
	 * I return 160 characters of the input string.
	 * If the string is truncated I add "..." to the end.
	 *
	 * @param string $string
	 * @param int $length
	 * @return string
	 */
	function allOrPart($string, $length=100) {
		$orig = $string;
		if ( strlen( $string ) < $length ) return $string;
		$string = substr($string,0,$length);
		$string = ereg_replace("[^ ]*$","...",$string);
		return '<span class="hasTip" title="Full Text :: '.$orig.'">'.trim(bfText::_($string)).'</span>';
	}

	/**
	 *	$text is the text for the button.
	 *	$leftorright refers to whether the text is on the left or
	 *	right of the button icon NOT whether the button is to the left or right
	 *	$highlight = 1 if the button is live 0 otherwise (e.g. First button
	 *	on the first page).
	 *	$page the page to go to
	 *
	 * @param unknown_type $text
	 * @param unknown_type $leftorright
	 * @param unknown_type $highlight
	 * @param unknown_type $page
	 */
	function paginationButton($text,$leftorright,$highlight,$page) {
		if ($highlight == 0) {
		?>
	      <span class="bfpagenav hasTip" title="<?php echo ucfirst(bfText::_($text)) .'::' . bfText::_('Go to ') .  ucfirst(bfText::_($text)); ?>"><?php echo ucfirst(bfText::_($text));?></span>
		<?php
		} else {
		?>
	      <a class="bfpagenav hasTip" title="<?php echo ucfirst(bfText::_($text)) .'::' . bfText::_('Go to ') .  ucfirst(bfText::_($text)); ?>"" onclick="javascript: xindex('page=<?php echo $page;?>');"><?php echo ucfirst(bfText::_($text));?></a>
		  <?php
		}
	}

	/**
	 * Add pagination
	 *
	 * @param int $page
	 * @param int $pagelength
	 * @param int $lastitem
	 */
	function pagination($page, $pagelength, $lastitem ) {

		/**
		 * Variables refer either to page numbers or to item numbers.
		 * I'm assuming for now that pagination refers to a list of
		 * all of the rows matching this SQL query without any limits.
		 *
		 * Parameters
		 *	$page		The number of this page
		 *	$pagelength	The number of items to display per page
		 *	$lastitem	The number of the very last item on the page
		 *
		 * Variables
		 *	$page	 	Equal to $firstonpage - the number of the page
		 *	$lastpagestart	The number of the first item on the last page
		 *	$lastpage	The page number of the last page
		 *	$thispage	The page number of the this page
		 */
		$firstonpage=((($page - 1)*$pagelength) + 1);
		$lastonpage=min($page*$pagelength,$lastitem);

		$lastpagestart = ((ceil((float)$lastitem / (float)$pagelength) - 1) * $pagelength) + 1;
		$lastpage=ceil((float)$lastitem/(float)$pagelength);
		$thispage=ceil((float)$firstonpage/(float)$pagelength);

		$havestart=0;
		$haveprev=0;
		$havenext=1;
		$haveend=1;


		if ($firstonpage > 1) {
			$havestart=1;
			$haveprev=1;
		}
		if ($firstonpage == $lastpagestart) {
			$haveend=0;
			$havenext=0;
		}
		/**
		 * Useful debug info HERE
		 */
		if (false) {
			$log=&bfLog::getInstance();
			$log->log("firstonpage = $firstonpage");
			$log->log("lastonpage = $lastonpage");
			$log->log("pagelength = $pagelength");
			$log->log("lastitem = $lastitem");
			$log->log("lastpagestart = $lastpagestart");
			$log->log("lastpage = $lastpage");
			$log->log("thispage = $thispage");
			$log->log("havestart = $havestart ");
			$log->log("haveprev = $haveprev ");
			$log->log("havenext = $havenext ");
			$log->log("haveend = $haveend ");
		}

		?>
		<br />
		<table class="bfadminlist"><tbody><tr><th colspan="3" class="pagination">

<?php
bfHTML::paginationButton("<< Start","right",$havestart,1);
bfHTML::paginationButton("< Prev","right",$haveprev,$thispage - 1);
$startpage=1;
$endpage=$lastpage;
$delta=2; // display -2 this_page +2
// If we're not in the endzone this is easy the start and
// end pages are just this page +/- the delta
if ($thispage - $delta > $startpage) $startpage=$thispage - $delta;
if ($thispage + $delta < $endpage) $endpage=$thispage + $delta;
// If we are in the endzone:
// If the startpage is in the startzone allow the end page to grow
if ($startpage < 2*$delta + 1) $endpage=min($lastpage, $startpage + (2*$delta));
// If the endpage is in the endzone allow the startpage to grow
if ($endpage > $lastpage - (2*$delta + 1)) $startpage=min($startpage, $endpage - (2*$delta));
$startpage=max($startpage,1);
$endpage=min($endpage,$lastpage);
		?>

		<!-- The page count section -->
		    <?php for ($p = $startpage; $p<= $endpage; $p++) { ?>
		      <?php if ($p == $thispage) { ?>
		        <span class="bfpagenav"><?php echo $p;?></span>
		      <?php } else { ?>
		        <a class="bfpagenav" title="<?php echo $p ?>"
			onclick="javascript: xindex('page=<?php echo $p; ?>');"><?php echo bfText::_($p); ?></a>
		      <?php } ?>
		    <?php } ?>

		<?php
		bfHTML::paginationButton("Next >","left",$havenext,$thispage + 1);
		bfHTML::paginationButton("End >> ","left",$haveend, $lastpage);
		?>

		<div class="limit">
		  Results <?php echo "$firstonpage - $lastonpage of $lastitem";?>
		</div>

		<div class="limit"><?php echo bfText::_('Display'); ?> #
		<select name="limit" id="limit" class="inputbox" size="1"
		  onchange="javascript:
		  xindex('page=1', 'limit='+this.options[this.selectedIndex].value);
		">
		  <?php
		  foreach( array(5,10,15,20,25,30,50,100) as $psize ) {
		  	if ($psize != $pagelength) {
		  		echo "<option value=\"$psize\">$psize</option>";
		  	} else {
		  		echo "<option value=\"$psize\" selected=\"selected\">$psize</option>";
		  	}
		  } ?>
		</select>
		</div>

</th></tr></table>
		<?php
	}

	/**
	 *	Output a set of radio buttons
	 *	Parameters:
	 *
	 *	$field	The column in the database table that this pertains to
	 *	$text_values
	 *		An array of text values for each radio button
	 *	$selected
	 *		The number or name of the selected button
	 *	$verticalorhorizontal
	 *		Set to "vertical" or "horizontal" depending how
	 *		you want this formatted.
	 *
	 *
	 * @param unknown_type $field
	 * @param unknown_type $text_values
	 * @param unknown_type $selected
	 * @param unknown_type $verticalorhorizontal
	 */
	function radio_set(	$field,	$text_values, $selected,	$verticalorhorizontal = "vertical")	{
		$i=0;
		$cr="<br>\n";
		if ($verticalorhorizontal == "horizontal") $cr="\n";
		foreach( $text_values as $text ) {
			$checked="";
			if (($i == $selected) || ($text == $selected)) {
				$checked="checked=\"checked\"";
			}
			echo "<input type=\"radio\" name=\"$field\" id=\"$field$i\" value=\"$i\" $checked> <label for=\"$field$i\">$text</label>$cr";
			$i++;
		}
	}

	function saveorderButton( $rows, $image='filesave.gif' ) {
		global $mainframe;
		$registry =& bfRegistry::getInstance($mainframe->get('component'));
		if ($registry->getValue('orderingview',0)==0){
			?>
			<th width="10"><a onclick="saveorder();" title="<?php echo bfText::_( 'Save Order' ); ?>">
			<img src="images/filesave.png" /></a></th><?php
		} else {
			echo '<th width="10"></th>';
		}
	}

	/**
	 * I draw a hidden id field
	 *
	 * @param string $arr
	 */
	function addHiddenIdField($arr, $key='id'){
		echo '<input type="hidden" name="'.$key.'" id="'.$key.'" value="'.$arr[$key].'" />'."\n";
	}

	/**
	 * I draw a hidden  field
	 *
	 * @param string $arr
	 */
	function addHidden($field, $value=''){
		echo '<input type="hidden" name="'.$field.'" id="'.$field.'" value="'.$value.'" />'."\n";
	}

	/**
	 * I return a <form> tag with all attributes
	 *
	 * @param string $accept
	 * @param string $action
	 * @param string $class
	 * @param string $dir
	 * @param string $enctype
	 * @param string $id
	 * @param string $lang
	 * @param string $method
	 * @param string $target
	 * @param string $title
	 * @return string The form tag
	 */
	function Tag_form($accept="", $action="", $class="", $enctype="", $id="", $method="", $target="_self", $title=""){
		echo '<form accept-charset="'.$accept.'" action="'.$action.'" class="'.$class.'" enctype="'.$enctype.'" id="form_'.$id.'" method="'.strtolower($method).'" target="'.$target.'">'."\n";
	}

	function Tag_hidden_Option(){
		echo '<input type="hidden" name="option" id="option" value="'.bfCompat::findOption().'" />'."\n";
	}

	function Tag_hidden_Itemid(){
		echo '<input type="hidden" name="Itemid" id="Itemid" value="'.bfCompat::findItemid().'" />'."\n";
	}

	function Tag_hidden_validationerrorcount(){
		echo '<input type="text" name="validationerrorcount" id="validationerrorcount" value="0" />'."\n";
	}

	function Tag_hidden_Process(){
		echo '<input type="hidden" name="bf_process" id="bf_process" value="1" />'."\n";
	}

	function Button_reset($text='Reset'){
		return '<input type="reset" name="reset_button" id="reset_button" value="'.bfText::_($text).'" class="button" />'."\n";
	}

	function Button_submit($text='Submit', $disableOnClick=true){
		$js = $disableOnClick ? ' onclick="this.disabled=true"' : '';
		return '<input type="submit" name="submit_button" id="submit_button" value="'.bfText::_($text).'" class="button"'.$js.' />'."\n";
	}

	function Branding_footer($xhtml=false, $customTXT='', $return=false){
		ob_start();
		echo '<div id="bf_branding" style="align: center; width: 100%; text-align:center; padding-top: 20px; border-top: 1px dashed #ccc;margin-top: 100px;">';
		if ($xhtml===true) {

			if ($customTXT === ''){
				# TRANSLATE?
				echo '<br /><small>This form is <a href="http://validator.w3.org/check?uri=referer">xHTML Compliant</a> and was created with <a href="http://www.blueflameit.ltd.uk/in.php?site='.bfCompat::getLiveSite().'">Joomla Forms</a></small>';
			} else {
				echo '<br /><small>'.$customTXT.'</small>';
			}
		} else {
			if ($customTXT === ''){
				# TRANSLATE?
				echo '<small>This form was created with <a href="http://www.blueflameit.ltd.uk/in.php?site='.bfURI::base().'">Joomla Forms</a></small>';
			} else {
				echo '<br /><small>'.$customTXT.'</small>';
			}
		}
		echo '</div>';
		$html = ob_get_contents();
		ob_clean();
		if ($return===false){
			echo $html;
		} else {
			return $html;
		}
	}

	function populateTemplate($html, $valuesArray){
	}

	function keepAlive(){
		$html = "<script>setTimeout('bfHandler(\'xkeepalive\');', 60000);</script>";
		echo $html;
	}

	function mooToolTip($title='no title', $tip='No Tip available', $justAttributes =false){
		$title = bfText::_($title);
		$tip = bfText::_($tip);
		if ($justAttributes===false){
			return '<span class="hasTip" title="'.$title.'::'.$tip.'">'.$title.'</span>';
		} else {
			return ' class="hasTip" title="'.$title.'::'.$tip.'" ';
		}
	}

	function displayAdminHeader($controller){
		echo '<div id="toolbar-box">
   				<div class="t">
					<div class="t">
						<div class="t"></div>
					</div>
				</div>
				<div class="m" style="text-align: left;">
					<div id="bftoolbar" class="bftoolbar">
					</div>
					<div id="bfheaderimagediv" class="header icon-48-bflogo reflect">
						<div id="bfHeader">'. $controller->getPageHeader() .'</div>
					</div>
					<div class="clr"></div>
				</div>
				<div class="b">
					<div class="b">
						<div class="b"></div>
					</div>
				</div>
			</div>';
	}
	function drawHitsLinks($model, $id=null, $hits, $tds=false){

		/* if there is no record dont display item */
		if($id===null) return bfText::_('Save Record First');

		if ($hits=='0'){
			$str = '<span id="hits'.$id.'">'.$hits.'</span><input type="hidden" id="hits" name="hits" value="'.$hits.'" />';
		} else {
			$str = '<span id="hits'.$id.'">'.$hits.' <small><a href="javascript:void(0);" class="hasTip" title="'.bfText::_('Reset Hits').' :: '.bfText::_('Click here to reset the hits on this item').'" onclick="resetHits(\''.$model.'\',\''.$id.'\')">'.bfText::_('reset').'</a></small></span><input type="hidden" id="hits" name="hits" value="'.$hits.'" />';
		}
		if ($tds===true){
			return '<td>'.$str.'</td>';
		} else {
			return $str;
		}
	}



	function CategorySelector($row=null,$modelObject,$size=1,$multiple=null,$a=null,$publishedOnly=false){

		$database =& bfCompat::getDBO();
		$user =& bfUser::getInstance();

		$rows = $modelObject->getAll();

		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach ($rows as $v ) {
			$pt = $v->parentid;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}

		$list = bfHTML::_pxtTreeRecurse( 0, '&nbsp;&nbsp;&nbsp;', array(), $children, 9999 );

		$options[] = bfHTML::makeOption('All', '0');
		foreach ($list as $item){
			$options[] = bfHTML::makeOption($item->treename, $item->id);
		}

		if ($row['cid']){
			$lookup = bfHTML::_pxtConvertCIDStoArray($row['cid']); //bfHTML::_pxtTreeRecurse($row->cid, '&nbsp;&nbsp;&nbsp;', array(), $children, 9999);
		}else {
			$lookup[] = bfHTML::makeOption("ROOT","0");
		}

		// = mosHTML::multipleselectList($options,'cid','','text','value',$cids);
		$m = $multiple ? $multiple="multiple=\"multiple\"" : '';
		$a = $multiple ? '[]' : '';
		$l = bfHTML::selectList( $options, 'cid'.$a, ' id="cid'.$a.'" class="kb-inputbox" size="'.$size.'" '.$m, 'text', 'value', $lookup );
		$l = bfHTML::selectList2($options,
		'cid'.$a
		, ' id="cid'.$a.'" class="kb-inputbox" size="'.$size.'" '.$m
		, 'text', 'value', $lookup );
		return $l;
	}


	function makeOption( $value, $text='', $value_name='value', $text_name='text' ) {
		$obj = new stdClass;
		$obj->$value_name = $value;
		$obj->$text_name = trim( $text ) ? $text : $value;
		return $obj;
	}

	function _pxtConvertCIDStoArray($cids) {
		if ($cids=="||") return array();
		$parts = explode ("|", $cids);
		if (count($parts)){
			foreach ($parts as $part){
				if ($part != "") {
					$str[] = bfHTML::makeOption("cid[]","$part");
				}
			}
			return $str;
		} else {
			return array();
		}

	}

	function _pxtTreeRecurse($id, $indent='', $list, &$children, $maxlevel=9999, $level=0) {
		if (@$children[$id] && $level <= $maxlevel) {
			foreach ($children[$id] as $v) {
				$id = $v->id;
				$txt = $v->title;
				$pt = $v->parentid;
				$list[$id] = $v;
				$list[$id]->treename = "$indent$txt";
				$list[$id]->children = count( @$children[$id] );
				$list = bfHTML::_pxtTreeRecurse( $id, "$indent$txt / ", $list, $children, $maxlevel, $level+1 );
			}
		}
		return $list;
	}

	function PluginInstallertoggle($mambotOrModule, $name){
		if ($mambotOrModule=='mambot'){
			$func='isMambotInstalled';
		} else {
			$func='isModuleInstalled';
		}

		$buttons = new bfButtons('left',false);

		if (bfUtils::$func($name)===true){
			/* currently installed */
			$buttons->addButton('cancel', 	'\'xuninstall'.$mambotOrModule.'\', \''.$name.'\'', 'Uninstall');
			$toggle = $buttons->getHTML();
			return $toggle; //.'<a href="javascript:void(0);" onclick="bfHandler(\'xuninstall'.$mambotOrModule.'\', \''.$name.'\');">'.bfText::_('Click to uninstall').'</a>';
		} else {
			/* currently uninstalled */
			$buttons->addButton('ok', 	'\'xinstall'.$mambotOrModule.'\', \''.$name.'\'', 'Install');
			$toggle = $buttons->getHTML();
			return $toggle; //.'<a href="javascript:void(0);" onclick="bfHandler(\'xinstall'.$mambotOrModule.'\', \''.$name.'\');">'.bfText::_('Click to install').'</a>';
		}
	}

	/**
	 * I display the help at the top of views...
	 *
	 * @param string $translatedString A string already translated
	 * @param bool $return Do you want to echo or return
	 * @return string
	 */
	function div_helpHeader($translatedString, $return=false){
		$str = '<div id="help-header"><span>&nbsp;%s</span><div style="clear:both;">&nbsp;</div></div><div style="clear:both;">&nbsp;</div>';
		if ($return===false) {
			echo sprintf($str, $translatedString);
		} else {
			return sprintf($str, $translatedString);
		}
	}

	/*
	* draw a required star
	*/
	function _img_requiredStar(){
		bfLoad('bfImg');
		return '<img class="offset inline hasTip" src="'.bfImg::url_bullet_star().'" border="0"
		alt="'.bfText::_('This field is required').'" title="'.bfText::_('Required Field') . '::'.bfText::_('This Field Is Required').'" align="absmiddle" />';
	}

	function showSecureFormIcon($form){
		bfLoad('bfImg');
		/* If form is secure - let the world know */
		if ($form['onlyssl']=='1' && $_SERVER['SERVER_PORT'] == '443'){ //443
		?>
			<br style="clear:both;" />
			<div style="padding-bottom: 30px;">
				<center><span class="bullet-secure"><?php echo bfText::_('This Form Is Secure. (Secured by SSL Certificate)'); ?></span></center>
			</div>
		<?php
		} else {
		?>
			<br style="clear:both;" />
			<div style="padding-bottom: 30px;">
				<center><span class="bullet-unsecure"><?php echo bfText::_('This Form Is Not Secure. (Not Secured by SSL Certificate)'); ?></span></center>
			</div>
		<?php
		}
	}

	/**
	 * Mouse over javascript for highlighing rows.
	 *
	 * @return string
	 */
	function hoverjs(){
		return 'onmouseover="jQuery(this).addClass(\'hover_highlight\');" onmouseout="jQuery(this).removeClass(\'hover_highlight\');"';
	}
}
?>
