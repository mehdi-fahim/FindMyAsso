<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Entity\InKindDonation;
use App\Form\DonationFormType;
use App\Form\InKindDonationFormType;
use App\Repository\AssociationRepository;
use App\Repository\WishlistItemRepository;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DonationController extends AbstractController
{
    #[Route('/donation/monetary', name: 'app_donation_monetary')]
    public function monetaryDonation(
        Request $request,
        EntityManagerInterface $entityManager,
        StripeService $stripeService
    ): Response {
        $donation = new Donation();
        $form = $this->createForm(DonationFormType::class, $donation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donation->setUser($this->getUser());
            $donation->setStatus('pending');
            $donation->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($donation);
            $entityManager->flush();

            // Créer la session Stripe Checkout
            $checkoutSession = $stripeService->createCheckoutSession($donation);

            return $this->redirect($checkoutSession->url);
        }

        return $this->render('donation/monetary.html.twig', [
            'donationForm' => $form->createView(),
        ]);
    }

    #[Route('/donation/in-kind', name: 'app_donation_in_kind')]
    public function inKindDonation(
        Request $request,
        EntityManagerInterface $entityManager,
        WishlistItemRepository $wishlistRepository
    ): Response {
        $donation = new InKindDonation();
        $form = $this->createForm(InKindDonationFormType::class, $donation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donation->setUser($this->getUser());
            $donation->setStatus('pending');
            $donation->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($donation);
            $entityManager->flush();

            // Essayer de faire correspondre avec des besoins d'associations
            $this->matchWithWishlistItems($donation, $wishlistRepository);

            $this->addFlash('success', 'Votre don en nature a été enregistré avec succès !');

            return $this->redirectToRoute('app_donations');
        }

        // Récupérer les éléments de wishlist pour afficher les besoins des associations
        $wishlistItems = $wishlistRepository->findActive();

        return $this->render('donation/in_kind.html.twig', [
            'donationForm' => $form->createView(),
            'wishlistItems' => $wishlistItems,
        ]);
    }

    #[Route('/donation/success', name: 'app_donation_success')]
    public function donationSuccess(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sessionId = $request->query->get('session_id');
        
        if (!$sessionId) {
            throw $this->createNotFoundException('Session ID manquant');
        }

        // TODO: Vérifier la session Stripe et mettre à jour le statut du don
        
        $this->addFlash('success', 'Merci pour votre don ! Votre contribution aide les animaux en détresse.');

        return $this->render('donation/success.html.twig');
    }

    #[Route('/donation/cancel', name: 'app_donation_cancel')]
    public function donationCancel(): Response
    {
        $this->addFlash('info', 'Votre don a été annulé. Vous pouvez réessayer à tout moment.');

        return $this->redirectToRoute('app_donations');
    }

    #[Route('/donation/webhook', name: 'app_donation_webhook')]
    public function webhook(Request $request, EntityManagerInterface $entityManager, StripeService $stripeService): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $endpointSecret = $this->getParameter('stripe.webhook_secret');

        try {
            $event = $stripeService->constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            return new Response('Webhook Error: ' . $e->getMessage(), 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutSessionCompleted($session, $entityManager);
                break;
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent, $entityManager);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent, $entityManager);
                break;
        }

        return new Response('Webhook received', 200);
    }

    #[Route('/donation/history', name: 'app_donation_history')]
    #[IsGranted('ROLE_USER')]
    public function donationHistory(): Response
    {
        $user = $this->getUser();
        
        return $this->render('donation/history.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/donation/matching', name: 'app_donation_matching')]
    public function donationMatching(
        WishlistItemRepository $wishlistRepository,
        AssociationRepository $associationRepository
    ): Response {
        $wishlistItems = $wishlistRepository->findAllActive();
        $associations = $associationRepository->findAllApproved();

        return $this->render('donation/matching.html.twig', [
            'wishlistItems' => $wishlistItems,
            'associations' => $associations,
        ]);
    }

    private function matchWithWishlistItems(InKindDonation $donation, WishlistItemRepository $wishlistRepository): void
    {
        $matchingItems = $wishlistRepository->findMatchingDonations(
            $donation->getType(),
            $donation->getRegion(),
            $donation->getCity()
        );

        if (!empty($matchingItems)) {
            // TODO: Envoyer des notifications aux associations concernées
            // TODO: Créer un système de matching automatique
        }
    }

    private function handleCheckoutSessionCompleted($session, EntityManagerInterface $entityManager): void
    {
        $donation = $entityManager->getRepository(Donation::class)->findOneBy([
            'stripeCheckoutId' => $session->id
        ]);

        if ($donation) {
            $donation->setStatus('paid');
            $donation->setStripePaymentIntentId($session->payment_intent);
            $entityManager->flush();
        }
    }

    private function handlePaymentIntentSucceeded($paymentIntent, EntityManagerInterface $entityManager): void
    {
        $donation = $entityManager->getRepository(Donation::class)->findOneBy([
            'stripePaymentIntentId' => $paymentIntent->id
        ]);

        if ($donation) {
            $donation->setStatus('paid');
            $entityManager->flush();
        }
    }

    private function handlePaymentIntentFailed($paymentIntent, EntityManagerInterface $entityManager): void
    {
        $donation = $entityManager->getRepository(Donation::class)->findOneBy([
            'stripePaymentIntentId' => $paymentIntent->id
        ]);

        if ($donation) {
            $donation->setStatus('failed');
            $entityManager->flush();
        }
    }
}
