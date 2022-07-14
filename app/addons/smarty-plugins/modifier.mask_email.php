<?php
/**
 * Smarty mask_email modifier plugin
 *
 * Type:    modifier<br>
 * Name:    mask_email<br>
 * @param    string
 * @param    int
 * @param    int
 * @param    string
 * @return    string
 */

function smarty_modifier_mask_email($string, $first = 2, $last = 1, $char = '*')
{

	$mail_parts = explode("@", $string);
	$domain_parts = explode('.', $mail_parts[1]);
	$mail_parts[0] = mask($mail_parts[0], $first, $last, $char);
	$domain_parts[0] = mask($domain_parts[0], $first, $last, $char);
	$mail_parts[1] = implode('.', $domain_parts);
	return implode("@", $mail_parts);
}

function mask($str, $first = 2, $last = 1, $char = '*')
{
	$len = strlen($str);
	$toShow = $first + $last;
	return substr($str, 0, $len <= $toShow ? 0 : $first) .
		str_repeat($char, $len - ($len <= $toShow ? 0 : $toShow)) .
		substr($str, $len - $last, $len <= $toShow ? 0 : $last);
}
