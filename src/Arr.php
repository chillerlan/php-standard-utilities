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

use Random\Engine\Secure;
use Random\Randomizer;
use function array_key_first;
use function array_key_last;
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

		if(PHP_VERSION_ID >= 80500){
			return \array_first($array);
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

		if(PHP_VERSION_ID >= 80500){
			return \array_last($array);
		}

		return $array[array_key_last($array)];
	}

	/**
	 * Returns a random element of the given array, `null` if the given array is empty.
	 *
	 * @see \Random\Randomizer::pickArrayKeys()
	 *
	 * @param array<string|int, mixed> $array
	 */
	public static function random(array $array):mixed{

		if($array === []){
			return null;
		}

		/** @phan-suppress-next-line PhanParamTooManyInternal (false positive) */
		$key = new Randomizer(new Secure)->pickArrayKeys($array, 1)[0];

		return $array[$key];
	}

}
