<?php
/**
 * Class Arrays
 *
 * @created      29.10.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Utilities;

use function array_key_first;
use function array_key_last;
use function array_keys;
use function count;
use function random_int;
use const PHP_VERSION_ID;

/**
 * Array functions
 */
final class Arr{

	/**
	 * Returns the first element of an array, `null` if the given array is empty.
	 *
	 * @param array<string|int, mixed> $array
	 *
	 * @codeCoverageIgnore
	 */
	public static function first(array $array):mixed{

		if($array === []){
			return null;
		}

		return $array[array_key_first($array)];
	}

	/**
	 * Returns the last element of an array, `null` if the given array is empty.
	 *
	 * @param array<string|int, mixed> $array
	 *
	 * @codeCoverageIgnore
	 */
	public static function last(array $array):mixed{

		if($array === []){
			return null;
		}

		return $array[array_key_last($array)];
	}

	/**
	 * Returns a random element of the given array, `null` if the given array is empty.
	 *
	 * @see \random_int() - PHP <= 8.1
	 * @see \Random\Randomizer::pickArrayKeys() - PHP >= 8.2
	 *
	 * @param array<string|int, mixed> $array
	 *
	 * @noinspection PhpFullyQualifiedNameUsageInspection
	 */
	public static function random(array $array):mixed{

		if($array === []){
			return null;
		}

		if(PHP_VERSION_ID >= 80200){
			$key = (new \Random\Randomizer(new \Random\Engine\Secure))->pickArrayKeys($array, 1)[0];
		}
		else{
			// array_rand() is not cryptographically secure
			$keys = array_keys($array);
			$key  = $keys[random_int(0, (count($keys) - 1))];
		}

		return $array[$key];
	}

}
