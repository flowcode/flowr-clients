<?php

namespace Flower\ClientsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


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
            ->add('contact', 'tetranz_select2entity', [
                'multiple' => false,
                'remote_route' => 'flower_api_clients_contact_findall_simple',
                'class' => '\Flower\ModelBundle\Entity\Clients\Contact',
                'text_property' => 'email',
                'minimum_input_length' => 2,
                'page_limit' => 10,
                'language' => 'es',
                'placeholder' => 'Seleccionar contactos',
            ])
            ->add('account', null, array('required' => false));
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
