<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: sameorignpolicy.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */
?>
<style>
.warning{
text-align: left;
width: 500px;
background-color: #fff;

}
</style>

<div class="warning">
<h1>Hi, We need you to read this...</h1>

<p>At the moment you are accessing this webpage using:<br />
<br />
http://###HOST###

</p>

<p>
Beacause of a browser security policy (called <a href="http://en.wikipedia.org/wiki/Same_origin_policy" target="_blank">Same Origin Policy</a>) dating back to Netscape days, the complex
 AJAX and Javascript contained in this component will not work unless you access this website using the <b>exact same</b>
 domain name as you have configured in configuration.php. Your joomla configuration.php contains your live site as:
<br /><br />
<a href="###LIVESITE###/administrator/">###LIVESITE###</a>

 <br /><br />
 The most common problem is that you have configured www.domain.com and you are accessing now with domain.com.
 These two are considered by your browser to be totally different sites and thus the security stops you running javascript between them.
</p>

<h2>The solution - easy!</h2>
<p>
In order to proceed just retype the correct web address (Probably: <a href="###LIVESITE###/administrator/">###LIVESITE###/administrator/</a>) into your web browser

</p>
</div>
