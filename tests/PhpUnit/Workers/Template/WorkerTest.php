<?php
/**
 * WorkerTest.php
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 * @license
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Builder
 */


namespace Foundry\Masonry\Builder\Tests\PhpUnit\Workers\FileSystem\Template;

use Foundry\Masonry\Module\FileSystem\FileSystem;
use Foundry\Masonry\Module\FileSystem\Workers\Template\Description;
use Foundry\Masonry\Module\FileSystem\Workers\Template\Worker;
use Foundry\Masonry\Core\Task;
use Foundry\Masonry\Tests\PhpUnit\Core\AbstractWorkerTest;
use Foundry\Masonry\Tests\PhpUnit\DeferredWrapper;


/**
 * Class WorkerTest
 * ${CARET}
 * @package Masonry-Builder
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Builder
 * @coversDefaultClass \Foundry\Masonry\Module\FileSystem\Workers\Template\Worker
 */
class WorkerTest extends AbstractWorkerTest
{
    protected function getDescriptionTypes()
    {
        return [
            Description::class
        ];
    }

    protected function getTestSubject()
    {
        return new Worker();
    }


    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Template\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredSuccess()
    {
        $deferredWrapper = new DeferredWrapper();

        // Test Data
        $template = 'Template file';
        $target = 'Target file';
        $params = [
            'key-1' => 'value-1',
        ];
        $input = 'Target file with {{ key-1 }}';
        $output = 'Target file with value-1';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('getFileContents')
            ->with($template)
            ->will($this->returnValue($input));
        $fileSystem
            ->expects($this->once())
            ->method('write')
            ->with($target, $output)
            ->will($this->returnValue(true));

        $description = new Description($template, $target, $params);

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
            "Created '{$target}' from template '{$template}'",
            (string)$deferredWrapper->getSuccessOutput()
        );

        $this->assertSame(
            "",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "File content generated, saving to {$target}",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Template\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredFailure()
    {
        $deferredWrapper = new DeferredWrapper();

        // Test Data
        $template = 'Template file';
        $target = 'Target file';
        $params = [
            'key-1' => 'value-1',
        ];
        $input = 'Target file with {{ key-1 }}';
        $output = 'Target file with value-1';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('getFileContents')
            ->with($template)
            ->will($this->returnValue($input));
        $fileSystem
            ->expects($this->once())
            ->method('write')
            ->with($target, $output)
            ->will($this->returnValue(false));

        $description = new Description($template, $target, $params);

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
            "Could not create '{$target}' from template '{$template}'",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "File content generated, saving to {$target}",
            (string)$deferredWrapper->getNotificationOutput()
        );
    }

    /**
     * @test
     * @covers ::processDeferred
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Template\Description
     * @uses Foundry\Masonry\Module\FileSystem\FileSystemTrait
     * @return void
     */
    public function testProcessDeferredException()
    {
        $deferredWrapper = new DeferredWrapper();

        // Test Data
        $template = 'Template file';
        $target = 'Target file';
        $params = [
            'key-1' => 'value-1',
        ];
        $input = 'Target file with {{ key-1 }}';
        $output = 'Target file with value-1';
        $exceptionMessage = 'Exception Message';

        /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject $fileSystem */
        $fileSystem = $this->getMock(FileSystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('getFileContents')
            ->with($template)
            ->will($this->returnValue($input));
        $fileSystem
            ->expects($this->once())
            ->method('write')
            ->with($target, $output)
            ->will($this->throwException(new \Exception($exceptionMessage)));

        $description = new Description($template, $target, $params);

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
            "Could not create '{$target}' from template '{$template}'",
            (string)$deferredWrapper->getFailureOutput()
        );

        $this->assertSame(
            "Could not write template: {$exceptionMessage}",
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
