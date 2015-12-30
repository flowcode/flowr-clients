<?php

namespace Flower\ClientsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubsidiaryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('name')
            ->add('street', null, array('required' => false))
            ->add('streetNumber', null, array('required' => false))
            ->add('department', null, array('required' => false))
            ->add('locality', null, array('required' => false))
            ->add('zipCode', null, array('required' => false))
            ->add('city', null, array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Clients\Subsidiary',
            'translation_domain' => 'Subsidiary',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'subsidiary';
    }
}
