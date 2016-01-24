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
    private $accountService;

    public function __construct(SecurityContext $securityContext, $accountService)
    {
        $this->securityContext = $securityContext;
        $this->accountService = $accountService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
                ->add('firstname', null, array('required' => false))
                ->add('lastname', null, array('required' => false))
                ->add('email')
                ->add('address', null, array('required' => false))
                ->add('phone', null, array('required' => false))
                ->add('observations', null, array('required' => false))
                ->add('accounts', 'entity', array(
                    'class' => 'FlowerModelBundle:Clients\Account',
                    'choices' => $this->accountService->findAll(),
                    'multiple'    => true,
                    'required' => false
                ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Clients\Contact',
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
