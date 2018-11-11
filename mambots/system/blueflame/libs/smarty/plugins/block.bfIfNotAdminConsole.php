<?php
function smarty_block_bfIfNotAdminConsole($params, $content, &$smarty, &$repeat){
	if (bfCompat::isAdmin()){
		return '';
	} else {
		if (isset($content)) {
			return $content;
		}
	}
}
?>