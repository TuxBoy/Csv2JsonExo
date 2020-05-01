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

	public function addArgument(
		string $name, bool $value = true, bool $required = false, string $description = null
	): self {
		$this->arguments[$name] = ['value' => $value, 'required' => $required, 'description' => $description];

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

	public function getRequiredArguments(): array
	{
		return array_filter($this->getArguments(), fn ($argument) => $argument['required'] === true);
	}

	public function addOption(string $name, bool $value = true, bool $required = false, string $description = null): self
	{
		$this->options[$name] = ['value' => $value, 'required' => $required, 'description' => $description];

		return $this;
	}

	public function hasArguments(): bool
	{
		return \count($this->getArguments()) > 0;
	}

	public function getOptions(): array
	{
		return $this->options;
	}

	public function getName(): string
	{
		return $this->name;
	}

}
