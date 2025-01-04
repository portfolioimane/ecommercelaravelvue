<?php
namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentSetting;

class KeysController extends Controller
{
    /**
     * Get the Stripe public key for frontend usage.
     */
    public function getStripePublicKey()
    {
        $stripeSettings = PaymentSetting::where('provider', 'stripe')->where('enabled', true)->first();

        if (!$stripeSettings || !$stripeSettings->public_key) {
            return response()->json(['error' => 'Stripe public key not found'], 500);
        }

        return response()->json(['publicKey' => $stripeSettings->public_key], 200);
    }

    /**
     * Get the PayPal public key for frontend usage.
     */
    public function getPaypalPublicKey()
    {
        $paypalSettings = PaymentSetting::where('provider', 'paypal')->where('enabled', true)->first();

        if (!$paypalSettings || !$paypalSettings->public_key) {
            return response()->json(['error' => 'PayPal public key not found'], 500);
        }

        return response()->json(['publicKey' => $paypalSettings->public_key], 200);
    }
}
