{*
 * @version $Id: 1679091c5a880faf6fb5e6087eb1b2dc.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.

 This is the default example layout
*}

<div class="headerTab blue">
	<div><h2><span>[ {$TAGNAME} ]</span></h2></div>
	 <br />{bfText}Questa pagina contiene una lista degli <b>articoli di approfondimento su {$TAGNAME} e dintorni</b> che sono stati etichettati col TAG [ <b>{$TAGNAME}</b> ] su <a href="http://www.trebiano.it/blog/" title="Trebiano E-Business Blog">Trebiano E-Business Blog</a>, il blog curato dallo Studio Trebiano ed inerente principalmente i seguenti argomenti: {$TAGNAME}, internet, marketing e comunicazione on-line. Attualmente ci sono (<b>{$COUNT}</b>) articoli aventi il TAG [ {$TAGNAME} ] associato:{/bfText}  
	<!-- <a href="{$TAGCLOUD_LINK}" class="cloud bullet-cloud" rel="index">{bfText}Guarda tutti i Tag{/bfText}</a> -->
<br /><br />
</div>
{if $TAGDESC}
<div id="bf_tagdesc">
{$TAGDESC}
</div>
{/if}
	
<ul class="bfsubmenu" style="margin-left: 20px;">
{if $content_items}
	{section name=i loop=$content_items}
		{strip}
		<li class="bullet-article"><h5><a href="{bfAmpReplace}{$content_items[i].url}{/bfAmpReplace}" title="{bfText}{$content_items[i].title}{/bfText}">{$content_items[i].title}</a></h5></li>
		{/strip}
	{/section}
{else}
	<li />
{/if}
</ul>


<div id="bfpagenav" style="text-align: center; align: center;">
##PAGENAV_LINKS##<br />
##PAGENAV_LIMITBOX##<br />
##PAGENAV_INFO##<br />
</div>


