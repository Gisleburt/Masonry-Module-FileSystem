<?php
/**
 * DescriptionTest.php
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 * @license
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Builder
 */


namespace Foundry\Masonry\Module\FileSystem\Tests\PhpUnit\Workers\FileSystem\Template;

use Foundry\Masonry\Module\FileSystem\Workers\Template\Description;
use Foundry\Masonry\Tests\PhpUnit\Core\AbstractDescriptionTest;


/**
 * Class DescriptionTest
 * @package Masonry-Builder
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Builder
 * @coversDefaultClass \Foundry\Masonry\Module\FileSystem\Workers\Template\Description
 */
class DescriptionTest extends AbstractDescriptionTest
{

    /**
     * @test
     * @covers ::createFromParameters
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Template\Description::__construct
     * @return void
     */
    public function testCreateFromParameters()
    {
        $template = 'Template file';
        $target = 'Target file';
        $params = [
            'key-1' => 'value-1',
        ];
        $parameters = [
            'template' => $template,
            'target'   => $target,
            'params'   => $params,
        ];
        $description = Description::createFromParameters($parameters);

        $this->assertSame(
            $template,
            $this->getObjectAttribute($description, 'template')
        );
        $this->assertSame(
            $target,
            $this->getObjectAttribute($description, 'target')
        );
        $this->assertSame(
            $params,
            $this->getObjectAttribute($description, 'params')
        );
    }

    /**
     * @test
     * @covers ::__construct
     * @return void
     */
    public function testConstruct()
    {
        $template = 'Template file';
        $target = 'Target file';
        $params = [
            'key-1' => 'value-1',
        ];
        $description = new Description($template, $target, $params);

        $this->assertSame(
            $template,
            $this->getObjectAttribute($description, 'template')
        );
        $this->assertSame(
            $target,
            $this->getObjectAttribute($description, 'target')
        );
        $this->assertSame(
            $params,
            $this->getObjectAttribute($description, 'params')
        );
    }

    /**
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $template is required
     * @return void
     */
    public function testConstructMissingTemplate()
    {
        $template = '';
        $target = '';
        $params = [];
        $description = new Description($template, $target, $params);

        $this->assertSame(
            $template,
            $this->getObjectAttribute($description, 'template')
        );
        $this->assertSame(
            $target,
            $this->getObjectAttribute($description, 'target')
        );
        $this->assertSame(
            $params,
            $this->getObjectAttribute($description, 'params')
        );
    }

    /**
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $target is required
     * @return void
     */
    public function testConstructMissingTarget()
    {
        $template = 'Template file';
        $target = '';
        $params = [];
        $description = new Description($template, $target, $params);

        $this->assertSame(
            $template,
            $this->getObjectAttribute($description, 'template')
        );
        $this->assertSame(
            $target,
            $this->getObjectAttribute($description, 'target')
        );
        $this->assertSame(
            $params,
            $this->getObjectAttribute($description, 'params')
        );
    }

    /**
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $params are required
     * @return void
     */
    public function testConstructMissingParams()
    {
        $template = 'Template file';
        $target = 'Target file';
        $params = [];
        $description = new Description($template, $target, $params);

        $this->assertSame(
            $template,
            $this->getObjectAttribute($description, 'template')
        );
        $this->assertSame(
            $target,
            $this->getObjectAttribute($description, 'target')
        );
        $this->assertSame(
            $params,
            $this->getObjectAttribute($description, 'params')
        );
    }

    /**
     * @test
     * @covers ::getTemplate
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Template\Description::__construct
     * @return void
     */
    public function testGetTemplate()
    {
        $template = 'Template file';
        $target = 'Target file';
        $params = [
            'key-1' => 'value-1',
        ];
        $description = new Description($template, $target, $params);

        $this->assertSame(
            $template,
            $description->getTemplate()
        );
    }

    /**
     * @test
     * @covers ::getTarget
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Template\Description::__construct
     * @return void
     */
    public function testGetTarget()
    {
        $template = 'Template file';
        $target = 'Target file';
        $params = [
            'key-1' => 'value-1',
        ];
        $description = new Description($template, $target, $params);

        $this->assertSame(
            $target,
            $description->getTarget()
        );
    }

    /**
     * @test
     * @covers ::getParams
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Template\Description::__construct
     * @return void
     */
    public function testGetParams()
    {
        $template = 'Template file';
        $target = 'Target file';
        $params = [
            'key-1' => 'value-1',
        ];
        $description = new Description($template, $target, $params);

        $this->assertSame(
            $params,
            $description->getParams()
        );
    }

}
