<?php

namespace Flower\ClientsBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * OpportunityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OpportunityRepository extends EntityRepository
{
	public function findByBoard($board){
        $qb = $this->createQueryBuilder("o");
        $qb->join("o.boards","b");
        $qb->where("b.id = :board")->setParameter("board", $board->getId());
        $querry = $qb->getQuery();
        return $querry->getOneOrNullResult();
    }
}