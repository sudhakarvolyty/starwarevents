<?PHP
namespace Yoda\EventBundle\Event;

use Symfony\Component\EventDispatcher\Event as EventDispatcher;
use Yoda\UserBundle\Entity\User;
use Yoda\EventBundle\Entity\Event;

class AttendeesEvent extends EventDispatcher
{
    /** @var User $_attendee */
    private $_attendee;

    /** @var Event $_toAnEvent */
    private $_toAnEvent;

    const NAME = "user.attending";

    public function __construct(User $attendee, Event $event)
    {
        $this->_attendee = $attendee;
        $this->_toAnEvent = $event;
    }

    public function getAttendee()
    {
        return $this->_attendee;
    }

    public function getEventOwner()
    {
        return $this->_toAnEvent->getOwner();
    }

    public function getEventOwnerEmail()
    {
        return $this->_toAnEvent->getOwner();
    }
}