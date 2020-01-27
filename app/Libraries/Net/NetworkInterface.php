<?php namespace App\Libraries\Net;

/**
 * NetworkInterface.php
 * 
 * Created: 31/10/2019
 * 
 * @author Mehdi Mehtarizadeh
 * @author Gregory Warren
 * 
 * This class interfaces the software with individual installations.
 * 
 * 
 */

 use App\Models\Settings;


class NetworkInterface
{
    private $targetURI;

    private $networkAdapter;

    private $responseCode;

    public function __construct(string $uri = null)
    {
        if ($uri) {
            $this->targetURI = $uri;
        }
        $curlOptions = [CURLOPT_RETURNTRANSFER => TRUE];
        $this->networkAdapter = new cURLAdapter(null, $curlOptions);
    }

    public function setTargetURI(string $uri)
    {
        $this->targetURI = $uri;
    }

    public function RequestToJoinNetwork(int $network_key, string $installation_key, string $email, string $justification, string $ipAddress, string $token, string $url)
    {
        $this->adapterw('NetworkApi/requestToJoinNetwork', ['network_key' => $network_key, 'installation_key' => $installation_key, 'email' => $email, 'justification' => $justification, 'ip_address' => $ipAddress, 'token' => $token, 'url' => $url]);
        $response = $this->networkAdapter->Send();
        $this->responseCode = $this->networkAdapter->getHTTPResponseCode();
        return $this->processResponse($response);
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    private function processResponse($response)
    {
        $responseObj = json_decode($response);
        return $responseObj;
    }

    public function adapterw(string $uriTail, array $data)
    {
        $this->networkAdapter->setOption(CURLOPT_URL, $this->targetURI . $uriTail);
        $this->networkAdapter->setOption(CURLOPT_POST, true);

        $this->networkAdapter->setOption(CURLOPT_POSTFIELDS, $data);
    }
}