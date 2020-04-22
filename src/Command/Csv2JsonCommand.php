<?php
declare(strict_types=1);

namespace App\Command;

use App\Command\AbstractCommand;
use App\Service\CsvService;
use App\Service\JsonService;
use App\Tools\Input;
use App\Tools\Output;

class Csv2JsonCommand extends AbstractCommand
{
	private const JSON_FILE = 'data.json';

	private CsvService $csvService;

	private JsonService $jsonService;

	public function __construct()
	{
		$this->csvService  = new CsvService();
		$this->jsonService = new JsonService();
	}

	public function execute(Output $output, Input $input): void
	{
		$aggregate       = $input->getOption('aggregate');
		$pretty          = (bool) $input->getOption('pretty');
		$csv             = $input->getArgument('file');
		$descriptionFile = $input->getOption('desc');

		$fields    = $this->getFieldsArrayToString($input->getOption('fields'));
		$data      = $this->csvService->csvToArray($csv, $aggregate);

		if ($fields !== []) {
			$this->csvService->filterByFields($data, $fields);
		}
		$jsonData = $this->jsonService->jsonEncode($data, $pretty, $descriptionFile);
		if ($jsonData === null || file_put_contents(self::JSON_FILE, $jsonData) === false) {
			$output->write("Une erreur est survenue lors de l'écriture du fichier");
		}

		$output->write('Les données ont bien été parsées en JSON dans le fichier.');
	}

	private function getFieldsArrayToString(?string $argument): array
	{
		return $argument ? explode(',', $argument) : [];
	}
}
