<?php

namespace App\Http\Controller;

use App\Domain\Auth\Authenticator;
use App\Domain\Auth\Event\UserCreatedEvent;
use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use App\Http\Form\RegistrationFormType;
use App\Infrastructure\Security\TokenGeneratorService;
use App\Infrastructure\Social\SocialLoginService;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private readonly EmailVerifier $emailVerifier)
    {
    }
    
    #[Route('/register', name: 'auth_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $rootErrors = [];
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        // do anything else you need here, like send an email
    
    
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setCreatedAt(new \DateTime());
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('auth_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('noreply@stayfocusedandco.fr', 'StayFocused&Co'))
                    ->to($user->getEmail())
                    ->subject('Merci de vérifier votre compte')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            
            $this->addFlash('success', 'Un message avec un lien de confirmation vous a été envoyé par mail. Veuillez suivre ce lien pour activer votre compte.');
            
            return $this->redirectToRoute('auth_login');
        } elseif ($form->isSubmitted()) {
            /** @var FormError $error */
            foreach ($form->getErrors() as $error) {
                if (null === $error->getCause()) {
                    $rootErrors[] = $error;
                }
            }
        }
        
        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'errors' => $rootErrors
        ]);
    }
    
    #[Route('/register/verify/email', name: 'auth_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');
        
        if (null === $id) {
            return $this->redirectToRoute('auth_register');
        }
        
        $user = $userRepository->find($id);
        
        if (null === $user) {
            return $this->redirectToRoute('auth_register');
        }
        
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            
            return $this->redirectToRoute('auth_register');
        }
        
        $this->addFlash('success', 'Your email address has been verified.');
        
        return $this->redirectToRoute('home');
    }
}
