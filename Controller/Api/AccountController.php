<?php

namespace Flower\ClientsBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Project controller.
 */
class AccountController extends FOSRestController
{
    public function getAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('FlowerModelBundle:Clients\Account')->findAll();

        $view = FOSView::create($projects, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    public function getByIdAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('FlowerModelBundle:Clients\Account')->find($id);

        $view = FOSView::create($project, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }
}
