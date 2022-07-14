<?php
/**
 * Smarty plugin
 * @package    Smarty
 * @subpackage plugins
 */


/**
 * Smarty transliterate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     transliterate<br>
 *
 * @param string
 *
 * @return string
 */

function smarty_modifier_transliterate($string) {
	$table = [
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
		'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
		'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
		'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
		'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH',
		'Ш' => 'SH', 'Щ' => 'SCH', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
		'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA',

		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
		'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
		'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
		'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
		'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
		'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
		'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
	];

	$output = str_replace(
		array_keys($table),
		array_values($table),
		$string
	);

	// таеже те символы что неизвестны
	$output = preg_replace('/\'"]/i', '', $output);
	$output = preg_replace('/[._\[\]]/i', '-', $output);
	$output = preg_replace('/[^-a-z0-9]/i', ' ', $output);
	$output = preg_replace('/ +/', '-', $output);
	$output = trim($output, '-');

	return $output;
}
