<?php
declare(strict_types=1);

namespace App;

use App\Command\AbstractCommand;
use App\Tools\Input;
use App\Tools\Output;

class Csv2JsonCommand extends AbstractCommand
{

	public function execute(Output $output, Input $input)
	{
		$aggregate = $input->getOption('aggregate');
		$pretty    = (bool) $input->getOption('pretty');
		$csv       = $input->getArgument('file');
		$fields    = $this->getFieldsArrayToString($input->getArgument('fields'));
		$data      = $this->csvToArray($csv, $aggregate);
		if ($fields !== []) {
			$this->filterByFields($data, $fields);
		}
		$jsonData = $this->jsonEncode($data, $pretty);
		if ($jsonData === null || file_put_contents('data.json', $jsonData) === false) {
			$output->write("Une erreur est survenue lors de l'écriture du fichier");
		}

		$output->write('Les données ont bien été parsées en JSON dans le fichier.');
	}

	private function csvToArray(?string $csv, string $aggregate = null): array
	{
		$contentFile = $this->readCsvFile($csv);
		$fields      = $contentFile[0]; // Le premier élément du tableau est le nom des champs
		unset($contentFile[0]);
		foreach ($contentFile as $index => $result) {
			foreach ($result as $key => $line) {
				// Remplacer l'index du tableau par le nom du champ
				$result[$fields[$key]] = $result[$key];
				unset($result[$key]);
			}
			$contentFile[$index] = $result;
		}

		if ($aggregate !== null) {
			$contentFile = $this->aggregateData($contentFile, $aggregate);
		}

		return $contentFile;
	}

	private function aggregateData(array $content, $aggregate): array
	{
		$aggregateArray = [];
		foreach ($content as $data) {
			$key = $data[$aggregate];
			foreach ($data as $field => $value) {
				if ($value === $key) {
					unset($data[$aggregate]);
					$aggregateArray[$key][] = $data;
				}
			}
		}

		return $aggregateArray;
	}

	private function readCsvFile(string $file, string $separator = ';'): ?array
	{
		$results = [];
		if (($handle = fopen($file, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, $separator)) !== FALSE) {
				$results[] = $data;
			}
			fclose($handle);
		}

		return $results;
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

	private function filterByFields(array &$data, array $fields)
	{
		foreach ($data as $key => $values) {
			$diff = array_diff_key($values, array_flip($fields));
			foreach ($values as $field => $value) {
				if (isset($diff[$field])) {
					unset($values[$field]);
				}
			}
			$data[$key] = $values;
		}
	}
}
