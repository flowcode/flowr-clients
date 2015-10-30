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
            "Identificador", "Nombre",
            "Teléfono", "Dirección",
            "Actividad");
        $index = 1;
        foreach ($accounts as $account) {
            $data[$index] = array(
                $account->getId(), $account->getName(),
                $account->getPhone(), $account->getAddress(),
                $account->getActivity());
            $index++;
        }
        return $data;
    }
}