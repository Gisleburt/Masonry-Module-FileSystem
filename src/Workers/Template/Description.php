<?php
/**
 * Description.php
 * PHP version 5.4
 * 2015-11-26
 *
 * @package   Foundry\Masonry-Website-Builder
 * @category
 * @author    Vladimir Hraban <vladimir.hraban@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 */


namespace Foundry\Masonry\Module\FileSystem\Workers\Template;

use Foundry\Masonry\Core\AbstractDescription;

/**
 * Class Description
 *
 * @package Foundry\Masonry-Website-Builder
 */
class Description extends AbstractDescription
{
    /**
     * @var string The template file to load
     */
    protected $template;

    /**
     * @var string The target file to save to
     */
    protected $target;

    /**
     * @var array Key-value pairs to replace placeholders
     */
    protected $params;

    public function __construct($template, $target, array $params = [])
    {
        if (!$template) {
            throw new \InvalidArgumentException('$template is required');
        }

        if (!$target) {
            throw new \InvalidArgumentException('$target is required');
        }

        if (!$params) {
            throw new \InvalidArgumentException('$params are required');
        }

        $this->template = $template;
        $this->target = $target;
        $this->params = $params;
    }

    /**
     * Get template
     *
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get target
     *
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
