<?php

namespace Flower\ClientsBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\ClientsBundle\Form\Type\CallEventType;
use Flower\ModelBundle\Entity\Board\History;
use Flower\ModelBundle\Entity\Clients\CallEvent;
use Flower\ModelBundle\Entity\Clients\CallEventStatus;
use Flower\ModelBundle\Entity\User\User;
use Flower\ModelBundle\Entity\Clients\Account;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
        $accountAlias = "a";
        $qb->leftJoin("ce.status","s");
        $qb->leftJoin("ce.account",$accountAlias);
        $qb->leftJoin("ce.assignee","u");
        /* filter by org security groups */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $secGroupSrv = $this->get('user.service.securitygroup');
            $qb = $secGroupSrv->addLowerSecurityGroupsFilter($qb, $this->getUser(), $accountAlias);
        }

        $translator = $this->get('translator');
        $dateformat = $translator->trans('fullDateTime');
        $filters = array(
            'statusFilter' => "s.id",
            'assigneeFilter' => "u.id",
            'accountAssigneeFilter' => "a.assignee",
            'accountFilter' => "a.id",
            'startDateFilter' => array("field"=> "ce.date", "type" => "date", "format" => $dateformat, "operation" => ">"),
            'endDateFilter' => array("field"=> "ce.date", "type" => "date","format" => $dateformat , "operation" => "<="),
            );

        if($request->query->has('reset')) {
            $request->getSession()->set('filter.callevent', null);
            return $this->redirectToRoute("callevent");
        }
        $this->saveFilters($request, $filters, 'callevent','callevent');
        $paginator = $this->filter($qb,'callevent',$request);

        $statuses = $em->getRepository('FlowerModelBundle:Clients\CallEventStatus')->findBy(array(),array("name" => "ASC"));
        $users = $em->getRepository('FlowerModelBundle:User\User')->findBy(array(),array("username" => "ASC"));
        
        $accountAlias = "ac";
        $qb = $em->getRepository('FlowerModelBundle:Clients\Account')->createQueryBuilder($accountAlias);
        /* filter by org security groups */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $secGroupSrv = $this->get('user.service.securitygroup');
            $qb = $secGroupSrv->addLowerSecurityGroupsFilter($qb, $this->getUser(), $accountAlias);
        }

        $accounts = $qb->getQuery()->getResult();


        $filters = $this->getFilters('callevent');
        return array(
            'startDateFilter' => isset($filters['startDateFilter'])?$filters['startDateFilter']["value"] : null,
            'endDateFilter' => isset($filters['endDateFilter'])?$filters['endDateFilter']["value"] : null,
            'assigneeFilter' => isset($filters['assigneeFilter'])?$filters['assigneeFilter']["value"] : null,
            'accountAssigneeFilter' => isset($filters['accountAssigneeFilter'])?$filters['accountAssigneeFilter']["value"] : null,
            'statusFilter' => isset($filters['statusFilter'])?$filters['statusFilter']["value"] : null,
            'users' => $users,
            'accountFilter' => isset($filters['accountFilter'])?$filters['accountFilter']["value"] : null,
            'accounts' => $accounts,
            'statuses' => $statuses,
            'paginator' => $paginator,
        );
    }

    /**
     * Lists all Account entities.
     *
     * @Route("/planner", name="callevent_planner")
     * @Method("GET")
     * @Template()
     */
    public function plannerAction(Request $request)
    {
        if($request->query->has('reset')) {
            $request->getSession()->set('filter.callevent_planner', null);
            $request->getSession()->set('sort.callevent_planner', null);
            return $this->redirectToRoute("callevent_planner");
        }
        $em = $this->getDoctrine()->getManager();
        $translator = $this->get('translator');
        $dateformat = $translator->trans('fullDateTime');
        $order = $this->getOrder("callevent_planner");
        $filters = array(
            'statusFilter' => "ce.status",
            'accountFilter' => "a.id",
            'startDateFilter' => array("field"=> "ce.date", "type" => "date", "format" => $dateformat, "operation" => ">"),
            'endDateFilter' => array("field"=> "ce.date", "type" => "date", "format" => $dateformat, "operation" => "<="),
            );
        $this->saveFilters($request, $filters, 'callevent_planner','callevent_planner');  
        $filters = $this->getFilters("callevent_planner");

        $count = count($em->getRepository('FlowerModelBundle:Clients\CallEvent')->countPlannerQuery($filters));
        $limit = 20;
        $current = $request->query->get('page', 1);
        $offset = ($current -1) * $limit;
        $pageCount = ceil($count /$limit );
        $pagesInRange = range(1, $pageCount);
        $where = "";
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $securityGroupSrv = $this->get('user.service.securitygroup');
            $lowerSecurityGroups = $securityGroupSrv->getLowerGroupsIds($this->getUser());
            if(count($lowerSecurityGroups) > 0){
                $where = " sg.id in (";
                $where .= implode(",", $lowerSecurityGroups).")";
            }else{
                $where = " 1=2 "; //if the user don't have security group don't show data
            }
        }

        $accountsfilterd = $em->getRepository('FlowerModelBundle:Clients\CallEvent')->getPlannerAccounts($filters,$order,$limit,$offset,$where);

        
        $statuses = $em->getRepository('FlowerModelBundle:Clients\CallEventStatus')->findBy(array(),array("name" => "ASC"));
        $accounts = $em->getRepository('FlowerModelBundle:Clients\Account')->findBy(array(),array("name" => "ASC"));
        $users = $em->getRepository('FlowerModelBundle:User\User')->findBy(array(),array("username" => "ASC"));
        $penddingStatus = $em->getRepository('FlowerModelBundle:Clients\CallEventStatus')->findBy(array("finished" => 0));
        $penddingStatusId = 0;
        if(count($penddingStatus)>0){
            $penddingStatusId = $penddingStatus[0]->getId();
        }
        return array(
            'startDateFilter' => isset($filters['startDateFilter'])?$filters['startDateFilter']["value"] : null,
            'endDateFilter' => isset($filters['endDateFilter'])?$filters['endDateFilter']["value"] : null,
            'statusFilter' => isset($filters['statusFilter'])?$filters['statusFilter']["value"] : null,
            'accountFilter' => isset($filters['accountFilter'])?$filters['accountFilter']["value"] : null,
            'users' => $users,
            'penddingStatusId' => $penddingStatusId,
            'accounts' => $accounts,
            'statuses' => $statuses,
            'paginator' => $accountsfilterd,
            'current' => $current,
            'pageCount' => $pageCount,
            'last' => $pageCount,
            'pagesInRange' => $pagesInRange,
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

        //Cantidad de paginado
        $limit = 20;
        $currPage = $request->query->get('page');
        if($currPage){
            $callevents = $this->filter($qb,'callevent',$request, $limit, $currPage);
        } else {
            $callevents = $this->filter($qb,'callevent',$request, -1);
        }

        $data = $this->get("client.service.callevent")->callEventDataExport($callevents);
        $this->get("client.service.excelexport")->exportData($data,"Llamadas","Mi descripcion");
        die();
        return $this->redirectToRoute("callevent");
    }
    /**
     *
     * @Route("/bulkcreate", name="callevent_bulk_create", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function bulkCreateAction(Request $request)
    {
        $accounts = $request->query->get("accounts");

        if(!$accounts){
            return new JsonResponse(null, 403);
        }
        $em = $this->getDoctrine()->getManager();
        $status = $request->query->get("status");
        if($status){
            $status = $em->getRepository('FlowerModelBundle:Clients\CallEventStatus')->find($status);
        }
        $assignee = $request->query->get("assignee");
        if($assignee){
            $assignee = $em->getRepository('FlowerModelBundle:User\User')->find($assignee);
        }
        $date = $request->query->get("date");

        $translator = $this->get('translator');
        $dateformat = $translator->trans('fullDateTime');
        $date = \DateTime::createFromFormat($dateformat, $date);

        $subject = $request->query->get("subject");
        foreach ($accounts as $account) {
            $account = $em->getRepository('FlowerModelBundle:Clients\Account')->find($account);
            $callevent = new CallEvent();
            $callevent->setAssignee($assignee);
            $callevent->setDate($date);
            $callevent->setCallEventSatus($status);
            $callevent->setSubject($subject);
            $callevent->setAccount($account);
            $em->persist($callevent);
        }
        $em->flush();
        return new JsonResponse(null, 200);
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
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $canSee = $this->get("user.service.securitygroup")->userCanSeeEntity($user,$callevent->getAccount());
            if(!$canSee){
                throw new AccessDeniedException();
            }
        }
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
        $callEvent->setDate(new \DateTime());
        $callEvent->setAssignee($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $statueses = $em->getRepository('FlowerModelBundle:Clients\CallEventStatus')->findBy(array("finished" => 0));
        if(count($statueses) > 0){
            $callEvent->setStatus($statueses[0]);
        }
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

            $this->get('board.service.history')->addSimpleUserActivity(History::TYPE_CALL_EVENT, $this->getUser(), $callEvent, History::CRUD_CREATE);

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'callevent_new' : 'callevent_show';
            if($form->get('saveAndAdd')->isClicked() && $callEvent->getAccount()){
                return $this->redirectToRoute("callevent_new_account", array("account" => $callEvent->getAccount()->getId()));
            }
            return $this->redirectToRoute($nextAction, array("id" => $callEvent->getId()));
        }

        return array(
            'callevent' => $callEvent,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new CallEvent entity.
     *
     * @Route("/new/{account}", name="callevent_new_account")
     * @Method("GET")
     * @Template("FlowerClientsBundle:CallEvent:show.html.twig")
     */
    public function newForAccountAction(Account $account)
    {
        $callEvent = new CallEvent();
        $callEvent->setAccount($account);
        $callEvent->setDate(new \DateTime());
        $callEvent->setAssignee($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $statueses = $em->getRepository('FlowerModelBundle:Clients\CallEventStatus')->findBy(array("finished" => 0));
        if(count($statueses) > 0){
            $callEvent->setStatus($statueses[0]);
        }
        $form = $this->createForm($this->get(CallEventController::formName), $callEvent, array(
            'action' => $this->generateUrl('callevent_create'),
            'method' => 'POST',
        ));
        return array(
            'callevent' => $callEvent,
            'edit_form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new CallEvent entity.
     *
     * @Route("/duplicate/{call}", name="callevent_duplicate")
     * @Method("GET")
     * @Template("FlowerClientsBundle:CallEvent:show.html.twig")
     */
    public function duplicateAction(CallEvent $call)
    {
        $callEvent = new CallEvent();
        $callEvent->setAccount($call->getAccount());
        $callEvent->setSubject($call->getSubject());
        $callEvent->setDate(new \DateTime());
        $callEvent->setAssignee($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $statueses = $em->getRepository('FlowerModelBundle:Clients\CallEventStatus')->findBy(array("finished" => 0));
        if(count($statueses) > 0){
            $callEvent->setStatus($statueses[0]);
        }
        $form = $this->createForm($this->get(CallEventController::formName), $callEvent, array(
            'action' => $this->generateUrl('callevent_create'),
            'method' => 'POST',
        ));
        return array(
            'callevent' => $callEvent,
            'edit_form' => $form->createView(),
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

            $this->get('board.service.history')->addSimpleUserActivity(History::TYPE_CALL_EVENT, $this->getUser(), $callEvent, History::CRUD_UPDATE);

            $nextAction = $editForm->get('saveAndAdd')->isClicked() ? 'callevent_new' : 'callevent_show';
            if($callEvent->getAccount() && $editForm->get('saveAndAdd')->isClicked()){
                return $this->redirectToRoute("callevent_new_account", array("account" => $callEvent->getAccount()->getId()));
            }
            return $this->redirectToRoute($nextAction, array("id" => $callEvent->getId()));

            //return $this->redirect($this->generateUrl('callevent_show', array('id' => $callEvent->getId())));
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
     * @Route("/planner/order/{field}/{type}", name="callevent_planner_sort")
     */
    public function sortplannerAction($field, $type)
    {
        $this->setOrder('callevent_planner', $field, $type);

        return $this->redirect($this->generateUrl('callevent_planner'));
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
