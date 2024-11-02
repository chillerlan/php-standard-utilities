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

}
