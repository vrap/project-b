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
        $builder->add('description', 'text',array(
                  'label'=> 'Sujet : '
                ))
            ->add('max', 'integer', array(
                  'label'=> 'Note maximale : ',
                  'attr' => array('min' => 1, 'max' => 20)
                ))
            ->add('module', 'entity', array(
                  'label'=> 'Module : ',
                  'class' => 'ProjectAppBundle:Module',
                  'property' => 'name'
                ))
            ->add('criterions_add', 'submit', array(
                    'label' => 'Ajouter un barème',
                    'attr' => (array( 'class' => 'btn btn-default' ))
                ))
            ->add('submit', 'submit', array(
                    'label' => 'Terminer',
                    'attr' => (array( 'class' => 'btn btn-second' ))
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Project\AppBundle\Entity\Evaluation',
            'cascade_validation' => true,
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
