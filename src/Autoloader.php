<?php
declare(strict_types=1);

namespace App;

final class Autoloader
{
	/**
	 * Mapping entre le namespace et son répertoire
	 *
	 * @var string[]
	 */
	private static array $namespaceMapPath = ['App\\', 'src/'];

	public static function register(): void
	{
		spl_autoload_register([__CLASS__, 'autoload']);
	}

	public static function autoload(string $class): void
	{
		[$namespace, $path] = static::$namespaceMapPath;
		$classPath = str_replace($namespace, $path, $class);
		$classPath = str_replace('\\', '/', $classPath) . '.php';
		if (!file_exists($classPath)) {
			throw new \Exception(sprintf('The %s class does not exist.', $classPath));
		}
		require_once $classPath;
	}

}
