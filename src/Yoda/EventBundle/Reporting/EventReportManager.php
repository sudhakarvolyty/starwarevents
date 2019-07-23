<?php


namespace Yoda\EventBundle\Reporting;

use Yoda\EventBundle\Entity\Event;
use Yoda\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;

class EventReportManager
{

    /** @var EntityManager $em  */
    private $em;

    private $router;

    public function __construct(EntityManager $em, Router $router )
    {
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * @return string
     */
    public function updatedEventsAction()
    {
        /** @var Event $updatedEvents */
        $updatedEvents = $this->em->getRepository('EventBundle:Event')->getRecentlyUpdatedEvents();

        $rows = array();

        foreach ($updatedEvents as $event) {
            $data = array(
                $event->getId(),
                $event->getName(),
                $event->getTime()->format('Y-m-d H:i:s'),
                $this->router->generate('event_show', array('slug' => $event->getSlug()))
            );
            $rows[] = implode(',', $data);
        }
        return implode("\n", $rows);

    }

}