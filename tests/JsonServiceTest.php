<?php

declare(strict_types=1);

final class JsonServiceTest extends \App\Test\Unit
{

	public function testJsonEncodeWithValidData(): void
	{
		$jsonService = new \App\Service\JsonService();

		$data = [
			[
				'name' => 'foo',
				'id'   => '5',
				'date' => '2020-05-03'
			],
		];

		$validJson = $jsonService->jsonEncode($data, false, null);

		$this->same('[{"name":"foo","id":5,"date":"2020-05-03"}]', $validJson);
	}

	public function testJsonEncodeWithEmptyData(): void
	{
		$jsonService = new \App\Service\JsonService();
		$validJson   = $jsonService->jsonEncode([], false, null);

		$this->same('[]', $validJson);
	}

}
