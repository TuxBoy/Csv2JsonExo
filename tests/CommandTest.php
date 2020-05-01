<?php

declare(strict_types=1);


use App\Command\Command;

final class CommandTest extends \App\Test\Unit
{

	public function testAddArguments(): void
	{
		$command = new Command('foo', function () {});
		$command->addArgument('name');

		$this->same(1, \count($command->getArguments()));

		$command = new Command('foo', function () {});
		$command
			->addArgument('name', true, false, 'une description')
			->addArgument('bar', false, true);

		$this->same(2, \count($command->getArguments()));
		$this->same([
			'name' => ['value' => true, 'required' => false, 'description' => 'une description'],
			'bar' => ['value' => false, 'required' => true, 'description' => null]
		], $command->getArguments());
	}

	public function testAddOptions(): void
	{
		$command = new Command('foo', function () {});
		$command->addOption('name');

		$this->same(1, \count($command->getOptions()));

		$command = new Command('foo', function () {});
		$command->addOption('name')->addOption('bar', false, true, 'une description');

		$this->same(2, \count($command->getOptions()));
		$this->same([
			'name' => ['value' => true, 'required' => false, 'description' => null],
			'bar' => ['value' => false, 'required' => true, 'description' => 'une description']
		], $command->getOptions());
	}

	public function testGetRequiredArguments(): void
	{
		$command = new Command('foo', function () {});
		$command->addArgument('name')->addArgument('bar', false, true);

		$this->same(1, \count($command->getRequiredArguments()));
	}

	public function testGetRequiredNotArguments(): void
	{
		$command = new Command('foo', function () {});
		$command->addArgument('name');

		$this->same(0, \count($command->getRequiredArguments()));
	}

	public function testHasArguments(): void
	{
		$command = new Command('foo', function () {});
		$command->addArgument('name');

		$this->same(true, $command->hasArguments());

		$command = new Command('foo', function () {});

		$this->same(false, $command->hasArguments());
	}

}
