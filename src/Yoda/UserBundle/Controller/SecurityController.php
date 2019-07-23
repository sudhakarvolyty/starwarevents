<?PHP

namespace Yoda\UserBundle\Controller;

use Yoda\EventBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends Controller
{

    /**
     * @Route("/login_form", name="login_form")
     * @param Request $request
     * @Template
     */

    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);

        $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return array(
            'last_username' => $lastUsername,
            'error' => $error
        );
    }

    /**
     * @Route("login_check", name="login_check")
     */
    public function loginCheckAction()
    {

    }

    /**
     * @Route("logout", name="logout")
     */
    public function logoutAction()
    {

    }
}