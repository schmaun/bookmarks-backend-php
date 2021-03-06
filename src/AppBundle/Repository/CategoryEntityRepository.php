<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Interfaces\Repository\CategoryRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CategoryEntityRepository extends NestedTreeRepository implements CategoryRepositoryInterface
{

    /**
     * @param int $id
     * @return null|object|Category
     */
    public function get($id)
    {
        return $this->find($id);
    }

    /**
     * @param array $orderBy
     * @return \AppBundle\Entity\Category[]
     */
    public function findAllAsTree($orderBy = array('position' => 'ASC'))
    {
        return $this->getChildren();
    }

    /**
     * @param Category $category
     * @throws Exception
     */
    public function delete($category)
    {
        $this->getEntityManager()->beginTransaction();
        try {
            $category = $this->find($category->getId(), LockMode::PESSIMISTIC_WRITE);
            $this->getEntityManager()->remove($category);
            $this->getEntityManager()->flush();
            $this->getEntityManager()->commit();
            $this->getEntityManager()->clear();
        } catch (Exception $e) {
            $this->getEntityManager()->rollback();
            throw $e;
        }
    }

    /**
     * @param Category $category
     */
    public function save($category)
    {
        $this->_em->persist($category);
        $this->_em->flush($category);
    }

    /**
     * @param User $user
     * @return QueryBuilder
     */
    public function getCategoriesForUserQueryBuilder(User $user)
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :userId')
            ->orderBy('c.name', 'ASC')
            ->setParameters(['userId' => $user->getId()]);
    }
}
