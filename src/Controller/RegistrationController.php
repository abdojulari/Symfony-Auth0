<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createFormBuilder()
             ->add('email', EmailType::class, [
                 'attr' => ['class' => 'bg-transparent text-white']
             ])
             ->add('password', RepeatedType::class, [
                 'type' => PasswordType::class,
                 'invalid_message' => 'The password fields must match',
                 'options' => ['attr' => ['class' => 'bg-transparent text-white password-field']],
                 'required' => true,
                 'first_options' => ['label' => 'Password'],
                 'second_options' => ['label' => 'Repeat Password'],
                 
                ])
                ->add('Register', SubmitType::class, [
                     'attr' => ['class' => 'btn btn-outline-light float-end']
                ])
                ->getForm();
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

            $en = $this->getDoctrine()->getManager();
            $en->persist($user);
            $en->flush();
            return $this->redirect($this->generateUrl('app_login'));
        }
        
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
