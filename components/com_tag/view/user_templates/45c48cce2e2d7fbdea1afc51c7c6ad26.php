{*
 * @version $Id: 45c48cce2e2d7fbdea1afc51c7c6ad26.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @subpackage Joomla Tags
 * @copyright Copyright (C) 2007 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.

 Most Popular Tags
*}
<div class="headerTab">
	<div><h2><span>{bfText}&raquo TAG più popolari{/bfText}</span></h2></div><br />
</div>
<ul class="noindent bfnostyle">
{section name=i loop=$popularitems}
	{strip}
	<li class="tagslink bfhot"><a style="margin-left: 15px;" rel="tag" href="{bfAmpReplace}{$popularitems[i].link}{/bfAmpReplace}">{$popularitems[i].tagname} {if $showqty} ({$popularitems[i].hits} {bfText}Hits{/bfText}){/if}</a></li>
	{/strip}
{/section}
</ul>
<!-- <div style="text-align: center;color:#AEC4E2;"><center><small><a class="taglineblue" href="{bfAmpReplace}{$TAGCLOUD}{/bfAmpReplace}">{bfText}View All Tags{/bfText}</a></small></center></div> -->



