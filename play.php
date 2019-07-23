<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;
use Yoda\UserBundle\Entity\UserProfile;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Yoda\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Yoda\EventBundle\Entity\Event;

$loader = require_once __DIR__ . '/app/bootstrap.php.cache';
Debug::enable();


require_once __DIR__ . '/app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$kernel->boot();
$container = $kernel->getContainer();
$container->enterScope('request');
$container->set('request', $request);

$em = $container->get('doctrine')->getManager();

$profile = new UserProfile();
$user = new User();

$user = $em->getRepository('UserBundle:User')->find(3);

dump($user); die;
$profile->setUser($user);
$em->flush($profile);
$em->flush();

//
//$event = new Event();
//$user = new User();
//
//
//$user->setUsername('nayasa');
//$encoder = $container->get('security.encoder_factory')->getEncoder($user);
//$pwd = $encoder->encodePassword('Passwo0rd', $user->getSalt());
//$user->setPassword($pwd);
//$user->setRoles(array('ROLE_USER'));
//$user->setEmail('nayasa@gmail.com');
//$user->setIsActive(true);
//$em->persist($user);
//$em->flush();
//die();
//
//
//
//
//// set an event
//$user = $em->getRepository('UserBundle:User')->find(1);
//
//$event->setName('new test event');
//$event->setDetails('blah blah .....');
//$event->setLocation('a dummy location');
//$event->setTime(new \DateTime());
//$event->setOwner($user);
//$em->persist($event);
//$em->flush(); die();




/*$user = new User();

$event = new Event();

$user = $em->getRepository('UserBundle:User')->find(1);


foreach ($user->getEvents() as $event) {
    echo $event->getName();
}

die();
*/
//$em->persist($user);
/*


$user = $em->getRepository('UserBundle:User')->find(2);

$user->setEmail('admin@mailinator.com');
$encoder = $container->get('security.encoder_factory')->getEncoder($user);
$pwd = $encoder->encodePassword('AdminPassword', $user->getSalt());
$user->setPassword($pwd);


$user->setUsername('administrator');

$encoder = $container->get('security.encoder_factory')->getEncoder($user);
$pwd = $encoder->encodePassword('AdminPassword', $user->getSalt());
$user->setPassword($pwd);
$user->setRoles(array('ROLE_ADMIN'));
$user->setEmail('sudhaker.ssr@gmail.com');
$user->setIsActive(false);
*/


/* //set user

$user->setUsername('admin');
$encoder = $container->get('security.encoder_factory')->getEncoder($user);
$pwd = $encoder->encodePassword('AdminPassword', $user->getSalt());
$user->setPassword($pwd);
$user->setRoles(array('ROLE_ADMIN'));
$user->setEmail('sudhaker.ssr@gmail.com');
$user->setIsActive(true);
$em->persist($user);
    */