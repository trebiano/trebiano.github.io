<bfSQL>
	<task>
		<applytoversion>0.1</applytoversion>
		<provides>0.3</provides>
		<tablename>tag_tags</tablename>
		<tasktitle>Test me</tasktitle>
		<sql><![CDATA[SHOW TABLES]]></sql>
	</task>
	<task>
		<applytoversion>0.3</applytoversion>
		<provides>0.4</provides>
		<tablename>tag_tags</tablename>
		<tasktitle>Add multi component column</tasktitle>
		<sql><![CDATA[ALTER TABLE `#__tag_tags` ADD `component` VARCHAR( 255 ) NOT NULL AFTER `parenttagid` ;]]></sql>
	</task>
	<task>
		<applytoversion>0.4</applytoversion>
		<provides>0.5</provides>
		<tablename>tag_tags</tablename>
		<tasktitle>Add multi component column</tasktitle>
		<sql><![CDATA[ALTER TABLE `#__tag_tags` ADD `created` DATETIME  AFTER `parenttagid`;]]></sql>
	</task>
	<task>
		<applytoversion>0.5</applytoversion>
		<provides>0.6</provides>
		<tablename>tag_tags</tablename>
		<tasktitle>Add multi component column</tasktitle>
		<sql><![CDATA[ALTER TABLE `#__tag_tags` ADD `created_by` INT(11) NOT NULL AFTER `parenttagid`;]]></sql>
	</task>
	
	
</bfSQL>