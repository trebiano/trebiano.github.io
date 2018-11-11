<?php
function smarty_block_bfAmpReplace($params, $content, &$smarty, &$repeat){
	if (isset($content)) {
        return bfUtils::ampReplace($content);
    }
}
?>