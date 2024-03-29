<?php


namespace Yoda\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array('required' => false))
            ->add('email', 'text', array('required' => false))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'required' => false)
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Yoda\UserBundle\Entity\User'));
    }

    public function getName()
    {
        return 'user_register';
    }

}