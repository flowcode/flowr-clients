<?php

namespace Flower\ClientsBundle\Controller\Client;

use Flower\ModelBundle\Entity\Project\Project;
use Flower\ModelBundle\Entity\Project\ProjectIteration;
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
 * @Route("/clients/p/projects")
 */
class ProjectController extends Controller
{


    /**
     * Finds and displays a Project entity.
     *
     * @Route("/{id}/show", name="client_access_project_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Project $project)
    {

        $em = $this->getDoctrine()->getManager();
        $overallSpent = $em->getRepository('FlowerModelBundle:Project\Project')->getOverallBy($project);
        $monthSpent = $em->getRepository('FlowerModelBundle:Project\Project')->getMonthBy($project);
        $weekSpent = $em->getRepository('FlowerModelBundle:Project\Project')->getWeekBy($project);
        if (!is_null($project->getEstimated())) {
            $spentPercentage = ($overallSpent * 100) / $project->getEstimated();
        } else {
            $spentPercentage = 0;
        }

        $spentPercentage = round($spentPercentage, 2);

        /* next events */
        $nextEvents = $em->getRepository('FlowerModelBundle:Planner\Event')->findBy(array("project" => $project), array("startDate" => "ASC"), 5);

        /* iterations */
        $iterations = $this->get('flower.project')->findWithStats($project, true);

        return array(
            'project' => $project,
            'overallSpent' => $overallSpent,
            'monthSpent' => $monthSpent,
            'weekSpent' => $weekSpent,
            'overallSpentRatio' => $spentPercentage,
            'nextEvents' => $nextEvents,
            'iterations' => $iterations,
        );
    }

    /**
     * Lists all Subsidiary entities.
     *
     * @Route("/", name="client_access_project")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Project\Project')->createQueryBuilder('p');

        $accounts = $this->getUser()->getAccounts();
        $accountsArr = array();
        foreach ($accounts as $account) {
            $accountsArr[] = $account->getId();
        }
        $qb->where('p.account IN (:accounts)')->setParameter('accounts', $accountsArr);
        $qb->andWhere('p.clientViewable = :is_viewable')->setParameter('is_viewable', true);

        $this->addQueryBuilderSort($qb, 'project');

        $paginator = $this->get('knp_paginator')->paginate($qb, $request->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * new iteration.
     *
     * @Route("/iteration/{id}", name="client_access_project_iteration_show")
     * @Method("GET")
     * @Template()
     */
    public function showIterationAction(ProjectIteration $iteration)
    {
        $em = $this->getDoctrine()->getManager();
        $burndown = array();

        $totalEstimated = 0;
        foreach ($iteration->getTasks() as $task) {
            $totalEstimated += $task->getEstimated();
        }


        $dataArr = array();
        $burndownPeriod = array();

        if(!is_null($iteration->getStartDate()) && !is_null($iteration->getDueDate())){

            $iterationPeriod = new \DatePeriod(
                $iteration->getStartDate(),
                new \DateInterval('P1D'),
                $iteration->getDueDate()->modify('+1 day')
            );

            foreach ($iterationPeriod as $iterationDate) {
                $insumed = $em->getRepository('FlowerModelBundle:Board\Task')->getEstimatedOn($iteration->getId(), $iterationDate);
                $insumed = is_null($insumed) ? 0 : $insumed;

                $dataArr[] = $totalEstimated - $insumed;
                $burndownPeriod[] = $iterationDate->format('d/m/Y');
            }

        }
        $tasksWithSpent = $em->getRepository('FlowerModelBundle:Board\Task')->getTaskWithSpent($iteration);


        $burndown = array(
            "label" => "Work",
            "fillColor" => "rgba(60,141,188,0.9)",
            "strokeColor" => "rgba(60,141,188,0.8)",
            "pointColor" => "#3b8bba",
            "pointStrokeColor" => "rgba(60,141,188,1)",
            "pointHighlightFill" => "#fff",
            "pointHighlightStroke" => "rgba(60,141,188,1)",
            "data" => $dataArr,
        );
        $iterations = $em->getRepository('FlowerModelBundle:Project\ProjectIteration')->findOneWithStats($iteration->getId());
        return array(
            'totalEstimated' => $totalEstimated,
            'burndownPeriod' => $burndownPeriod,
            'burndown' => $burndown,
            'tasks' => $tasksWithSpent,
            'iteration' => $iteration,
            "countTodo" => $iterations["todo_count"],
            "countDone" => $iterations["done_count"],
            "countInProgress" => $iterations["doing_count"],
        );
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="client_access_project_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('project', $field, $type);

        return $this->redirect($this->generateUrl('client_access_project'));
    }

    /**
     * @param string $name session name
     * @param string $field field name
     * @param string $type sort type ("ASC"/"DESC")
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
     * @param string $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }


}
