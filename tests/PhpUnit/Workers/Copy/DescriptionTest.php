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


namespace Foundry\Masonry\Module\FileSystem\Tests\PhpUnit\Workers\Copy;

use Foundry\Masonry\Module\FileSystem\Workers\Copy\Description;
use Foundry\Masonry\Tests\PhpUnit\Core\AbstractDescriptionTest;

/**
 * Class DescriptionTest
 * @coversDefaultClass Foundry\Masonry\Module\FileSystem\Workers\Copy\Description
 * @package Foundry\Masonry-Website-Builder
 */
class DescriptionTest extends AbstractDescriptionTest
{

    /**
     * @test
     * @covers ::createFromParameters
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Copy\Description::__construct
     * @return void
     */
    public function testCreateFromParameters()
    {
        $from = 'fromLocation';
        $to = 'toLocation';

        $parameters = [
            'from' => $from,
            'to'   => $to,
        ];
        $description = Description::createFromParameters($parameters);
        $this->assertInstanceOf(
            Description::class,
            $description
        );
        $this->assertSame(
            $from,
            $this->getObjectAttribute($description, 'from')
        );
        $this->assertSame(
            $to,
            $this->getObjectAttribute($description, 'to')
        );

        $parameters = [
            'to'   => $to,
            'from' => $from,
        ];
        $description = Description::createFromParameters($parameters);
        $this->assertInstanceOf(
            Description::class,
            $description
        );
        $this->assertSame(
            $from,
            $this->getObjectAttribute($description, 'from')
        );
        $this->assertSame(
            $to,
            $this->getObjectAttribute($description, 'to')
        );
    }

    /**
     * @test
     * @covers ::__construct
     * @return void
     */
    public function testConstruct()
    {
        $from = 'from';
        $to   = 'to';
        $description = new Description($from, $to);
        $this->assertSame(
            $from,
            $this->getObjectAttribute($description, 'from')
        );
        $this->assertSame(
            $to,
            $this->getObjectAttribute($description, 'to')
        );
    }

    /**
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $to is required
     * @return void
     */
    public function testConstructException1()
    {
        new Description('from', '');
    }

    /**
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $from is required
     * @return void
     */
    public function testConstructException2()
    {
        new Description('', '');
    }

    /**
     * @test
     * @covers ::getFrom
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Copy\Description::__construct
     * @return void
     */
    public function testGetFrom()
    {
        $from = 'from';
        $to   = 'to';
        $description = new Description($from, $to);
        $this->assertSame(
            $from,
            $description->getFrom()
        );
    }

    /**
     * @test
     * @covers ::getTo
     * @uses Foundry\Masonry\Module\FileSystem\Workers\Copy\Description::__construct
     * @return void
     */
    public function testGetTo()
    {
        $from = 'from';
        $to   = 'to';
        $description = new Description($from, $to);
        $this->assertSame(
            $to,
            $description->getTo()
        );
    }
}
