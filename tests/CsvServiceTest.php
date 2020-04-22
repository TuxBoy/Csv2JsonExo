<?php declare(strict_types=1);

use App\Exception\CsvException;
use App\Service\CsvService;
use App\Test;

/**
 * CsvServiceTest.
 */
final class CsvServiceTest extends Test\Unit
{

	public function testNotValidCsvFile(): void
	{
		$csvService = new CsvService;

		$this->same(null, $csvService->csvToArray('/fake.csv'));
	}

	public function testSimpleCsvToArray(): void
	{
		$csvService = new CsvService;
		$actual = $csvService->csvToArray(__DIR__ . '/dummy.csv');

		$expected = [
			[
				'name' => 'foo',
				'id'   => '5',
				'date' => '2020-05-03'
			],
			[
				'name' => 'foo',
				'id'   => '9',
				'date' => '2020-05-03'
			],
			[
				'name' => 'bar',
				'id'   => '10',
				'date' => '2020-05-03'
			],
		];

		$this->same($expected, $actual);
	}

	public function testCsvArrayWithAggregate(): void
	{
		$csvService = new CsvService;
		$actual     = $csvService->csvToArray(
			__DIR__ . '/dummy.csv', 'name'
		);

		$expected = [
			'foo' => [
				['id' => '5', 'date' => '2020-05-03'],
				['id' => '9', 'date' => '2020-05-03'],
			],
			'bar' => [
				['id' => '10', 'date' => '2020-05-03'],
			],
		];

		$this->same($expected, $actual);
	}

	public function testCsvArrayAggregateWithUnknownField(): void
	{
		$csvService = new CsvService;
		try {
			$csvService->csvToArray(
				__DIR__ . '/dummy.csv', 'unknown'
			);
		} catch (CsvException $exception) {
			$this->same(true, is_a($exception, CsvException::class, true));
			$this->same("Impossible d'aggréger sur unknown, le champ ne doit pas être valide.", $exception->getMessage());
		}
	}

}
