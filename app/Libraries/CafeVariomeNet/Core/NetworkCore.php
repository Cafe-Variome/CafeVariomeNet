<?php namespace App\Libraries\CafeVariomeNet\Core;

/**
 * NetworkCore.php
 * 
 * Created: 17/12/2019
 * 
 * @author Mehdi Mehtarizadeh
 * 
 */

use App\Models\Installation;
use App\Models\Network;
use App\Models\NetworkRequest;
use App\Libraries\Net\KeyGen;
use App\Libraries\Net\NetworkInterface;

 class NetworkCore 
 {
    private $networkModelInstance;

    public function __construct() {
        $this->networkModelInstance = new Network();
    }

    public function getNetwork(int $network_key): array
    {
        $network = $this->networkModelInstance->getNetworks(null, ['network_key' => $network_key]);

        if (count($network) == 1) {
            return $network[0];
        }
        
        return null;
    }

    public function getNetworksByInstallationKey(string $installationKey): array
    {
        $networks = $this->networkModelInstance->getNetworksByInstallationKey($installationKey);

        return $networks;
    }

    public function createNetwork(array $data, bool $uniquename = false): int
    {
        try {
            if ($uniquename) {
                $networks = $this->networkModelInstance->getNetworks('network_name', ['network_name' => $data['network_name']]);
                if (count($networks) > 0){
                    //network name is not unique
                    return 0;
                }
            }

            return $this->networkModelInstance->createNetwork($data);
            
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }
        return -1;
    }

    public function addInstallationToNetwork(string $installation_key, int $network_key)
    {
        $this->networkModelInstance->addInstallationToNetwork($installation_key, $network_key);   
    }

    public function getNetworkThreshold(int $network_key): int
    {
        $network = $this->networkModelInstance->getNetworks('network_threshold', ['network_key' => $network_key]);

        if (count($network) == 1) {
            return $network[0]['network_threshold'];
        }

        return -1;
    }

    public function setNetworkThreshold(int $network_key, int $network_threshold): bool
    {
        $data = ['network_threshold' =>  $network_threshold];

        return $this->networkModelInstance->updateNetworks($data, ['network_key' => $network_key]);         
    }

    public function leaveNetwork(string $installation_key, int $network_key)
    {
        $this->networkModelInstance->leaveNetwork($installation_key, $network_key);
    }

    public function getAvailableNetworks(string $installation_key): array
    {
        return $this->networkModelInstance->getAvailableNetworks($installation_key);
    }

    public function requestToJoinNetwork(string $installation_key, int $network_key, string $justification, string $email, string $ip_address): bool
    {
        $networkInterface = new NetworkInterface();

        $networkRequestModel = new NetworkRequest();

        $keyGen = new KeyGen();
        $requestToken = $keyGen->generateNetworkRequestToken();

        $request = [
            'token' => $requestToken,
            'status' => -1 //A status of (-1) shows a pending request.
        ];

        try {
            $networkRequestModel->createNetworkRequest($request);
            // Call the endpoint on the target installation

            $installationModel = new Installation();
            $installations = $installationModel->getInstallationsByNetworkKey($network_key);

            $sourceUrl = $installationModel->getInstallations('base_url', ['installation_key' => $installation_key])[0]['base_url'];

            $installation_count = count($installations);
            $failure_count = 0;

            foreach ($installations as $installation) {
                $networkInterface->setTargetURI($installation['base_url']);
                try {
                    $joinResponse = $networkInterface->RequestToJoinNetwork($network_key, $installation_key, $email, $justification, $ip_address, $requestToken, $sourceUrl);
                    // save the response in the logs if necessary

                    if ($joinResponse != null && $networkInterface->getResponseCode() == 200) {
                        //Indicates a successful request. The response can be processed.
                    }
                    else {
                        $failure_count++;
                    }
                } catch (\Exception $ex) {
                    error_log($ex->getMessage());
                    $failure_count++;
                }
            }

            return $installation_count > $failure_count;
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }

        return false;
    }
 }
 