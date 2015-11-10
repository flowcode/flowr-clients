<?php

namespace Flower\ClientsBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\ClientsBundle\Form\Type\ContactType;
use Flower\ModelBundle\Entity\Clients\Contact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contact controller.
 *
 * @Route("/contact")
 */
class ContactController extends Controller
{

    /**
     * Lists all Contact entities.
     *
     * @Route("/", name="contact")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Clients\Contact')->createQueryBuilder('c');        
        $this->addQueryBuilderSort($qb, 'contact');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Contact entity.
     *
     * @Route("/{id}/show", name="contact_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Contact $contact)
    {
        $editForm = $this->createForm($this->get('form.type.contact'), $contact, array(
            'action' => $this->generateUrl('contact_update', array('id' => $contact->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($contact->getId(), 'contact_delete');

        return array(
            'edit_form' => $editForm->createView(),
            'contact' => $contact,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Contact entity.
     *
     * @Route("/new", name="contact_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $contact = new Contact();
        $form = $this->createForm($this->get('form.type.contact'), $contact);

        return array(
            'contact' => $contact,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Contact entity.
     *
     * @Route("/account/{id}/new", name="contact_new_to_account")
     * @Method("GET")
     * @Template("FlowerClientsBundle:Contact:new.html.twig")
     */
    public function newToAccountAction(\Flower\ModelBundle\Entity\Clients\Account $account)
    {
        $contact = new Contact();
        $contact->addAccount($account);
        $form = $this->createForm($this->get('form.type.contact'), $contact);

        return array(
            'contact' => $contact,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Contact entity.
     *
     * @Route("/create", name="contact_create")
     * @Method("POST")
     * @Template("FlowerClientsBundle:Contact:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm($this->get('form.type.contact'), $contact);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();            
            $em->persist($contact);
            $em->flush();

            return $this->redirect($this->generateUrl('contact_show', array('id' => $contact->getId())));
        }

        return array(
            'contact' => $contact,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Contact entity.
     *
     * @Route("/{id}/edit", name="contact_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Contact $contact)
    {
        $editForm = $this->createForm($this->get('form.type.contact'), $contact, array(
            'action' => $this->generateUrl('contact_update', array('id' => $contact->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($contact->getId(), 'contact_delete');

        return array(
            'contact' => $contact,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Contact entity.
     *
     * @Route("/{id}/update", name="contact_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerClientsBundle:Contact:edit.html.twig")
     */
    public function updateAction(Contact $contact, Request $request)
    {
        $editForm = $this->createForm($this->get('form.type.contact'), $contact, array(
            'action' => $this->generateUrl('contact_update', array('id' => $contact->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('contact_show', array('id' => $contact->getId())));
        }
        $deleteForm = $this->createDeleteForm($contact->getId(), 'contact_delete');

        return array(
            'contact' => $contact,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="contact_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('contact', $field, $type);

        return $this->redirect($this->generateUrl('contact'));
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
     * Deletes a Contact entity.
     *
     * @Route("/{id}/delete", name="contact_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Contact $contact, Request $request)
    {
        $form = $this->createDeleteForm($contact->getId(), 'contact_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contact);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('contact'));
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
