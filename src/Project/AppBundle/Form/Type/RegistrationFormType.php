<?php

namespace Project\AppBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('username', null, array(/*'label' => 'form.username',*/ 'translation_domain' => 'FOSUserBundle'))
            ->add('email', 'email', array(/*'label' => 'form.email',*/ 'translation_domain' => 'FOSUserBundle'))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Password confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('surname', null)
        ;
    }

    public function getName()
    {
        return 'override_fosuser_registration';
    }

}
