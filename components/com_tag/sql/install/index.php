<?php
/**
 * @version $Id: index.php 1168 2007-07-24 14:17:24Z phil $
 * @package bfFramework
 * @subpackage Joomla Tags
 * @copyright Copyright (C) 2007 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */
header("HTTP/1.0 404 Not Found");
?>
<html>
<title>404 - Page Not Found.</title>
<style type="text/css">div#error{align:center;text-align:center;border:2px double red;width:770px;margin:0 auto;padding:50px;}#bfCopyright{border-top:1px solid #CCC;clear:both;display:block;margin-top:50px;padding-top:20px;text-align:center;color:#333;font-family:Arial,Helvetica,sans-serif;font-size:12px;}</style>
<body>
<div id="error">
	<h1>Page Not Found</h1>
		<p>For More Joomla Components Please Visit Our Site At:
		<br />
		<a rel="index" href="http://www.joomla-components.co.uk">Joomla Extensions</a></p>
</div>

<div id="bfCopyright">
	<b>
		<i>Power In Simplicity!</i>
	</b>
	<br />
	&copy; <?php echo date('Y'); ?> <a target="_blank" href="http://www.phil-taylor.com/" class="hasTip">Blue Flame IT Ltd.</a>
	<br />
</div>
</body>
</html>