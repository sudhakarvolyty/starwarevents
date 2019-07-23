<?php


namespace Yoda\UserBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Yoda\UserBundle\Entity\User;
use Yoda\UserBundle\Entity\UserProfile;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Yoda\UserBundle\Entity\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class UserManager
{
    /** @var EntityManager $em */
    private $em;

    private $router;

    /** @var User $user */
    private $user;

    /** @var EncoderFactory $encodeFactoryManager */
    private $encodeFactoryManager;

    /** @var TokenStorageInterface $tokenStorageInterface */
    private $tokenStorageInterface;

    public function __construct(EntityManager $em, Router $router, EncoderFactory $encodeFactoryManager, TokenStorageInterface $tokenStorageInterface)
    {
        $this->em = $em;
        $this->router = $router;
        $this->encodeFactoryManager = $encodeFactoryManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
        /** @var User $user */
        $this->user = $this->tokenStorageInterface->getToken()->getUser();
    }

    public function registerUser(User $user)
    {
        $pwd = $this->encodePassword($user, $user->getPlainPassword());

        $user->setPassword($pwd);
        $user->setRoles(array('ROLE_USER'));

        $this->em->persist($user);
        $this->em->flush();
    }

    private function encodePassword(User $user, $plainPassword)
    {
        $encoder = $this->encodeFactoryManager->getEncoder($user);
        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }

    public function resetPassword($email = null, $password)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->em->getRepository("UserBundle:User");
        $user = $this->user;

        if ($this->tokenStorageInterface->getToken()->getUser() === "anon.") {
            $user = $userRepository->findOneBy(array('email' => $email));
        }

        if ($user instanceof User) {

            $pwd = $this->encodePassword($user, $password);
            $user->setPassword($pwd);
            $this->em->persist($user);
            $this->em->flush();

        } else {
            throw new UsernameNotFoundException('User not found with email: ' . $email);
        }
    }


    public function getEvents()
    {
        /** @var User $user */
        $user = $this->user;
        $events = $user->getEvents();

        if (count($events) > 0) {
            return $events;
        } else {
            return null;
        }
    }

    /**
     * @param User $user
     * @return object|\Yoda\UserBundle\Entity\UserProfile|null
     */
    public function getUserProfile($user)
    {
        $userProfileRepository = $this->em->getRepository('UserBundle:UserProfile');
        $userProfile = $userProfileRepository->findOneBy(array('user' => $user->getId()));
        return $userProfile;

    }

    /**
     * @param UserProfile $userProfile
     * @param $userProfileData
     * @return UserProfile
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateUserProfile($userProfile, $userProfileData)
    {
        $user = $this->user;

        $userProfile = $this->checkProfileExists($this->getUserProfile($user));

        foreach ($userProfileData as $postData) {

            $userProfile->setFirstName($postData['firstName']);
            $userProfile->setLastName($postData['lastName']);
            $userProfile->setStreet1($postData['street1']);
            $userProfile->setStreet2($postData['street2']);
            $userProfile->setPincode($postData['pincode']);
            $userProfile->setCreatedAt(new \DateTime());
            $userProfile->setUpdatedAt(new \DateTime());
        }

        $this->em->persist($userProfile);
        $this->em->flush();

        return $userProfile;
    }

    public function updateProfilePic($request, $uploadPath, $filename)
    {
        $user = $this->user;
        $userProfile = $this->checkProfileExists($this->getUserProfile($user));

        try {
            $profilePic = $userProfile->getProfilePic();
            if ($profilePic != '') {
                @unlink(__DIR__.'/../../../../web/uploads/profiles/'.$profilePic);
            }
            $request->files->get('Profile_Pic')->move(
                $uploadPath,
                $filename
            );
            $userProfile->setProfilePic($filename);
            $userProfile->setUpdatedAt(new \DateTime());

            $this->em->persist($userProfile);
            $this->em->flush();

        } catch (FileException $e) {
            dump($e->getMessage());
        }
    }

    private function checkProfileExists($userProfile)
    {
        $user = $this->user;

        if (!$userProfile instanceof UserProfile) {
            $userProfile = new UserProfile();
            $userProfile->setProfilePic('');
            $userProfile->setUser($user->getId());
        }

        return $userProfile;
    }

    /**
     * @param $userId
     * @return object|User|null
     */
    public function getUserEmailById($userId)
    {

        $queryBuilder = $this->em->createQueryBuilder();
        $user = $queryBuilder->select('u.email')
                ->from('UserBundle:User', 'u')
                ->where('u.id = '.$userId)
                ->getQuery()
                ->execute();

        return $user[0]['email'];
    }
}