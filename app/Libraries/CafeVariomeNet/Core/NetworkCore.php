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
        $networsWithInstallationsDetails = [];

        $installationModel = new Installation();
        foreach ($networks as $network) {
            $installations = [];
            $installationsInNetwork = $installationModel->getInstallationsByNetworkKey($network['network_key']);
        
            $network['installations_count'] = count($installationsInNetwork);
            $network['installations'] = $installationsInNetwork;
            array_push($networsWithInstallationsDetails, $network);
        }

        return $networsWithInstallationsDetails;
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
            'network_key' => $network_key,
            'installation_key' => $installation_key,
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

    public function acceptRequest(string $token): bool
    {
        $networkRequestModel = new NetworkRequest();

        $networkRequest = $networkRequestModel->getNetworkRequests('installation_key, network_key', ['token' => $token]);

        if (count($networkRequest) == 1) {
            $data = ['status' =>  1]; // Status 1 indicates an accepted request.

            try {
                $networkRequestModel->updateNetworkRequests($data, ['token' => $token]); //Update request status.
                $this->networkModelInstance->addInstallationToNetwork($networkRequest[0]['installation_key'], $networkRequest[0]['network_key']); // Add installation to network.

                return true;
            } catch (\Exception $ex) {
                error_log($ex->getMessage());
            }
        }
        return false;
    }

    public function denyRequest(string $token): bool
    {
        $networkRequestModel = new NetworkRequest();

        $networkRequest = $networkRequestModel->getNetworkRequests('installation_key, network_key', ['token' => $token]);

        if (count($networkRequest) == 1) {
            $data = ['status' =>  0]; // Status 0 indicates a deied request.

            try {
                $networkRequestModel->updateNetworkRequests($data, ['token' => $token]); //Update request status.

                return true;
            } catch (\Exception $ex) {
                error_log($ex->getMessage());
            }
        }
        return false;
    }
 }
 