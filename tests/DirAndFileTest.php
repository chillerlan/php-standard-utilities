<?php
/**
 * Class FIleTest
 *
 * @created      01.11.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\UtilitiesTest;

use chillerlan\Utilities\Directory;
use chillerlan\Utilities\File;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function realpath;
use const DIRECTORY_SEPARATOR;

#[CoversClass(Directory::class)]
#[CoversClass(File::class)]
class DirAndFileTest extends TestCase{

	protected const testDir     = __DIR__.'/filetest';
	protected const testFile    = self::testDir.'/test.txt';
	protected const testData    = 'Hello world!';
	protected const testNewDir  = self::testDir.DIRECTORY_SEPARATOR.'some dir';
	protected const invalidDir  = DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'bar';
	protected const invalidFile = self::invalidDir.DIRECTORY_SEPARATOR.'whatever.txt';

	#[Test]
	public function saveFile():void{
		$this::assertSame(12, File::save(self::testFile, self::testData));
	}

	#[Test]
	public function saveInvalidDirectoryException():void{
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('target directory is not writable or does not extist');

		File::save(self::invalidFile, 'nope');
	}

	#[Test]
	#[Depends('saveFile')]
	public function loadFile():void{
		$this::assertSame(self::testData, File::load(self::testFile));
	}

	#[Test]
	public function loadInvalidFileException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid file path');

		File::load(self::invalidFile);
	}

	#[Test]
	#[Depends('loadFile')]
	public function deleteFile():void{
		$this::assertTrue(File::delete(self::testFile));
	}

	#[Test]
	public function deleteInvalidFileException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid file path');

		File::delete(self::invalidFile);
	}

	#[Test]
	public function createDirectory():void{
		// we can't realpath the whole path here because it does not exist yet
		$expected = realpath(self::testDir).DIRECTORY_SEPARATOR.'some dir';

		$this::assertSame($expected, Directory::create(self::testNewDir));
	}

	#[Test]
	public function createDirectoryEmptyNameException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid directory');

		Directory::create('');
	}

	#[Test]
	public function createDirectoryExistsAsFileException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('already exists as a file or link');

		Directory::create(self::testDir.'/.gitkeep');
	}

	#[Test]
	#[Depends('createDirectory')]
	public function removeDirectory():void{
		$this::assertTrue(Directory::remove(self::testNewDir));
	}

	#[Test]
	public function removeDirectoryEmptyNameException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid directory');

		Directory::remove('');
	}

}
