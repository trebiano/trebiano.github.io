<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: post_view.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

/* Include our state */
$bfsession =& bfSession::getInstance();
echo $bfsession->get_hidden_field_defaults_html();
?>
</form>
<!-- End of adminForm -->