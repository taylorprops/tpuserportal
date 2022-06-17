<?php

namespace App\Classes;

class Zimbra
{

    public $ServerAddress;
    public $AdminUserName;
    public $AdminPassword;
    public $COSId;

    public function __construct($ServerAddress, $AdminUserName, $AdminPassword, $COSId)
    {

        $this -> ServerAddress = config('global.email_server_id');
        $this -> AdminUserName = config('global.email_server_username');
        $this -> AdminPassword = config('global.email_server_password');
        $this -> COSId = config('global.email_server_cosid');

    }

    public function ZimbraAdminCreateAccount($NewUserEmail, $NewUserPassword, $NewUserName)
    {

        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        // ------ Send the zimbraCreateAccount request -----
        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                <soap:Header>
                                        <context xmlns="urn:zimbra">
                                                <authToken>'.$authToken.'</authToken>
                                                <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                        </context>
                                </soap:Header>
                                <soap:Body>
                                        <CreateAccountRequest xmlns="urn:zimbraAdmin">
                                                <name>'.$NewUserEmail.'</name>
                                                <password>'.$NewUserPassword.'</password>
												<a xmlns="" n="displayName">'.$NewUserName.'</a>
                                                <a n="zimbraCOSId">'.$this -> COSId.'</a>
                                        </CreateAccountRequest>
                                </soap:Body>
                        </soap:Envelope>';
        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);

        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            $msg = 'ERROR: curl_exec - ('.curl_errno($CurlHandle).') '.curl_error($CurlHandle);
            return response() -> json(['error' => $msg]);
        }

        return ($ZimbraSOAPResponse);

    }

    public function ZimbraAdminAddForward($UserEmail, $NewForward)
    {

        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
								<soap:Header>
										<context xmlns="urn:zimbra">
												<authToken>'.$authToken.'</authToken>
												<sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
										</context>
								</soap:Header>
								<soap:Body>
										<GetAccountRequest xmlns="urn:zimbraAdmin">
												<account by="name">'.$UserEmail.'</account>
										</GetAccountRequest>
								</soap:Body>
						</soap:Envelope>';
        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            $msg = "ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle);
            mail('mike@taylorprops.com', 'Email Account Error', $msg, 'From: Mike <mike@taylorprops.com>');
        }
        $zimbraId = strstr($ZimbraSOAPResponse, "<a n=\"zimbraId\"");
        $zimbraId = strstr($zimbraId, ">");
        $zimbraId = substr($zimbraId, 1, strpos($zimbraId, "<") - 1);
        if ($zimbraId == "") {
            return (false);
        }
        // ------ Send the zimbraModifyAccount request -----
        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                <soap:Header>
                                        <context xmlns="urn:zimbra">
                                                <authToken>'.$authToken.'</authToken>
                                                <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                        </context>
                                </soap:Header>
                                <soap:Body>
                                        <ModifyAccountRequest xmlns="urn:zimbraAdmin">
                                                <id>'.$zimbraId.'</id>
                                                <a n="zimbraPrefMailForwardingAddress">'.$NewForward.'</a>
												<a n="zimbraPrefMailLocalDeliveryDisabled">TRUE</a>
                                                <a n="zimbraCOSId">'.$COSId.'</a>
                                        </ModifyAccountRequest>
                                </soap:Body>
                        </soap:Envelope>';
        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            $msg = "ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle);
            mail('mike@taylorprops.com', 'Email Account Error', $msg, 'From: Mike <mike@taylorprops.com>');
        }
        return ($ZimbraSOAPResponse);
    }

    public function ZimbraAdminActivateAccount($UserEmail)
    {

        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                            <soap:Header>
                                    <context xmlns="urn:zimbra">
                                            <authToken>'.$authToken.'</authToken>
                                            <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                    </context>
                            </soap:Header>
                            <soap:Body>
                                    <GetAccountRequest xmlns="urn:zimbraAdmin">
                                            <account by="name">'.$UserEmail.'</account>
                                    </GetAccountRequest>
                            </soap:Body>
                    </soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);

        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            print("ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle));
            return (false);
        }
        $zimbraId = strstr($ZimbraSOAPResponse, "<a n=\"zimbraId\"");
        $zimbraId = strstr($zimbraId, ">");
        $zimbraId = substr($zimbraId, 1, strpos($zimbraId, "<") - 1);
        if ($zimbraId == "") {
            return (false);
        }
        // ------ Send the zimbraModifyAccount request -----
        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                <soap:Header>
                                        <context xmlns="urn:zimbra">
                                                <authToken>'.$authToken.'</authToken>
                                                <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                        </context>
                                </soap:Header>
                                <soap:Body>
                                        <ModifyAccountRequest xmlns="urn:zimbraAdmin">
                                                <id>'.$zimbraId.'</id>
                                                <a n="zimbraAccountStatus">active</a>
                                        </ModifyAccountRequest>
                                </soap:Body>
                        </soap:Envelope>';
        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            print("ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle));
            return (false);
        }
        return ($ZimbraSOAPResponse);
    }

    public function ZimbraAdminInactivateAccount($UserEmail)
    {

        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                <soap:Header>
                                        <context xmlns="urn:zimbra">
                                                <authToken>'.$authToken.'</authToken>
                                                <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                        </context>
                                </soap:Header>
                                <soap:Body>
                                        <GetAccountRequest xmlns="urn:zimbraAdmin">
                                                <account by="name">'.$UserEmail.'</account>
                                        </GetAccountRequest>
                                </soap:Body>
                        </soap:Envelope>';
        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            //print("ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle));
            return (false);
        }
        $zimbraId = strstr($ZimbraSOAPResponse, "<a n=\"zimbraId\"");
        $zimbraId = strstr($zimbraId, ">");
        $zimbraId = substr($zimbraId, 1, strpos($zimbraId, "<") - 1);
        if ($zimbraId == "") {
            return (false);
        }

        // ------ Send the zimbraModifyAccount request -----
        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                <soap:Header>
                                        <context xmlns="urn:zimbra">
                                                <authToken>'.$authToken.'</authToken>
                                                <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                        </context>
                                </soap:Header>
                                <soap:Body>
                                        <ModifyAccountRequest xmlns="urn:zimbraAdmin">
                                                <id>'.$zimbraId.'</id>
                                                <a n="zimbraAccountStatus">closed</a>
                                        </ModifyAccountRequest>
                                </soap:Body>
                        </soap:Envelope>';
        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            //print("ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle));
            return (false);
        }
        return ($ZimbraSOAPResponse);
    }

    public function ZimbraAdminDeleteAccount($UserEmail)
    {

        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <GetAccountRequest xmlns="urn:zimbraAdmin">
                                                    <account by="name">'.$UserEmail.'</account>
                                            </GetAccountRequest>
                                    </soap:Body>
                            </soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            print("ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle));
            return (false);
        }
        $zimbraId = strstr($ZimbraSOAPResponse, "<a n=\"zimbraId\"");
        $zimbraId = strstr($zimbraId, ">");
        $zimbraId = substr($zimbraId, 1, strpos($zimbraId, "<") - 1);
        if ($zimbraId == "") {
            return (false);
        }
        // ------ Send the zimbraModifyAccount request -----
        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <DeleteAccountRequest xmlns="urn:zimbraAdmin">
                                                    <id>'.$zimbraId.'</id>
                                            </DeleteAccountRequest>
                                    </soap:Body>
                            </soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            //print("ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle));
            return (false);
        }
        return ($ZimbraSOAPResponse);
    }

    public function ZimbraAdminCreateAlias($NewUserAlias, $UserEmail)
    {

        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <GetAccountRequest xmlns="urn:zimbraAdmin">
                                                    <account by="name">'.$UserEmail.'</account>
                                            </GetAccountRequest>
                                    </soap:Body>
                            </soap:Envelope>';
        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            print("ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle));
            return (false);
        }
        $zimbraId = strstr($ZimbraSOAPResponse, "<a n=\"zimbraId\"");
        $zimbraId = strstr($zimbraId, ">");
        $zimbraId = substr($zimbraId, 1, strpos($zimbraId, "<") - 1);
        if ($zimbraId == "") {
            return (false);
        }
        // ------ Send the zimbraAddAccountAlias request -----
        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <AddAccountAliasRequest xmlns="urn:zimbraAdmin">
                                                    <id>'.$zimbraId.'</id>
                                                    <alias>'.$NewUserAlias.'</alias>
                                            </AddAccountAliasRequest>
                                    </soap:Body>
                            </soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            print("ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle));
            return (false);
        }
        return ($ZimbraSOAPResponse);
    }

    public function ZimbraAdminGetAccountInfo($UserEmail)
    {

        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <GetAccountRequest xmlns="urn:zimbraAdmin">
                                                    <account by="name">'.$UserEmail.'</account>
                                            </GetAccountRequest>
                                    </soap:Body>
                            </soap:Envelope>';
        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            $msg = "ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle);
            mail('mike@taylorprops.com', 'Email Account Error', $msg, 'From: Mike <mike@taylorprops.com>');
        }

        return ($ZimbraSOAPResponse);
    }

    public function ZimbraAdminGetDistLists($Domain)
    {
        $
        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <GetAllDistributionListsRequest xmlns="urn:zimbraAdmin">
                                                    <domain by="name">'.$Domain.'</domain>
                                            </GetAllDistributionListsRequest>
                                    </soap:Body>
                            </soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);

        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            $msg = "ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle);
            //mail('mike@taylorprops.com', 'Email Account Error', $msg, 'From: Mike <mike@taylorprops.com>');
            echo $msg;
        }

        //print("Raw Zimbra SOAP Response:<BR>" . htmlentities($ZimbraSOAPResponse) . "<BR><BR>\n");

        return ($ZimbraSOAPResponse);
    }

    public function ZimbraAdminAddToDistList($UserEmail, $DistList)
    {

        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <GetDistributionListRequest xmlns="urn:zimbraAdmin">
                                                    <dl by="name">'.$DistList.'</dl>
                                            </GetDistributionListRequest>
                                    </soap:Body>
                            </soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            print("ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle));
            return (false);
        }
        $zimbraId = strstr($ZimbraSOAPResponse, "<a n=\"zimbraId\"");
        $zimbraId = strstr($zimbraId, ">");
        $zimbraId = substr($zimbraId, 1, strpos($zimbraId, "<") - 1);
        if ($zimbraId == "") {
            return (false);
        }
        // ------ Send the zimbraModifyAccount request -----
        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <AddDistributionListMemberRequest xmlns="urn:zimbraAdmin">
                                                    <id>'.$zimbraId.'</id>
                                                    <dlm>'.$UserEmail.'</dlm>
                                            </AddDistributionListMemberRequest>
                                    </soap:Body>
                            </soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);

        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            $msg = "ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle);
            //mail('mike@taylorprops.com', 'Email Account Error', $msg, 'From: Mike <mike@taylorprops.com>');
            //echo $msg;
        }
        return ($ZimbraSOAPResponse);
    }

    public function ZimbraAdminRemoveFromDistList($UserEmail, $DistList)
    {

        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <GetDistributionListRequest xmlns="urn:zimbraAdmin">
                                                    <dl by="name">'.$DistList.'</dl>
                                            </GetDistributionListRequest>
                                    </soap:Body>
                            </soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            print("ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle));
            return (false);
        }
        $zimbraId = strstr($ZimbraSOAPResponse, "<a n=\"zimbraId\"");
        $zimbraId = strstr($zimbraId, ">");
        $zimbraId = substr($zimbraId, 1, strpos($zimbraId, "<") - 1);
        if ($zimbraId == "") {
            return (false);
        }
        // ------ Send the zimbraModifyAccount request -----
        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <RemoveDistributionListMemberRequest xmlns="urn:zimbraAdmin">
                                                    <id>'.$zimbraId.'</id>
                                                    <dlm>'.$UserEmail.'</dlm>
                                            </RemoveDistributionListMemberRequest>
                                    </soap:Body>
                            </soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);

        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            $msg = "ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle);
            //mail('mike@taylorprops.com', 'Email Account Error', $msg, 'From: Mike <mike@taylorprops.com>');
            //echo $msg;
        }
        return ($ZimbraSOAPResponse);
    }

    public function ZimbraAdminAddDistList($DistList)
    {

        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                <soap:Header>
                                        <context xmlns="urn:zimbra">
                                                <authToken>'.$authToken.'</authToken>
                                                <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                        </context>
                                </soap:Header>
                                <soap:Body>
                                        <CreateDistributionListRequest xmlns="urn:zimbraAdmin">
                                                <name>'.$DistList.'</name>
                                        </CreateDistributionListRequest>
                                </soap:Body>
                        </soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);

        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            $msg = "ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle);
            //mail('mike@taylorprops.com', 'Email Account Error', $msg, 'From: Mike <mike@taylorprops.com>');
            //echo $msg;
        }
        return ($ZimbraSOAPResponse);
    }

    public function ZimbraAdminDeleteDistList($DistList)
    {

        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <GetDistributionListRequest xmlns="urn:zimbraAdmin">
                                                    <dl by="name">'.$DistList.'</dl>
                                            </GetDistributionListRequest>
                                    </soap:Body>
                            </soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            print("ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle));
            return (false);
        }
        print("Raw Zimbra SOAP Response:<BR>" . htmlentities($ZimbraSOAPResponse) . "<BR><BR>\n");

        $zimbraId = strstr($ZimbraSOAPResponse, "<a n=\"zimbraId\"");
        $zimbraId = strstr($zimbraId, ">");
        $zimbraId = substr($zimbraId, 1, strpos($zimbraId, "<") - 1);
        if ($zimbraId == "") {
            return (false);
        }
        // ------ Send the zimbraModifyAccount request -----
        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <DeleteDistributionListRequest xmlns="urn:zimbraAdmin">
                                                    <id>'.$zimbraId.'</id>
                                            </DeleteDistributionListRequest>
                                    </soap:Body>
                            </soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);

        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            $msg = "ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle);
            //mail('mike@taylorprops.com', 'Email Account Error', $msg, 'From: Mike <mike@taylorprops.com>');
            //echo $msg;
        }
        return ($ZimbraSOAPResponse);
        print("Raw Zimbra SOAP Response:<BR>" . htmlentities($ZimbraSOAPResponse) . "<BR><BR>\n");
    }

    public function AccountInfo($UserEmail)
    {

        $auth = $this -> zimbra_auth();
        $CurlHandle = $auth['CurlHandle'];
        $sessionId = $auth['sessionId'];
        $authToken = $auth['authToken'];

        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                                    <soap:Header>
                                            <context xmlns="urn:zimbra">
                                                    <authToken>'.$authToken.'</authToken>
                                                    <sessionId id="'.$sessionId.'">'.$sessionId.'</sessionId>
                                            </context>
                                    </soap:Header>
                                    <soap:Body>
                                            <GetAccountRequest xmlns="urn:zimbraAdmin">
                                                    <account by="name">'.$UserEmail.'</account>
                                            </GetAccountRequest>
                                    </soap:Body>
                            </soap:Envelope>';
        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);
        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            $msg = "ERROR: curl_exec - (" . curl_errno($CurlHandle) . ") " . curl_error($CurlHandle);
            mail('mike@taylorprops.com', 'Email Account Error', $msg, 'From: Mike <mike@taylorprops.com>');
        }

        $forward = $this -> get_response($ZimbraSOAPResponse, 'zimbraPrefMailForwardingAddress');
        $main_account = $this -> get_response($ZimbraSOAPResponse, 'zimbraMailDeliveryAddress');
        preg_match_all('#zimbraMailAlias"\>([a-zA-Z0-9\.\-@]+)\<\/a#i', $ZimbraSOAPResponse, $alias_array);

        $results = array();
        $results['forward'] = $forward;
        $results['main_account'] = $main_account;
        $results['aliases'] = array();
        foreach ($alias_array[1] as $aliases) {
            $results['aliases'][] = $aliases;
        }
        return $results;

    }

    public function zimbra_auth()
    {

        $CurlHandle = curl_init();
        curl_setopt($CurlHandle, CURLOPT_URL, 'https://'.$this -> ServerAddress.':7071/service/admin/soap');
        curl_setopt($CurlHandle, CURLOPT_POST, true);
        curl_setopt($CurlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($CurlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($CurlHandle, CURLOPT_SSL_VERIFYHOST, false);
        // ------ Send the zimbraAdmin AuthRequest -----
        $SOAPMessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
								<soap:Header>
										<context xmlns="urn:zimbra"/>
								</soap:Header>
								<soap:Body>
										<AuthRequest xmlns="urn:zimbraAdmin">
												<name>'.$this -> AdminUserName.'</name>
												<password>'.$this -> AdminPassword.'</password>
										</AuthRequest>
								</soap:Body>
						</soap:Envelope>';

        curl_setopt($CurlHandle, CURLOPT_POSTFIELDS, $SOAPMessage);

        if (!($ZimbraSOAPResponse = curl_exec($CurlHandle))) {
            return response() -> json(['error' => curl_errno($CurlHandle)]);
        }

        $sessionId = strstr($ZimbraSOAPResponse, '<sessionId');
        $sessionId = strstr($sessionId, '>');
        $sessionId = substr($sessionId, 1, strpos($sessionId, '<') - 1);
        $authToken = strstr($ZimbraSOAPResponse, '<authToken');
        $authToken = strstr($authToken, '>');
        $authToken = substr($authToken, 1, strpos($authToken, '<') - 1);

        return ['CurlHandle' => $CurlHandle, 'sessionId' => $sessionId, 'authToken' => $authToken];

    }

    public function get_response($s, $n)
    {
        $v = strstr($s, "<a n=\"".$n . "\"");
        $v = strstr($v, ">");
        $v = substr($v, 1, strpos($v, "<") - 1);
        return $v;
    }

}
