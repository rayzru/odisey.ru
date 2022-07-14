<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty Carbon DateTime modifier plugin
 *
 * Type:     modifier<br>
 * Name:     humandate<br>
 * @param string
 * @return string
 */

use Carbon\Carbon;

function smarty_modifier_humandate($timestamp) {
	Carbon::setLocale('ru');
	$d = new Carbon($timestamp);
	return $d->diffForHumans();
}
