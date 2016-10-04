<?php

namespace AppBundle\Tests\Functional\Repository;

use AppBundle\DataFixtures\ORM\LoadCategoriesData;
use AppBundle\DataFixtures\ORM\LoadFullTreeBookmarksData;
use AppBundle\DataFixtures\ORM\LoadFullTreeCategoriesData;
use AppBundle\DataFixtures\ORM\LoadUsersData;
use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryEntityRepository;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class CategoryRepositoryTest extends WebTestCase
{
    /**
     * @var CategoryEntityRepository
     */
    protected $repository;

    /**
     * @var ReferenceRepository
     */
    protected $fixtureRepository;

    public function setUp()
    {
        $this->fixtureRepository = $this
            ->loadFixtures(array(LoadUsersData::class, LoadCategoriesData::class))
            ->getReferenceRepository();

        $this->repository = $this->getContainer()->get('doctrine')->getRepository(Category::class);
    }

    public function testGet()
    {
        $expected = new Category();
        $expected->setId(LoadCategoriesData::ROOT_ID);
        $expected->setName('root');

        $actual = $this->repository->get(LoadCategoriesData::ROOT_ID);

        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getName(), $actual->getName());
    }

    public function testFindAllOrderedByPosition()
    {
        $actual = $this->repository->findAllAsTree();

        $actualRoot = $actual[0];
        /** @var Category[] $actualChildren */
        $actualChildren = $actualRoot->getChildren();

        $expectedRoot = new Category();
        $expectedRoot->setName('root');
        $expectedRoot->setId(LoadCategoriesData::ROOT_ID);

        $this->assertEquals($expectedRoot->getId(), $actualRoot->getId());
        $this->assertEquals($expectedRoot->getName(), $actualRoot->getName());
        $this->assertCount(2, $actualRoot->getChildren());

        $this->assertCount(0, $actualChildren[0]->getChildren());
    }

    public function testSave()
    {
        $category = $this->repository->get(LoadCategoriesData::ROOT_ID);

        $user = $this->fixtureRepository->getReference(LoadUsersData::REFERENCE_2);
        $category->setName('other Name');
        $category->setUser($user);
        $this->repository->save($category);

        $saved = $this->repository->get(LoadCategoriesData::ROOT_ID);
        $this->assertEquals($saved->getId(), $saved->getId());
        $this->assertEquals($saved->getName(), $saved->getName());
        $this->assertEquals($saved->getUser()->getId(), $saved->getUser()->getId());
    }
}
