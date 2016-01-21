<?php
/**
 * FileSystemTestTrait.php
 *
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 * @license
 * @see       https://github.com/Visionmongers/
 */


namespace Foundry\Masonry\Module\FileSystem\Tests\PhpUnit;

use Foundry\Masonry\Module\FileSystem\FileSystem;
use Foundry\Masonry\Module\FileSystem\FileSystemTrait;


/**
 * Trait FileSystemTestTrait
 * ${CARET}
 * @package Masonry-Builder
 * @see     https://github.com/Visionmongers/
 */
trait FileSystemTestTrait
{

    /**
     *
     * @return FileSystemTrait
     */
    protected abstract function getTestSubject();

    /**
     * @covers ::setFileSystem
     * @return void
     */
    public function testSetFileSystem()
    {
        $fileSystemUser = $this->getTestSubject();

        $fileSystem = new FileSystem();
        $fileSystemUser->setFileSystem($fileSystem);

        $this->assertSame(
            $fileSystem,
            $this->getObjectAttribute($fileSystemUser, 'fileSystem')
        );
    }

    /**
     * @covers ::getFileSystem
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait::setFileSystem
     * @return void
     */
    public function testGetFileSystem()
    {
        $fileSystemUser = $this->getTestSubject();

        $fileSystem = new FileSystem();

        $getFileSystem = $this->getObjectMethod($fileSystemUser, 'getFileSystem');

        $this->assertInstanceOf(
            FileSystem::class,
            $getFileSystem()
        );

        $this->assertNotSame(
            $fileSystem,
            $getFileSystem()
        );

        $fileSystemUser->setFileSystem($fileSystem);

        $this->assertInstanceOf(
            FileSystem::class,
            $getFileSystem()
        );

        $this->assertSame(
            $fileSystem,
            $getFileSystem()
        );
    }
}
