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
 * @Route("/clients/report")
 */
class ReportController extends Controller
{
    /**
     * Lists all Subsidiary entities.
     *
     * @Route("/opportunities", name="clients_report_opportunity")
     * @Method("GET")
     * @Template()
     */
    public function opportunitiesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $stats = $em->getRepository('FlowerModelBundle:Clients\Opportunity')->getQuickStatus();

        return array(
            'stats' => $stats,
        );
    }


}
