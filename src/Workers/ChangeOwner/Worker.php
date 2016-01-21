<?php
/**
 * Worker.php
 * PHP version 5.4
 * 2015-12-01
 *
 * @package   Foundry\Masonry-Website-Builder
 * @category
 * @author    Vladimir Hraban <vladimir.hraban@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 */


namespace Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner;

use Foundry\Masonry\Core\AbstractWorker;
use Foundry\Masonry\Core\Notification;
use Foundry\Masonry\Module\FileSystem\FileSystemTrait;
use Foundry\Masonry\Interfaces\TaskInterface;
use React\Promise\Deferred;

class Worker extends AbstractWorker
{

    use FileSystemTrait;

    /**
     * Change the target owner
     * @param Deferred $deferred
     * @param TaskInterface $task
     * @return bool
     */
    protected function processDeferred(Deferred $deferred, TaskInterface $task)
    {
        yield;

        /** @var Description $description */
        $description = $task->getDescription();

        try {
            $deferred->notify(
                new Notification(
                    "Changing '{$description->getTarget()}' owner to '{$description->getOwner()}'",
                    Notification::PRIORITY_NORMAL
                )
            );

            if ($this->getFileSystem()->changeOwner($description->getTarget(), $description->getOwner())) {
                $deferred->resolve(
                    "Changed '{$description->getTarget()}' owner to '{$description->getOwner()}' successfully"
                );
                return;
            }
        } catch (\Exception $e) {
        // do noting, we reject below
            $deferred->reject("Failed to change the owner of {$description->getTarget()}: " . $e->getMessage());
            return;
        }

        $deferred->reject("Failed to change the owner of {$description->getTarget()} to {$description->getOwner()}");

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
