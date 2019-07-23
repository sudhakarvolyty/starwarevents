<?php


namespace Yoda\UserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Yoda\UserBundle\Entity\UserProfile;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text', array('label' => 'First Name:'))
            ->add('lastName', 'text', array('label' => 'Last Name:'))
            ->add('street1', 'text', array('label' => 'Enter Street1:'))
            ->add('street2', 'text', array('label' => 'Enter Street2:'))
            ->add('pincode', 'text', array('label' => 'Enter Pincode:'));

        //$builder->get('contactName')->setRequired(false);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Yoda\UserBundle\Entity\UserProfile'));
    }

}