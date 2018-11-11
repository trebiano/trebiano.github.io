<bfSQL>
	<tag_tags_version>0.6</tag_tags_version>
	<tag_layouts_version>0.3</tag_layouts_version>
	<tag_category_map_version>0.3</tag_category_map_version>
	
	<tag_category_map>
		<![CDATA[
		CREATE TABLE IF NOT EXISTS `#__tag_category_map` (
		`id` int(11) NOT NULL auto_increment,
		`contentid` int(11) NOT NULL default '0',
		`tagid` int(11) NOT NULL default '0',
		`scope` varchar(255) collate utf8_bin NOT NULL,
		`created`datetime NOT NULL,
  		`created_by` int(11) NOT NULL,
  		`checked_out_time` datetime NOT NULL,
  		`checked_out` int(11) NOT NULL,
		PRIMARY KEY  (`id`),
		KEY `contentid` (`contentid`),
		KEY `tagid` (`tagid`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
		]]>
	</tag_category_map>

	<tag_layouts>
		<![CDATA[
		CREATE TABLE IF NOT EXISTS `#__tag_layouts` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(255) collate utf8_bin NOT NULL,
		  `filename` varchar(255) collate utf8_bin NOT NULL,
		  `appliesto` varchar(255) collate utf8_bin NOT NULL,
		  `framework` int(11) NOT NULL default '0',
		  `desc` mediumtext NOT NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1000;
		]]>
	</tag_layouts>

	<tag_tags>
		<![CDATA[
		CREATE TABLE `#__tag_tags` (
  		`id` int(11) NOT NULL auto_increment,
  		`tagname` varchar(255) collate utf8_bin NOT NULL,
  		`parenttagid` int(11) NOT NULL default '0',
  		`component` varchar(255) collate utf8_bin NOT NULL,
  		`weight` int(11) NOT NULL default '0',
  		`count` int(11) NOT NULL default '0',
  		`countpublished` int(11) NOT NULL default '0',
  		`access` int(11) NOT NULL default '0',
  		`hits` int(11) NOT NULL default '0',
  		`template` varchar(255) collate utf8_bin NOT NULL,
  		`output` varchar(255) collate utf8_bin NOT NULL,
  		`sef` varchar(255) collate utf8_bin NOT NULL,
  		`tagtext` mediumtext collate utf8_bin NOT NULL,
  		`published` int(11) NOT NULL,
  		`checked_out_time` datetime NOT NULL,
  		`checked_out` int(11) NOT NULL,
  		`desc` mediumtext collate utf8_bin NOT NULL,
  		`meta_title` varchar(255) collate utf8_bin NOT NULL,
  		`meta_desc` mediumtext collate utf8_bin NOT NULL,
  		`meta_keywords` mediumtext collate utf8_bin NOT NULL,
  		`layout_dir` varchar(10) collate utf8_bin NOT NULL,
  		`layout_orderby` varchar(255) collate utf8_bin NOT NULL,
  		`created`datetime NOT NULL,
  		`created_by` int(11) NOT NULL,
  		PRIMARY KEY  (`id`),
  		KEY `tagname` (`tagname`),
  		KEY `parenttagid` (`parenttagid`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin
		]]>
	</tag_tags>
</bfSQL>