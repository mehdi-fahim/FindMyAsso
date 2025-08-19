<?php

namespace App\Service;

use App\Entity\Donation;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeService
{
    private string $secretKey;
    private string $publishableKey;
    private string $webhookSecret;

    public function __construct(string $stripeSecretKey, string $stripePublishableKey, string $stripeWebhookSecret)
    {
        $this->secretKey = $stripeSecretKey;
        $this->publishableKey = $stripePublishableKey;
        $this->webhookSecret = $stripeWebhookSecret;
        
        Stripe::setApiKey($this->secretKey);
    }

    public function createCheckoutSession(Donation $donation): Session
    {
        $successUrl = $this->generateUrl('app_donation_success', ['session_id' => '{CHECKOUT_SESSION_ID}']);
        $cancelUrl = $this->generateUrl('app_donation_cancel');

        $sessionData = [
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => strtolower($donation->getCurrency()),
                        'product_data' => [
                            'name' => 'Don à ' . ($donation->getAssociation() ? $donation->getAssociation()->getName() : 'FindMyAsso'),
                            'description' => $donation->getMessage() ?: 'Don pour la protection animale',
                        ],
                        'unit_amount' => $donation->getAmount() * 100, // Stripe utilise les centimes
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'donation_id' => $donation->getId(),
                'user_id' => $donation->getUser() ? $donation->getUser()->getId() : '',
                'association_id' => $donation->getAssociation() ? $donation->getAssociation()->getId() : '',
            ],
        ];

        return Session::create($sessionData);
    }

    public function createPaymentIntent(Donation $donation): \Stripe\PaymentIntent
    {
        return \Stripe\PaymentIntent::create([
            'amount' => $donation->getAmount() * 100,
            'currency' => strtolower($donation->getCurrency()),
            'metadata' => [
                'donation_id' => $donation->getId(),
                'user_id' => $donation->getUser() ? $donation->getUser()->getId() : '',
                'association_id' => $donation->getAssociation() ? $donation->getAssociation()->getId() : '',
            ],
        ]);
    }

    public function constructEvent(string $payload, string $sigHeader, string $endpointSecret): Event
    {
        try {
            return Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            throw new \Exception('Invalid signature: ' . $e->getMessage());
        }
    }

    public function getPublishableKey(): string
    {
        return $this->publishableKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getWebhookSecret(): string
    {
        return $this->webhookSecret;
    }

    public function createCustomer(string $email, string $name): \Stripe\Customer
    {
        return \Stripe\Customer::create([
            'email' => $email,
            'name' => $name,
        ]);
    }

    public function createSubscription(string $customerId, string $priceId): \Stripe\Subscription
    {
        return \Stripe\Subscription::create([
            'customer' => $customerId,
            'items' => [
                ['price' => $priceId],
            ],
        ]);
    }

    public function cancelSubscription(string $subscriptionId): \Stripe\Subscription
    {
        $subscription = \Stripe\Subscription::retrieve($subscriptionId);
        return $subscription->cancel();
    }

    public function getCustomer(string $customerId): \Stripe\Customer
    {
        return \Stripe\Customer::retrieve($customerId);
    }

    public function updateCustomer(string $customerId, array $data): \Stripe\Customer
    {
        return \Stripe\Customer::update($customerId, $data);
    }

    public function createRefund(string $paymentIntentId, ?int $amount = null): \Stripe\Refund
    {
        $refundData = ['payment_intent' => $paymentIntentId];
        
        if ($amount !== null) {
            $refundData['amount'] = $amount;
        }
        
        return \Stripe\Refund::create($refundData);
    }

    public function getPaymentIntent(string $paymentIntentId): \Stripe\PaymentIntent
    {
        return \Stripe\PaymentIntent::retrieve($paymentIntentId);
    }

    public function getSession(string $sessionId): Session
    {
        return Session::retrieve($sessionId);
    }

    private function generateUrl(string $route, array $parameters = []): string
    {
        // Cette méthode devrait utiliser le Router de Symfony
        // Pour l'instant, on retourne une URL relative
        $url = '/' . $route;
        
        if (!empty($parameters)) {
            $url .= '?' . http_build_query($parameters);
        }
        
        return $url;
    }
}
