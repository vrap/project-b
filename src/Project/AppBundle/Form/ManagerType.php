<?php

namespace Project\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ManagerType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', new UserType())
            ->add('formation', 'entity', array(
                'class'    => 'ProjectAppBundle:Formation',
                'property' => 'name',
                'multiple' => false,
                'expanded' => false,
                'label'    => 'Formation :'
            ));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Project\AppBundle\Entity\Manager'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project_appbundle_manager';
    }
}
