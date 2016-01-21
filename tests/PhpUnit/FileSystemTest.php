<?php
/**
 * FileSystemTest.php
 *
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 * @license   MIT
 * @see       https://github.com/Visionmongers/
 */


namespace Foundry\Masonry\Module\FileSystem\Tests\PhpUnit;


use Foundry\Masonry\Module\FileSystem\FileSystem;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;

/**
 * Class FileSystemTest
 * @package Foundry\Masonry-Builder
 * @see     https://github.com/Visionmongers/Masonry-Builder
 * @coversDefaultClass Foundry\Masonry\Module\FileSystem\FileSystem
 */
class FileSystemTest extends TestCase
{

    /**
     * @covers ::copy
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::makeDirectory
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::isDirectory
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::isFile
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::changeMode
     * @throws \Exception
     * @return void
     */
    public function testCopy()
    {
        $root = 'root';
        $from = 'testDir';
        $to = 'testDirCopy';

        $mockFileSystem = vfsStream::setup($root, 0777);
        $mockFileSystem->addChild(vfsStream::create([
            $from => [
                'secondLevelDir' => [
                    'secondLevelFile.txt' => 'second level file contents'
                ],
                'file.txt' => 'file contents'
            ]
        ]));

        $fromUrl = vfsStream::url('root/' . $from);
        $toUrl = vfsStream::url('root/' . $to);

        $fileSystemHelper = new FileSystem();

        $this->assertTrue(
            is_file($fromUrl . '/file.txt')
        );
        $this->assertFalse(
            is_file($toUrl . '/file.txt')
        );
        $this->assertTrue(
            is_file($fromUrl . '/secondLevelDir/secondLevelFile.txt')
        );
        $this->assertFalse(
            is_file($toUrl . '/secondLevelDir/secondLevelFile.txt')
        );

        $this->assertTrue(
            $fileSystemHelper->copy(
                $fromUrl,
                $toUrl
            )
        );

        $this->assertTrue(
            is_file($fromUrl . '/file.txt')
        );
        $this->assertTrue(
            is_file($toUrl . '/file.txt')
        );
        $this->assertTrue(
            is_file($fromUrl . '/secondLevelDir/secondLevelFile.txt')
        );
        $this->assertTrue(
            is_file($toUrl . '/secondLevelDir/secondLevelFile.txt')
        );
    }

    /**
     * @covers ::copy
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::makeDirectory
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::isDirectory
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::isFile
     * @throws \Exception
     * @expectedException \Exception
     * @expectedExceptionMessage Could not copy file
     * @return void
     */
    public function testCopyFileException()
    {
        $root = 'root';
        $from = 'testDir';
        $to = 'testDirCopy';

        $mockFileSystem = vfsStream::setup($root, 0000);
        $mockFileSystem->addChild(vfsStream::create([
            $from => [
                'file.txt' => 'file contents'
            ]
        ]));

        $fromUrl = vfsStream::url('root/' . $from);
        $toUrl = vfsStream::url('root/' . $to);

        $fileSystemHelper = new FileSystem();

        $this->assertTrue(
            is_file($fromUrl . '/file.txt')
        );

        // This should throw an exception
        $fileSystemHelper->copy(
            $fromUrl . '/file.txt',
            $toUrl . '/file.txt'
        );
    }

    /**
     * @covers ::copy
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::makeDirectory
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::isDirectory
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::isFile
     * @throws \Exception
     * @expectedException \Exception
     * @expectedExceptionMessage Could not create directory
     * @return void
     */
    public function testCopyDirException()
    {
        $root = 'root';
        $from = 'testDir';
        $to = 'testDirCopy';

        $mockFileSystem = vfsStream::setup($root, 0000);
        $mockFileSystem->addChild(vfsStream::create([
            $from => [
                'file.txt' => 'file contents'
            ]
        ]));

        $fromUrl = vfsStream::url('root/' . $from);
        $toUrl = vfsStream::url('root/' . $to);

        $fileSystemHelper = new FileSystem();

        $this->assertTrue(
            is_file($fromUrl . '/file.txt')
        );

        // This should throw an exception
        $fileSystemHelper->copy(
            $fromUrl,
            $toUrl
        );
    }

