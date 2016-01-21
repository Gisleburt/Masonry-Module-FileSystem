<?php
/**
 * DescriptionTest.php
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 * @license
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Builder
 */


namespace Foundry\Masonry\Builder\Tests\PhpUnit\Workers\FileSystem\FileExists;

use Foundry\Masonry\Module\FileSystem\Workers\FileExists\Description;
use Foundry\Masonry\Tests\PhpUnit\Core\AbstractDescriptionTest;

/**
 * Class DescriptionTest
 * @package Masonry-Builder
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Builder
 * @coversDefaultClass \Foundry\Masonry\Module\FileSystem\Workers\FileExists\Description
 */
class DescriptionTest extends AbstractDescriptionTest
{

    /**
     * @test
     * @covers ::createFromParameters
     * @uses Foundry\Masonry\Module\FileSystem\Workers\FileExists\Description::__construct
     * @return void
     */
    public function testCreateFromParameters()
    {

        $name = 'Some name';
        $parameters = ['name' => $name];
        $description = Description::createFromParameters($parameters);
        $this->assertSame(
            $name,
            $this->getObjectAttribute($description, 'name')
        );
    }

    /**
     * @test
     * @covers ::__construct
     * @return void
     */
    public function testConstruct()
    {
        $name = 'Some name';
        $description = new Description($name);
        $this->assertSame(
            $name,
            $this->getObjectAttribute($description, 'name')
        );
    }

    /**
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $name is required
     * @return void
     */
    public function testConstructInvalidName()
    {
        $name = '';
        new Description($name);
    }

    /**
     * @test
     * @covers ::getName
     * @uses Foundry\Masonry\Module\FileSystem\Workers\FileExists\Description::__construct
     * @return void
     */
    public function testGetName()
    {
        $name = 'Some name';
        $description = new Description($name);
        $this->assertSame(
            $name,
            $description->getName()
        );
    }
}
