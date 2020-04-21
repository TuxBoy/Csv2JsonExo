<?php
declare(strict_types=1);

use App\Test\Unit;

final class Csv2JsonTest extends Unit
{

	public function testParseCsvFile2Json(): void
	{
		$csv2Json = new \App\Csv2JsonCommand();
		$actual = $csv2Json->execute(new \App\Tools\Output(), new \App\Tools\Input(['file' => 'test.csv']));
	}

}
