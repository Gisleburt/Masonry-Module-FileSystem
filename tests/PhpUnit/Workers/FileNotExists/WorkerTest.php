<?php
/**
 * WorkerTest.php
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 * @license
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Builder
 */


namespace Foundry\Masonry\Builder\Tests\PhpUnit\Workers\FileSystem\FileNotExists;

use Foundry\Masonry\Module\FileSystem\FileSystem;
use Foundry\Masonry\Module\FileSystem\Tests\PhpUnit\FileSystemTestTrait;
use Foundry\Masonry\Module\FileSystem\Workers\FileNotExists\Description;
use Foundry\Masonry\Module\FileSystem\Workers\FileNotExists\Worker;
use Foundry\Masonry\Core\Task;
use Foundry\Masonry\Tests\PhpUnit\Core\AbstractWorkerTest;
use Foundry\Masonry\Tests\PhpUnit\DeferredWrapper;

/**
 * Class WorkerTest
 * @package Masonry-Builder
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Builder
 * @coversDefaultClass Foundry\Masonry\Module\FileSystem\Workers\FileNotExists\Worker
 */
class WorkerTest extends AbstractWorkerTest
{

    use FileSystemTestTrait;

    /**
     * @return Worker
     */
    protected function getTestSubject()
    {
        return new Worker();
    }

    /**
     * @return string[]
     */
    protected function getDescriptionTypes()
    {
        return [
            Description::class
        ];
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\FileNotExists\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredSuccess()
    {
        $deferredWrapper = new DeferredWrapper();

        // Test Data
        $name = 'Some name';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('isFile')
            ->with($name)
            ->will($this->returnValue(false));

        $description = new Description($name);

        $task = new Task($description);
        $worker = $this->getTestSubject();
        $worker->setFileSystem($fileSystem);

        $processDeferred = $this->getObjectMethod($worker, 'processDeferred');

        /** @var \Generator $generator */
        $generator = $processDeferred($deferredWrapper->getDeferred(), $task);
        while ($generator->valid()) {
            $generator->next();
        }

        // Test messages
        $this->assertSame(
            "File '{$name}' does not exist",
            (string)$deferredWrapper->getSuccessOutput()
        );

        $this->assertSame(
            "",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Checking that file '{$name}' does not exist",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\FileNotExists\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredFailure()
    {
        $deferredWrapper = new DeferredWrapper();

        // Test Data
        $name = 'Some name';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('isFile')
            ->with($name)
            ->will($this->returnValue(true));

        $description = new Description($name);

        $task = new Task($description);
        $worker = $this->getTestSubject();
        $worker->setFileSystem($fileSystem);

        $processDeferred = $this->getObjectMethod($worker, 'processDeferred');

        /** @var \Generator $generator */
        $generator = $processDeferred($deferredWrapper->getDeferred(), $task);
        while ($generator->valid()) {
            $generator->next();
        }

        // Test messages
        $this->assertSame(
            "",
            (string)$deferredWrapper->getSuccessOutput()
        );

        $this->assertSame(
            "File '{$name}' exists",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Checking that file '{$name}' does not exist",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }
}
