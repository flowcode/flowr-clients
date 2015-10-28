<?php

namespace Flower\ClientsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CallEventStatusType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('name')
            ->add('finished')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Clients\CallEventStatus',
            'translation_domain' => 'CallEventStatus',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'calleventstatus';
    }
}
