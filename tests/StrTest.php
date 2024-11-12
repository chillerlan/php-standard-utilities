<?php
/**
 * Class StringTest
 *
 * @created      30.10.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\UtilitiesTest;

use chillerlan\Utilities\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
class StrTest extends TestCase{

	/**
	 * @return array<string, array{0: array<int, string>, 1: bool, 2: bool}>
	 */
	public static function startsWithProvider():array{
		return [
			'empty'         => [[], false, true],
			'filtered'      => [[1, [], true], false, true],
			'default'       => [['nope', 'Hello'], false, true],
			'case mismatch' => [['nope', 'hello'], false, false],
			'ignore case'   => [['nope', 'hello'], true, true],
		];
	}

	/**
	 * @param string[] $needles
	 */
	#[Test]
	#[DataProvider('startsWithProvider')]
	public function startsWith(array $needles, bool $ignoreCase, bool $expected):void{
		$this::assertSame(Str::startsWith('Hello world!', $needles, $ignoreCase), $expected);
	}

	/**
	 * @return array<string, array{0: array<int, string>, 1: bool, 2: bool}>
	 */
	public static function containsAllProvider():array{
		return [
			'empty'         => [[], false, true],
			'filtered'      => [[1, [], true], false, true],
			'default'       => [['world', 'Hello'], false, true],
			'case mismatch' => [['World', 'hello'], false, false],
			'ignore case'   => [['World', 'hello'], true, true],
		];
	}

	/**
	 * @param string[] $needles
	 */
	#[Test]
	#[DataProvider('containsAllProvider')]
	public function containsAll(array $needles, bool $ignoreCase, bool $expected):void{
		$this::assertSame(Str::containsAll('Hello world!', $needles, $ignoreCase), $expected);
	}

	/**
	 * @return array<string, array{0: array<int, string>, 1: bool, 2: bool}>
	 */
	public static function containsAnyProvider():array{
		return [
			'empty'         => [[], false, true],
			'filtered'      => [[1, [], true], false, true],
			'default'       => [['nope', 'Hello'], false, true],
			'case mismatch' => [['nope', 'hello'], false, false],
			'ignore case'   => [['nope', 'hello'], true, true],
		];
	}

	/**
	 * @param string[] $needles
	 */
	#[Test]
	#[DataProvider('containsAnyProvider')]
	public function containsAny(array $needles, bool $ignoreCase, bool $expected):void{
		$this::assertSame(Str::containsAny('Hello world!', $needles, $ignoreCase), $expected);
	}

}
