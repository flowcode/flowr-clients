<?php

namespace Flower\ClientsBundle\Service;

use Flower\ModelBundle\Entity\Clients\Account;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PHPExcel_Style_Fill;

/**
 * Description of AccountService
 *
 * @author Pedro Barri <pbarri@flowcode.com.ar>
 */
class AccountService
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(ContainerInterface $container = NULL)
    {
        $this->container = $container;
        $this->em = $this->container->get("doctrine.orm.entity_manager");
    }

    /**
     * Fin all accounts for authenticated user.
     *
     * @param bool $enabledOnly
     * @return mixed
     */
    public function findAll($enabledOnly = true)
    {
        $alias = 'a';
        $qb = $this->em->getRepository('FlowerModelBundle:Clients\Account')->getFindAllQueryBuilder($alias);
        //$qb->andWhere($alias.".enabled = :enabled")->setParameter('enabled', $enabledOnly);

        /* filter by org security groups */
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            $secGroupSrv = $this->container->get('user.service.securitygroup');
            $qb = $secGroupSrv->addLowerSecurityGroupsFilter($qb, $user, $alias);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Fin all clients accounts for authenticated user.
     *
     * @param bool $enabledOnly
     * @return mixed
     */
    public function findClients($enabledOnly = true)
    {
        $alias = 'a';
        $qb = $this->em->getRepository('FlowerModelBundle:Clients\Account')->getFindAllQueryBuilder($alias);
        $qb->andWhere($alias . ".client = :is_client")->setParameter('is_client', true);
        //$qb->andWhere($alias . ".enabled = :is_enabled")->setParameter('is_enabled', $enabledOnly);

        /* filter by org security groups */
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            $secGroupSrv = $this->container->get('user.service.securitygroup');
            $qb = $secGroupSrv->addLowerSecurityGroupsFilter($qb, $user, $alias);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Fin all suppliers accounts for authenticated user.
     *
     * @param bool $enabledOnly
     * @return mixed
     */
    public function findSuppliers($enabledOnly = true)
    {
        $alias = 'a';
        $qb = $this->em->getRepository('FlowerModelBundle:Clients\Account')->getFindAllQueryBuilder($alias);
        $qb->andWhere($alias . ".supplier = :is_supplier")->setParameter('is_supplier', true);
        //$qb->andWhere($alias . ".enabled = :is_enabled")->setParameter('is_enabled', $enabledOnly);

        /* filter by org security groups */
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            $secGroupSrv = $this->container->get('user.service.securitygroup');
            $qb = $secGroupSrv->addLowerSecurityGroupsFilter($qb, $user, $alias);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $filter
     * @param array $order
     * @return mixed
     */
    public function getFilteredQueryBuilder($filter = array(), $order = array())
    {
        $qb = $this->em->getRepository('FlowerModelBundle:Clients\Account')->getFindFilteredQueryBuilder($filter);
        return $qb;
    }

    /**
     * @param array $filter
     * @param array $order
     * @param int $maxResults
     * @param int $page
     * @return mixed
     */
    public function getFindAllPaged($filter = array(), $order = array(), $maxResults = 20, $page = 1)
    {
        $qb = $this->getFindAllPagedQueryBuilder($filter, $order, $maxResults, $page);
        return $qb->getQuery()->getResult();
    }

    /**
     * AccountDataExport() genera el contenido a ser exportado segun vista.
     *
     */
    public function accountDataExport($accounts)
    {
        $data = array();
        $data["header"] =
            array("values" =>
                array(
                    "id",
                    "Nombre",
                    "Razón Social",
                    "ciut/cuil",
                    "Teléfono", "Dirección",
                    "Actividad"),
                "styles" => array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'd22729')
                    )
                ));
        $index = 1;
        foreach ($accounts as $account) {
            $id = $account->getId() ?: " ";
            $name = $account->getName() ?: " ";
            $businessName = $account->getBusinessName() ?: " ";
            $cuit = $account->getCuit() ?: " ";
            $phone = $account->getPhone() ?: " ";
            $address = $account->getAddress() ?: " ";
            $activity = $account->getActivity() ?: " ";
            $data[$index] =
                array("values" =>
                    array(
                        $id,
                        $name,
                        $businessName,
                        $cuit,
                        $phone,
                        $address,
                        $activity));
            $index++;
        }
        return $data;
    }

    public function validateNewAccount(Account $account)
    {
        $errors = array();
        $warning = array();
        $otherAccount = $this->em->getRepository('FlowerModelBundle:Clients\Account')->findBy(array("businessName" => $account->getBusinessName()));
        if ($otherAccount) {
            $warning[] = "Existe un cliente con este nombre";
        }
        return $errors;
    }

    /**
     * Add security groups.
     *
     * @param Account $account
     * @return Account
     */
    public function addSecurityGroups(Account $account)
    {
        $assignee = $account->getAssignee();
        $assigneeGroup = $this->container->get("user.service.securitygroup")->getDefaultForUser($assignee);
        $account->addSecurityGroup($assigneeGroup);

        //$parentGroups = $this->container->get("user.service.securitygroup")->getParentsGroups($assignee);
        //foreach ($parentGroups as $securityGroup) {
        //    if (!$account->getSecurityGroups()->contains($securityGroup)) {
        //        $account->addSecurityGroup($securityGroup);
        //    }
        //}

        return $account;

    }
}