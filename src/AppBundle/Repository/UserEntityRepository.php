<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use AppBundle\Interfaces\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;

class UserEntityRepository extends EntityRepository implements UserRepository
{
    /**
     * @param int $id
     * @return User|null
     */
    public function get($id)
    {
        return $this->find($id);
    }

    /**
     * @param User $user
     */
    public function save(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush($user);
    }
}
