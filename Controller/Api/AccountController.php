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
        $accounts = $em->getRepository('FlowerModelBundle:Clients\Account')->findAll();

        $view = FOSView::create($accounts, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('api'));
        return $this->handleView($view);
    }

    public function getByIdAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository('FlowerModelBundle:Clients\Account')->find($id);

        $view = FOSView::create($account, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('api'));
        return $this->handleView($view);
    }
    public function publicGetAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $accounts = $em->getRepository('FlowerModelBundle:Clients\Account')->findAll();

        $view = FOSView::create($accounts, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    public function publicGetByIdAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository('FlowerModelBundle:Clients\Account')->find($id);

        $view = FOSView::create($account, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }
}
