<?php

namespace Flower\ClientsBundle\Form\Type\Api;

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
            ->add('account')
            ->add('street', null, array('required' => false))
            ->add('street_number',null,array(
                'property_path' => 'streetNumber',
                'required' => false))
            ->add('department', null, array('required' => false))
            ->add('locality', null, array('required' => false))
            ->add('zip_code',null,array(
                'property_path' => 'zipCode',
                'required' => false))
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
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return '';
    }
}
