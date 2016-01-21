<?php
/**
 * Worker.php
 * PHP version 5.4
 * 2015-10-01
 *
 * @package   Foundry\Masonry-Website-Builder
 * @category
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 */


namespace Foundry\Masonry\Builder\Tests\PhpUnit\Workers\FileSystem\MakeDirectory;

use Foundry\Masonry\Module\FileSystem\FileSystem;
use Foundry\Masonry\Module\FileSystem\Tests\PhpUnit\FileSystemTestTrait;
use Foundry\Masonry\Module\FileSystem\Workers\MakeDirectory\Worker;
use Foundry\Masonry\Module\FileSystem\Workers\MakeDirectory\Description;
use Foundry\Masonry\Core\Task;
use Foundry\Masonry\Tests\PhpUnit\Core\AbstractWorkerTest;
use Foundry\Masonry\Tests\PhpUnit\DeferredWrapper;

/**
 * Class WorkerTest
 * @coversDefaultClass Foundry\Masonry\Module\FileSystem\Workers\MakeDirectory\Worker
 * @package Foundry\Masonry-Website-Builder
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
     * @uses Foundry\Masonry\Module\FileSystem\Workers\MakeDirectory\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredSuccess()
    {
        $deferredWrapper = new DeferredWrapper();

        // The rest of test data
        $testDir = 'schema://root/test';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('isDirectory')
            ->with($testDir)
            ->will($this->returnValue(false));
        $fileSystem
            ->expects($this->once())
            ->method('makeDirectory')
            ->with($testDir)
            ->will($this->returnValue(true));

        $description = new Description($testDir);
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
            "Created directory '{$testDir}'",
            (string)$deferredWrapper->getSuccessOutput()
        );

        $this->assertSame(
            "",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Creating directory '{$testDir}'",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\MakeDirectory\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredExists()
    {
        $deferredWrapper = new DeferredWrapper();

        // The rest of test data
        $testDir = 'schema://root/test';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('isDirectory')
            ->with($testDir)
            ->will($this->returnValue(true));

        $description = new Description($testDir);
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
            "Directory '{$testDir}' already exists",
            (string)$deferredWrapper->getSuccessOutput()
        );

        $this->assertSame(
            "",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Creating directory '{$testDir}'",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\MakeDirectory\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredFailure()
    {
        $deferredWrapper = new DeferredWrapper();

        // The rest of test data
        $testDir = 'schema://root/test';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('isDirectory')
            ->with($testDir)
            ->will($this->returnValue(false));
        $fileSystem
            ->expects($this->once())
            ->method('makeDirectory')
            ->with($testDir)
            ->will($this->returnValue(false));

        $description = new Description($testDir);
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
            "Directory '{$testDir}' could not be created",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Creating directory '{$testDir}'",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }

    /**
     * @test
     * @covers ::getDescriptionTypes
     */
    public function testGetDescriptionTypes()
    {
        $worker = new Worker();

        $this->assertCount(
            1,
            $worker->getDescriptionTypes()
        );
        $this->assertSame(
            Description::class,
            $worker->getDescriptionTypes()[0]
        );
    }
}
