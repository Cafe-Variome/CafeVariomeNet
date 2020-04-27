<?php namespace App\Controllers;

/**
 * NetworkApi.php
 * 
 * Created : 14/10/2019
 * 
 * @author Gregory Warren
 * @author Mehdi Mehtarizadeh
 * 
 * This controller contains RESTful listeners for network operations. 
 * 
 */

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Models\Network;

use App\Libraries\CafeVariomeNet\Core\APIResponseBundle;
use App\Libraries\CafeVariomeNet\Core\NetworkCore;
use App\Libraries\CafeVariomeNet\Core\InstallationCore;

class NetworkApi extends ResourceController{

    private $networkModel;

    private $installation_key;

    private $networkCoreInstance;

    private $responseBundleInstance;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
 
        $this->validateInstallation();

        $this->networkCoreInstance = new NetworkCore();
        $this->responseBundleInstance = new APIResponseBundle();
    }

    public function validateInstallation() 
    {
        $this->installation_key = $this->request->getVar("installation_key");

        $isValid = \App\Libraries\Net\CafeVariomeNet::validateInstallation($this->installation_key);
        if (!$isValid) {
            echo json_encode('Unauthorised.');
            exit;
        }
    }

    public function createNetwork()
    {
        $network_name = $this->request->getVar("network_name");
        $network_type = $this->request->getVar("network_type");
        $network_threashold = $this->request->getVar("network_threshold");
        $network_status = $this->request->getVar("network_status");

		$data = ['network_name' => $network_name,
				 'network_type' => $network_type,
				 'network_threshold' => $network_threashold,
				 'network_status' => $network_status
                ]; 

        try {
            $network_key = $this->networkCoreInstance->createNetwork($data, true);

            if ($network_key == 0) {
                $this->responseBundleInstance->initiateResponse(0);
                $this->responseBundleInstance->setResponseMessage('Network name is not unique.');
            }
            elseif ($network_key == -1) {
                $this->responseBundleInstance->initiateResponse(0);
            }
            else {
                $this->responseBundleInstance->initiateResponse(1, ['network_key' => $network_key]);
            }
        }
        catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->responseBundleInstance->initiateResponse(0);
            $this->responseBundleInstance->setResponseMessage($ex->getMessage());
        }

        return $this->respond($this->responseBundleInstance->getResponseJSON());
    }

    public function addInstallationToNetwork()
    {
        $installation_key = $this->request->getVar("installation_key");
        $network_key = $this->request->getVar("network_key");

        try {
            $this->networkCoreInstance->addInstallationToNetwork($installation_key, $network_key);
            $this->responseBundleInstance->initiateResponse(1);
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->responseBundleInstance->initiateResponse(0);
            $this->responseBundleInstance->setResponseMessage($ex->getMessage());
        }
        
        return $this->respond($this->responseBundleInstance->getResponseJSON());
    }

    public function getNetwork()
    {
        $network_key = $this->request->getVar("network_key");

        try {
            $networkModel = new Network();
            $network = $networkModel->getNetwork((int)$network_key);
            $this->responseBundleInstance->initiateResponse(1, $network);
        } catch (\Exception $ex) {
            $this->responseBundleInstance->initiateResponse(0);
            $this->responseBundleInstance->setResponseMessage($ex->getMessage());
        }

        return $this->respond($this->responseBundleInstance->getResponseJSON());
    }

    public function getNetworksByInstallationKey()
    {
        $installation_key = $this->request->getVar("installation_key");

        try {
            $networks = $this->networkCoreInstance->getNetworksByInstallationKey($installation_key);
            $this->responseBundleInstance->initiateResponse(1, $networks);
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->responseBundleInstance->initiateResponse(0);
            $this->responseBundleInstance->setResponseMessage($ex->getMessage());
        }

        return $this->respond($this->responseBundleInstance->getResponseJSON());
    }

    public function getNetworkThreshold()
    {
        $network_key = $this->request->getVar('network_key');

        try {
            $network_threshold = $this->networkCoreInstance->getNetworkThreshold((int)$network_key);
            if ($network_threshold != -1) {
                $this->responseBundleInstance->initiateResponse(1, ['network_threshold' => $network_threshold]);
            }
            else {
                $this->responseBundleInstance->initiateResponse(0);
                $this->responseBundleInstance->setResponseMessage('Network not found.');
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->responseBundleInstance->initiateResponse(0);
            $this->responseBundleInstance->setResponseMessage($ex->getMessage());
        }

        return $this->respond($this->responseBundleInstance->getResponseJSON());
    }

    public function setNetworkThreshold()
    {
        $network_key = $this->request->getVar('network_key');
        $network_threshold = $this->request->getVar('network_threshold');

        try {
            $result = $this->networkCoreInstance->setNetworkThreshold($network_key, $network_threshold);
            if ($result) {
                $this->responseBundleInstance->initiateResponse(1);
            }
            else {
                $this->responseBundleInstance->initiateResponse(0);
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->responseBundleInstance->initiateResponse(0);
            $this->responseBundleInstance->setResponseMessage($ex->getMessage());
        }

            return $this->respond($this->responseBundleInstance->getResponseJSON());
    }

    public function leaveNetwork()
    {
        $network_key = $this->request->getVar('network_key');
        $installation_key = $this->request->getVar("installation_key");

        try {
            $this->networkCoreInstance->leaveNetwork($installation_key, (int)$network_key);
            $this->responseBundleInstance->initiateResponse(1);
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->responseBundleInstance->initiateResponse(0);
            $this->responseBundleInstance->setResponseMessage($ex->getMessage());
        }

        return $this->respond($this->responseBundleInstance->getResponseJSON());
    }

    public function getAvailableNetworks()
    {
        try {
            $networks = $this->networkCoreInstance->getAvailableNetworks($this->installation_key);
            $this->responseBundleInstance->initiateResponse(1, $networks);
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->responseBundleInstance->initiateResponse(0);
            $this->responseBundleInstance->setResponseMessage($ex->getMessage());
        }

        return $this->respond($this->responseBundleInstance->getResponseJSON());
    }

    public function requestToJoinNetwork()
    {
        $network_key = $this->request->getVar('network_key');
        $email = $this->request->getVar('email');
        $justification = $this->request->getVar('justification');

        try {
            $joinNetworkStatus = $this->networkCoreInstance->requestToJoinNetwork($this->installation_key, (int)$network_key, $justification, $email, $this->request->getIPAddress());
            if($joinNetworkStatus)
            {
                $this->responseBundleInstance->initiateResponse(1);
            }
            else
            {
                $this->responseBundleInstance->initiateResponse(0);
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->responseBundleInstance->initiateResponse(0);
            $this->responseBundleInstance->setResponseMessage($ex->getMessage());
        }

        return $this->respond($this->responseBundleInstance->getResponseJSON());
    }

    public function acceptRequest()
    {
        $token = $this->request->getVar('token');

        try {
            if($this->networkCoreInstance->acceptRequest($token)){
                $this->responseBundleInstance->initiateResponse(1);
            }
            else{
                $this->responseBundleInstance->initiateResponse(0);
            }

        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->responseBundleInstance->initiateResponse(0);
            $this->responseBundleInstance->setResponseMessage($ex->getMessage());
        }

        return $this->respond($this->responseBundleInstance->getResponseJSON());
    }

    public function denyRequest()
    {
        $token = $this->request->getVar('token');

        try {
            if($this->networkCoreInstance->denyRequest($token)){
                $this->responseBundleInstance->initiateResponse(1);
            }
            else{
                $this->responseBundleInstance->initiateResponse(0);
            }

        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->responseBundleInstance->initiateResponse(0);
            $this->responseBundleInstance->setResponseMessage($ex->getMessage());
        }

        return $this->respond($this->responseBundleInstance->getResponseJSON());
    }

    public function getInstallationsByNetworkKey()
    {
        $network_key = $this->request->getVar('network_key');

        try {
            $installationCore = new InstallationCore();
            $installations = $installationCore->getInstallationsByNetworkKey((int)$network_key);
            $this->responseBundleInstance->initiateResponse(1, $installations);
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->responseBundleInstance->initiateResponse(0);
            $this->responseBundleInstance->setResponseMessage($ex->getMessage());
        }

        return $this->respond($this->responseBundleInstance->getResponseJSON());
    }
}