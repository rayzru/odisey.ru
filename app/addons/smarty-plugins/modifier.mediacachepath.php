<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty mediacachepath modifier plugin
 *
 * Type:     modifier<br>
 * Name:     mediacachepath<br>
 * @param string
 * @param string
 * @param bool
 * @return string
 */

function smarty_modifier_mediacachepath($filename, $size = '', $cdn = false)
{
	$cached = (!empty($size) ? '_cache/' . $size . '/' : '');
	$shortcut =
		mb_substr($filename, 0, 1, 'utf-8') . '/' .
		mb_substr($filename, 1, 1, 'utf-8') . '/' .
		$filename;

	// if there is no cached resource - don't use CDN
	if ($cdn) {
		$root = implode(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], 'assets', 'images', 'catalog']);
		$image = implode(DIRECTORY_SEPARATOR, [$root, $cached . $shortcut]);
		$cdn = $cdn && file_exists($image);
	}

	return ($cdn) ?
		'http://cdn.' . $_SERVER['SERVER_NAME'] . '/' . $cached . $shortcut :
		'/assets/images/catalog/' . $cached . $shortcut;
}
