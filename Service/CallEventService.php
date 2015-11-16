<?php

namespace Flower\ClientsBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use PHPExcel_Style_Fill;
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
            if($index == 0){
                $data[$index++] = 
                 array("values" => 
                        array(
                            "Id" => " ",
                            "Name" => "Name",
                            "Actividad" => "Actividad",
                            "Cuit/Cuil" => "Cuit",
                            "Dirección" => "Dirección",
                            "Teléfono" => "Teléfono",
                            "Responsable" => "Responsable",
                            ),
                        "styles" =>  array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'ca3e44')
                            )
                        ));
            }
            if($callevent->getAccount()){
                $newAccount = $callevent->getAccount()->getId();
                if($newAccount != $oldAccount){
                	//Primera fila Nombre del Account
                   $data[$index++] = array();
                   $account = $callevent->getAccount();
                   $assigne = " ";
                   if($account->getAssignee()){
                    $assigne = $account->getAssignee();
                   }
                   $data[$index++] = 
                   array("values" => 
                               array(
                                    "Id" => "Cuenta:",
                                    "Name" => $account->getName(),
                                    "Actividad" => $account->getActivity(),
                                    "Cuit/Cuil" => $account->getCuit(),
                                    "Dirección" => $account->getAddress(),
                                    "Teléfono" => $account->getPhone(),
                                    "Responsable" => $assigne,
                                    ));
                    $data[$index++] = 
                    array("values" => 
                        array( 
                         " ","Subject",
                        "Nombre de Usuario", "Nombre de Contacto",
                        "Estado llamado","Fecha"));
                }
                $oldAccount = $callevent->getAccount()->getId();
            }else{
                if($oldAccount == "" ){
    		        $data[$index++] = 
                    array("values" => 
                        array( 
        		            " ","Fecha de llamado", "Subject",
        		            "Nombre de Usuario", "Nombre de Contacto",
        		            "Estado llamado",
                            "Descripción"));
                }
                $oldAccount = "done";
            }

            $subject = $callevent->getSubject()? : " ";
            $assignee = $callevent->getAssignee()? : " ";
            $contactname = $callevent->getContactName()? : " ";
            $status = $callevent->getStatus()? : " ";
            $description = $callevent->getDescription()? : " ";
            $date = $callevent->getDate()? $callevent->getDate()->format("d/m/y H:i"): " ";
            $data[$index++] = 
            array("values" => 
                    array(
                        " ",
                        $date,
                        $subject ,
                        $assignee ,
                        $contactname ,
                        $status ,
                        $description
                         ));
            
        }
        return $data;
    }
}