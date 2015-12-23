<?php

namespace Flower\ClientsBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Activity controller.
 */
class ActivityController extends FOSRestController
{
    public function getAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $activities = $em->getRepository('FlowerModelBundle:Clients\Activity')->findAll();

        $view = FOSView::create($activities, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('api'));
        return $this->handleView($view);
    }

}
