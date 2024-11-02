<?php
/**
 * Class CryptoTest
 *
 * @created      29.10.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\UtilitiesTest;

use chillerlan\Utilities\Crypto;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Crypto::class)]
class CryptoTest extends TestCase{

	// https://www.php.net/manual/en/function.hash.php
	protected const hashdata = 'The quick brown fox jumped over the lazy dog.';

	#[Test]
	public function sha256():void{
		$this::assertSame(
			Crypto::sha256($this::hashdata),
			'68b1282b91de2c054c36629cb8dd447f12f096d3e3c587978dc2248444633483',
		);
	}

	#[Test]
	public function sha512():void{
		$this::assertSame(
			Crypto::sha512($this::hashdata),
			'0a8c150176c2ba391d7f1670ef4955cd99d3c3ec8cf06198cec30d436f2ac0c9'.
			'b64229b5a54bdbd5563160503ce992a74be528761da9d0c48b7c74627302eb25',
		);
	}

	#[Test]
	public function randomString():void{
		$this::assertMatchesRegularExpression('/^[a-f\d]{32}/i', Crypto::randomString(32, Crypto::HEXADECIMAL));
		$this::assertMatchesRegularExpression('/^[a-z]{32}/', Crypto::randomString(32, Crypto::ASCII_LOWER));
	}

	/**
	 * @return array<string, array<int, int>>
	 */
	public static function encryptionFormatProvider():array{
		return [
			'binary' => [Crypto::ENCRYPT_FORMAT_BINARY],
			'base64' => [Crypto::ENCRYPT_FORMAT_BASE64],
			'hex'    => [Crypto::ENCRYPT_FORMAT_HEX],
		];
	}

	#[Test]
	#[DataProvider('encryptionFormatProvider')]
	public function encryptDecrypt(int $format):void{
		$data = 'hello this is a test string!';
		$key  = Crypto::createEncryptionKey();

		$encrypted = Crypto::encrypt($data, $key, $format);
		$decrypted = Crypto::decrypt($encrypted, $key, $format);

		$this::assertSame($data, $decrypted);
	}

}
