<?php

namespace Project\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CorporationType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                        'label' => 'Nom : '
                ))
            ->add('email', 'email', array(
                        'label' => 'Adresse email : '
                ))
            ->add('phone', 'text', array(
                        'label' => 'Téléphone : '
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Project\AppBundle\Entity\Corporation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project_appbundle_corporation';
    }
}
