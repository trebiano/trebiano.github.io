<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: pre_view.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */
global $mainframe;
?>
<form action="index.php?option=<?php echo $mainframe->get('component');?>" method="POST" name="adminForm" id="adminForm" onsubmit="" enctype="multipart/form-data">