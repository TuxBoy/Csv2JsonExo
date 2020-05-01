<?php
declare(strict_types=1);

namespace App;

use App\Command\AbstractCommand;
use App\Command\Command;
use App\Tools\Input;
use App\Tools\Output;
use App\Tools\Str;
use Closure;

final class Kernel
{

	private array $arguments;

	private array $commands = [];

	private int $numberArgs;

	public function __construct(array $arguments, int $numberArgs)
	{
		// Supprimer le premier élément du tableau qui est le nom du fichier console
		array_shift($arguments);
		$this->arguments  = $arguments;
		$this->numberArgs = $numberArgs;
	}

	/**
	 * @param string $name
	 * @param Closure|array|string $callable
	 * @return Command
	 */
	public function addCommand(string $name, $callable): Command
	{
		$command = new Command($name, $callable);
		$this->commands[$name] = $command;

		return $command;
	}

	public function listCommand(Output $output): void
	{
		$message = "Liste des commandes disponibles :\n\n";
		$message .= join("\n", array_keys($this->commands)) . "\n";
		$output->write($message);
	}

	public function parseArgumentsInfoMessage(Command $command)
	{
		$message = "Usage:\n\n";
		$hasCommand = $command->hasArguments() ? ' <argument>' : '';
		$message .= "\t" . $command->getName() . " " . $hasCommand . "\n\n";
		if ($command->hasArguments()) {
			$message .= "Options:\n\n";
			foreach (array_merge($command->getArguments(), $command->getOptions()) as $name => $argument) {
				$message .= "\t" . ' -  --' . $name . ' : ' . ($argument['description'] ?? 'No description') . "\n";
			}
		}

		(new Output())->write($message);
	}

	private function getCalledCommand(): Command
	{
		if ($this->numberArgs > 1) {
			foreach ($this->arguments as $argument) {
				if (isset($this->commands[$argument])) {
					return $this->commands[$argument];
				}
			}
		}

		return new Command('help', [$this, 'listCommand']);
	}

	private function parseCliArgument(array $params = []): array
	{
		$results = [];
		foreach ($params as $name => $options) {
			foreach ($this->arguments as $argument) {
				if (substr($argument, 0, 2) === '--') {
					if ($options['value'] !== true) {
						$argument .= '=true';
					}
					preg_match("#^--($name)=([a-zA-Z].*)$#", $argument, $matches);
					if (empty($matches)) {
						continue;
					}
					array_shift($matches);
					if (!isset($matches[1]) || $matches[1] === '' && $options['required'] === true) {
						throw new \Exception(sprintf('Le paramètre %s est obligatoire', $name));
					}
					$results[$name] = $matches[1];
				}
			}
		}

		return $results;
	}

	public function run()
	{
		$command   = $this->getCalledCommand();

		// Arrivée ici avant d'aller plus loin, je dois vérifier si la commande a des ^arguments obligatoire
		if (\count($command->getRequiredArguments()) > 0 && \count($this->arguments) <= 1) {
			return $this->parseArgumentsInfoMessage($command);
		} else {
			$callable  = $command->getCallable();
			$arguments = $this->parseCliArgument($command->getArguments());
			$options   = $this->parseCliArgument($command->getOptions());
		}

		if (is_string($callable) && Str::isA($callable, AbstractCommand::class)) {
			$callable = [$callable, 'execute'];
		}
		if (is_array($callable)) {
			[$class, $method] = $callable;
			if (is_string($class)) {
				$class = new $class;
			}
			$callable = Closure::fromCallable([$class, $method]);
		}

		return call_user_func_array($callable, [new Output(), new Input($arguments, $options)]);
	}

	/**
	 * @return Command[]
	 */
	public function getCommands(): array
	{
		return $this->commands;
	}
}
