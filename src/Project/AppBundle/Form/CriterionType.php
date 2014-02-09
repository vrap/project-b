<?php

namespace Project\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CriterionType extends AbstractType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', 'text', array(
                    'label' => 'Libellé : '
                ))
            ->add('max', 'integer', array(
                    'label' => 'Note : ',
                    'attr' => array('min' => 1, 'max' => 20)
                ))
            ->add('evaluation', 'entity', array(
                    'label'=> 'Evaluation : ',
                    'class' => 'ProjectAppBundle:Evaluation',
                    'property' => 'description'
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Project\AppBundle\Entity\Criterion',
            ''
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project_appbundle_criterion';
    }
}
