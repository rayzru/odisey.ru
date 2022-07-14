<?php
/**
 * Smarty  modifier plugin
 *
 * Type:     modifier<br>
 * Example: {$var|plural:'штука':'штуки':'штук'}
 * @author   becon
 * @version  1.0
 * @param int
 * @param string
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_plural(int $count, string $form1, string $form2, string $form3) {
	$count = str_replace(' ', '', $count);
	if ($count > 10 && floor(($count % 100) / 10) == 1) {
		return $form3;
	} else {
		switch ($count % 10) {
			case 1:
				return $form1;
			case 2:
			case 3:
			case 4:
				return $form2;
			default:
				return $form3;
		}
	}
}
