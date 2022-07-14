<?php
/**
 * Smarty plugin
 * @package    Smarty
 * @subpackage plugins
 */

/**
 * Smarty Carbon DateTime modifier plugin
 *
 * Type:     modifier<br>
 * Name:     carbondate<br>
 *
 * @param string
 *
 * @return string
 */

use Carbon\Carbon;

function smarty_modifier_carbondate($timestamp, $format) {
	Carbon::setLocale('ru');
	$d = new Carbon($timestamp);
	return $d->formatLocalized($format);
}
