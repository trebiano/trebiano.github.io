<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: configuration.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */
global $mainframe;

bfLoad('bfImg');

/* @var $config bfConfig */

/* Set the Document HTML's HEAD tag text */
$controller->setPageTitle (bfText::_('Edit Preferences'));

/* Set the Page Header */
$controller->setPageHeader(bfText::_('Edit Preferences'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('refresh','configuration', bfText::_('Reload configuration'));
$toolbar->addButton('apply','applyconfig', bfText::_('Save &amp; Reload configuration'));
$toolbar->addButton('save','saveconfig', bfText::_('Save configuration'));
$toolbar->addButton('cancel','customise', bfText::_('Cancel and loose changes'));
$toolbar->render(true);

/* Disable the enter button globally */
bfHTML::disableEnterInFormFields();
/* @var $bfConfig bfconfig */

$config =& bfConfig::getInstance($mainframe->get('component'));
$registry->setValue('usedTabs',1);
$registry->setValue('hasPopups',1);
?>
 <div id="bfTabs">
            <ul class="anchors">
            <?php
            $tabs = $registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.config_tabs');
            foreach ($tabs as $tab){
            	echo '<li><a href="#page-'.strtolower(str_replace(' ','',$tab)).'">'.ucwords($tab).'</a></li>';
            }
            ?>
            </ul>
			<?php
			if (!$config->isWriteable())
			echo '<span style="color:red; height: 50px; font-size: 16px;">
			<img src="'.bfImg::buildURL('icon_error').'" align="absmiddle" />
            '.bfText::_('Your configuration file is not writeable, the file is').': '
			.$config->_configfileWithPath.'</span>';
			$first = '0';
			foreach ($tabs as $tab){
				if ($first==='0') {
					$style = 'block';
					$class = ' class="active"';
				} else {
					$style = 'none';
					$class ='';
				}
	?>
	<div id="page-<?php echo strtolower(str_replace(' ','',$tab)); ?>"  class="fragment2">
		<table class="bfadminlist">
		<thead>
	    	<tr>
				<th><span class="indent bullet-config">Configuration Item</a></th>
				<th>Current Value</th>
				<th>Configuration Value</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$k=0;
		$items = $registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.config_vars');
		foreach ($items as $configItem){
			if (strtolower($configItem[5])==strtolower($tab)){
				echo '<tr class="row'.$k.'"><td class="blue"><span class="title bold">';
				echo bfHTML::mooToolTip($configItem[1], $configItem[6]);
				echo '</span>';
				echo '</td><td>'. $registry->getValue('config.'.$configItem[0]).'</td><td width="25%" align="center">' .bfHTML::convertArrayToHTML($configItem, $registry->getValue('config.'.$configItem[0])) .'</td></tr>';
				$k = 1 - $k;
			}
		}
		?>
		</tbody>
		</table>
	</div>
<?php $first++; } ?>
</div>