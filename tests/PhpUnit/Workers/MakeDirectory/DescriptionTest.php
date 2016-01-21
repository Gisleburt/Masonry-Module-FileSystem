<?php
/**
 * Description.php
 * PHP version 5.4
 * 2015-10-01
 *
 * @package   Foundry\Masonry-Website-Builder
 * @category
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 */


namespace Foundry\Masonry\Builder\Tests\PhpUnit\Workers\FileSystem\MakeDirectory;

use Foundry\Masonry\Module\FileSystem\Workers\MakeDirectory\Description;
use Foundry\Masonry\Tests\PhpUnit\Core\AbstractDescriptionTest;

/**
 * Class DescriptionTest
 * @package Foundry\Masonry\Builder\Tests\PhpUnit\Workers\FileSystem\MakeDirectory
 * @coversDefaultClass Foundry\Masonry\Module\FileSystem\Workers\MakeDirectory\Description
 */
class DescriptionTest extends AbstractDescriptionTest
{

    /**
     * @test
     * @covers ::createFromParameters
     * @uses Foundry\Masonry\Module\FileSystem\Workers\MakeDirectory\Description::__construct
     * @return void
     */
    public function testCreateFromParameters()
    {
        $name = 'fromLocation';

        $parameters = [
            'name' => $name,
        ];
        $description = Description::createFromParameters($parameters);
        $this->assertInstanceOf(
            Description::class,
            $description
        );
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
        $name = 'test';
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
    public function testConstructException()
    {
        new Description('');
    }

    /**
     * @test
     * @covers ::getName
     * @uses Foundry\Masonry\Module\FileSystem\Workers\MakeDirectory\Description::__construct
     * @return void
     */
    public function testGetName()
    {
        $name = 'test';
        $description = new Description($name);
        $this->assertSame(
            $name,
            $description->getName()
        );
    }
}
