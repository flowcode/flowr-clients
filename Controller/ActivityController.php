<?php

namespace Flower\ClientsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Clients\Activity;
use Flower\ClientsBundle\Form\Type\ActivityType;
use Doctrine\ORM\QueryBuilder;

/**
 * Activity controller.
 *
 * @Route("/admin/clients_activity")
 */
class ActivityController extends Controller
{
    /**
     * Lists all Activity entities.
     *
     * @Route("/", name="admin_clients_activity")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Clients\Activity')->createQueryBuilder('c');
        $this->addQueryBuilderSort($qb, 'activity');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Activity entity.
     *
     * @Route("/{id}/show", name="admin_clients_activity_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Activity $activity)
    {
        $editForm = $this->createForm(new ActivityType(), $activity, array(
            'action' => $this->generateUrl('admin_clients_activity_update', array('id' => $activity->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($activity->getId(), 'admin_clients_activity_delete');

        return array(

        'activity' => $activity,
        'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new Activity entity.
     *
     * @Route("/new", name="admin_clients_activity_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $activity = new Activity();
        $form = $this->createForm(new ActivityType(), $activity);

        return array(
            'activity' => $activity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Activity entity.
     *
     * @Route("/create", name="admin_clients_activity_create")
     * @Method("POST")
     * @Template("FlowerCoreBundle:Activity:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $activity = new Activity();
        $form = $this->createForm(new ActivityType(), $activity);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($activity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_clients_activity_show', array('id' => $activity->getId())));
        }

        return array(
            'activity' => $activity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Edits an existing Activity entity.
     *
     * @Route("/{id}/update", name="admin_clients_activity_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerCoreBundle:Activity:edit.html.twig")
     */
    public function updateAction(Activity $activity, Request $request)
    {
        $editForm = $this->createForm(new ActivityType(), $activity, array(
            'action' => $this->generateUrl('admin_clients_activity_update', array('id' => $activity->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_clients_activity_show', array('id' => $activity->getId())));
        }
        $deleteForm = $this->createDeleteForm($activity->getId(), 'admin_clients_activity_delete');

        return array(
            'activity' => $activity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="admin_clients_activity_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('activity', $field, $type);

        return $this->redirect($this->generateUrl('admin_clients_activity'));
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
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }

    /**
     * Deletes a Activity entity.
     *
     * @Route("/{id}/delete", name="admin_clients_activity_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Activity $activity, Request $request)
    {
        $form = $this->createDeleteForm($activity->getId(), 'admin_clients_activity_delete');
        $em = $this->getDoctrine()->getManager();
        $accounts = $em->getRepository("FlowerModelBundle:Clients\Account")->findBy(array("activity" => $activity->getId()));
        if(count($accounts) > 0){
            $this->addFlash(
                    'danger',
                     $this->get('translator')->trans('error.delete.activity')
                );
            return $this->redirect($this->generateUrl('admin_clients_activity_show',array("id" => $activity->getId())));
        }
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($activity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_clients_activity'));
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
