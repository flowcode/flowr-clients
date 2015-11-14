<?php

namespace Flower\ClientsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class AccountType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name')
                ->add('businessName', null, array('required' => false))
                ->add('phone', null, array('required' => false))
                ->add('address', null, array('required' => false))
                ->add('activity')
                ->add('status')
                ->add('billingType', null, array('required' => false))
                ->add('cuit', null, array('required' => false,"label" => "CUIT/CUIL/DNI"))
                ->add('assignee',null, array('query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                ->orderBy('u.username', 'ASC');
                        },))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Clients\Account',
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
