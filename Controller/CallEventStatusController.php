<?php

namespace Flower\ClientsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Clients\CallEventStatus;
use Flower\ClientsBundle\Form\Type\CallEventStatusType;

/**
 * CallEventStatus controller.
 *
 * @Route("/admin/calleventstatus")
 */
class CallEventStatusController extends Controller
{
    /**
     * Lists all CallEventStatus entities.
     *
     * @Route("/", name="calleventstatus")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
$qb = $em->getRepository('FlowerModelBundle:Clients\CallEventStatus')->createQueryBuilder('c');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a CallEventStatus entity.
     *
     * @Route("/{id}/show", name="calleventstatus_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(CallEventStatus $calleventstatus)
    {
        $deleteForm = $this->createDeleteForm($calleventstatus->getId(), 'calleventstatus_delete');

        return array(
            'calleventstatus' => $calleventstatus,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new CallEventStatus entity.
     *
     * @Route("/new", name="calleventstatus_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $calleventstatus = new CallEventStatus();
        $form = $this->createForm(new CallEventStatusType(), $calleventstatus);

        return array(
            'calleventstatus' => $calleventstatus,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new CallEventStatus entity.
     *
     * @Route("/create", name="calleventstatus_create")
     * @Method("POST")
     * @Template("FlowerClientsBundle:CallEventStatus:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $calleventstatus = new CallEventStatus();
        $form = $this->createForm(new CallEventStatusType(), $calleventstatus);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($calleventstatus);
            $em->flush();

            return $this->redirect($this->generateUrl('calleventstatus_show', array('id' => $calleventstatus->getId())));
        }

        return array(
            'calleventstatus' => $calleventstatus,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CallEventStatus entity.
     *
     * @Route("/{id}/edit", name="calleventstatus_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(CallEventStatus $calleventstatus)
    {
        $editForm = $this->createForm(new CallEventStatusType(), $calleventstatus, array(
            'action' => $this->generateUrl('calleventstatus_update', array('id' => $calleventstatus->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($calleventstatus->getId(), 'calleventstatus_delete');

        return array(
            'calleventstatus' => $calleventstatus,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing CallEventStatus entity.
     *
     * @Route("/{id}/update", name="calleventstatus_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerClientsBundle:CallEventStatus:edit.html.twig")
     */
    public function updateAction(CallEventStatus $calleventstatus, Request $request)
    {
        $editForm = $this->createForm(new CallEventStatusType(), $calleventstatus, array(
            'action' => $this->generateUrl('calleventstatus_update', array('id' => $calleventstatus->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('calleventstatus_show', array('id' => $calleventstatus->getId())));
        }
        $deleteForm = $this->createDeleteForm($calleventstatus->getId(), 'calleventstatus_delete');

        return array(
            'calleventstatus' => $calleventstatus,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a CallEventStatus entity.
     *
     * @Route("/{id}/delete", name="calleventstatus_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(CallEventStatus $calleventstatus, Request $request)
    {
        $form = $this->createDeleteForm($calleventstatus->getId(), 'calleventstatus_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($calleventstatus);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('calleventstatus'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
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
