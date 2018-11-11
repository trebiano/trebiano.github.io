<?php
function smarty_block_bfText($params, $content, &$smarty, &$repeat){
    if (isset($content)) {
        return bfText::_($content);
    }
}
?>