<?php
function smarty_block_sefRelToAbs($params, $content, &$smarty, &$repeat){
	if (isset($content)) {
		if (function_exists('sefRelToAbs')){
			return sefRelToAbs($content);
		}
	}
}
?>
