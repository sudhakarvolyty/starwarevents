<?php

namespace Yoda\EventBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Yoda\EventBundle\Entity\Event;


class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $em = $this->getDoctrine()->getManager();

        $eventRep = $em->getRepository('EventBundle:Event');
        /** @var Event $event */
        $event = $eventRep->findOneBy(array('id'=>1));
        //var_dump($event->getName()); exit;
        return $this->render('EventBundle:Default:index.html.twig', array('event' => $event));

//        $data = array( "id" => '1', "firstName" => $name);
//        $json = json_encode($data);
//
//        $response = new Response($json);
//        $response->headers->set('Content-Type', 'application/json');
//        return $response;
    }
}
