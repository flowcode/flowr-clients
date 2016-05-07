<?php

namespace Flower\ClientsBundle\Service;

use Flower\ModelBundle\Entity\Clients\Contact;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Description of CallEventService
 *
 * @author Juan Manuel Aguero <jaguero@flowcode.com.ar>
 */
class ClientService implements UserProviderInterface
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(ContainerInterface $container = NULL)
    {
        $this->container = $container;
        $this->em = $this->container->get("doctrine.orm.entity_manager");
    }

    public function loadUserByUsername($username)
    {
        $contact = $this->em->getRepository('FlowerModelBundle:Clients\Contact')->findOneBy(array(
            'email' => $username,
        ));

        if ($contact) {
            return $contact;
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof Contact) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Flower\ModelBundle\Entity\Clients\Contact';
    }

}