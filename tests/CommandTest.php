<?php
declare(strict_types=1);

use App\Kernel;
use App\Test\Unit;

final class CommandTest extends Unit
{

	public function testAddCommand(): void
	{
		$command = new Kernel(['console', 'foo'], 2);

		$commandInstance = $command->addCommand('foo', function () { echo 'Foo command'; });

		$this->same(($commandInstance instanceof \App\Command\Command), true);
		$this->same(isset($command->getCommands()['foo']), true);
		$this->same(1, count($command->getCommands()));
	}

}
