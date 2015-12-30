<?php

namespace Flower\ClientsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Clients\Subsidiary;
use Flower\ModelBundle\Entity\Clients\Account;
use Flower\ClientsBundle\Form\Type\SubsidiaryType;
use Doctrine\ORM\QueryBuilder;

/**
 * Subsidiary controller.
 *
 * @Route("/subsidiary")
 */
class SubsidiaryController extends Controller
{
    /**
     * Lists all Subsidiary entities.
     *
     * @Route("/{account}", name="subsidiary")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Clients\Subsidiary')->createQueryBuilder('c');
        $this->addQueryBuilderSort($qb, 'subsidiary');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Subsidiary entity.
     *
     * @Route("/{id}/show", name="subsidiary_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Subsidiary $subsidiary)
    {
        $editForm = $this->createForm(new SubsidiaryType(), $subsidiary, array(
            'action' => $this->generateUrl('subsidiary_update', array('id' => $subsidiary->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($subsidiary->getId(), 'subsidiary_delete');

        return array(

        'subsidiary' => $subsidiary,
        'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new Subsidiary entity.
     *
     * @Route("/new/{account}", name="subsidiary_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Account $account)
    {
        $subsidiary = new Subsidiary();
        $subsidiary->setAccount($account);
        $form = $this->createForm(new SubsidiaryType(), $subsidiary);

        return array(
            'account' => $account,
            'subsidiary' => $subsidiary,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Subsidiary entity.
     *
     * @Route("/{account}/create", name="subsidiary_create")
     * @Method("POST")
     * @Template("FlowerCoreBundle:Subsidiary:new.html.twig")
     */
    public function createAction(Account $account, Request $request)
    {
        $subsidiary = new Subsidiary();
        $subsidiary->setAccount($account);
        $form = $this->createForm(new SubsidiaryType(), $subsidiary);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($subsidiary);
            $em->flush();

            return $this->redirect($this->generateUrl('subsidiary_show', array('id' => $subsidiary->getId())));
        }

        return array(
            'subsidiary' => $subsidiary,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Subsidiary entity.
     *
     * @Route("/{id}/edit", name="subsidiary_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Subsidiary $subsidiary)
    {
        $editForm = $this->createForm(new SubsidiaryType(), $subsidiary, array(
            'action' => $this->generateUrl('subsidiary_update', array('id' => $subsidiary->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($subsidiary->getId(), 'subsidiary_delete');

        return array(
            'subsidiary' => $subsidiary,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Subsidiary entity.
     *
     * @Route("/{id}/update", name="subsidiary_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerCoreBundle:Subsidiary:edit.html.twig")
     */
    public function updateAction(Subsidiary $subsidiary, Request $request)
    {
        $editForm = $this->createForm(new SubsidiaryType(), $subsidiary, array(
            'action' => $this->generateUrl('subsidiary_update', array('id' => $subsidiary->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('subsidiary_show', array('id' => $subsidiary->getId())));
        }
        $deleteForm = $this->createDeleteForm($subsidiary->getId(), 'subsidiary_delete');

        return array(
            'subsidiary' => $subsidiary,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="subsidiary_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('subsidiary', $field, $type);

        return $this->redirect($this->generateUrl('subsidiary'));
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
     * Deletes a Subsidiary entity.
     *
     * @Route("/{id}/delete", name="subsidiary_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Subsidiary $subsidiary, Request $request)
    {
        $form = $this->createDeleteForm($subsidiary->getId(), 'subsidiary_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($subsidiary);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('subsidiary'));
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
