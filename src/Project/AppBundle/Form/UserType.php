<?php

namespace Project\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'label' => 'Nom',
                ))
            ->add('surname', 'text', array(
                'label' => 'Prénom',
                ))
            ->add('phone', 'text', array(
                'label' => 'Téléphone',
                ))
            ->add('email', 'email', array(
                'label' => 'Mail',
                ))
            ->add('plainPassword', 'password', array(
                'label' => 'Mot de passe',
                ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Project\AppBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project_appbundle_user';
    }
}
