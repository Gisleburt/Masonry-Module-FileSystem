<?php
/**
 * Worker.php
 * PHP version 5.4
 * 2015-09-30
 *
 * @package   Foundry\Masonry-Website-Builder
 * @category
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 */


namespace Foundry\Masonry\Module\FileSystem\Workers\Move;

use Foundry\Masonry\Core\AbstractWorker;
use Foundry\Masonry\Core\Notification;
use Foundry\Masonry\Module\FileSystem\FileSystemTrait;
use Foundry\Masonry\Interfaces\TaskInterface;
use React\Promise\Deferred;

class Worker extends AbstractWorker
{
    use FileSystemTrait;

    /**
     * Make a directory as described in the task description
     * @param Deferred $deferred
     * @param TaskInterface $task
     * @return bool
     */
    protected function processDeferred(Deferred $deferred, TaskInterface $task)
    {
        yield;

        /** @var Description $description */
        $description = $task->getDescription();

        $deferred->notify(
            new Notification(
                "Moving '{$description->getFrom()}' to '{$description->getTo()}'",
                Notification::PRIORITY_NORMAL
            )
        );

        if ($this->getFileSystem()->move($description->getFrom(), $description->getTo())) {
            $deferred->resolve("Moved '{$description->getFrom()}' to '{$description->getTo()}'");
            return;
        }

        $deferred->reject("Could not move '{$description->getFrom()}' to '{$description->getTo()}'");
    }

    /**
     * Lists, as strings, the class/interface names this worker can handle.
     * Each worker should be responsible for one type of Task, however there might be multiple ways to describe the
     * task. The names of each possible description should be returned here.
     * @return string[]
     */
    public function getDescriptionTypes()
    {
        return [
            Description::class
        ];
    }
}
