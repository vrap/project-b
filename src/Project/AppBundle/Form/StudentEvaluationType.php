<?php

namespace Project\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StudentEvaluationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('student', 'entity', array(
                                                  'class' => 'ProjectAppBundle:Student',
                                                  'property' => 'id',
                                                  'disabled' => true,
                                                  'expanded' => true
                                                  ));
        $builder->add('score');
        $builder->add('comment');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Project\AppBundle\Entity\StudentEvaluation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project_app_evaluation_create';
    }
}
