<?php
/**
 * Class String
 *
 * @created      29.10.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Utilities;

use RuntimeException;
use function array_filter;
use function array_map;
use function array_values;
use function is_string;
use function json_decode;
use function json_encode;
use function mb_strtolower;
use function sodium_bin2base64;
use function str_contains;
use function str_replace;
use function str_starts_with;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const SODIUM_BASE64_VARIANT_ORIGINAL;

/**
 * string handling helpers
 */
final class Str{

	public const JSON_ENCODE_FLAGS_DEFAULT = (JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

	/**
	 * Filters an array and removes all elements that are not strings. Array keys are *not* retained.
	 *
	 * @see array_filter()
	 * @see array_values()
	 * @see is_string()
	 *
	 * @param array<string|int, mixed> $mixed
	 * @return string[]
	 */
	public static function filter(array $mixed):array{
		return array_filter(array_values($mixed), is_string(...));
	}

	/**
	 * Converts the strings in an array to uppercase
	 *
	 * @see mb_strtoupper()
	 * @see self::filter()
	 *
	 * @param string[] $strings
	 * @return string[]
	 *
	 * @codeCoverageIgnore
	 */
	public static function toUpper(array $strings):array{
		return array_map(mb_strtoupper(...), self::filter($strings));
	}

	/**
	 * Converts the strings in an array to lowercase
	 *
	 * @see mb_strtolower()
	 * @see self::filter()
	 *
	 * @param string[] $strings
	 * @return string[]
	 *
	 * @codeCoverageIgnore
	 */
	public static function toLower(array $strings):array{
		return array_map(mb_strtolower(...), self::filter($strings));
	}

	/**
	 * Checks whether the given string starts with *any* of the given array of needles.
	 *
	 * @see \str_starts_with()
	 *
	 * @param string[] $needles
	 */
	public static function startsWith(string $haystack, array $needles, bool $ignoreCase = false):bool{
		$needles = self::filter($needles);

		if($needles === []){
			return true;
		}

		if($ignoreCase){
			$haystack = mb_strtolower($haystack);
			$needles  = array_map(mb_strtolower(...), $needles);
		}

		foreach($needles as $needle){
			if($needle !== '' && str_starts_with($haystack, $needle)){
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks whether the given string (haystack) contains *all* of the given array of needles.
	 * The given array is filtered for string values.
	 *
	 * @see \str_contains()
	 *
	 * @param string[] $needles
	 */
	public static function containsAll(string $haystack, array $needles, bool $ignoreCase = false):bool{
		$needles = self::filter($needles);

		if($needles === []){
			return true;
		}

		if($ignoreCase){
			$haystack = mb_strtolower($haystack);
			$needles  = array_map(mb_strtolower(...), $needles);
		}

		foreach($needles as $needle){
			if($needle !== '' && !str_contains($haystack, $needle)){
				return false;
			}
		}

		return true;
	}

	/**
	 * Checks whether the given string (haystack) contains *any* of the given array of needles.
	 * The given array is filtered for string values.
	 *
	 * @param string[] $needles
	 */
	public static function containsAny(string $haystack, array $needles, bool $ignoreCase = false):bool{
		$needles = self::filter($needles);

		if($needles === []){
			return true;
		}

		if($ignoreCase){
			$haystack = mb_strtolower($haystack);
			$needles  = array_map(mb_strtolower(...), $needles);
		}

		return str_replace($needles, '', $haystack) !== $haystack;
	}

	/**
	 * Decodes a JSON string
	 *
	 * @throws \JsonException
	 * @codeCoverageIgnore
	 */
	public static function jsonDecode(string $json, bool $associative = false, int $flags = 0):mixed{
		$flags |= JSON_THROW_ON_ERROR;

		return json_decode(json: $json, associative: $associative, flags: $flags);
	}

	/**
	 * Encodes a value into a JSON representation
	 *
	 * @throws \JsonException
	 * @codeCoverageIgnore
	 */
	public static function jsonEncode(mixed $data, int $flags = self::JSON_ENCODE_FLAGS_DEFAULT):string{
		$flags |= JSON_THROW_ON_ERROR;

		$encoded = json_encode($data, $flags);

		// the chance to run into this is near zero but hey, at least phpstan is happy
		if($encoded === false){
			throw new RuntimeException('json_encode() error'); // @codeCoverageIgnore
		}

		return $encoded;
	}

	/**
	 * Encodes a binary string to base64 (timing-safe)
	 *
	 * @see sodium_bin2base64()
	 *
	 * @throws \SodiumException
	 * @codeCoverageIgnore
	 */
	public static function base64encode(string $string, int $variant = SODIUM_BASE64_VARIANT_ORIGINAL):string{
		return sodium_bin2base64($string, $variant);
	}

	/**
	 * Decodes a base64 string into binary (timing-safe)
	 *
	 * @see sodium_base642bin()
	 *
	 * @throws \SodiumException
	 * @codeCoverageIgnore
	 */
	public static function base64decode(string $base64, int $variant = SODIUM_BASE64_VARIANT_ORIGINAL):string{
		return sodium_base642bin($base64, $variant);
	}

}
