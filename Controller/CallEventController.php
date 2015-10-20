<?php

namespace Flower\ClientsBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\ClientsBundle\Form\Type\CallEventType;
use Flower\ModelBundle\Entity\Clients\CallEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
/**
 * CallEvent controller.
 *
 * @Route("/callevent")
 */
class CallEventController extends Controller
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
        $this->addQueryBuilderSort($qb, 'callevent');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
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
        return array(
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
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="callevent_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('callevent', $field, $type);

        return $this->redirect($this->generateUrl('callevent'));
    }

    /**
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $this->getRequest()->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $session = $this->getRequest()->getSession();

        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            if (strpos($order['field'], '.') !== FALSE){
                $qb->orderBy($order['field'], $order['type']);
            }else{
                $qb->orderBy($alias . '.' . $order['field'], $order['type']);
            }            
        }
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
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl($route, array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
