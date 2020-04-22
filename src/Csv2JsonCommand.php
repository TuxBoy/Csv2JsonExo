<?php
declare(strict_types=1);

namespace App;

use App\Command\AbstractCommand;
use App\Service\CsvService;
use App\Tools\Input;
use App\Tools\Output;

class Csv2JsonCommand extends AbstractCommand
{

	private CsvService $csvService;

	public function __construct()
	{
		$this->csvService = new CsvService();
	}

	public function execute(Output $output, Input $input): void
	{
		$aggregate = $input->getOption('aggregate');
		$pretty    = (bool) $input->getOption('pretty');
		$csv       = $input->getArgument('file');
		$fields    = $this->getFieldsArrayToString($input->getArgument('fields'));
		$data      = $this->csvService->csvToArray($csv, $aggregate);

		if ($fields !== []) {
			$this->csvService->filterByFields($data, $fields);
		}
		$jsonData = $this->jsonEncode($data, $pretty);
		if ($jsonData === null || file_put_contents('data.json', $jsonData) === false) {
			$output->write("Une erreur est survenue lors de l'écriture du fichier");
		}

		$output->write('Les données ont bien été parsées en JSON dans le fichier.');
	}

	private function getFieldsArrayToString(?string $argument): array
	{
		return $argument ? explode(',', $argument) : [];
	}

	private function jsonEncode(array $data = [], bool $pretty = false): string
	{
		$pretty  = $pretty ? JSON_PRETTY_PRINT : 0;
		$options = JSON_NUMERIC_CHECK | JSON_THROW_ON_ERROR  | $pretty;

		return json_encode($data, $options);
	}
}
