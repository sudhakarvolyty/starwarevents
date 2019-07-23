<?php


namespace Yoda\UserBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Yoda\EventBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Yoda\UserBundle\Entity\User;
use Yoda\UserBundle\Form\RegisterFormType;
use Yoda\UserBundle\Services\UserManager;


class RegisterController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @Template()
     * @param Request $request
     */
    public function registerAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(new RegisterFormType(), $user);

        $form->handleRequest($request);
        if ($form->isValid()) {

            $user = $form->getData();

            /** @var UserManager $userRegisterManager */
            $userRegisterManager = $this->get('user_register_manager');

            $userRegisterManager->registerUser($user);
            $this->authenticateUser($user);

            $request->getSession()
                ->getFlashbag()
                ->add('success', 'Welcome to the starwars world');

            $url = $this->generateUrl('event');

            return $this->redirect($url);
        }

        return array('form' => $form->createView());
    }


    private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area'; //firewall name
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->getSecurityContext()->setToken($token);
    }

    /**
     * @Route("/reset_password", name="reset_password")
     * @param Request $request
     */
    public function resetPasswordAction(Request $request)
    {
        try {
            $userEmail = $request->get('email');
            $password = $request->get('password');

            /** @var UserManager $userManager */
            $userManager = $this->get('user_register_manager');

            $userManager->resetPassword($userEmail, $password);
            $msg = array('success' => "yes", "message" => "Password reset successfully");
        } catch ( UsernameNotFoundException $e) {
            $msg = array('success' => "no", "message" => $e->getMessage());
        }

        return new JsonResponse($msg);
    }
}