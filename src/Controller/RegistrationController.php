<?php

declare(strict_types=1);

namespace Skeletton\Controller;

use Skeletton\Entity\User;
use Skeletton\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController
 * @package Skeletton\Controller
 */
class RegistrationController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return mixed
     * @Route("/{_locale}/register", name="skeletton_user_registration")
     * @Route("/register", name="skeletton_user_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $form->getData();
            $exist = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            if ($exist === null) {

                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);

                // 4)set the User's roles
                $user->setRoles(['ROLE_USER']);

                // 5) save the User!
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash(
                    'notice',
                    'Thanks you for registering, you can now login'
                );

                return $this->redirectToRoute('skeletton_security_login');
            }

            $form->get('email')->addError(new FormError('Email already used'));
        }

        $errors = $form->getErrors(true);

        return $this->render(
            'registration/register.html.twig', [
                'form' => $form->createView(),
                'errors' => $errors
            ]
        );
    }
}