<?php
/**
 * Module.php
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2016 The Foundry Visionmongers
 * @license
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Module-FileSystem
 */

namespace Foundry\Masonry\Module\FileSystem;

use Foundry\Masonry\Core\Module\WorkerModule;
use Foundry\Masonry\Module\FileSystem\Workers;

/**
 * Class Module
 * ${CARET}
 * @package Masonry-Module-FileSystem
 * @see     https://github.com/TheFoundryVisionmongers/Masonry-Module-FileSystem
 */
class Module extends WorkerModule
{

    /**
     * Module constructor.
     * @param array $workers
     */
    public function __construct(array $workers)
    {
        $moduleWorkers = [
            new Workers\ChangeOwner\Worker(),
            new Workers\Copy\Worker(),
            new Workers\Delete\Worker(),
            new Workers\FileExists\Worker(),
            new Workers\FileNotExists\Worker(),
            new Workers\MakeDirectory\Worker(),
            new Workers\Move\Worker(),
            new Workers\Template\Worker(),
        ];
        $workers += $moduleWorkers;
        parent::__construct($workers);
    }

}
