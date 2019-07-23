<?PHP

namespace Yoda\EventBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Yoda\UserBundle\Services\UserManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

class AttendeesSubscriber implements EventSubscriberInterface
{

    private $user_manager;
    public $swift_mailer;

    public function __construct(UserManager $user_manager, \Swift_Mailer $swift_mailer)
    {
        $this->user_manager = $user_manager;
        $this->swift_mailer = $swift_mailer;
    }

    public static function getSubscribedEvents()
    {
        return array(
            AttendeesEvent::NAME => 'onEventAttendence'
        );
    }

    public function onEventAttendence(AttendeesEvent $attendeesEventObj)
    {

        $eventOwnerEmail = $this->user_manager->getUserEmailById($attendeesEventObj->getEventOwnerEmail()->getId());

        $msg = \Swift_Message::newInstance();

        $msg->setSubject('Test Subject');
        $msg->setTo('hassan@mailinator.com');
        $msg->setBody('<h1>Hey man</h1>');
        $msg->setContentType('text/html');
        $msg->setCharset('utf-8');
        $msg->setFrom('test@gmail.com');

        $this->swift_mailer->send($msg);
        echo 'sd';
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        dump($event); die;
    }
}