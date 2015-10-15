<?php

namespace Flower\ClientsBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

class ContactType extends AbstractType
{

    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userAccount = $this->securityContext->getToken()->getUser()->getUserAccount();

        $builder
                ->add('firstname', null, array('required' => false))
                ->add('lastname', null, array('required' => false))
                ->add('email')
                ->add('address', null, array('required' => false))
                ->add('phone', null, array('required' => false))
                ->add('accounts', 'entity', array(
                    'class' => 'FlowerModelBundle:Account',
                    'multiple'    => true,
                    'query_builder' => function (EntityRepository $er) use ($userAccount) {
                        return $er->createQueryBuilder('ac')
                                ->where('ac.userAccount = :user_account')->setParameter("user_account", $userAccount);
                    },
                ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Contact',
            'translation_domain' => 'Contact',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'contact';
    }

}
