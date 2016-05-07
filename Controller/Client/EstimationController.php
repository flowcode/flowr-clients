<?php

namespace Flower\ClientsBundle\Controller\Client;

use Flower\ModelBundle\Entity\Project\Estimation;
use Flower\ModelBundle\Entity\Project\Project;
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
 * @Route("/clients/p/estimations")
 */
class EstimationController extends Controller
{


    /**
     * Finds and displays a Project entity.
     *
     * @Route("/{id}/show", name="client_access_estimation_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Estimation $estimation)
    {
        $estimationService = $this->get("flower.estimation");

        $processedData = $estimationService->getProcessedData($estimation);

        return array(
            'estimation' => $estimation,
            'data' => $processedData,
        );
    }

    /**
     * Lists all Subsidiary entities.
     *
     * @Route("/", name="client_access_estimation")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Project\Estimation')->createQueryBuilder('e');

        $accounts = $this->getUser()->getAccounts();
        $accountsArr = array();
        foreach ($accounts as $account) {
            $accountsArr[] = $account->getId();
        }
        $qb->where('e.account IN (:accounts)')->setParameter('accounts', $accountsArr);
        $qb->andWhere('e.clientViewable = :is_viewable')->setParameter('is_viewable', true);

        $this->addQueryBuilderSort($qb, 'project');

        $paginator = $this->get('knp_paginator')->paginate($qb, $request->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="client_access_estimation_sort")
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
