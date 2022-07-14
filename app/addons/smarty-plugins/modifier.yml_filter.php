<?php
/**
 * Smarty modifier plugin
 *
 * Type:     modifier<br>
 * Example: {$var|yml_filter}
 * @version  1.0
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_yml_filter($string) {
    $allowed_tags = array("p", "ul", "h3", "br", "li");
    $allowed = implode(array_map(function ($e){ return '<' . $e . '>'; }, $allowed_tags));
    $string = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $string);
    return strip_tags($string, $allowed);
}
