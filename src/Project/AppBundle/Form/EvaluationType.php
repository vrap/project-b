<?php

namespace Project\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EvaluationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description', 'text')
            ->add('max', 'text')
            ->add('module_id', 'integer');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Project\AppBundle\Entity\Evaluation'
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
