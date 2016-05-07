<?php

namespace Flower\ClientsBundle\Controller\Client;

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
 * @Route("/clients/p/reports")
 */
class ReportController extends Controller
{

    /**
     * Lists all Subsidiary entities.
     *
     * @Route("/", name="client_access_report")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return array();
    }


}
