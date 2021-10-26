<?php

namespace App\Http\Controllers\AuthNet;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Billing\CreditCards;
use App\Http\Controllers\Controller;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class AuthNetController extends Controller {

    public function authenticate() {

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

    public static function AddCreditCard($user_id, $number, $expire_month, $expire_year, $zip, $first, $last, $code) {


        $user_id = $request -> user_id > 0 ? $request -> user_id : 'na_'.time();

        $issuers = [
            'Visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
            'Mastercard' => '/^5[1-5][0-9]{14}$/',
            'American Express' => '/^3[47][0-9]{13}$/',
            'Discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
        ];

        $number = $request -> number;
        foreach ($issuers as $key => $val) {
            if (preg_match($val, $number)) {
                $issuer = $key;
            }
        }

        $expire = $request -> expire_year.'-'.$request -> expire_month;

        $card = [
            'user_id' => $user_id,
            'profile_id' => $profile_id,
            'first' => $request -> first,
            'last' => $request -> last,
            'email' => $request -> email,
            'street' => $request -> street,
            'city' => $request -> city,
            'state' => $request -> state,
            'zip' => $request -> zip,
            'number' => $request -> number,
            'expire' => $expire, // 2021-04
            'code' => $request -> code,
            'issuer' => $issuer,
        ];

        $this -> createCustomerPaymentProfile($card);

    }

    public function createCustomerPaymentProfile($card) {

        $merchantAuthentication = $this -> authenticate();

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
            echo "Successfully created customer profile : " . $response -> getCustomerProfileId() . "\n";
            $paymentProfiles = $response -> getCustomerPaymentProfileIdList();
            echo "SUCCESS: PAYMENT PROFILE ID : " . $paymentProfiles[0] . "\n";
        } else {
            echo "ERROR :  Invalid response\n";
            $errorMessages = $response -> getMessages() -> getMessage();
            echo "Response : " . $errorMessages[0]-> getCode() . "  " . $errorMessages[0]-> getText() . "\n";
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

        return $response;
    }



}
