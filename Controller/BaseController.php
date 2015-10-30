<?php

namespace Flower\ClientsBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Base controller.
 *
 * @Route("/base")
 */
class BaseController extends Controller
{
    protected function filter($qb,$name,$request)
    {
    	$filterNumber = 0;
        $this->addQueryBuilderSort($qb, $name);
        if(!is_null($this->getFilters($name))){
            foreach ($this->getFilters($name) as $search) {
            	$filterNumber++;
            	$value = $search["value"];
            	$options  = $search["options"];
            	if(is_array($options)) {
            		$this->addCustomFilter($qb,$value, $options,$filterNumber);
            	} else {
                	$this->addFilter($qb, $value, $options,$filterNumber);
            	}
            }
        }
        return $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

    }
    private function addCustomFilter($qb,$value, $options,$filterNumber){
    	$operation = $options["operation"];
    	$field = $options["field"];
    	$type =  $options["type"];
    	if($type == "date"){
    		$value = \DateTime::createFromFormat('d/m/Y H:i', $value);
    	}
    	if($type == "array"){
    		$this->addFilter($qb,$value,$field);
    	}else{
    		$name = str_replace(".","_",$field)."_".$filterNumber;
			$qb->andWhere($field. " ".$operation." :".$name."_value") ->setParameter($name."_value", $value);
    	}
    }
    /**
     * return an array like this array("value"=> $request->query->get($filterName), "options" => $options);
     * @param  [type] $name to search in session
     */
    protected function getFilters($name)
    {
        return $this->getRequest()->getSession()->get('filter.' . $name);
    }

    protected function saveFilters($request , $filters, $name, $route = null, array $params = null)
    {
        $myFilters = array();
        foreach ($filters as $filterName => $options) {
            if($request->query->get($filterName)){
                $myFilters[$filterName] = array("value"=> $request->query->get($filterName), "options" => $options);
            }   
        }
        if(count($myFilters) > 0){
            $request->getSession()->set('filter.' . $name, $myFilters);
        }
    }

    protected function addFilter($qb, $filter, $field,$filterNumber)
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
                	$name = str_replace(".","_",$field)."_".$filterNumber;
                    $qb->andWhere(" ( ".$field." in (:".$name."_param) ".$nullFilter." )")->setParameter($name."_param", $filterAux);
                }else{
                    $qb->andWhere(" ( 1 = 2 ".$nullFilter." )");
                }
            }
        }
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