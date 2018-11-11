<bfSQL>
	<task>
		<title>Rename main tags table</title>
		<sql>RENAME TABLE `#__tags_details` TO  `#__tag_tags`;</sql>
	</task>
	<task>
		<title>Change Tags Table Primary id</title>
		<sql>ALTER TABLE `#__tag_tags` CHANGE `tagid` `id` INT( 11 ) NOT NULL AUTO_INCREMENT;</sql>
	</task>
	<task>
		<title>Remove unused field phpfile from #__tag_tags</title>
		<sql>ALTER TABLE `#__tag_tags` DROP `phpfile`;</sql>
	</task>
	<task>
		<title>Add published field</title>
		<sql>ALTER TABLE `#__tag_tags` ADD `published` INT( 11 ) NOT NULL ;</sql>
	</task>
	<task>
		<title>Publish All Tags (You might want to review this, we needed to do this to ensure successful migration!)</title>
		<sql>UPDATE `#__tag_tags` SET published = '1';</sql>
	</task>
	<task>
		<title>Add checkout support</title>
		<sql>ALTER TABLE `#__tag_tags` ADD `checked_out_time` DATETIME NOT NULL , ADD `checked_out` INT( 11 ) NOT NULL ;</sql>
	</task>
	<task>
		<title>Add tag description support</title>
		<sql>ALTER TABLE `#__tag_tags` ADD `desc` MEDIUMTEXT NOT NULL ;</sql>
	</task>
	<task>
		<title>Add meta data support</title>
		<sql>ALTER TABLE `#__tag_tags` ADD `meta_title` VARCHAR( 255 ) NOT NULL , ADD `meta_desc` MEDIUMTEXT NOT NULL , ADD `meta_keywords` MEDIUMTEXT NOT NULL ;</sql>
	</task>
	<task>
		<title>Rename mapping table</title>
		<sql>RENAME TABLE `#__tags_content_map`  TO `#__tag_category_map` ;</sql>
	</task>
	<task>
		<title>Set mapping table id</title>
		<sql>ALTER TABLE `#__tag_category_map` CHANGE `autonum` `id` INT( 11 ) NOT NULL AUTO_INCREMENT;</sql>
	</task>
	<task>
		<title>Enhance Tagging for future 3rd party integration</title>
		<sql>ALTER TABLE `#__tag_category_map` ADD `scope` VARCHAR( 255 ) NOT NULL ;</sql>
	</task>
	<task>
		<title>UTF8 Fixes 1</title>
		<sql>ALTER TABLE `#__tag_tags` CHANGE `tagname` `tagname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;</sql>
	</task>
	<task>
		<title>UTF8 Fixes 2</title>
		<sql>ALTER TABLE `#__tag_tags` CHANGE `output` `output` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;</sql>
	</task>
	<task>
		<title>UTF8 Fixes 3</title>
		<sql>ALTER TABLE `#__tag_tags` CHANGE `template` `template` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;</sql>
	</task>
	<task>
		<title>Remove unused state field</title>
		<sql>ALTER TABLE `#__tag_tags` DROP `state`;</sql>
	</task>
	<task>
		<title>Remove Blank Tag Maps 1</title>
		<sql>delete FROM `#__tag_category_map` WHERE tagid=0;</sql>
	</task>
	<task>
		<title>Remove Blank Tag Maps 2</title>
		<sql>delete FROM `#__tag_category_map` WHERE contentid=0;</sql>
	</task>
	<task>
		<title>UTF8 Fixes 4</title>
		<sql>ALTER TABLE `#__tag_tags` CHANGE `sef` `sef` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;</sql>
	</task>
	<task>
		<title>Add more layout options</title>
		<sql>ALTER TABLE `#__tag_tags` ADD `layout_dir` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL , ADD `layout_orderby` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;</sql>
	</task>
</bfSQL>