<?php

namespace Flower\ClientsBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;
use Flower\ClientsBundle\Form\Type\ContactType;

/**
 * Project controller.
 */
class ContactController extends FOSRestController
{
    public function getAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('FlowerModelBundle:Clients\Contact')->findBy(array(), array(), 20);

        $view = FOSView::create($projects, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    public function getByIdAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('FlowerModelBundle:Clients\Contact')->find($id);

        $view = FOSView::create($project, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    public function getByAccountAction(Request $request, $accountId)
    {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('FlowerModelBundle:Clients\Contact')->findBy(array("account" => $accountId));

        $view = FOSView::create($project, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    //
     // parameters={
     //      {"name"="account_id", "dataType"="int", "required"=true, "description"="The account id"},
     //      {"name"="firstname", "dataType"="string", "required"=false, "description"="The contact firstname"},
     //      {"name"="lastname", "dataType"="string", "required"=false, "description"="The contact lastname"}
     //      {"name"="email", "dataType"="string", "required"=true, "description"="The contact email"},
     //      {"name"="address", "dataType"="string", "required"=false, "description"="The contact address"},
     //      {"name"="phone", "dataType"="string", "required"=false, "description"="The contact phone"},
     //      {"name"="observations", "dataType"="string", "required"=false, "description"="The contact observations"},
     // },
     //  statusCodes={
     //         200="Returned when successful",
     //         404="When not all required parameters was found",
     //     }
     // )
     ///
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $account = $this->getByAccountAction($request->get('account_id')); //se puede llamar asi a getByAccountAction?????

        $contact = new Contact();
        $form = $this->createForm($this->get('form.type.contact'), $contact);

        $form->submit($request);
        if ($account && $form->isValid()) {            
            $em->persist($contact);
            $em->flush();

            $response = array("success" => true, "message" => "Contact created" );
            return $this->handleView(FOSView::create($response, Codes::HTTP_OK)->setFormat("json"));
        }

        $response= array('success' => false, 'errors' => $form->getErrors());
        return $this->handleView(FOSView::create($response, Codes::HTTP_NOT_FOUND)->setFormat("json"));
    }

     // @ApiDoc(
     //  description="Update contact",
     // parameters={
     //      {"name"="contact_id", "dataType"="int", "required"=true, "description"="The contact id"},
     //      {"name"="firstname", "dataType"="string", "required"=false, "description"="The contact firstname"},
     //      {"name"="lastname", "dataType"="string", "required"=false, "description"="The contact lastname"}
     //      {"name"="email", "dataType"="string", "required"=true, "description"="The contact email"},
     //      {"name"="address", "dataType"="string", "required"=false, "description"="The contact address"},
     //      {"name"="phone", "dataType"="string", "required"=false, "description"="The contact phone"},
     //      {"name"="observations", "dataType"="string", "required"=false, "description"="The contact observations"},
     // },
     //  statusCodes={
     //         200="Returned when successful",
     //         404="When not all required parameters was found",
     //     }
     // )
     ///
    public function contactUpdateAction(Request $request)
    {
        $user = $this->getUser();
        $userService = $this->get("flou.user");
        $form = $this->createForm(new UserType(), $user, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $userService->update($user);

            $triviaService = $this->get("flou.trivia");

            $user->setTriviaPoints($triviaService->getUserPoints($user));

            $response= array('success' => true, 'user' => $user);

            $view = FOSView::create($response, Codes::HTTP_OK)->setFormat("json");
            $view->getSerializationContext()->setGroups(array('entity', 'api_public'));
            return $this->handleView($view);
        }

        $response= array('success' => false, 'errors' => $form->getErrors());
        return $this->handleView(FOSView::create($response, Codes::HTTP_NOT_FOUND)->setFormat("json"));
    }

}
