<?php

/**
 * Stripe API keys — get these from https://dashboard.stripe.com/apikeys
 * Use the "Test mode" toggle while developing and testing (test keys start
 * with sk_test_/pk_test_), then switch to live keys only once you're ready
 * to accept real payments (they start with sk_live_/pk_live_).
 *
 * Fill these in directly on your live server after uploading. Do NOT put
 * real keys in version control — this file ships with empty placeholders
 * on purpose.
 */

const STRIPE_SECRET_KEY = ''; // sk_test_... or sk_live_...

/**
 * Signing secret for the webhook endpoint (api/stripe-webhook.php). Create
 * the webhook in the Stripe Dashboard (Developers -> Webhooks -> Add
 * endpoint, URL: https://yourdomain/api/stripe-webhook.php, event:
 * checkout.session.completed) and paste the "Signing secret" it gives you
 * here. See README.md "Payments — Stripe" for the full walkthrough.
 */
const STRIPE_WEBHOOK_SECRET = ''; // whsec_...
