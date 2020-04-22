<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\CsvException;

/**
 * Cette classe regroupe toutes les méthodes utiles pour faire les traitements sur le fichier csv
 */
class CsvService
{

	public function csvToArray(?string $csv, string $aggregate = null): ?array
	{
		if (!is_file($csv) || !is_readable($csv)) {
			return null;
		}

		$contentFile = $this->readCsvFile($csv);
		$fields      = $contentFile[0]; // Le premier élément du tableau est le nom des champs
		unset($contentFile[0]);
		$contentFile = array_values($contentFile);
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
			if (!array_key_exists($aggregate, $data)) {
				throw new CsvException(
					"Impossible d'aggréger sur $aggregate, le champ ne doit pas être valide."
				);
			}
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

	private function readCsvFile(string $csv, string $separator = null): ?array
	{
		$separator ??= $this->detectSeparator($csv);
		$results   = [];
		if (($handle = fopen($csv, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, $separator)) !== FALSE) {
				$results[] = $data;
			}
			fclose($handle);
		}

		return $results;
	}

	public function detectSeparator($csvFile): string
	{
		$validSeparators = [';' => 0, ',' => 0, '\t' => 0, '|' => 0];
		$handle          = fopen($csvFile, "r");
		$firstLine       = fgets($handle);
		fclose($handle);
		foreach ($validSeparators as $separator => &$count) {
			$count = \count(str_getcsv($firstLine, $separator));
		}


		return \array_search(max($validSeparators), $validSeparators);
	}

	public function filterByFields(array &$data, array $fields)
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
