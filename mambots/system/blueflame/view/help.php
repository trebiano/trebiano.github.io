<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: help.php 946 2007-06-28 16:27:29Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */
error_reporting(E_ALL);
$controller->setPageTitle(bfText::_('Help &amp; Assistance'));
$controller->setPageHeader(bfText::_('Help &amp; Assistance'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('refresh','xhelp', bfText::_('Reload configuration'));
$toolbar->render(true);

?><div style="text-align: left; float:left;">	<?php

$sourcesite = 'http://127.0.0.1';
$sourceurl = $sourcesite . '/live/index2.php?option=com_kb&task=rss&format=RSS2.0&no_html=1&pop=1&type=latestlistingspercategory&category=';

switch ($mainframe->get('component')){
	case 'com_kb':
		$xmlFile = $sourceurl . '23';
		break;
	default:
		$xmlFile = 'NONE';
		break;
}

if ($xmlFile !=='NONE'){

	include_once(bfCompat::getAbsolutePath() . DS . 'administrator' . DS . 'components' . DS. $mainframe->get('component') . DS.'bfXML.php');
	$xml = new bfXml();
	$string = file_get_contents($xmlFile);
	if (ereg('rss version',$string)){
		$xml = new bfXml();
		?><h1><?php echo bfText::_('FAQ For this product'); ?></h1><?php
		$arr = $xml->parse($string,'STRING');

		echo '<Span style="text-align: left;"><ul class="bfsubmenu">';
		if (count($arr['channel']['item'])){
			foreach ($arr['channel']['item'] as $item){
				$link = implode('',$item['link']);
				echo '<li class="bullet-info"><a href="'.$link.'" target="_blank">' . $item['title'] . '</a></li>';
			}
		}else {
			echo '<li class="bullet-info">None Found</li>';
		}

		echo '</ul></span>';
	}
}
?>

	<h1><?php echo bfText::_('Where to get help and assistance'); ?></h1>		

	<ul class="bfsubmenu">
		<li class="submenuicon-comments"><a href="http://forum.phil-taylor.com"><?php echo bfText::_('The Support Forums'); ?></a></li>
		<li class="submenuicon-info"><a href="http://www.phil-taylor.com/kb/"><?php echo bfText::_('The Knowledgebase'); ?></a></li>
		<li class="submenuicon-notes"><a href="http://bugs.phil-taylor.com/kb/"><?php echo bfText::_('Report A Confirmed Bug'); ?></a></li>
		<li class="submenuicon-blueflame"><a href="http://blog.phil-taylor.com"><?php echo bfText::_('Latest Blue Flame News'); ?></a></li>
		<li class="submenuicon-blueflame"><a href="http://www.phil-taylor.com/Contact_Us/"><?php echo bfText::_('Contact Blue Flame IT Support Desk'); ?></a></li>
		<li class="submenuicon-download-refresh"><a href="http://www.phil-taylor.com/cc/"><?php echo bfText::_('Download Latest Version'); ?></a></li>
	</ul>
</div>


<?php
if ($mainframe->get('component')=='com_tag'){
?>
<div style="width: 200px; float:right;">
<p id="skype-publicchat" style="border: 1px solid #009de9 ! important; background: white url('http://download.skype.com/share/publicchat/background.png') repeat-x scroll left bottom ! important; -moz-background-clip: -moz-initial ! important; -moz-background-origin: -moz-initial ! important; -moz-background-inline-policy: -moz-initial ! important; font-family: Arial,Helvetica,sans-serif ! important; font-style: normal ! important; font-variant: normal ! important; font-weight: normal ! important; font-size: 11px ! important; line-height: 16px ! important; font-size-adjust: none ! important; font-stretch: normal ! important">
<h1 style="margin: 0pt ! important; padding: 50px 10px 9px ! important; background: transparent url('http://download.skype.com/share/publicchat/snippet_head_blue.png') no-repeat scroll left top ! important; font-family: Arial,Helvetica,sans-serif ! important; font-style: normal ! important; font-variant: normal ! important; font-size: 12px ! important; line-height: 16px ! important; font-size-adjust: none ! important; font-stretch: normal ! important; font-weight: bold ! important; color: #999999 ! important; -moz-background-clip: -moz-initial ! important; -moz-background-origin: -moz-initial ! important; -moz-background-inline-policy: -moz-initial ! important"><a href="http://www.skype.com/go/joinpublicchat?chat&amp;skypename=prazgod&amp;topic=Joomla+Tags+Public+Preview&amp;blob=unFaXnyBtvLsmjqp2Li_YBq_WOwIEXJCwHIBtc0uvd4V-dBkyy07FAot4lSxuvU" style="color: #006699 ! important; text-decoration: none ! important">Joomla Tags Community Support Chat</a> hosted by <a href="skype:prazgod?info" style="color: #006699 ! important; text-decoration: none ! important">Blue Flame IT Ltd</a>.</h1>
<p style="margin: 0pt 10px 10px ! important"><a href="http://www.skype.com/go/joinpublicchat?chat&amp;skypename=prazgod&amp;topic=Joomla+Tags+Public+Preview&amp;blob=unFaXnyBtvLsmjqp2Li_YBq_WOwIEXJCwHIBtc0uvd4V-dBkyy07FAot4lSxuvU" style="background: transparent url('http://download.skype.com/share/publicchat/chat_icon.png') no-repeat scroll left center ! important; color: #006699 ! important; -moz-background-clip: -moz-initial ! important; -moz-background-origin: -moz-initial ! important; -moz-background-inline-policy: -moz-initial ! important; padding-left: 20px ! important; display: block ! important">Join now</a></p>
<hr style="border: medium none ; margin: 5px 10px ! important; background: #cccccc none repeat scroll 0% 50% ! important; height: 1px ! important; -moz-background-clip: -moz-initial ! important; -moz-background-origin: -moz-initial ! important; -moz-background-inline-policy: -moz-initial ! important" />
</div>
<?php
}
?>