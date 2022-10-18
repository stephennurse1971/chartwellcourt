<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\DefaultTennisPlayerAvailabilityHoursRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'role' => 'All',
            'role_title' => 'All'
        ]);
    }

    /**
     * @Route("/role/{role}", name="user_role_index", methods={"GET"})
     */
    public function indexRole(string $role, UserRepository $userRepository): Response
    {

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findByRole($role),
            'role' => $role,
            'role_title' => $role
        ]);
    }

    /**
     * @Route("/group/AX", name="user_ax", methods={"GET"})
     */
    public function indexAX(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findByCompany('AX'),
            'role'=>"AX"
        ]);
    }

    /**
     * @Route("/group/Personal", name="user_personal", methods={"GET"})
     */
    public function indexPersonal(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findByCompany('Personal'),
            'role'=>"Personal"
        ]);
    }


    /**
     * @Route("/delete_all_AX", name="/user/delete_all_AX")
     */
    public function deleteAllAXUsers(UserRepository $userRepository)
    {
        $allAXUser=$userRepository->findByCompany('AX');
        foreach ($allAXUser as $AXUser)
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($AXUser);
            $entityManager->flush();
        }
        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/delete_all_Personal", name="/user/delete_all_Personal")
     */
    public function deleteAllPersonalUsers(UserRepository $userRepository)
    {
        $allAXUser=$userRepository->findByCompany('Personal');
        foreach ($allAXUser as $AXUser)
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($AXUser);
            $entityManager->flush();
        }
        return $this->redirectToRoute('user_index');
    }


    /**
     * @Route("/admin/new", name="user_new", methods={"GET","POST"})
     */

    public function new(MailerInterface $mailer, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['email1' => $user->getEmail(), 'email2' => $user->getEmail2()]);
        $roles = $this->getUser()->getRoles();

        if (!in_array('ROLE_SUPER_ADMIN', $roles)) {
            $form->remove('role');
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $get_roles = $form->get('role')->getData();
            $roles = $get_roles;
            $password = $form->get('password')->getData();
            if ($password != '') {
                $user->setPlainPassword($password);
                $user->setPassword($passwordEncoder->encodePassword($user, $password));
            }
            $user->setRoles($roles);

            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();
            $user->setFullName($firstName . ' ' . $lastName);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            if ($form['sendEmail']->getData() == 1) {
                $html = $this->renderView('emails/welcome_email.html.twig');
                $email = (new Email())
                    ->from('nurse_stephen@hotmail.com')
                    ->to($user->getEmail())
                    ->cc('nurse_stephen@hotmail.com')
                    ->subject("Welcome to SN's personal website")
                    ->html($html);
                $mailer->send($email);
            }


            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(int $id, MailerInterface $mailer, Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $hasAccess = in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles());
        if ($this->getUser()->getId() == $id || $hasAccess) {
            $logged_user_id = $this->getUser()->getId();
            $plainPassword = $user->getPlainPassword();
            $roles = $user->getRoles();
            $form = $this->createForm(UserType::class, $user, ['email1' => $user->getEmail(), 'email2' => $user->getEmail2()]);
            $logged_user_roles = $this->getUser()->getRoles();
            if (!in_array('ROLE_SUPER_ADMIN', $logged_user_roles)) {
                $form->remove('role');
            }
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->has('role')) {
                    $get_roles = $form->get('role')->getData();

                    $roles = $get_roles;
                    $user->setRoles($roles);
                }
                $password = $form->get('password')->getData();
                if ($password != '') {
                    $user->setPassword($passwordEncoder->encodePassword($user, $password));
                    $user->setPlainPassword($password);
                }

                $firstName = $user->getFirstName();
                $lastName = $user->getLastName();
                $user->setFullName($firstName . ' ' . $lastName);

                $this->getDoctrine()->getManager()->flush();

                if ($form['sendEmail']->getData() == 1) {
                    $html = $this->renderView('emails/welcome_email.html.twig');
                    $email = (new Email())
                        ->from('nurse_stephen@hotmail.com')
                        ->to($user->getEmail())
                        ->cc('nurse_stephen@hotmail.com')
                        ->subject("Welcome to SN's personal website")
                        ->html($html);
                    $mailer->send($email);
                }
                if ($logged_user_id != $id) {
                    return $this->redirectToRoute('user_index');
                } else {
                    $this->redirectToRoute('app_login');
                }
            }

            return $this->render('user/edit.html.twig', [

                'user' => $user,
                'form' => $form->createView(),
                'password' => $plainPassword,
                'roles' => $roles
            ]);
        }
        $referer = $request->server->get('HTTP_REFERER');
        if ($referer) {
            return $this->redirect($referer);

        } else {
            return $this->redirectToRoute('user_index');
        }
    }

    /**
     * @Route("/{id}/{role}/{active}/edit", name="user_edit_button", methods={"GET","POST"})
     */
    public function editAuto(string $role, Request $request, User $user, EntityManagerInterface $entityManager): Response
    {

        $get_roles = $user->getRoles();
        if (!in_array($role, $get_roles)) {
            $get_roles[] = $role;
        } else {
            $get_roles = array_merge(array_diff($get_roles, [$role]));
        }
        $user->setRoles($get_roles);
        $entityManager->flush();
        $referer = $request->server->get('HTTP_REFERER');
        return $this->redirect($referer);
    }

    /**
     * @Route("/admin/{id}", name="user_delete", methods={"POST"})
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('user_index');
    }


    /**
     * @Route("/{userid}/invite-email", name="user_invite", methods={"GET"})
     */
    public function inviteEmail(int $userid, MailerInterface $mailer, Request $request, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($userid);
        $html = $this->renderView('emails/welcome_email.html.twig', [
            'user'=> $user
        ]);
        $email = (new Email())
            ->from('nurse_stephen@hotmail.com')
            ->to($user->getEmail())
            ->cc('nurse_stephen@hotmail.com')
            ->subject("Welcome to SN's personal website")
            ->html($html);
        $mailer->send($email);
        $referer = $request->server->get('HTTP_REFERER');
        return $this->redirect($referer);
    }
}
