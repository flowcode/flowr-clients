<?php

namespace Flower\ClientsBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
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
     * AccountDataExport() genera el contenido a ser exportado segun vista.
     *
     */
    public function accountDataExport($accounts)
    {
        $data = array();
        $data["header"] = array( 
            "Nombre",
            "Teléfono", "Dirección",
            "Actividad");
        $index = 1;
        foreach ($accounts as $account) {
            $name = $account->getName()? : " ";
            $phone = $account->getPhone()? : " ";
            $address = $account->getAddress()? : " ";
            $activity = $account->getActivity()? : " ";
            $data[$index] = array(
                $name ,
                $phone ,
                $address ,
                $activity );
            $index++;
        }
        return $data;
    }
}