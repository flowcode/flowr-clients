<?php

namespace Flower\ClientsBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Description of CallEventService
 *
 * @author Pedro Barri <pbarri@flowcode.com.ar>
 */
class CallEventService
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
     * CallEventDataExport() genera el contenido a ser exportado segun vista.
     *
     */
    public function callEventDataExport($callevents)
    {
        $data = array();
        $index = 0;
        $oldAccount = "";
        foreach ($callevents as $callevent) {
        	$newAccount = $callevent->getAccount()->getId();
            if($newAccount != $oldAccount){
            	//Primera fila Nombre del Account
                $data[$index++] = array(
                    "Id" => $callevent->getAccount()->getId(),
                    "Name" => $callevent->getAccount()->getName());
		        $data[$index++] = array( 
		            "Identificador", "Subject",
		            "Nombre de Usuario", "Nombre de Contacto",
		            "Estado llamado","Fecha");
            }
            $id = $callevent->getId()? : " ";
            $subject = $callevent->getSubject()? : " ";
            $assignee = $callevent->getAssignee()? : " ";
            $contactname = $callevent->getContactName()? : " ";
            $status = $callevent->getStatus()? : " ";
            $date = $callevent->getDate()? : " ";
            $data[$index++] = array(
                $id ,
                $subject ,
                $assignee ,
                $contactname ,
                $status ,
                $date );
            $oldAccount = $callevent->getAccount()->getId();
        }
        return $data;
    }
}