<?php

namespace App\Http\Controllers\AuthNet;

use App\Models\User;
use Illuminate\Http\Request;
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

    public function add_card(Request $request) {

        $card = [
            'user_id' => '123',
            'first' => 'Joe',
            'last' => 'Jones',
            'email' => 'info@taylorprops.com',
            'street' => '123 Main Street',
            'city' => 'Downtown',
            'state' => 'WA',
            'zip' => '98004',
            'number' => '4242424242424242',
            'expire' => '2038-12',
            'code' => '145',
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

        return $response;
    }



}
