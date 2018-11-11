{*
 * @version $Id: c9f0f895fb98ab9159f51fd0297e236d.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.

 Tag Cloud Page
 Used by Admin and frontend - with slight differences
*}

<div class="headerTab">
	<div><h2><span>{bfText}&raquo TAG Cloud{/bfText}</span></h2></div><br />
</div>

<p class="TagCloud">
{section name=i loop=$items}
	{strip}
	<a {bfIfAdminConsole} target="_blank" {/bfIfAdminConsole}title="{$items[i].qty} {bfText}items tagged with{/bfText} {$items[i].tagname}" style="font-size: {$items[i].size}%;" href="{bfAmpReplace}{$items[i].link}{/bfAmpReplace}">{$items[i].tagname}{if $showqty} ({$items[i].qty}){/if}</a>
	{/strip}
{/section}
</p>

<!-- <div style="color:#000;background-color:#AEC4E2;height:1px;font-size:1px;margin-top: 10px;"></div> -->

<div class="headerTab green">
	<div><h2><span>{bfText}&raquo TAG più cliccati{/bfText}</span></h2></div><br />
</div>

<p class="TagCloud">
{section name=i loop=$itemsbyhits}
	{strip}
	<a {bfIfAdminConsole} target="_blank" {/bfIfAdminConsole}title="{bfText}Tag visited{/bfText} {$itemsbyhits[i].qty} {bfText}times{/bfText} {$itemsbyhits[i].tagname}" style="font-size: {$itemsbyhits[i].size}%;" href="{bfAmpReplace}{$itemsbyhits[i].link}{/bfAmpReplace}">{$itemsbyhits[i].tagname}{if $showqty} ({$itemsbyhits[i].qty}){/if}</a>
	{/strip}
{/section}
</p>

<!-- <div style="color:#000;background-color:#70B794;height:1px;font-size:1px;margin-top: 10px;"></div> -->


<div class="col-left">
{*
 Show Latest Tags
*}

{include file="8f14e45fceea167a5a36dedd4bea2543.php"}
</div>

<div class="col-right">
{*
 Show Popular Tags
*}
{include file="45c48cce2e2d7fbdea1afc51c7c6ad26.php"}
</div>
<div class="clearer">&nbsp;</div>

{bfIfNotAdminConsole}
	<!--<form method="get" action="index.php">
	<p class="jumpbox">
	<b>Cerca TAG :</b>
	<input type="text" class="inputbox bfinputbox" style="width: 200px;" name="tag"/>
	<input type="hidden" name="option" value="com_tag"/>
	<input class="button bfbutton" type="submit" value="GO"/>
	</p>
	</form>-->
{/bfIfNotAdminConsole}


