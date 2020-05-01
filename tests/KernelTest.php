<?php
declare(strict_types=1);

use App\Kernel;
use App\Test\Unit;

final class KernelTest extends Unit
{

	public function testAddCommand(): void
	{
		$kernel = new Kernel(['console', 'foo'], 2);

		$commandInstance = $kernel->addCommand('foo', function () { echo 'Foo command'; });

		$this->same(($commandInstance instanceof \App\Command\Command), true);
		$this->same(isset($kernel->getCommands()['foo']), true);
		$this->same(1, count($kernel->getCommands()));
	}

}
