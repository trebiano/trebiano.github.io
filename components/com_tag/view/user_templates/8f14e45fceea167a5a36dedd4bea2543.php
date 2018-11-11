{*
 * @version $Id: 8f14e45fceea167a5a36dedd4bea2543.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @subpackage Joomla Tags
 * @copyright Copyright (C) 2007 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.

 Latest Tags
*}
<div class="headerTab red">
	<div><h2><span>{bfText}&raquo Ultimi TAG inseriti{/bfText}</span></h2></div><br />
</div>
<ul class="noindent bfnostyle">
{section name=i loop=$latestitems}
	{strip}
	<li class="tagslink bfnew" style="margin-left: 15px;"><a rel="tag" href="{bfAmpReplace}{$latestitems[i].link}{/bfAmpReplace}">{$latestitems[i].tagname} 
{if $showqty} ({$latestitems[i].hits} {bfText}Hits{/bfText}) {/if}</a></li>
	{/strip}
{/section}
</ul>
<!-- <div style="text-align: center;color:#DC9C92;"><center><small><a class="taglinered" href="{bfAmpReplace}{$TAGCLOUD}{/bfAmpReplace}">{bfText}View All Tags{/bfText}</a></small></center></div> -->


