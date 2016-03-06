<?php

namespace Flower\ClientsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Flower\ModelBundle\Entity\Clients\Opportunity;

class OpportunityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name')
            ->add('description')
            ->add('price')
            ->add('status')
            ->add('assignee')
            ->add('contact', null, array('required' => false))
            ->add('account', null, array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Clients\Opportunity',
            'translation_domain' => 'Opportunity',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'opportunity';
    }
}
