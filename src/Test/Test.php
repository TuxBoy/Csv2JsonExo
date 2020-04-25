<?php
declare(strict_types=1);

namespace App\Test;

use App\Tools\Output;
use App\Tools\Str;
use Exception;
use ReflectionClass;

class Test
{
	private int $count      = 0;
	private array $passed   = [];
	private array $failures = [];

	public function run(Output $output): void
	{
		// TODO Gérer le cas des sous dossier, car ici ça implique que toutes les classes soient au même niveau
		$classesTest = glob(dirname(__DIR__, 2) . '/tests/*Test.php');
		foreach ($classesTest as $classTest) {
			require_once $classTest;
			$classTest = str_replace('.php', '', $classTest);
			[, $class] = explode('tests/', $classTest);
			if (Str::isA($class, Unit::class)) {
				$this->count += $this->loadUnitClass($class);
			}
		}

		$output->write('Tests executed: ' . $this->count . "\n");
		$output->write($this->showMessage());
	}

	private function showMessage(): string
	{
		$message = '';
		if (empty($this->failures)) {
			$message = "\033[42m All tests are successful ! . \033[0m";
		} else {
			$message .= "\033[41m Test KO: " . count($this->failures) . "\033[0m\n\n";
			foreach ($this->failures as $failure) {
				$message .= $failure . "\n";
			}
		}

		return $message;
	}

	private function loadUnitClass(string $class): int
	{
		/** @var $class Unit */
		$class = new $class;
		$reflectionClass = new ReflectionClass($class);
		$count = 0;
		foreach ($reflectionClass->getMethods() as $method) {
			if (substr($method->getName(), 0, 4) === 'test') {
				try {
					$class->{$method->getName()}();
					if ($class->passed > 0) {
						$this->passed[] = $class->passed;
					}
				} catch (Exception $exception) {
					$this->failures[] = $exception->getMessage();
				}

				$count = $class->count;
			}
		}

		return $count;
	}

}
