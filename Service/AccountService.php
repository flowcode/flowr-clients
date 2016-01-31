<?php

namespace Flower\ClientsBundle\Service;

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
    public function findAll($enabledOnly = true){
        $alias = 'a';
        $qb = $this->em->getRepository('FlowerModelBundle:Clients\Account')->getFindAllQueryBuilder($alias);
        //$qb->andWhere($alias.".enabled = :enabled")->setParameter('enabled', $enabledOnly);

        /* filter by org security groups */
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            $secGroupSrv = $this->container->get('user.service.securitygroup');
            $qb = $secGroupSrv->addSecurityGroupFilter($qb, $user, $alias);
        }

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
            "styles" =>  array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'd22729')
                                )
                            ));
        $index = 1;
        foreach ($accounts as $account) {
            $id = $account->getId()? : " ";
            $name = $account->getName()? : " ";
            $businessName = $account->getBusinessName()? : " ";
            $cuit = $account->getCuit()? : " ";
            $phone = $account->getPhone()? : " ";
            $address = $account->getAddress()? : " ";
            $activity = $account->getActivity()? : " ";
            $data[$index] = 
            array("values" => 
                array(
                    $id,
                    $name ,
                    $businessName,
                    $cuit,
                    $phone ,
                    $address ,
                    $activity ));
            $index++;
        }
        return $data;
    }
}