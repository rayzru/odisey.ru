<?php
/**
 * Smarty modifier plugin
 *
 * Type:     modifier<br>
 * Example: {$var|yml_prep}
 * @version  1.0
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_yml_prep($string) {
	return str_replace(
		["\"", "&", "'", "<", ">"],
		["&quot;", "&amp;", "&apos;", "&lt;", "&gt;"],
		$string
	);
}
