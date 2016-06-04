<?php

namespace Flower\ClientsBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;
use Flower\ClientsBundle\Form\Type\Api\ContactType;
use Flower\ModelBundle\Entity\Clients\Contact;

/**
 * contact controller.
 */
class ContactController extends FOSRestController
{
    public function getAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $contacts = $em->getRepository('FlowerModelBundle:Clients\Contact')->getAll(0, 20, $request->get('q'));

        $view = FOSView::create($contacts, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    public function getAllSimpleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $contacts = $em->getRepository('FlowerModelBundle:Clients\Contact')->getAll(0, 10, $request->get('q'));

        $simpleContacts = array();
        foreach ($contacts as $contact) {
            $contactArr = array(
                'id' => $contact->getId(),
                'text' => $contact->getHappyName(),
            );
            $simpleContacts[] = $contactArr;
        }

        $view = FOSView::create($simpleContacts, Codes::HTTP_OK)->setFormat('json');
        return $this->handleView($view);
    }

    public function getByIdAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository('FlowerModelBundle:Clients\Contact')->find($id);

        $view = FOSView::create($contact, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    public function getByAccountAction(Request $request, $accountId)
    {
        $em = $this->getDoctrine()->getManager();
        $contacts = $em->getRepository('FlowerModelBundle:Clients\Contact')->getByAccountQuery($accountId)->getQuery()->getResult();

        $view = FOSView::create($contacts, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    //
    // parameters={
    //      {"name"="accounts=asdasd, "dataType"="int", "required"=true, "description"="The account id"},
    //      {"name"="firstname=asdasd, "dataType"="string", "required"=false, "description"="The contact firstname"},
    //      {"name"="lastname=asdasd, "dataType"="string", "required"=false, "description"="The contact lastname"}
    //      {"name"="email=asdasd, "dataType"="string", "required"=true, "description"="The contact email"},
    //      {"name"="address=asdasd, "dataType"="string", "required"=false, "description"="The contact address"},
    //      {"name"="phone=asdasd, "dataType"="string", "required"=false, "description"="The contact phone"},
    //      {"name"="observations=asdasd, "dataType"="string", "required"=false, "description"="The contact observations"},
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
//        $account = $em->getRepository('FlowerModelBundle:Clients\Account')->find($account_id);

        $contact = new Contact();
        $form = $this->createForm($this->get('form.api.type.contact'), $contact);

        $form->submit($request);
        if ($form->isValid()) {
            $em->persist($contact);
            $em->flush();

            $response = array("success" => true, "message" => "Contact created", "entity" => $contact);
            return $this->handleView(FOSView::create($response, Codes::HTTP_OK)->setFormat("json"));
        }

        $response = array('success' => false, 'errors' => $form->getErrors());
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

            $response = array('success' => true, 'user' => $user);

            $view = FOSView::create($response, Codes::HTTP_OK)->setFormat("json");
            $view->getSerializationContext()->setGroups(array('entity', 'api_public'));
            return $this->handleView($view);
        }

        $response = array('success' => false, 'errors' => $form->getErrors());
        return $this->handleView(FOSView::create($response, Codes::HTTP_NOT_FOUND)->setFormat("json"));
    }

}
