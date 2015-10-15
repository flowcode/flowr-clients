<?php

namespace Flower\ClientsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AccountType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name')
                ->add('phone', null, array('required' => false))
                ->add('address', null, array('required' => false))
                ->add('cuit', null, array('required' => false,"label" => "CUIT/CUIL/DNI"))
                ->add('assignee')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Account',
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
