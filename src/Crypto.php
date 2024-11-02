<?php
/**
 * Class Crypto
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
use function hash;
use function random_bytes;
use function random_int;
use function sodium_bin2hex;
use function sodium_crypto_secretbox;
use function sodium_crypto_secretbox_keygen;
use function sodium_crypto_secretbox_open;
use function sodium_hex2bin;
use function sodium_memzero;
use function strlen;
use function substr;
use const PHP_VERSION_ID;
use const SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;

/**
 * Basic cryptographic utilities
 */
final class Crypto{

	public const ENCRYPT_FORMAT_BINARY = 0b00;
	public const ENCRYPT_FORMAT_BASE64 = 0b01;
	public const ENCRYPT_FORMAT_HEX    = 0b10;

	public const NUMERIC         = '0123456789';
	public const ASCII_LOWER     = 'abcdefghijklmnopqrstuvwxyz';
	public const ASCII_UPPER     = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	public const ASCII_SYMBOL    = ' !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~';
	public const HEXADECIMAL     = self::NUMERIC.'abcdef';
	public const ASCII_ALPHANUM  = self::NUMERIC.self::ASCII_LOWER.self::ASCII_UPPER;
	public const ASCII_PRINTABLE = self::NUMERIC.self::ASCII_LOWER.self::ASCII_UPPER.self::ASCII_SYMBOL;
	public const ASCII_COMMON_PW = self::ASCII_ALPHANUM.'!#$%&()*+,-./:;<=>?@[]~_|';

	/**
	 * Generates an SHA-256 hash for the given value
	 *
	 * @see \hash()
	 */
	public static function sha256(string $data, bool $binary = false):string{
		return hash('sha256', $data, $binary);
	}

	/**
	 * Generates an SHA-512 hash for the given value
	 *
	 * @see \hash()
	 */
	public static function sha512(string $data, bool $binary = false):string{
		return hash('sha512', $data, $binary);
	}

	/**
	 * Generates a secure random string of the given $length, using the characters (8-bit byte) in the given $keyspace.
	 *
	 * @noinspection PhpFullyQualifiedNameUsageInspection
	 * @SuppressWarnings(PHPMD.MissingImport)
	 */
	public static function randomString(int $length, string $keyspace = self::ASCII_COMMON_PW):string{

		// use the Randomizer if available
		// https://github.com/phpstan/phpstan/issues/7843
		if(PHP_VERSION_ID >= 80300){
			return (new \Random\Randomizer(new \Random\Engine\Secure))->getBytesFromString($keyspace, $length);
		}

		$len = (strlen($keyspace) - 1);
		$str = '';

		for($i = 0; $i < $length; $i++){
			$str .= $keyspace[random_int(0, $len)];
		}

		return $str;
	}

	/**
	 * Creates a new cryptographically secure random encryption key (returned in hexadecimal format)
	 *
	 * @throws \SodiumException
	 */
	public static function createEncryptionKey():string{
		return sodium_bin2hex(sodium_crypto_secretbox_keygen());
	}

	/**
	 * Encrypts the given $data with $key, $format output [binary, base64, hex]
	 *
	 * @see \sodium_crypto_secretbox()
	 * @see \sodium_bin2base64()
	 * @see \sodium_bin2hex()
	 *
	 * @throws \SodiumException
	 */
	public static function encrypt(string $data, string $keyHex, int $format = self::ENCRYPT_FORMAT_HEX):string{
		$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
		$box   = sodium_crypto_secretbox($data, $nonce, sodium_hex2bin($keyHex));

		$out = match($format){
			self::ENCRYPT_FORMAT_BINARY => $nonce.$box,
			self::ENCRYPT_FORMAT_BASE64 => Str::base64encode($nonce.$box),
			self::ENCRYPT_FORMAT_HEX    => sodium_bin2hex($nonce.$box),
			default                     => throw new InvalidArgumentException('invalid format'), // @codeCoverageIgnore
		};

		sodium_memzero($data);
		sodium_memzero($keyHex);
		sodium_memzero($nonce);
		sodium_memzero($box);

		return $out;
	}

	/**
	 * Decrypts the given $encrypted data with $key from $format input [binary, base64, hex]
	 *
	 * @see \sodium_crypto_secretbox_open()
	 * @see \sodium_base642bin()
	 * @see \sodium_hex2bin()
	 *
	 * @throws \SodiumException
	 */
	public static function decrypt(string $encrypted, string $keyHex, int $format = self::ENCRYPT_FORMAT_HEX):string{

		$bin = match($format){
			self::ENCRYPT_FORMAT_BINARY => $encrypted,
			self::ENCRYPT_FORMAT_BASE64 => Str::base64decode($encrypted),
			self::ENCRYPT_FORMAT_HEX    => sodium_hex2bin($encrypted),
			default                     => throw new InvalidArgumentException('invalid format'), // @codeCoverageIgnore
		};

		$nonce = substr($bin, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
		$box   = substr($bin, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
		$data  = sodium_crypto_secretbox_open($box, $nonce, sodium_hex2bin($keyHex));

		sodium_memzero($encrypted);
		sodium_memzero($keyHex);
		sodium_memzero($bin);
		sodium_memzero($nonce);
		sodium_memzero($box);

		if($data === false){
			throw new RuntimeException('decryption failed'); // @codeCoverageIgnore
		}

		return $data;
	}

}
