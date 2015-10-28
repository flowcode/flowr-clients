<?php

namespace Flower\ClientsBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\ClientsBundle\Form\Type\AccountType;
use Flower\ModelBundle\Entity\Clients\Account;
use Flower\ModelBundle\Entity\Board\TaskStatus;
use Flower\ModelBundle\Entity\Board\Board;
use Flower\ModelBundle\Entity\Board\TaskType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Account controller.
 *
 * @Route("/account")
 */
class AccountController extends Controller
{

    /**
     * Lists all Account entities.
     *
     * @Route("/", name="account")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();        
        $qb = $em->getRepository('FlowerModelBundle:Clients\Account')->createQueryBuilder('a');
        $qb->leftJoin("a.activity","ac");
        $this->addQueryBuilderSort($qb, 'account');
        $activityFilter = $request->query->get('activityFilter');
        $this->addFilter($qb,$activityFilter,"ac.id");
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        $activities = $em->getRepository('FlowerModelBundle:Clients\Activity')->findAll();
        return array(
            'paginator' => $paginator,
            'activityFilter' => $activityFilter,
            'activities' => $activities,
        );
    }

    private function addFilter($qb, $filter, $field)
    {
        if($filter && count($filter) > 0){    
            if( implode(",", $filter) != ""){
                $filterAux = array();
                $nullFilter = "";
                foreach ($filter as $element) {
                    if($element == "-1"){
                        $nullFilter = " OR  (".$field." is NULL)";
                    }else{
                        $filterAux[] = $element;
                    }
                }
                if(count($filterAux) > 0){
                    $qb->andWhere(" ( ".$field." in (:".str_replace(".","_",$field)."_param) ".$nullFilter." )")->setParameter(str_replace(".","_",$field)."_param", $filterAux);
                }else{
                    $qb->andWhere(" ( 1 = 2 ".$nullFilter." )");
                }
            }
        }
    }

    /**
     * Exports displayed Account entities.
     *
     * @Route("/export", name="account_export")
     * @Method("GET")
     * @Template()
     */
    public function exportDataAction(Request $request)
    {
        $accounts = $request->query->get('activities');
            print_r($accounts[0]);
            echo " || ";
            die("asd");
        foreach ($accounts as $account) {
        }
        $this->get('client.service.excelexport')->exportAll();
    }

    /**
     * Finds and displays a Account entity.
     *
     * @Route("/{id}/show", name="account_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Account $account, Request $request)
    {
        $deleteForm = $this->createDeleteForm($account->getId(), 'account_delete');

        $em = $this->getDoctrine()->getManager();

        $todoStatus = $em->getRepository("FlowerModelBundle:Board\TaskStatus")->findOneBy(array("name" => TaskStatus::STATUS_TODO));
       // $nextBugs = $em->getRepository("FlowerModelBundle:Task")->findByStatusAndType(TaskType::TYPE_BUG, $todoStatus->getId(), $account->getId(), null, null, 10);

        //$todoTasks = $em->getRepository("FlowerModelBundle:Task")->findByStatusAndType(TaskType::TYPE_TASK, $todoStatus->getId(), $account->getId(), null, null, 10);
        $accountBoards = $account->getBoards();

        $currentProjects = $em->getRepository("FlowerModelBundle:Project\Project")->findBy(array("account" => $account));

        $qb = $em->getRepository('FlowerModelBundle:Clients\Contact')->getByAccountQuery($account->getId());
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        $accauntcalls = $em->getRepository('FlowerModelBundle:Clients\CallEvent')->findBy(array("account" => $account),array("date" => "DESC"),10);

        return array(
            'accauntcalls' => $accauntcalls,
            'account' => $account,
           // 'nextBugs' => $nextBugs,
            //'todoTasks' => $todoTasks,
            'accountBoards' => $accountBoards,
            'currentProjects' => $currentProjects,
            'paginator' => $paginator,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Account entity.
     *
     * @Route("/new", name="account_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $account = new Account();
        $form = $this->createForm(new AccountType(), $account);

        return array(
            'account' => $account,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Account entity.
     *
     * @Route("/create", name="account_create")
     * @Method("POST")
     * @Template("FlowerClientsBundle:Account:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $account = new Account();
        $form = $this->createForm(new AccountType(), $account);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            return $this->redirect($this->generateUrl('account_show', array('id' => $account->getId())));
        }

        return array(
            'account' => $account,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Account entity.
     *
     * @Route("/{id}/edit", name="account_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Account $account)
    {
        $editForm = $this->createForm(new AccountType(), $account, array(
            'action' => $this->generateUrl('account_update', array('id' => $account->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($account->getId(), 'account_delete');

        return array(
            'account' => $account,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Account entity.
     *
     * @Route("/{id}/update", name="account_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerClientsBundle:Account:edit.html.twig")
     */
    public function updateAction(Account $account, Request $request)
    {
        $editForm = $this->createForm(new AccountType(), $account, array(
            'action' => $this->generateUrl('account_update', array('id' => $account->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('account_show', array('id' => $account->getId())));
        }
        $deleteForm = $this->createDeleteForm($account->getId(), 'account_delete');

        return array(
            'account' => $account,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="account_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('account', $field, $type);

        return $this->redirect($this->generateUrl('account'));
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
     * Deletes a Account entity.
     *
     * @Route("/{id}/delete", name="account_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Account $account, Request $request)
    {
        $form = $this->createDeleteForm($account->getId(), 'account_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($account);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('account'));
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

    /**
     * Displays a form to create a new Board entity.
     *
     * @Route("/account/{id}/new", name="board_new_to_account")
     * @Method("GET")
     * @Template("FlowerBoardBundle:Board:new.html.twig")
     */
    public function newBoardAction(Account $account)
    {
        $board = new Board();
        $form = $this->createForm($this->get('form.type.board'), $board);
                $form = $this->createForm($this->get("form.type.board"), $board, array(
                    'action' => $this->generateUrl('account_board_create',array("id" => $account->getId())),
                    'method' => 'POST',
                ));

        return array(
            'board' => $board,
            'form' => $form->createView(),
        );
    }


    /**
     * Creates a new Board entity.
     *
     * @Route("/{id}/create", name="account_board_create")
     * @Method("POST")
     * @Template("FlowerBoardBundle:Board:new.html.twig")
     */
    public function createBoardAction(Account $account, Request $request)
    {
        $board = new Board();
        $form = $this->createForm($this->get('form.type.board'), $board);
        if ($form->handleRequest($request)->isValid()) {
            $account->addBoard($board);
            $em = $this->getDoctrine()->getManager();
            $em->persist($board);
            $em->flush();

            return $this->redirect($this->generateUrl('board_show', array('id' => $board->getId())));
        }

        return array(
            'board' => $board,
            'form'   => $form->createView(),
        );
    }
}
