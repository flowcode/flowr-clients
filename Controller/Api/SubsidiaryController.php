<?php

namespace Flower\ClientsBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;
use Flower\ModelBundle\Entity\Clients\Account;
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

}
