<?php


namespace Yoda\UserBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Yoda\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Yoda\UserBundle\Form\RegisterFormType;
use Yoda\UserBundle\Form\ProfileFormType;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Yoda\UserBundle\Services\UserManager;
use Yoda\EventBundle\Entity\Event;
use Yoda\UserBundle\Entity\UserProfile;

use Symfony\Component\HttpFoundation\JsonResponse;


class UserController extends BaseController
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function profileAction(Request $request)
    {
        $userManager = $this->get('user_register_manager');

        /** @var User $user */
        $user = $this->getUser();

        /** @var UserProfile $userProfile */
        $userProfile = $userManager->getUserProfile($user);

        if ($request->isMethod('POST')) {
            $userProfile = $userManager->updateUserProfile($userProfile, $request->request);

            $request->getSession()
                ->getFlashbag()
                ->add('profileSuccess', 'Profile updated successfully');
        }

        $form = $this->createForm(new ProfileFormType(), $userProfile);
        $form->handleRequest($request);


        /** @var Event[] $eventCollection */
        $eventCollection = $userManager->getEvents();

        $events = array();

        if ($eventCollection != null) {
            foreach ($eventCollection as $event) {
                $events[] = array(
                    'name' => $event->getName(),
                    'slug' => $event->getSlug(),
                    'id' => $event->getId()
                );
            }
        }

        return $this->render('UserBundle:User:profile.html.twig', array(
            'user' => $user,
            'userProfile' => $userProfile,
            'events' => $events,
            'profile_form' => $form->createView(),
        ));
    }

    public function updateProfilePicAction(Request $request)
    {

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('Profile_Pic')->getClientOriginalName();

        $filename = md5(uniqid()) . '.jpg';
        $uploadPath = $this->container->getParameter('kernel.root_dir') . '/../web/uploads/profiles/';

        $userManager = $this->get('user_register_manager');

        $uploaded = $userManager->updateProfilePic($request, $uploadPath, $filename);
        return new JsonResponse(true);


    }

}