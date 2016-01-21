<?php
/**
 * DescriptionTest.php
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 * @license
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Builder
 */


namespace Foundry\Masonry\Module\FileSystem\Tests\PhpUnit\Workers\ChangeOwner;

use Foundry\Masonry\Tests\PhpUnit\Core\AbstractDescriptionTest;
use Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner\Description;

/**
 * Class DescriptionTest
 * @package Masonry-Builder
 * @see       https://github.com/TheFoundryVisionmongers/Masonry-Builder
 * @coversDefaultClass \Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner\Description
 */
class DescriptionTest extends AbstractDescriptionTest
{

    /**
     * @test
     * @covers ::createFromParameters
     * @uses Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner\Description::__construct
     * @return void
     */
    public function testCreateFromParameters()
    {
        $target = 'targetDirectory';
        $owner = 'owningUser';

        $parameters = [
            'target' => $target,
            'owner' => $owner,
        ];

        $description = Description::createFromParameters($parameters);
        $this->assertInstanceOf(
            Description::class,
            $description
        );
        $this->assertSame(
            $target,
            $this->getObjectAttribute($description, 'target')
        );
        $this->assertSame(
            $owner,
            $this->getObjectAttribute($description, 'owner')
        );

        $parameters = [
            'owner' => $owner,
            'target' => $target,
        ];

        $description = Description::createFromParameters($parameters);
        $this->assertInstanceOf(
            Description::class,
            $description
        );
        $this->assertSame(
            $target,
            $this->getObjectAttribute($description, 'target')
        );
        $this->assertSame(
            $owner,
            $this->getObjectAttribute($description, 'owner')
        );
    }

    /**
     * @test
     * @covers ::__construct
     * @return void
     */
    public function testConstruct()
    {
        $target = 'targetDirectory';
        $owner = 'owningUser';

        $description = new Description($target, $owner);
        $this->assertSame(
            $target,
            $this->getObjectAttribute($description, 'target')
        );
        $this->assertSame(
            $owner,
            $this->getObjectAttribute($description, 'owner')
        );
    }

    /**
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $target is required
     * @return void
     */
    public function testConstructInvalidTarget()
    {
        $target = '';
        $owner = 'owningUser';
        new Description($target, $owner);
    }

    /**
     * @test
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $owner is required
     * @return void
     */
    public function testConstructInvalidOwner()
    {
        $target = 'targetDirectory';
        $owner = '';
        new Description($target, $owner);
    }

    /**
     * @test
     * @covers ::getTarget
     * @uses \Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner\Description::__construct
     * @return void
     */
    public function testGetTarget()
    {
        $target = 'targetDirectory';
        $owner = 'owningUser';

        $description = new Description($target, $owner);
        $this->assertSame(
            $target,
            $description->getTarget()
        );
    }

    /**
     * @test
     * @covers ::getOwner
     * @uses \Foundry\Masonry\Module\FileSystem\Workers\ChangeOwner\Description::__construct
     * @return void
     */
    public function testGetOwner()
    {
        $target = 'targetDirectory';
        $owner = 'owningUser';

        $description = new Description($target, $owner);
        $this->assertSame(
            $owner,
            $description->getOwner()
        );
    }
}
