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


namespace Foundry\Masonry\Builder\Tests\PhpUnit\Workers\FileSystem\Copy;

use Foundry\Masonry\Module\FileSystem\FileSystem;
use Foundry\Masonry\Module\FileSystem\Tests\PhpUnit\FileSystemTestTrait;
use Foundry\Masonry\Module\FileSystem\Workers\Copy\Worker;
use Foundry\Masonry\Module\FileSystem\Workers\Copy\Description;
use Foundry\Masonry\Core\Task;
use Foundry\Masonry\Tests\PhpUnit\Core\AbstractWorkerTest;
use Foundry\Masonry\Tests\PhpUnit\DeferredWrapper;

/**
 * Class WorkerTest
 * @coversDefaultClass Foundry\Masonry\Module\FileSystem\Workers\Copy\Worker
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
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Copy\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredSuccess()
    {
        $deferredWrapper = new DeferredWrapper();

        // The rest of test data
        $testFrom = 'schema://root/test';
        $testTo = 'schema://root/test-copy';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('copy')
            ->with($testFrom, $testTo)
            ->will($this->returnValue(true));

        $description = new Description($testFrom, $testTo);

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
            "Copied '{$testFrom}' to '{$testTo}'",
            (string)$deferredWrapper->getSuccessOutput()
        );

        $this->assertSame(
            "",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Copying '{$testFrom}' to '{$testTo}'",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Copy\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredFailure()
    {
        $deferredWrapper = new DeferredWrapper();

        // The rest of test data
        $testFrom = 'schema://root/test';
        $testTo = 'schema://root/test-copy';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('copy')
            ->with($testFrom, $testTo)
            ->will($this->throwException(new \Exception()));

        $description = new Description($testFrom, $testTo);

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
            "Could not copy '{$testFrom}' to '{$testTo}'",
            (string)$deferredWrapper->getFailureOutput()
        );

        // Last notification is of failure
        $this->assertSame(
            "Copy failed: ",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Copy\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredPartialSuccess()
    {
        $deferredWrapper = new DeferredWrapper();

        // The rest of test data
        $testFrom = 'schema://root/test';
        $testTo = 'schema://root/test-copy';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('copy')
            ->with($testFrom, $testTo)
            ->will($this->returnValue(false));

        $description = new Description($testFrom, $testTo);

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
            "Copied '{$testFrom}' to '{$testTo}'",
            (string)$deferredWrapper->getSuccessOutput()
        );

        $this->assertSame(
            "",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Directory permissions were not applied correctly",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }
}
