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
                ->add('account', 'genemu_jqueryselect2_entity', array(
                    'class' => 'Flower\ModelBundle\Entity\Clients\Account',
                    'property' => 'name',
                    'multiple' => false,
                ))
                ->add('contactName',null, array( 'required' => false))
                ->add('date','collot_datetime', array( 'required' => true,'pickerOptions' =>
                                                array('format' => 'dd/mm/yyyy  hh:ii',
                                                    'autoclose' => true,
                                                    'todayBtn' => true,
                                                    'todayHighlight' => true,
                                                    'keyboardNavigation' => true,
                                                    'language' => 'en',
                                                    )))
                ->add('status')
                ->add('assignee', 'genemu_jqueryselect2_entity', array(
                    'class' => 'Flower\ModelBundle\Entity\User\User',
                    'property' => 'username',
                    'multiple' => false,
                ))
                ->add("description", 'textarea', array(
                        'required' => false,
                        'attr' => array('rows' => '10'),
                    ))
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