    /**
     * @covers ::copy
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::makeDirectory
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::isDirectory
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::isFile
     * @throws \Exception
     * @expectedException \Exception
     * @expectedExceptionMessage does not exist or is not accessible
     * @return void
     */
    public function testCopyNotExistsException()
    {
        $root = 'root';
        $from = 'testDir';
        $to = 'testDirCopy';

        $mockFileSystem = vfsStream::setup($root, 0000);

        $fromUrl = vfsStream::url('root/' . $from);
        $toUrl = vfsStream::url('root/' . $to);

        $fileSystemHelper = new FileSystem();

        // This should throw an exception
        $fileSystemHelper->copy(
            $fromUrl,
            $toUrl
        );
    }

    /**
     * @test
     * @covers ::delete
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::isDirectory
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::isFile
     * @uses Foundry\Masonry\Module\FileSystem\FileSystem::isSymlink
     * @return void
     */
    public function testDelete()
    {
        $root = 'root';
        $delDir = 'testDir';

        $mockFileSystem = vfsStream::setup($root, 0777);
        $mockFileSystem->addChild(vfsStream::create([
            $delDir => [
                'secondLevelDir' => [
                    'secondLevelFile.txt' => 'second level file contents'
                ],
                'file.txt' => 'file contents'
            ]
        ]));


        $delDirUrl = vfsStream::url('root/' . $delDir);

        $fileSystemHelper = new FileSystem();

        // Test failures
        $this->assertFalse(
            $fileSystemHelper->delete($delDirUrl . '/not-a-file.txt')
        );


        // Test successes
        $this->assertTrue(
            is_dir($delDirUrl)
        );
        $this->assertTrue(
            is_file($delDirUrl . '/file.txt')
        );
        $this->assertTrue(
            is_dir($delDirUrl . '/secondLevelDir')
        );
        $this->assertTrue(
            is_file($delDirUrl . '/secondLevelDir/secondLevelFile.txt')
        );

        $this->assertTrue(
            $fileSystemHelper->delete($delDirUrl)
        );

        $this->assertFalse(
            is_file($delDirUrl . '/secondLevelDir/secondLevelFile.txt')
        );
        $this->assertFalse(
            is_dir($delDirUrl . '/secondLevelDir')
        );
        $this->assertFalse(
            is_file($delDirUrl . '/file.txt')
        );
        $this->assertFalse(
            is_dir($delDirUrl)
        );

    }

    /**
     * @test
     * @covers ::makeDirectory
     * @return void
     */
    public function testMakeDirectory()
    {
        $root = 'root';
        $newDir = 'testDir';

        vfsStream::setup($root, 0777);
        $newDirUrl = vfsStream::url('root/' . $newDir);

        $fileSystemHelper = new FileSystem();

        $this->assertFalse(
            is_dir($newDirUrl)
        );

        $this->assertTrue(
            $fileSystemHelper->makeDirectory($newDirUrl)
        );

        $this->assertTrue(
            is_dir($newDirUrl)
        );
    }

    /**
     * @test
     * @covers ::move
     * @return void
     */
    public function testMove()
    {
        $root = 'root';
        $from = 'testDir';
        $to = 'testDirMoved';

        $mockFileSystem = vfsStream::setup($root, 0777);
        $mockFileSystem->addChild(vfsStream::create([
            $from => [
                'secondLevelDir' => [
                    'secondLevelFile.txt' => 'second level file contents'
                ],
                'file.txt' => 'file contents'
            ]
        ]));

        $fromUrl = vfsStream::url('root/' . $from);
        $toUrl = vfsStream::url('root/' . $to);

        $fileSystemHelper = new FileSystem();

        $this->assertTrue(
            is_file($fromUrl . '/file.txt')
        );
        $this->assertFalse(
            is_file($toUrl . '/file.txt')
        );
        $this->assertTrue(
            is_file($fromUrl . '/secondLevelDir/secondLevelFile.txt')
        );
        $this->assertFalse(
            is_file($toUrl . '/secondLevelDir/secondLevelFile.txt')
        );

        $this->assertTrue(
            $fileSystemHelper->move(
                $fromUrl,
                $toUrl
            )
        );

        $this->assertFalse(
            is_file($fromUrl . '/file.txt')
        );
        $this->assertTrue(
            is_file($toUrl . '/file.txt')
        );
        $this->assertFalse(
            is_file($fromUrl . '/secondLevelDir/secondLevelFile.txt')
        );
        $this->assertTrue(
            is_file($toUrl . '/secondLevelDir/secondLevelFile.txt')
        );
    }

