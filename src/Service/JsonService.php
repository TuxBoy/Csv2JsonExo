<?php
declare(strict_types=1);

namespace App\Service;

/**
 * JsonService.
 */
class JsonService
{

	private array $mapTypes = [
		'int'      => 'integer',
		'bool'     => 'boolean',
		'float'    => 'double',
		'string'   => 'string',
		'array'    => 'array',
		'integer'  => 'integer',
		'boolean'  => 'boolean',
		'date'     => 'date',
		'datetime' => 'datetime'
	];

	private function prepareJsonEncode(array $data, bool $pretty): ?string
	{
		$jsonPretty = $pretty ? JSON_PRETTY_PRINT : 0;
		$options    = JSON_NUMERIC_CHECK | JSON_THROW_ON_ERROR  | $jsonPretty;

		return json_encode($data, $options) ?? null;
	}


	public function jsonEncode(array $data, bool $pretty, ?string $descriptionFile): string
	{
		$result = $this->prepareJsonEncode($data, $pretty);
		if ($descriptionFile !== null) {
			['errors' => $errors, 'data' => $result] = $this->checkValidTypes($result, $pretty, $descriptionFile);
			if (is_array($errors) && $errors !== []) {
				// TODO il faudra afficher les erreurs.
				throw new \Exception("Des erreurs de typages sont présentes dans vos données.\n");
			}
		}

		return $result;
	}

	private function checkValidTypes(string $result, bool $pretty, string $descriptionFile)
	{
		$errors = [];
		$data   = json_decode($result, true);
		$descriptionFileContent = file_get_contents($descriptionFile);
		preg_match_all('#([a-z].*)=(.*)#', $descriptionFileContent, $matches);
		$mappingTypes = [];
		foreach ($matches[0] as $match) {
			[$field, $type] = array_map('trim', explode('=', $match));
			$nullable       = substr($type, 0, 1) === '?';
			$type           = str_replace('?', '', $type);
			$mappingTypes[$field] = [
				'type'     => $type,
				'nullable' => $nullable
			];
		}

		foreach ($data as $key => $values) {
			foreach ($mappingTypes as $field => $types) {
				$currentType = gettype($values[$field]);
				if ($values[$field] === '' && $types['nullable'] === true) {
					$values[$field] = null;
				}
				if ($field !== 'date' && ($currentType !== $this->mapTypes[$types['type']]) && $values[$field] !== null) {
					$errors[$field]['type'] = 'Erreur de typage de base';
				}
			}
			$data[$key] = $values;
		}

		return ['errors' => $errors, 'data' => $this->prepareJsonEncode($data, $pretty)];
	}

}
