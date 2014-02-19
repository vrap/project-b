<?php

namespace Project\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PromotionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('formation', 'entity', array(
                                               'label' => 'Formation :',
                                               'class' => 'ProjectAppBundle:Formation',
                                               'property' => 'name'
            ))
            ->add('startDate', 'date', array(
                'label' => 'Date de début',
            ))
            ->add('endDate', 'date', array(
                'label' => 'Date de fin',
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Project\AppBundle\Entity\Promotion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project_appbundle_promotion';
    }
}
