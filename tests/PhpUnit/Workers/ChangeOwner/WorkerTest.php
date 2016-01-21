<?php
/**
 * WorkerTest.php
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 * @license
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Builder
 */


namespace Foundry\Masonry\Module\FileSystem\Tests\PhpUnit\Workers\ChangeOwner;

use Foundry\Masonry\Core\Task;
use Foundry\Masonry\Module\FileSystem\FileSystem;
use Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner\Description;
use Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner\Worker;
use Foundry\Masonry\Tests\PhpUnit\Core\AbstractWorkerTest;
use Foundry\Masonry\Tests\PhpUnit\DeferredWrapper;

/**
 * Class WorkerTest
 * @package Masonry-Builder
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Builder
 * @coversDefaultClass Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner\Worker
 */
class WorkerTest extends AbstractWorkerTest
{

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
     * @uses Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredSuccess()
    {
        $deferredWrapper = new DeferredWrapper();

        // Test Data
        $target = 'Target file or location';
        $owner = 'Who to make the new owner';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('changeOwner')
            ->with($target, $owner)
            ->will($this->returnValue(true));

        $description = new Description($target, $owner);

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
            "Changed '{$target}' owner to '{$owner}' successfully",
            (string)$deferredWrapper->getSuccessOutput()
        );

        $this->assertSame(
            "",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Changing '{$target}' owner to '{$owner}'",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredFailure()
    {
        $deferredWrapper = new DeferredWrapper();

        // Test Data
        $target = 'Target file or location';
        $owner = 'Who to make the new owner';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('changeOwner')
            ->with($target, $owner)
            ->will($this->returnValue(false));

        $description = new Description($target, $owner);

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
            "Failed to change the owner of {$target} to {$owner}",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Changing '{$target}' owner to '{$owner}'",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredException()
    {
        $deferredWrapper = new DeferredWrapper();

        // Test Data
        $target = 'Target file or location';
        $owner = 'Who to make the new owner';
        $exceptionMessage = 'Something went wrong';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('changeOwner')
            ->with($target, $owner)
            ->will($this->throwException(new \Exception($exceptionMessage)));

        $description = new Description($target, $owner);

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
            "Failed to change the owner of {$target}: {$exceptionMessage}",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Changing '{$target}' owner to '{$owner}'",
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
