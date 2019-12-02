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

    public function __construct(string $uri) {

        $this->targetURI = $uri;
        $curlOptions = [CURLOPT_RETURNTRANSFER => TRUE];
        $this->networkAdapter = new cURLAdapter(null, $curlOptions);
    }

    public function RequestToJoinNetwork(int $network_key, string $installation_key, string $email, string $justification, string $ipAddress, string $token, string $url)
    {
        $this->adapterw('networkapi/requestToJoinNetwork', ['network_key' => $network_key, 'email' => $email, 'justification' => $justification]);
        $response = $this->networkAdapter->Send();
        return $this->processResponse($response);
    }

    public function adapterw(string $uriTail, array $data)
    {
        $this->networkAdapter->setOption(CURLOPT_URL, $this->targetURI . $uriTail);
        $this->networkAdapter->setOption(CURLOPT_POST, true);

        $this->networkAdapter->setOption(CURLOPT_POSTFIELDS, $data);
    }
}