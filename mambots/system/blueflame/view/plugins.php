<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: plugins.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */
$controller->setPageTitle(bfText::_('Addons'));
$controller->setPageHeader(bfText::_('Addons'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('help','help', bfText::_('Click here to get help'));
$toolbar->render(true);

/* read the addons out of the framework config file */
$mambots = $registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.addons.plugins');
$modules = $registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.addons.modules');

bfLoad('bfButtons');

$buttons = new bfButtons();


//$buttons->display();
?>
<table class="bfadminlist">
	<thead>
		<tr>
			<th><?php echo bfText::_('Addon Title'); ?></th>
		    <th width="80"><?php echo bfText::_('Name'); ?></th>
		    <th width="80"><?php echo bfText::_('Type'); ?></th>
		    <th width="80"><?php echo bfText::_('Status'); ?></th>
			<th width="120"><?php echo bfText::_('Toggle'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	$row = 0;
	if (count($mambots)){
		foreach ($mambots as $mambot=>$desc){

			/* Get the mambot type */
			$parts = explode('.',$mambot);
			$type = $parts[0];

			/* Is it already installed ? */
			$instaled = bfUtils::isMambotInstalled($mambot);
			$status = bfText::_($instaled ?  'Installed' : 'Uninstalled');
			$class = $instaled ?  'green' : 'red';


			/* Generate the correct xajax task and button */
			$toggle = bfHTML::PluginInstallertoggle('mambot', $mambot);



			/* display the row */
			echo sprintf('<tr class="row%s"><td><span class="bullet-plugin">%s</span></td><td>%s</td><td>%s</td><td id="status-%s" class="%s">%s</td><td id="toggle-%s">%s</td></tr>',
			$row,
			bfText::_($desc),
			$mambot,
			bfText::_('mambot'),
			str_replace('.','_',$mambot),
			$class,
			$status,
			str_replace('.','_',$mambot),
			$toggle
			);

			/* row zebra colors */
			$row = 1 - $row;
		}
	}
	if (count($modules)){
		foreach ($modules as $module=>$desc){

			/* Get the mambot type */
			$parts = explode('.',$module);
			$type = $parts[0];

			/* Is it already installed ? */
			$instaled = bfUtils::isModuleInstalled($module);
			$status = bfText::_($instaled ?  'Installed' : 'Uninstalled');
			$class = $instaled ?  'green' : 'red';

			/* Generate the correct xajax task and button */
			$toggle = bfHTML::PluginInstallertoggle('module', $module);

			/* display the row */
			echo sprintf('<tr class="row%s"><td><span class="bullet-plugin">%s</span></td><td>%s</td><td>%s</td><td id="status-%s" class="%s">%s</td><td id="toggle-%s">%s</td></tr>',
			$row,
			bfText::_($desc),
			$module,
			bfText::_('module'),
			str_replace('.','_',$module),
			$class,
			$status,
			str_replace('.','_',$module),
			$toggle
			);

			/* row zebra colors */
			$row = 1 - $row;
		}
	}

	/**
			 * The fish compatibility
			 */
	$fish_enabled = false;
	$filename = _BF_JPATH_BASE . DS . bfCompat::mambotsfoldername() . DS . 'system' . DS . 'jfdatabase.systembot.php';
	if (file_exists($filename)){
		$fish_enabled = true;
	}
	
	if ($registry->getValue('joomfish_compatible', false) === true && $fish_enabled ===true){
		/* Get the mambot type */
		$type = 'fish';

		/* Is it already installed ? */
		$instaled = bfUtils::isModuleInstalled($module);



		$status = bfText::_($instaled ?  'Installed' : 'Uninstalled');
		$class = $instaled ?  'green' : 'red';

		/* Generate the correct xajax task and button */
		//		$toggle = bfHTML::PluginInstallertoggle('module', $module);

		$title = '<img src="components/com_joomfish/images/fish.png" align="left" />'.bfText::_('Joom!Fish v1.7 Content Elements') . '<sup>BETA</sup>';
		$desc = bfText::_('To enable Joom!Fish integration copy the files in <br />') 	
		. '/components/'. $mainframe->get('component') .'/addons/fish/' . '<br />' . bfText::_('to the Joom!Fish content elements folder.');
		/* display the row */
		echo sprintf('<tr class="row%s"><td><span class="bullet-plugin">%s</span><br/ >%s</td><td>%s</td><td>%s</td><td id="status-%s" class="%s">%s</td><td id="toggle-%s">%s</td></tr>',
		$row,
		$title,
		$desc,
		'XML File',
		bfText::_('XML File'),
		str_replace('.','_',$module),
		'',
		bfText::_('Manual Install Only'),
		str_replace('.','_',$module),
		''//$toggle
		)
		;

		/* row zebra colors */
		$row = 1 - $row;
	}
?>
	</tbody>
</table>