<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Billing\CreditCards;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class AuthNet {


    public static function authenticate() {

        if(config('app.env') == 'production') {
            $login = config('global.authnet_login');
            $key = config('global.authnet_transkey');
        } else {
            $login = config('global.authnet_login_sandbox');
            $key = config('global.authnet_transkey_sandbox');
        }

        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication -> setName($login);
        $merchantAuthentication -> setTransactionKey($key);

        return $merchantAuthentication;



    }

    public static function AddCreditCard($user_id, $profile_id, $email, $number, $expire_month, $expire_year, $street, $city, $state, $zip, $first, $last, $code) {


        $user_id = $user_id > 0 ? $user_id : 'na_'.time();

        $issuers = [
            'Visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
            'Mastercard' => '/^5[1-5][0-9]{14}$/',
            'Amex' => '/^3[47][0-9]{13}$/',
            'Discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
        ];

        $number = $number;
        $issuer = null;
        foreach ($issuers as $key => $val) {
            if (preg_match($val, $number)) {
                $issuer = $key;
            }
        }

        $expire = $expire_year.'-'.$expire_month;

        $card = [
            'user_id' => $user_id,
            'profile_id' => $profile_id,
            'first' => $first,
            'last' => $last,
            'email' => $email,
            'street' => $street,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'number' => $number,
            'expire' => $expire, // 2021-04
            'code' => $code,
            'issuer' => $issuer,
        ];


        if($profile_id) {

            CreditCards::where('profile_id', $profile_id)
            -> update([
                'default' => 'no'
            ]);

            return self::createCustomerPaymentProfile($card);
        } else {
            return self::createCustomerProfile($card);
        }

    }

    public static function createCustomerProfile($card) {

        $merchantAuthentication = self::authenticate();

        // Set the transaction's refId
        $refId = 'ref' . time();

        // Set credit card information for payment profile
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard -> setCardNumber($card['number']);
        $creditCard -> setExpirationDate($card['expire']);
        $creditCard -> setCardCode($card['code']);
        $paymentCreditCard = new AnetAPI\PaymentType();
        $paymentCreditCard -> setCreditCard($creditCard);

        // Create the Bill To info for new payment type
        $billTo = new AnetAPI\CustomerAddressType();
        $billTo -> setFirstName($card['first']);
        $billTo -> setLastName($card['last']);
        $billTo -> setAddress($card['street']);
        $billTo -> setCity($card['city']);
        $billTo -> setState($card['state']);
        $billTo -> setZip($card['zip']);
        $billTo -> setCountry('USA');

        // Create a new CustomerPaymentProfile object
        $paymentProfile = new AnetAPI\CustomerPaymentProfileType();
        $paymentProfile -> setCustomerType('individual');
        $paymentProfile -> setBillTo($billTo);
        $paymentProfile -> setPayment($paymentCreditCard);
        $paymentProfiles[] = $paymentProfile;

        // Create a new CustomerProfileType and add the payment profile object
        $customerProfile = new AnetAPI\CustomerProfileType();
        $customerProfile -> setDescription($card['first'].' '.$card['last']);
        $customerProfile -> setMerchantCustomerId($card['user_id']);
        $customerProfile -> setEmail($card['email']);

        $customerProfile -> setPaymentProfiles($paymentProfiles);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateCustomerProfileRequest();
        $request -> setMerchantAuthentication($merchantAuthentication);
        $request -> setRefId($refId);
        $request -> setProfile($customerProfile);

        // Create the controller and get the response
        $controller = new AnetController\CreateCustomerProfileController($request);
        $response = $controller -> executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

        if (($response != null) && ($response -> getMessages() -> getResultCode() == "Ok")) {

            $paymentProfiles = $response -> getCustomerPaymentProfileIdList();

        } else {

            $errorMessages = $response -> getMessages() -> getMessage();
            $error_text = $errorMessages[0] -> getText();

            $result = AuthNet::error_text($error_text);
            return $result;

        }

        $add_card = new CreditCards();
        $add_card -> user_id = $card['user_id'];
        $add_card -> first = $card['first'];
        $add_card -> last = $card['last'];
        $add_card -> email = $card['email'];
        $add_card -> street = $card['street'];
        $add_card -> city = $card['city'];
        $add_card -> state = $card['state'];
        $add_card -> zip = $card['zip'];
        $add_card -> last_four = substr($card['number'], -4);
        $add_card -> expire = $card['expire'];
        $add_card -> code = $card['code'];
        $add_card -> issuer = $card['issuer'];
        $add_card -> profile_id = $response -> getCustomerProfileId();
        $add_card -> payment_profile_id = $paymentProfiles[0];
        $add_card -> save();


        User::find($card['user_id']) -> update([
            'profile_id' => $response -> getCustomerProfileId()
        ]);

        //return true;
    }

    public static function createCustomerPaymentProfile($card) {

        $merchantAuthentication = self::authenticate();

        // Set the transaction's refId
        $refId = 'ref' . time();

        // Set credit card information for payment profile
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard -> setCardNumber($card['number']);
        $creditCard -> setExpirationDate($card['expire']);
        $creditCard -> setCardCode($card['code']);
        $paymentCreditCard = new AnetAPI\PaymentType();
        $paymentCreditCard -> setCreditCard($creditCard);

        // Create the Bill To info for new payment type
        $billTo = new AnetAPI\CustomerAddressType();
        $billTo -> setFirstName($card['first']);
        $billTo -> setLastName($card['last']);
        $billTo -> setAddress($card['street']);
        $billTo -> setCity($card['city']);
        $billTo -> setState($card['state']);
        $billTo -> setZip($card['zip']);
        $billTo -> setCountry('USA');

        // Create a new Customer Payment Profile object
        $paymentProfile = new AnetAPI\CustomerPaymentProfileType();
        $paymentProfile -> setCustomerType('individual');
        $paymentProfile -> setBillTo($billTo);
        $paymentProfile -> setPayment($paymentCreditCard);
        $paymentProfile -> setDefaultPaymentProfile(true);

        // Assemble the complete transaction request
        $paymentProfileRequest = new AnetAPI\CreateCustomerPaymentProfileRequest();
        $paymentProfileRequest -> setMerchantAuthentication($merchantAuthentication);

        // Add an existing profile id to the request
        $paymentProfileRequest -> setCustomerProfileId($card['profile_id']);
        $paymentProfileRequest -> setPaymentProfile($paymentProfile);
        $paymentProfileRequest -> setValidationMode('liveMode');

        // Create the controller and get the response
        $controller = new AnetController\CreateCustomerPaymentProfileController($paymentProfileRequest);
        $response = $controller -> executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

        if (($response != null) && ($response -> getMessages() -> getResultCode() == 'Ok') ) {

            $paymentProfileID = $response -> getCustomerPaymentProfileId();

        } else {

            $errorMessages = $response -> getMessages() -> getMessage();
            $error_text = $errorMessages[0] -> getText();

            $result = AuthNet::error_text($error_text);
            return $result;

        }

        $add_card = new CreditCards();
        $add_card -> user_id = $card['user_id'];
        $add_card -> first = $card['first'];
        $add_card -> last = $card['last'];
        $add_card -> email = $card['email'];
        $add_card -> street = $card['street'];
        $add_card -> city = $card['city'];
        $add_card -> state = $card['state'];
        $add_card -> zip = $card['zip'];
        $add_card -> last_four = substr($card['number'], -4);
        $add_card -> expire = $card['expire'];
        $add_card -> code = $card['code'];
        $add_card -> issuer = $card['issuer'];
        $add_card -> profile_id = $card['profile_id'];
        $add_card -> payment_profile_id = $paymentProfileID;
        $add_card -> save();

        //return true;

    }

    public static function error_text($error_text) {

        $dash = strrpos($error_text, '-');
        if ($dash) {
            if (preg_match('/cardCode/', $error_text)) {
                $field = 'CVV';
            } elseif (preg_match('/cardNumber/', $error_text)) {
                $field = 'Credit Card';
            }
            $error_text = $field.' Error: '.substr($error_text, $dash + 1);
        }

        if (stristr($error_text, 'duplicate customer payment profile')) {
            $error_text = 'That credit card has already been added';
        }
        if (stristr($error_text, 'The record cannot be found')) {
            $error_text = 'Profile ID is incorrect. Please contact our IT support info@taylorprops.com to correct this.';
        }

        return $error_text;

    }


    public static function deleteCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId) {

        $merchantAuthentication = self::authenticate();

        $refId = 'ref' . time();

	    // Use an existing payment profile ID for this Merchant name and Transaction key

        $request = new AnetAPI\DeleteCustomerPaymentProfileRequest();
        $request -> setMerchantAuthentication($merchantAuthentication);
        $request -> setCustomerProfileId($customerProfileId);
        $request -> setCustomerPaymentProfileId($customerPaymentProfileId);
        $controller = new AnetController\DeleteCustomerPaymentProfileController($request);
        $response = $controller -> executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
        if (($response != null) && ($response -> getMessages() -> getResultCode() == "Ok") ) {

            CreditCards::where('profile_id', $customerProfileId) -> where('payment_profile_id', $customerPaymentProfileId) -> delete();

            return response() -> json(['success' => true]);

        } else {

            $errorMessages = $response -> getMessages() -> getMessage();
            return response() -> json(['error' => $errorMessages[0]-> getText()]);

        }

    }

}
