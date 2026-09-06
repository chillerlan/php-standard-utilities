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
use Random\Engine\Secure;
use Random\Randomizer;
use RuntimeException;
use function hash;
use function random_bytes;
use function sodium_bin2hex;
use function sodium_crypto_secretbox;
use function sodium_crypto_secretbox_keygen;
use function sodium_crypto_secretbox_open;
use function sodium_hex2bin;
use function sodium_memzero;
use function substr;
use const SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;

/**
 * Basic cryptographic utilities
 */
final class Crypto{

	public const int ENCRYPT_FORMAT_BINARY = 0b00;
	public const int ENCRYPT_FORMAT_BASE64 = 0b01;
	public const int ENCRYPT_FORMAT_HEX    = 0b10;

	public const string NUMERIC         = '0123456789';
	public const string ASCII_LOWER     = 'abcdefghijklmnopqrstuvwxyz';
	public const string ASCII_UPPER     = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	public const string ASCII_SYMBOL    = ' !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~';
	public const string HEXADECIMAL     = self::NUMERIC.'abcdef';
	public const string ASCII_ALPHANUM  = self::NUMERIC.self::ASCII_LOWER.self::ASCII_UPPER;
	public const string ASCII_PRINTABLE = self::NUMERIC.self::ASCII_LOWER.self::ASCII_UPPER.self::ASCII_SYMBOL;
	public const string ASCII_COMMON_PW = self::ASCII_ALPHANUM.'!#$%&()*+,-./:;<=>?@[]~_|';

	/**
	 * Generates an SHA-256 hash for the given value
	 *
	 * @see \hash()
	 */
	public static function sha256(string $data, bool $binary = false):string{
		return hash('sha256', $data, $binary);
	}

	/**
	 * Generates an SHA-256 hash for the given file
	 *
	 * @see \hash_file()
	 */
	public static function sha256file(string $path, bool $binary = false):string{
		return hash_file('sha256', File::realpath($path), $binary);
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
	 * Generates an SHA-512 hash for the given file
	 *
	 * @see \hash_file()
	 */
	public static function sha512file(string $path, bool $binary = false):string{
		return hash_file('sha512', File::realpath($path), $binary);
	}

	/**
	 * Generates a secure random string of the given `$length`, using the characters (8-bit byte) in the given `$keyspace`.
	 *
	 * @see \Random\Randomizer
	 */
	public static function randomString(int $length, string $keyspace = self::ASCII_COMMON_PW):string{
		return new Randomizer(new Secure)->getBytesFromString($keyspace, $length);
	}

	/**
	 * Creates a new cryptographically secure random encryption key for use with `encrypt()` and `decrypt()` (returned in hexadecimal format)
	 *
	 * @see \sodium_crypto_secretbox_keygen()
	 * @see \sodium_crypto_secretbox())
	 *
	 * @throws \SodiumException
	 */
	public static function createEncryptionKey():string{
		return sodium_bin2hex(sodium_crypto_secretbox_keygen());
	}

	/**
	 * Encrypts the given `$data` with `$key`, formats the output according to `$format` [binary, base64, hex]
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
	 * Decrypts the given `$encrypted` data with `$key` from input formatted according to `$format` [binary, base64, hex]
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
