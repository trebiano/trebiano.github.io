<?php
function smarty_block_bfIfAdminConsole($params, $content, &$smarty, &$repeat){
	if (bfCompat::isAdmin()){
		if (isset($content)) {
			return $content;
		}
	} else {
		return '';
	}
}
?>