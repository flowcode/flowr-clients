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
 * @Route("/clients/p/security")
 */
class LoginController extends Controller
{
    /**
     * Lists all Subsidiary entities.
     *
     * @Route("/login", name="client_access_security_login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return array(
            'last_username' => $lastUsername,
            'error' => $error,
        );
    }

    /**
     * Lists all Subsidiary entities.
     *
     * @Route("/logout", name="client_access_security_logout")
     * @Template()
     */
    public function logoutAction(Request $request)
    {
        return array();
    }

}
