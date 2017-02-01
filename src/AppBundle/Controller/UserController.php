<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Form\LoginForm;
use AppBundle\Form\Search\SearchUserType;
use AppBundle\Form\User\Model\ChangePassword;
use AppBundle\Form\User\UserEditType;
use AppBundle\Form\UserType;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

class UserController extends Controller
{

    /**
     * Show user list
     *
     * @Route("/user", name="user_index")
     * @Template()
     * @Method("GET")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('AppBundle:User:index.html.twig', [
            'user' => $user,
            'categories' => null,
        ]);
    }

    /**
     * Show user by id
     *
     * @Route("/user/{id}", name="user_show", requirements={"id": "\d+"} )
     * @Template()
     * @Method("GET")
     */
    public function showAction(Request $request, User $user)
    {
        $criteria = Criteria::create()
            ->orderBy(array("id" => Criteria::DESC))
            ->setMaxResults(5);
        $em = $this->getDoctrine()->getManager();
        $postRepository = $em->getRepository('AppBundle:Post');
        return $this->render('AppBundle:User:show.html.twig', [
            'user' => $user,
            'posts' => $postRepository->findApprovedUserPosts($user),
            'lastCommentedPosts' => $user->getComments()->matching($criteria),
        ]);
    }

    /**
     * @param Request $request
     *
     * @Route("/{_locale}/user/login", name="user_login")
     * @Route("/user/login")
     */
    public function loginAction(Request $request)
    {

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginForm::class, [
            '_username' => $lastUsername,
        ]);
        return $this->render('AppBundle:user:login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'form' => $form->createView(),
            'categories' => null,
        ));
    }

    /**
     * @Route("/user/logout", name="user_logout")
     */
    public function logoutAction()
    {
    }

    /**
     * Registering a new user
     *
     * @Route("/{_locale}/user/registration", name="user_registration")
     * @Route("/user/registration")
     * @Method({"GET", "POST"})
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->get('app.form.manager')->createRegisterUserForm($request, $user);

        if ($form instanceof Form) {
            return $this->render('AppBundle:user:new.html.twig', array(
                    'user' => $user,
                    'form' => $form->createView(),
                )
            );
        }
        else {
            $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main');
            $request->getSession()->set('_locale', $user->getLanguage());
            return $this->redirectToRoute('user_index');
        }

    }


    /**
     * Editing user
     *
     * @Route("/user/edit", name="user_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $editForm = $this->get('app.form_manager')->createEditUserForm($request, $user);

        if ($editForm instanceof Form) {
            return $this->render('AppBundle:User:edit.html.twig', array(
                'user' => $user,
                'form' => $editForm->createView(),
                'categories' => null
            ));
        }
        else {
            return $this->redirect($editForm);
        }
    }

    /**
     *
     * @Route("/search/user", name="user_search")
     * @Method({"GET", "POST"})
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryRepository = $em->getRepository('AppBundle:Category');

        $result = $this->get('app.form_manager')
            ->createSearchForm($request, 'AppBundle:User');

        if ($result['valid'] == true) {
            return $this->render('AppBundle:User:search.html.twig', array(
                'users' => $result['data'],
                'categories' => $categoryRepository->findAll(),
                'form' => $result['form']->createView(),
            ));
        }

        return $this->render('AppBundle:User:search.html.twig', array(
                'categories' => $categoryRepository->findAll(),
                'form' => $result['form']->createView(),
                'users' => null,
            )
        );
    }
    /**
     *
     * @Route("/user/{id}/posts/{page}", name="user_posts")
     * @Method({"GET"})
     */
    public function postsAction(Request $request, User $user, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $postRepository = $em->getRepository('AppBundle:Post');

        if ($this->getUser() === $user) {
            $pagination = $this->get('knp_paginator')->paginate($user->getPosts(),
                $request->query->getInt('page', $page), 4
            );
        }
        else {
            $pagination = $this->get('knp_paginator')->paginate($postRepository->findApprovedUserPosts($user),
                $request->query->getInt('page', $page), 4
            );
        }

        return $this->render('AppBundle:User:posts.html.twig', [
            'user' => $user,
            'posts' => $pagination,
            'categories' => null,
        ]);
    }
}
