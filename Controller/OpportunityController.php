<?php

namespace Flower\ClientsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Opportunity;
use Doctrine\ORM\QueryBuilder;
use Flower\ClientsBundle\Form\Type\OpportunityType;

/**
 * Opportunity controller.
 *
 * @Route("/opportunity")
 */
class OpportunityController extends Controller
{
    /**
     * Lists all Opportunity entities.
     *
     * @Route("/", name="opportunity")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Clients/Opportunity')->createQueryBuilder('o');
        $this->addQueryBuilderSort($qb, 'opportunity');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Opportunity entity.
     *
     * @Route("/{id}/show", name="opportunity_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Opportunity $opportunity)
    {
        $deleteForm = $this->createDeleteForm($opportunity->getId(), 'opportunity_delete');

        $em = $this->getDoctrine()->getManager();
        $opportunityBoards = $em->getRepository("FlowerModelBundle:Board")->getCurrentBoards(null,null,$opportunity->getId());

        return array(
            'opportunity' => $opportunity,
            'opportunityBoards' => $opportunityBoards,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Opportunity entity.
     *
     * @Route("/new", name="opportunity_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $opportunity = new Opportunity();
        $form = $this->createForm(new OpportunityType(), $opportunity);

        return array(
            'opportunity' => $opportunity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Opportunity entity.
     *
     * @Route("/create", name="opportunity_create")
     * @Method("POST")
     * @Template("FlowerClientsBundle:Opportunity:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $opportunity = new Opportunity();
        $form = $this->createForm(new OpportunityType(), $opportunity);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($opportunity);
            $em->flush();

            return $this->redirect($this->generateUrl('opportunity_show', array('id' => $opportunity->getId())));
        }

        return array(
            'opportunity' => $opportunity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Opportunity entity.
     *
     * @Route("/{id}/edit", name="opportunity_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Opportunity $opportunity)
    {
        $editForm = $this->createForm(new OpportunityType(), $opportunity, array(
            'action' => $this->generateUrl('opportunity_update', array('id' => $opportunity->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($opportunity->getId(), 'opportunity_delete');

        return array(
            'opportunity' => $opportunity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Opportunity entity.
     *
     * @Route("/{id}/update", name="opportunity_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerClientsBundle:Opportunity:edit.html.twig")
     */
    public function updateAction(Opportunity $opportunity, Request $request)
    {
        $editForm = $this->createForm(new OpportunityType(), $opportunity, array(
            'action' => $this->generateUrl('opportunity_update', array('id' => $opportunity->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('opportunity_show', array('id' => $opportunity->getId())));
        }
        $deleteForm = $this->createDeleteForm($opportunity->getId(), 'opportunity_delete');

        return array(
            'opportunity' => $opportunity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="opportunity_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('opportunity', $field, $type);

        return $this->redirect($this->generateUrl('opportunity'));
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
     * Deletes a Opportunity entity.
     *
     * @Route("/{id}/delete", name="opportunity_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Opportunity $opportunity, Request $request)
    {
        $form = $this->createDeleteForm($opportunity->getId(), 'opportunity_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($opportunity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('opportunity'));
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
        /**
     * Displays a form to create a new Board entity.
     *
     * @Route("/opportunity/{id}/new", name="board_new_to_opportunity")
     * @Method("GET")
     * @Template("FlowerBoardBundle:Board:new.html.twig")
     */
    public function newToOpportunityAction(Opportunity $opportunity)
    {
        $board = new Board();
        $board->setOpportunity($opportunity);
        $form = $this->createForm($this->get("form.type.board"), $board, array(
            'action' => $this->generateUrl('board_create'),
            'method' => 'POST',
        ));

        return array(
            'board' => $board,
            'form' => $form->createView(),
        );
    }
}
