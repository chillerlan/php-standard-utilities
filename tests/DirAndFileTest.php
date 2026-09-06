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
use function array_keys;
use function realpath;
use function sprintf;
use const DIRECTORY_SEPARATOR;

#[CoversClass(Directory::class)]
#[CoversClass(File::class)]
final class DirAndFileTest extends TestCase{

	protected const string testDir     = __DIR__.'/filetest';
	protected const string testFile    = self::testDir.'/test.txt';
	protected const string testData    = 'Hello world!';
	protected const string testNewDir  = self::testDir.DIRECTORY_SEPARATOR.'some dir';
	protected const string invalidDir  = DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'bar';
	protected const string invalidFile = self::invalidDir.DIRECTORY_SEPARATOR.'whatever.txt';

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
		$this->expectExceptionMessage('invalid path');

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
		$this->expectExceptionMessage('invalid path');

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

	#[Test]
	public function relativePath():void{
		$relative = Directory::relativePath(__DIR__.'/filetest/.gitkeep', __DIR__.'/..', '/');

		$this::assertSame('tests/filetest', $relative);
	}

	#[Test]
	public function filelist():void{
		$dir = __DIR__.'/..';
		// filter by extension
		$list = Directory::filelist($dir, ['dist']);
		$this::assertSame(['phpcs.xml.dist', 'phpmd.xml.dist', 'phpunit.xml.dist'], array_keys($list));
		// no extension
		$list = Directory::filelist($dir, ['']);
		$this::assertSame(['LICENSE'], array_keys($list));
		// files starting with a dot are extensions
		$list = Directory::filelist($dir, ['gitignore']);
		$this::assertSame(['.gitignore'], array_keys($list));
		// filter by part of name
		$list = Directory::filelist($dir, null, 'git');
		$this::assertSame(['.gitattributes', '.gitignore'], array_keys($list));
	}

	#[Test]
	public function clear():void{

		for($i = 0; $i < 3; $i++){
			File::save(sprintf('%s/file%s.txt', self::testDir, $i), 'testfile');
		}

		$deleted = Directory::clear(self::testDir, ['txt']);

		$this::assertSame($deleted, ['file0.txt' => true, 'file1.txt' => true, 'file2.txt' => true]);
		$this::assertSame(['.gitkeep'], array_keys(Directory::filelist(self::testDir)));
	}

}
