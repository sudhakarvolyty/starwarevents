<?php

namespace Yoda\EventBundle\Controller;

use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Yoda\EventBundle\Entity\Event;
use Yoda\EventBundle\Event\AttendeesEvent;
use Yoda\EventBundle\Event\AttendeesSubscriber;
use Yoda\EventBundle\Form\EventType;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Yoda\FormatObject;
use Yoda\traits\FormatObjectTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Yoda\UserBundle\Entity\User;
use Yoda\UserBundle\Services\UserManager;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;

/**
 * Event controller.
 *
 */
class EventController extends Controller
{
    use FormatObjectTrait;

    /**
     * Lists all Event entities.
     * @Template()
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('EventBundle:Event')->getUpcomingEvents();

//        return $this->render('EventBundle:Event:index.html.twig', array(
//            'entities' => $entities,
//        ));
        return array('entities' => $entities);
    }

    /**
     * Creates a new Event entity
     */
    public function createAction(Request $request)
    {
        $this->ensureUserSecurity('ROLE_USER');
        $entity = new Event();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setOwner($this->getUser());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show', array('slug' => $entity->getSlug())));
        }

        return $this->render('EventBundle:Event:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Event entity.
     *
     * @param Event $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('event_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Event entity.
     *
     */
    public function newAction()
    {
        $this->ensureUserSecurity('ROLE_USER');
        $entity = new Event();
        $form = $this->createCreateForm($entity);

        return $this->render('EventBundle:Event:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Event entity.
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:Event')->findOneBy(array('slug' => $slug));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $deleteForm = $this->createDeleteForm($entity->getId());

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
//        return $this->render('EventBundle:Event:show.html.twig', array(
//            'entity'      => $entity,
//            'delete_form' => $deleteForm->createView(),
//        ));
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     */
    public function editAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:Event')->findOneBy(array('slug' => $slug));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $this->enforceOwnerSecurity($entity);

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($entity->getId());

        return $this->render('EventBundle:Event:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Event entity.
     *
     * @param Event $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('event_update', array('slug' => $entity->getSlug())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Event entity.
     *
     */
    public function updateAction(Request $request, $slug)
    {
        $this->ensureUserSecurity();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:Event')->findOneBy(array('slug' => $slug));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $this->enforceOwnerSecurity($entity);

        $deleteForm = $this->createDeleteForm($entity->getSlug());
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('event_edit', array('slug' => $slug)));
        }

        return $this->render('EventBundle:Event:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Event entity.
     *
     */

    public function deleteAction(Request $request, $slug)
    {
        $this->ensureUserSecurity();
        $form = $this->createDeleteForm($slug);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EventBundle:Event')->findOneBy(array('slug' => $slug));

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Event entity.');
            }

            $this->enforceOwnerSecurity($entity);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('event'));
    }


    /**
     * Creates a form to delete a Event entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($slug)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('slug' => $slug)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function ensureUserSecurity($role = 'ROLE_USER')
    {
        if (!$this->getSecurityContext()->isGranted($role)) {
            throw new AccessDeniedException('Need ROLE_USER');
        }
    }

    public function attendAction($slug, $format)
    {
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository('EventBundle:Event')->findOneBy(array('slug' => $slug));

        if (!$event) {
            throw new ResourceNotFoundException('Sorry the event you are looking for is not availbale');
        }

        if (!$event->hasAttendee($this->getUser())) {
            $event->getAttendees()->add($this->getUser());

            $attendeeObj = $this->get('security.token_storage')->getToken()->getUser();

            /** @var User $attendee */
            $attendee = $em->getRepository('UserBundle:User')->find($attendeeObj->getId());

            $dispatcher = $this->get('event_dispatcher');

            $listener = $this->get('event_attendees_subscriber');
            //$dispatcher->addListener(AttendeesEvent::NAME, array($listener, 'onEventAttendence'));

            $eventAttendee = new AttendeesEvent($attendee, $event);
            $retObj = $dispatcher->dispatch(AttendeesEvent::NAME, $eventAttendee);

            $em->persist($event);
            $em->flush();

        }
        $format  = 'json';

        return $this->createAttendeeResponse($event, $format);
    }

    public function unattendAction($slug, $format)
    {
        $format  = 'json';
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository('EventBundle:Event')->findOneBy(array('slug' => $slug));

        if (!$event) {
            throw new ResourceNotFoundException('Sorry the event you are looking for is not availbale');
        }

        if ($event->hasAttendee($this->getUser())) {
            $event->getAttendees()->removeElement($this->getUser());
            $em->persist($event);
            $em->flush();
        }

        return $this->createAttendeeResponse($event, $format);


    }


    private function createAttendeeResponse(Event $event, $format)
    {
        if ($format == 'json') {
            return new JsonResponse(
                array(
                    'attending' => $event->hasAttendee($this->getUser())
                )
            );
        }
    }


}
