<?php
/**
 * Description.php
 * PHP version 5.4
 * 2015-12-01
 *
 * @package   Foundry\Masonry-Website-Builder
 * @category
 * @author    Vladimir Hraban <vladimir.hraban@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 */


namespace Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner;

use Foundry\Masonry\Core\AbstractDescription;


/**
 * Class Description
 *
 * @package Foundry\Masonry-Website-Builder
 */
class Description extends AbstractDescription
{
    /**
     * @var string The target file/directory to change permissions for
     */
    protected $target;

    /**
     * @var string The owner. Following *nix style, it can be either user or user:group
     */
    protected $owner;

    /**
     * @param string $name The name of the file
     */
    public function __construct($target, $owner)
    {
        if (!$target) {
            throw new \InvalidArgumentException('$target is required');
        }

        if (!$owner) {
            throw new \InvalidArgumentException('$owner is required');
        }

        $this->target = $target;
        $this->owner = $owner;
    }

    /**
     * Get the target file/directory to change permissions for
     * @return string The target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Get owner. Following *nix style, it can be either user or user:group
     * @return string The owner
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
