<bfSQL>
	<tag_tags_version>0.4</tag_tags_version>
	<tag_layouts_version>0.3</tag_layouts_version>
	<tag_category_map_version>0.3</tag_category_map_version>
	
	<tag_category_map>
		<![CDATA[
		CREATE TABLE IF NOT EXISTS `#__tag_category_map` (
		  `id` int(11) NOT NULL auto_increment,
		  `contentid` int(11) NOT NULL default '0',
		  `tagid` int(11) NOT NULL default '0',
		  `scope` varchar(255) NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `contentid` (`contentid`),
		  KEY `tagid` (`tagid`)
		) ENGINE=MyISAM;
		]]>
	</tag_category_map>

	<tag_layouts>
		<![CDATA[
		CREATE TABLE IF NOT EXISTS `#__tag_layouts` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(255) NOT NULL,
		  `filename` varchar(255) NOT NULL,
		  `appliesto` varchar(255) NOT NULL,
		  `framework` int(11) NOT NULL default '0',
		  `desc` mediumtext NOT NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1000;
		]]>
	</tag_layouts>

	<tag_tags>
		<![CDATA[
		CREATE TABLE IF NOT EXISTS  `#__tag_tags` (
		  `id` int(11) NOT NULL auto_increment,
		  `tagname` varchar(255) NOT NULL,
		  `parenttagid` int(11) NOT NULL default '0',
		    `component` varchar(255) NOT NULL,
		  `weight` int(11) NOT NULL default '0',
		  `count` int(11) NOT NULL default '0',
		  `countpublished` int(11) NOT NULL default '0',
		  `access` int(11) NOT NULL default '0',
		  `hits` int(11) NOT NULL default '0',
		  `template` varchar(255) NOT NULL,
		  `output` varchar(255) NOT NULL,
		  `sef` varchar(255) NOT NULL,
		  `tagtext` mediumtext  NOT NULL,
		  `published` int(11) NOT NULL,
		  `checked_out_time` datetime NOT NULL,
		  `checked_out` int(11) NOT NULL,
		  `desc` mediumtext  NOT NULL,
		  `meta_title` varchar(255)  NOT NULL,
		  `meta_desc` mediumtext  NOT NULL,
		  `meta_keywords` mediumtext  NOT NULL,
		  `layout_dir` varchar(10)  NOT NULL,
		  `layout_orderby` varchar(255) NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `tagname` (`tagname`),
		  KEY `parenttagid` (`parenttagid`)
		) ENGINE=MyISAM ;
		]]>
	</tag_tags>
</bfSQL>