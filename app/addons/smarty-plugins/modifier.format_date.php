<?php
/**
 * Smarty plugin
 * Type:     modifier<br>
 * Name:     format_date<br>
 * @package    Smarty
 * @subpackage plugins
 */
use Carbon\Carbon;

function smarty_modifier_format_date($string, $format = '%b %e, %Y') {
	Carbon::setLocale('ru_RU');
	$dt = Carbon::parse($string);
	$date =  $dt->formatLocalized($format);

	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		return iconv('cp1251', 'utf-8', $date);
	} else {
		return $date;
	}
}
