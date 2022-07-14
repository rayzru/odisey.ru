<?php
/**
 * Smarty mask modifier plugin
 *
 * Type:     modifier<br>
 * Name:     mask<br>
 * @param string
 * @param int
 * @param int
 * @param string
 * @return string
 */


function smarty_modifier_mask($str, $first = 2, $last = 1, $char = '*') {
	$len = strlen($str);
	$toShow = $first + $last;
	return substr($str, 0, $len <= $toShow ? 0 : $first) .
		str_repeat($char, $len - ($len <= $toShow ? 0 : $toShow)) .
		substr($str, $len - $last, $len <= $toShow ? 0 : $last);
}

function mask_email($email)
{
	$mail_parts = explode("@", $email);
	$domain_parts = explode('.', $mail_parts[1]);

	$mail_parts[0] = mask($mail_parts[0], 2, 1); // show first 2 letters and last 1 letter
	$domain_parts[0] = mask($domain_parts[0], 2, 1); // same here
	$mail_parts[1] = implode('.', $domain_parts);

	return implode("@", $mail_parts);
}