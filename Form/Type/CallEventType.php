<?php

namespace Flower\ClientsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CallEventType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('subject')
                ->add('account')
                ->add('date','collot_datetime', array( 'required' => true,'pickerOptions' =>
                                                array('format' => 'dd/mm/yyyy  hh:ii',
                                                    'autoclose' => true,
                                                    'todayBtn' => true,
                                                    'todayHighlight' => true,
                                                    'keyboardNavigation' => true,
                                                    'language' => 'en',
                                                    )))
                ->add('status')
                ->add('assignee')
                ->add("description")
                ->add('save', 'submit', array('label' => 'Save'))
                ->add('saveAndAdd', 'submit', array('label' => 'Save and add'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Clients\CallEvent',
            'translation_domain' => 'CallEvent',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'callevent';
    }

}
