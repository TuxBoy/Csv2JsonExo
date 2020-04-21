<?php
declare(strict_types=1);

namespace App\Test;

abstract class Unit
{
	public int $count     = 0;
	public int $passed    = 0;
	public array $failure = [];

	protected function same($expected, $actual, string $method = null): void
	{
		if ($method === null) {
			["function" => $functionName, 'class' => $class] = debug_backtrace()[1];
			$infoTest = "$class::$functionName";
		} else {
			$infoTest = $method;
		}

		if ($expected === $actual) {
			$this->passed++;
		} else {
			$this->failure[] = ' * ' . $infoTest;
		}
		$this->count++;
	}

}
