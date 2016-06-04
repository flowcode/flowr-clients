<?php

namespace Flower\ClientsBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;
use Flower\ClientsBundle\Form\Type\Api\AccountType;
use Flower\ModelBundle\Entity\Clients\Account;
use Symfony\Component\Form\Form;

/**
 * Project controller.
 */
class AccountController extends FOSRestController
{
    public function getAllAction(Request $request)
    {
        $accounts = $this->get("client.service.account")->findAll();
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
        $filter = array(
            'name' => $request->get('q')
        );
        $accounts = $this->get("client.service.account")->getFindAllPaged($filter);

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

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $force = $request->get('force');
        $account = new Account();
        $form = $this->createForm(new AccountType(), $account);
        $accountService = $this->get("client.service.account");
        $errors = $accountService->validateNewAccount($account);
        if (count($errors) == 0) {
            $form->submit($request);
            if ($form->isValid()) {
                $account->setAssignee($this->getUser());
                /* add default security groups */
                $account = $accountService->addSecurityGroups($account);
                $em->persist($account);
                $em->flush();

                $response = array("success" => true, "message" => "Account created", "entity" => $account);
                return $this->handleView(FOSView::create($response, Codes::HTTP_OK)->setFormat("json"));
            }
            $response = array('success' => false, 'errors' => $form->getErrors());
            return $this->handleView(FOSView::create($response, Codes::HTTP_NOT_FOUND)->setFormat("json"));
        } else {
            $response = array('success' => false, 'errors' => $errors);
            return $this->handleView(FOSView::create($response, Codes::HTTP_NOT_FOUND)->setFormat("json"));
        }
    }
}
