<?php

namespace App\Http\Controller;

use App\Domain\Auth\Event\TestEvent;
use App\Domain\Billing\Entity\Plan;
use App\Domain\Billing\Event\BillingSubscriptionEvent;
use App\Domain\Billing\Repository\PlanRepository;
use App\Http\Controller\AbstractController;
use App\Http\Form\AccountEditType;
use App\Http\Form\RegistrationFormType;
use App\Infrastructure\Payment\Event\PaymentEvent;
use App\Infrastructure\Payment\Stripe\StripeApi;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SubscriptionController extends AbstractController
{
    public function __construct(private readonly StripeApi $api)
    {
    }
    
    #[Route('/account/billing', name: 'account_billing_index')]
    public function register(PlanRepository $pr): Response
    {
        $redirectUrl = $this->generateUrl('account_billing_index', [], UrlGeneratorInterface::ABSOLUTE_URL);
        return $this->render('account/billing.html.twig', [
            'plans' => $pr->findAll(),
            'billingUrl' => $this->api->getBillingUrl($this->getUser(), $redirectUrl)
        ]);
    }
    
    #[Route('/account/billing/manage', name: 'account_billing_manager')]
    public function manager(): Response
    {
        $user = $this->getUser();
        $redirectUrl = $this->generateUrl('account_billing_index', [], UrlGeneratorInterface::ABSOLUTE_URL);
        if (null === $user->getStripeId()) {
            $this->addFlash('error', "Vous n'avez pas d'abonnement actif");
            
            return $this->redirect($redirectUrl);
        }
        
        return $this->redirect($this->api->getBillingUrl($user, $redirectUrl));
    }
    
    #[Route('/subscription', name: 'subscription_index')]
    public function index(PlanRepository $pr, EventDispatcherInterface $dispatcher): Response
    {
        $dispatcher->dispatch(new TestEvent('test event'));
    
        return $this->render('subscription/index.html.twig', [
            'plans' => $pr->findAll()
        ]);
    }
    
    /**
     * @Route("/subscription/stripe/checkout", name="subscription_stripe", methods={"POST"})
     */
    public function subscriptionStripe(
        EntityManagerInterface $em,
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        PlanRepository $pr
    ): JsonResponse
    {
        // Récupérer le plan_id depuis la requête POST
        $data = json_decode($request->getContent(), true);
        $planId = $data['plan_id'];
        $plan = $pr->findOneBy(['id' => $planId]);
        
        $isSubscription = '1' === $request->get('subscription');
        $url = $urlGenerator->generate('account_index', [], UrlGeneratorInterface::ABSOLUTE_URL);
        try {
            $this->api->createCustomer($this->getUser());
            $em->flush();
            
            return $this->json([
                'id' => $isSubscription ? $this->api->createSubscriptionSession($this->getUser(), $plan, $url) : $this->api->createPaymentSession($this->getUser(), $plan, $url),
            ]);
        } catch (\Exception) {
            return $this->json(['title' => "Impossible de contacter l'API Stripe"], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
