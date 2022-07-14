<?php
namespace odissey;

use \Illuminate\Validation\Factory;
use \Illuminate\Filesystem\Filesystem;
use \Illuminate\Translation\FileLoader;
use \Illuminate\Translation\Translator;

class Validation extends Factory
{
	public function __construct() {

		$filesystem = new Filesystem();
		$fileLoader = new FileLoader($filesystem, '');
		$translator = new Translator($fileLoader, 'ru_RU');

		parent::__construct($translator);
	}
}
