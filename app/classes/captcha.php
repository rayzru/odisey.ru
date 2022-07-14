<?php
namespace odissey;

class Captcha
{

	public $operands = array('+', '-');
	public $maxValue = 10;

	public function __construct() {
		//if (session_status() == PHP_SESSION_NONE) session_start();
		//$this->operands ;
		if (!isset($_SESSION['captcha'])) {
			$this->set();
		}
	}

	public function getOperand() {
		return $this->operands[mt_rand(0, count($this->operands) - 1)];
	}

	public function getRandom() {
		return round(mt_rand(1, $this->maxValue));
	}

	public function set() {
		$operand = $this->getOperand();
		$firstValue = $this->getRandom();
		do {
			$secondValue = $this->getRandom();
		} while ($operand == '-' && $secondValue > $firstValue);
		$string = $firstValue . $operand . $secondValue;
		$_SESSION['captcha'] = $string;
		return $string;
	}

	public function get() {
		return $_SESSION['captcha'];
	}

	public function calculate() {
		$string = $this->get();
		if (preg_match('/(\d+)(.?)(\d+)/', $string, $matches)) {
			$val1 = (int)$matches[1];
			$val2 = (int)$matches[3];
			$res = ($matches[2] == '+') ? $val1+$val2 : $val1-$val2;
			return $res;
		};
		return false;
	}

	public function __destruct() {
		//@unset($_SESSION['captcha']);
	}
}
