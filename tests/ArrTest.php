<?php
/**
 * ArrTest.php
 *
 * @created      25.06.2025
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2025 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\UtilitiesTest;

use chillerlan\Utilities\Arr;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arr::class)]
final class ArrTest extends TestCase{

	private const testArray = [
		'one'   => 1,
		'two'   => 2,
		'three' => 3,
		'four'  => 4,
		'five'  => 5,
	];

	#[Test]
	public function first():void{
		$first = Arr::first(self::testArray);

		$this::assertSame(1, $first);
	}

	#[Test]
	public function last():void{
		$last = Arr::last(self::testArray);

		$this::assertSame(5, $last);
	}

	#[Test]
	public function random():void{
		$random = Arr::random(self::testArray);

		$this::assertContains($random, self::testArray);
	}

}
