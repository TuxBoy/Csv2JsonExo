<?php
declare(strict_types=1);

namespace App\Tools;

abstract class Str
{

	/**
	 * Vérifie si l'objet possède bien la class_name comme parent, si on souhaite vérifié qu'un objet
	 * implémente bien une autre class, le is_a natif de PHP ne permet pas de vérfier les classes abstraite/trait
	 *
	 * @param $object     string|object
	 * @param $class_name string
	 * @return bool
	 */
	public static function isA($object, string $class_name): bool
	{
		$search = interface_exists($class_name) ? class_implements($object) : class_parents($object);

		return array_key_exists($class_name, $search);
	}

}
