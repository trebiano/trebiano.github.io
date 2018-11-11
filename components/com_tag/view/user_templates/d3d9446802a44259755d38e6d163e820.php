{*

*}
<div id="innertab">
{if $content_id > 0 }
	<div class="headerTab blue">
		<div><h2><span>{bfText}Current Tags{/bfText}</span></h2></div>
		{bfText}Click a tag to remove it{/bfText} <a href="javascript:void(0);" onclick="jQuery('ul#currenttags > li').hide('slow');removalAllTags(jQuery('input[@name=id]').val(), '{$COMPONENT}');">{bfText}or clear all{/bfText}</a>...
		<br />
	</div>
	<ul id="currenttags" class="nooffset">
	{section name=i loop=$tagitems}
		{strip}
		<li class="bullet-tag"><a class="tagslink" rel="tag" onclick="jQuery(this).hide('slow');
		removeTagFromContent(jQuery('input[@name=id]').val(), {$tagitems[i].id}, '{$COMPONENT}');" href="javascript:void(0);">{$tagitems[i].tagname}</a></li>
		{/strip}
	{/section}
	</ul>

	<div style="color:#000;background-color:#AEC4E2;height:1px;font-size:1px;margin-top: 10px;"></div>
	<div class="headerTab green">
		<div><h2><span>{bfText}Search{/bfText} &amp; {bfText}Add Tags{/bfText}</span></h2></div>
	{if $tagcount < 100 }
	<a href="javascript:void(0);" onclick="searchtagsfromtab('ALL', '{$COMPONENT}');"> {bfText}View all tags{/bfText}</a>{/if}...
		<br />
	</div>

	<input type="text" name="q" id="q" style="border: 1px solid #70B794; width: 100%;margin-bottom:20px;"  autocomplete="off" onkeyup="handleEnter(this, event);searchtagsfromtab(this.value, '{$COMPONENT}');" />
	<span id="taglist"></span>
	<div style="color:#000;background-color:#70B794;height:1px;font-size:1px;margin-top: 10px;"></div>

	

{else}

<div class="headerTab">
		<div><h2><span>{bfText}Joomla Tags{/bfText}</span></h2></div>
		<br />
	</div>

<b>{bfText}You must save this item before adding tags{/bfText}</b>!
<br /> ({bfText}Because we assign tags to the items id, which is only created on the first save{/bfText})
<br /><br /><br /><br /><br />
{/if}
</div>