    /**
     * @test
     * @covers ::isDirectory
     * @return void
     */
    public function testIsDirectory()
    {
        $root = 'root';
        $realDir = 'real-dir';
        $fakeDir = 'fake-dir';
        $realFile = 'real-file.txt';

        $mockFileSystem = vfsStream::setup($root, 0777);
        $mockFileSystem->addChild(vfsStream::create([
            $realDir => [
                $realFile => 'file content'
            ]
        ]));

        $fileSystemHelper = new FileSystem();

        $this->assertTrue(
            $fileSystemHelper->isDirectory(vfsStream::url("$root/$realDir"))
        );
        $this->assertFalse(
            $fileSystemHelper->isDirectory(vfsStream::url("$root/$fakeDir"))
        );
        $this->assertFalse(
            $fileSystemHelper->isDirectory(vfsStream::url("$root/$realDir/$realFile"))
        );
    }

    /**
     * @test
     * @covers ::isFile
     * @return void
     */
    public function testIsFile()
    {
        $root = 'root';
        $realDir = 'real-dir';
        $fakeDir = 'fake-dir';
        $realFile = 'real-file.txt';

        $mockFileSystem = vfsStream::setup($root, 0777);
        $mockFileSystem->addChild(vfsStream::create([
            $realDir => [
                $realFile => 'file content'
            ]
        ]));

        $fileSystemHelper = new FileSystem();

        $this->assertFalse(
            $fileSystemHelper->isFile(vfsStream::url("$root/$realDir"))
        );
        $this->assertFalse(
            $fileSystemHelper->isFile(vfsStream::url("$root/$fakeDir/$realFile"))
        );
        $this->assertTrue(
            $fileSystemHelper->isFile(vfsStream::url("$root/$realDir/$realFile"))
        );
    }

//    /**
//     * @test
//     * @covers ::isSymlink
//     * @return void
//     */
//    public function testIsSymlink()
//    {
//
//    }

    /**
     * @test
     * @covers ::changeMode
     * @return void
     */
    public function testChangeMode()
    {
        $root = 'root';
        $file = 'file';
        $startMode = 0660;
        $newMode = 0400;

        $mockFile = new vfsStreamFile($file, $startMode);
        $mockFileSystem = vfsStream::setup($root, 0777);
        $mockFileSystem->addChild($mockFile);

        $location = vfsStream::url("$root/$file");

        $fileSystemHelper = new FileSystem();
        $this->assertTrue(
            $fileSystemHelper->changeMode($location, $newMode)
        );

        $this->assertSame(
            $newMode,
            $mockFile->getPermissions()
        );
    }

    /**
     * @test
     * @covers ::getFileContents
     * @return void
     */
    public function testGetFileContents()
    {
        $root = 'root';
        $realDir = 'real-dir';
        $realFile = 'real-file.txt';
        $fileContent = 'file content';

        $mockFileSystem = vfsStream::setup($root, 0777);
        $mockFileSystem->addChild(vfsStream::create([
            $realDir => [
                $realFile => $fileContent
            ]
        ]));

        $fileSystemHelper = new FileSystem();
        $this->assertSame(
            $fileContent,
            $fileSystemHelper->getFileContents(vfsStream::url("$root/$realDir/$realFile"))
        );
    }

    /**
     * @test
     * @covers ::getFileContents
     * @expectedException \RuntimeException
     * @expectedExceptionMessage could not be read
     * @return void
     */
    public function testGetFileContentsException()
    {
        $root = 'root';
        $realDir = 'real-dir';
        $missingFile = 'real-file.txt';

        $mockFileSystem = vfsStream::setup($root, 0777);
        $mockFileSystem->addChild(vfsStream::create([
            $realDir => [

            ]
        ]));

        $fileSystemHelper = new FileSystem();
        $fileSystemHelper->getFileContents(vfsStream::url("$root/$realDir/$missingFile"));

    }
}
