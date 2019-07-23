<?php


namespace Yoda\UserBundle\Validations;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class UserRegistration
{
    public static function validateUserName($object, ExecutionContextInterface $context)
    {
       $arrNames = array('sai', 'ram', 'shiva', 'krishna');
        $userName = $object->getUserName();

       if (in_array($userName, $arrNames)) {
            $context->buildViolation('Username "'. $userName . '" is a reserved, kinldy choose other username')
                ->atPath('username')
                ->addViolation();
       }
    }
}