<?php
/**
 * Class Directory
 *
 * @created      29.10.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Utilities;

use FilesystemIterator;
use InvalidArgumentException;
use RuntimeException;
use function clearstatcache;
use function dirname;
use function file_exists;
use function in_array;
use function is_dir;
use function is_file;
use function is_readable;
use function is_writable;
use function mkdir;
use function rmdir;
use function sprintf;
use function str_contains;
use function str_replace;
use function trim;
use const DIRECTORY_SEPARATOR;

/**
 * Basic directory utilities
 */
final class Directory{

	/**
	 * Checks whether a directory exists
	 *
	 * @codeCoverageIgnore
	 */
	public static function exists(string $dir):bool{
		return file_exists($dir) && is_dir($dir);
	}

	/**
	 * Checks whether the given directory is readable
	 *
	 * @codeCoverageIgnore
	 */
	public static function isReadable(string $dir):bool{
		return self::exists($dir) && is_readable($dir);
	}

	/**
	 * Checks whether the given directory is writable
	 *
	 * @codeCoverageIgnore
	 */
	public static function isWritable(string $dir):bool{
		return self::exists($dir) && is_writable($dir);
	}

	/**
	 * Creates a directory
	 *
	 * @throws \InvalidArgumentException|\RuntimeException
	 */
	public static function create(string $dir, int $permissions = 0o777, bool $recursive = true):string{
		$dir = trim($dir);

		if($dir === ''){
			throw new InvalidArgumentException('invalid directory');
		}

		// $dir exists but is not a directory
		if(file_exists($dir) && !is_dir($dir)){
			throw new InvalidArgumentException(sprintf('cannot create directory: %s already exists as a file or link', $dir));
		}

		// $dir doesn't exist and the attempt to create failed
		if(!file_exists($dir) && !mkdir($dir, $permissions, $recursive)){
			throw new RuntimeException(sprintf('could not create directory: %s', $dir)); // @codeCoverageIgnore
		}

		clearstatcache();

		return File::realpath($dir);
	}

	/**
	 * Removes a directory
	 */
	public static function remove(string $dir):bool{

		if($dir === '' || !self::isWritable($dir)){
			throw new InvalidArgumentException('invalid directory');
		}

		if(!rmdir($dir)){
			throw new RuntimeException('could not delete the given directory'); // @codeCoverageIgnore
		}

		clearstatcache();

		return true;
	}

	/**
	 * Returns the relative path from the given directory (realpath)
	 */
	public static function relativePath(string $path, string $from, string $separator = DIRECTORY_SEPARATOR):string{
		$path = File::realpath($path);
		$from = File::realpath($from);

		if(is_file($path)){
			$path = dirname($path);
		}

		if(is_file($from)){
			$from = dirname($from);
		}

		return trim(str_replace([$from, '\\', '/'], ['', $separator, $separator], $path), '\\/');
	}

	/**
	 * Lists the files in the given directory, with the file names as array keys, ordered.
	 * Excludes files that don't match the given extensions or part of the name, case-sensitive.
	 *
	 * Please note that files that start with a dot (hidden) are considered extensions.
	 *
	 * @see \str_contains()
	 *
	 * @return array<string, \SplFileInfo>
	 * @throws \InvalidArgumentException
	 */
	public static function filelist(string $path, array|null $extensions = null, string|null $nameContains = null):array{
		$path = File::realpath($path);

		if(!self::isReadable($path)){
			throw new InvalidArgumentException(sprintf('cannot read the given directory: %s', $path));
		}

		$files = [];
		/** @var \SplFileInfo $finfo */
		foreach(new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS) as $finfo){
			// skip directories
			if(!$finfo->isFile()){
				continue;
			}
			// skip extensions that don't match
			if($extensions !== null && !in_array($finfo->getExtension(), $extensions, true)){
				continue;
			}
			// skip names that don't match
			if($nameContains !== null && !str_contains($finfo->getFilename(), $nameContains)){
				continue;
			}

			$files[$finfo->getFilename()] = $finfo;
		}

		ksort($files);

		return $files;
	}

	/**
	 * Deletes files in the given directory, returns an array with the results: [filename => (bool) success/failure]
	 *
	 * @see \chillerlan\Utilities\Directory::filelist()
	 *
	 * @return string[]
	 */
	public static function clear(string $path, array|null $extensions = null, string|null $nameContains = null):array{
		$currentFiles = self::filelist($path, $extensions, $nameContains);
		$deletedFiles = [];

		foreach($currentFiles as $fileName => $fileInfo){

			try{
				$deletedFiles[$fileName] = File::delete($fileInfo->getRealPath());
			}
			catch(RuntimeException){
				$deletedFiles[$fileName] = false;
			}

		}

		return $deletedFiles;
	}

}
