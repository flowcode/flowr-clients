<?php

namespace Flower\ClientsBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\ClientsBundle\Form\Type\CallEventType;
use Flower\ModelBundle\Entity\Clients\CallEvent;
use Flower\ModelBundle\Entity\Clients\CallEventStatus;
use Flower\ModelBundle\Entity\User\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * CallEvent controller.
 *
 * @Route("/callevent")
 */
class CallEventController extends BaseController
{
    const formName = "form.type.callevent";

    /**
     * Lists all Account entities.
     *
     * @Route("/", name="callevent")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();        
        $qb = $em->getRepository('FlowerModelBundle:Clients\CallEvent')->createQueryBuilder('ce');
        $qb->leftJoin("ce.status","s");
        $qb->leftJoin("ce.account","a");
        $qb->leftJoin("ce.assignee","u");

        $filters = array(
            'statusFilter' => "s.id",
            'assigneeFilter' => "u.id",
            'accountFilter' => "a.id",
            'startDateFilter' => array("field"=> "ce.date", "type" => "date" , "operation" => ">"),
            'endDateFilter' => array("field"=> "ce.date", "type" => "date" , "operation" => "<="),
            );

        if($request->query->has('reset')) {
            $request->getSession()->set('filter.callevent', null);
            return $this->redirectToRoute("callevent");
        }
        $this->saveFilters($request, $filters, 'callevent','callevent');
        $paginator = $this->filter($qb,'callevent',$request);

        $statuses = $em->getRepository('FlowerModelBundle:Clients\CallEventStatus')->findAll();
        $users = $em->getRepository('FlowerModelBundle:User\User')->findAll();
        $accounts = $em->getRepository('FlowerModelBundle:Clients\Account')->findAll();

        return array(
            'startDateFilter' => $request->query->get('startDateFilter'),
            'endDateFilter' => $request->query->get('endDateFilter'),
            'assigneeFilter' => $request->query->get('assigneeFilter'),
            'statusFilter' => $request->query->get('statusFilter'),
            'users' => $users,
            'accountFilter' => $request->query->get('accountFilter'),
            'accounts' => $accounts,
            'statuses' => $statuses,
            'paginator' => $paginator,
        );
    }

    /**
     *
     * @Route("/export", name="callevent_export")
     * @Method("GET")
     */
    public function exportViewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();        
        $qb = $em->getRepository('FlowerModelBundle:Clients\CallEvent')->createQueryBuilder('ce');
        $qb->leftJoin("ce.status","s");
        $qb->leftJoin("ce.account","a");
        $qb->leftJoin("ce.assignee","u");

        $qb->orderBy("a.id");

        $callevents = $this->filter($qb,'callevent',$request);
        $data = $this->get("client.service.callevent")->callEventDataExport($callevents);
        $this->get("client.service.excelexport")->exportData($data,"Llamadas","Mi descripcion");
        return $this->redirectToRoute("callevent");
    }

    /**
     *
     * @Route("/{id}/bulk_user", name="callevent_bulk_user", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function bulkSetAssigneeAction(User $user, Request $request)
    {
        $callevents = $request->query->get("callevents");
        if(!$callevents){
            return new JsonResponse(null, 403);
        }
        $em = $this->getDoctrine()->getManager();
        foreach ($callevents as $calleventsId) {
            $task = $em->getRepository('FlowerModelBundle:Clients\CallEvent')->find($calleventsId);
            $task->setAssignee($user);
        }
        $em->flush();
        return new JsonResponse(null, 200);
    }
    /**
     *
     * @Route("/{id}/bulk_status", name="callevent_bulk_status", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function bulkSetStatusAction(CallEventStatus $status, Request $request)
    {
        $callevents = $request->query->get("callevents");
        if(!$callevents){
            return new JsonResponse(null, 403);
        }
        $em = $this->getDoctrine()->getManager();
        foreach ($callevents as $calleventId) {
            $task = $em->getRepository('FlowerModelBundle:Clients\CallEvent')->find($calleventId);
            $task->setStatus($status);
        }
        $em->flush();
        return new JsonResponse(null, 200);
    }
    /**
     * Finds and displays a CallEvent entity.
     *
     * @Route("/{id}/show", name="callevent_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(CallEvent $callevent, Request $request)
    {
        $deleteForm = $this->createDeleteForm($callevent->getId(), 'callevent_delete');

        $editForm = $this->createForm($this->get(CallEventController::formName), $callevent, array(
            'action' => $this->generateUrl('callevent_update', array('id' => $callevent->getid())),
            'method' => 'PUT',
        ));
        return array(
            'edit_form' => $editForm->createView(),
            'callevent' => $callevent,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new CallEvent entity.
     *
     * @Route("/new", name="callevent_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $callEvent = new CallEvent();
        $form = $this->createForm($this->get(CallEventController::formName), $callEvent);

        return array(
            'callevent' => $callEvent,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new CallEvent entity.
     *
     * @Route("/create", name="callevent_create")
     * @Method("POST")
     * @Template("FlowerClientsBundle:CallEvent:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $callEvent = new CallEvent();
        $form = $this->createForm($this->get(CallEventController::formName), $callEvent);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($callEvent);
            $em->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'callevent_new' : 'callevent';

            return $this->redirectToRoute($nextAction, array("id" => $callEvent->getId()));
        }

        return array(
            'callevent' => $callEvent,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CallEvent entity.
     *
     * @Route("/{id}/edit", name="callevent_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(CallEvent $callEvent)
    {
        $editForm = $this->createForm($this->get(CallEventController::formName), $callEvent, array(
            'action' => $this->generateUrl('callevent_update', array('id' => $callEvent->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($callEvent->getId(), 'callevent_delete');

        return array(
            'callevent' => $callEvent,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing CallEvent entity.
     *
     * @Route("/{id}/update", name="callevent_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerClientsBundle:CallEvent:edit.html.twig")
     */
    public function updateAction(CallEvent $callEvent, Request $request)
    {
        $editForm = $this->createForm($this->get(CallEventController::formName), $callEvent, array(
            'action' => $this->generateUrl('callevent_update', array('id' => $callEvent->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('callevent_show', array('id' => $callEvent->getId())));
        }
        $deleteForm = $this->createDeleteForm($callEvent->getId(), 'callevent_delete');

        return array(
            'callevent' => $callEvent,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a CallEvent entity.
     *
     * @Route("/{id}/delete", name="callevent_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(CallEvent $callEvent, Request $request)
    {
        $form = $this->createDeleteForm($callEvent->getId(), 'callevent_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($callEvent);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('callevent'));
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="callevent_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('callevent', $field, $type);

        return $this->redirect($this->generateUrl('callevent'));
    }

}
