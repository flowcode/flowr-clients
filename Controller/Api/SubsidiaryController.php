<?php

namespace Flower\ClientsBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;
use Flower\ModelBundle\Entity\Clients\Account;
use Flower\ModelBundle\Entity\Clients\Subsidiary;
use Flower\ClientsBundle\Form\Type\Api\SubsidiaryType;
/**
 * Subsidiary controller.
 */
class SubsidiaryController extends FOSRestController
{
    public function getAllAction(Account $account, Request $request)
    {
        $subsidiaries = $account->getSubsidiaries();
        $view = FOSView::create($subsidiaries, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $subsidiary = new Subsidiary();
        $form = $this->createForm(new SubsidiaryType(), $subsidiary);

        $form->submit($request);
        if ($form->isValid()) {            
            $em->persist($subsidiary);
            $em->flush();

            $response = array("success" => true, "message" => "subsidiary created", "entity" =>$subsidiary );
            return $this->handleView(FOSView::create($response, Codes::HTTP_OK)->setFormat("json"));
        }

        $response= array('success' => false, 'errors' => $form->getErrors());
        return $this->handleView(FOSView::create($response, Codes::HTTP_NOT_FOUND)->setFormat("json"));
    }
}
