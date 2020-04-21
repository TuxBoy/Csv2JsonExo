<?php
declare(strict_types=1);

namespace App\Command;

use Closure;

class Command
{

	private string $name;

	/**
	 * @var Closure|array|string $callable
	 */
	private $callable;

	private array $arguments = [];

	private array $options = [];

	public function __construct(string $name, $callable)
	{
		$this->name     = $name;
		$this->callable = $callable;
	}

	public function addArgument(string $name, bool $value = true, bool $required = false): self
	{
		$this->arguments[$name] = ['value' => $value, 'required' => $required];

		return $this;
	}

	/**
	 * @return array|Closure|string
	 */
	public function getCallable()
	{
		return $this->callable;
	}

	public function getArguments(): array
	{
		return $this->arguments;
	}

	public function addOption(string $name, bool $value = true, bool $required = false): self
	{
		$this->options[$name] = ['value' => $value, 'required' => $required];

		return $this;
	}

	public function getOptions(): array
	{
		return $this->options;
	}

}
