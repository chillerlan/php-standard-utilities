<?php
/**
 * Class File
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
use function dirname;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_file;
use function is_link;
use function is_readable;
use function is_writable;
use function readlink;
use function realpath;
use function unlink;

/**
 * Basic file operations
 */
final class File{

	/**
	 * Checks whether a file exists
	 *
	 * @codeCoverageIgnore
	 */
	public static function exists(string $file):bool{
		return file_exists($file) && is_file($file);
	}

	/**
	 * Checks whether the given file is readable
	 *
	 * @codeCoverageIgnore
	 */
	public static function isReadable(string $file):bool{
		return self::exists($file) && is_readable($file);
	}

	/**
	 * Checks whether the given file is writable
	 *
	 * @codeCoverageIgnore
	 */
	public static function isWritable(string $file):bool{
		return self::exists($file) && is_writable($file);
	}

	/**
	 * Returns the absolute real path to the given file or directory
	 *
	 * @codeCoverageIgnore
	 */
	public static function realpath(string $path):string{

		if(is_link($path)){
			$path = readlink($path);
		}

		$realpath = realpath($path);

		if($realpath === false){
			throw new InvalidArgumentException('invalid path');
		}

		return $realpath;
	}

	/**
	 * Deletes a file
	 */
	public static function delete(string $file):bool{
		$file = self::realpath($file);

		if(!self::isWritable($file)){
			throw new RuntimeException('cannot read the given file'); // @codeCoverageIgnore
		}

		if(!unlink($file)){
			throw new RuntimeException('unlink error'); // @codeCoverageIgnore
		}

		clearstatcache();

		return true;
	}

	/**
	 * reads the given file into a string
	 *
	 * @see \file_get_contents()
	 *
	 * @throws \RuntimeException
	 */
	public static function load(string $file, int $offset = 0, int|null $length = null):string{
		$file = self::realpath($file);

		if(!self::isReadable($file)){
			throw new RuntimeException('cannot read the given file'); // @codeCoverageIgnore
		}

		$content = file_get_contents(filename: $file, offset: $offset, length: $length);

		if($content === false){
			throw new RuntimeException('could not load file contents'); // @codeCoverageIgnore
		}

		return $content;
	}

	/**
	 * saves the given data string to the given file path
	 */
	public static function save(string $file, string $data):int{

		if(!Directory::isWritable(dirname($file))){
			throw new RuntimeException('target directory is not writable or does not extist');
		}

		$result = file_put_contents($file, $data);

		if($result === false){
			throw new RuntimeException('could not save file contents'); // @codeCoverageIgnore
		}

		return $result;
	}

	/**
	 * load a JSON string from file into an array or object (convenience)
	 *
	 * @codeCoverageIgnore
	 */
	public static function loadJSON(string $file, bool $associative = false, int $flags = 0):mixed{
		return Str::jsonDecode(self::load($file), $associative, $flags);
	}

	/**
	 * save to a JSON file (convenience)
	 *
	 * @codeCoverageIgnore
	 */
	public static function saveJSON(string $file, mixed $data, int $flags = Str::JSON_ENCODE_FLAGS_DEFAULT):int{
		return self::save($file, Str::jsonEncode($data, $flags));
	}

}
