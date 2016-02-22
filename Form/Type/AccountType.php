<?php

namespace Flower\ClientsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AccountType extends AbstractType
{

    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('businessName', null, array('required' => true))
            ->add('phone', null, array('required' => false))
            ->add('address', null, array('required' => false))
            ->add('activity')
            ->add('status')
            ->add('billingType', null, array('required' => false))
            ->add('cuit', null, array('required' => false, "label" => "CUIT/CUIL/DNI"))
            ->add('assignee')
        ;

        if ($this->authorizationChecker->isGranted("ROLE_ADMIN")) {
            $builder->add('securityGroups', 'entity', array(
                'class' => 'FlowerModelBundle:User\SecurityGroup',
                'property' => 'name',
                'required' => false,
                'multiple' => true,
            ));
        }

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Clients\Account',
            'translation_domain' => 'Account',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'account';
    }

}
