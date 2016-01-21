<?php
/**
 * Worker.php
 * PHP version 5.4
 * 2015-11-26
 *
 * @package   Foundry\Masonry-Website-Builder
 * @category
 * @author    Vladimir Hraban <vladimir.hraban@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 */


namespace Foundry\Masonry\Module\FileSystem\Workers\Template;

use Foundry\Masonry\Core\AbstractWorker;
use Foundry\Masonry\Core\Notification;
use Foundry\Masonry\Module\FileSystem\FileSystemTrait;
use Foundry\Masonry\Interfaces\TaskInterface;
use React\Promise\Deferred;

class Worker extends AbstractWorker
{

    use FileSystemTrait;

    /**
     * Load contents of a template, replace placeholders with passed values and save to a target file
     *
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
                    "Loading template {$description->getTemplate()}'",
                    Notification::PRIORITY_NORMAL
                )
            );

            $contents = $this->getFileSystem()->getFileContents($description->getTemplate());

            $keyValuePairs = [];
            foreach ($description->getParams() as $key => $value) {
                $keyValuePairs["{{ ". $key . " }}"] = $value;
            }

            $processedContents = strtr($contents, $keyValuePairs);

            $deferred->notify("File content generated, saving to {$description->getTarget()}");

            if($this->getFileSystem()->write($description->getTarget(), $processedContents)) {
                $deferred->resolve("Created '{$description->getTarget()}' from template '{$description->getTemplate()}'");
                return;
            }

        } catch (\Exception $e) {
            $deferred->notify("Could not write template: " . $e->getMessage());
        }
        $deferred->reject(
            "Could not create '{$description->getTarget()}' from template '{$description->getTemplate()}'"
        );

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
