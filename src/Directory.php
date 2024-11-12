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

use InvalidArgumentException;
use RuntimeException;
use function clearstatcache;
use function file_exists;
use function is_dir;
use function is_readable;
use function is_writable;
use function mkdir;
use function realpath;
use function rmdir;
use function sprintf;
use function trim;

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

		$dir = realpath($dir);

		// reaplpath error
		if(!$dir){
			throw new RuntimeException('invalid directory (realpath)'); // @codeCoverageIgnore
		}

		clearstatcache();

		return $dir;
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

}
