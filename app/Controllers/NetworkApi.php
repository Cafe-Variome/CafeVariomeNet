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

class NetworkApi extends ResourceController{

    private $networkModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    public function validateInstallation()
    {
        $key = $this->request->getVar("installation_key");

        $isValid = \App\Libraries\Net\CafeVariomeNet::validateInstallation($key);
        return $this->respond($isValid);

    }

    public function getNetworks()
    {
        $networkModel = new Network();
        return $this->respond($networkModel->getNetworks());
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

        $networkModel = new Network();
        
        $networkModel->createNetwork($data, true);

        return $this->respond($networkModel->getResponseJSON());      
    }

    public function addInstallationToNetwork()
    {
        $installation_key = $this->request->getVar("installation_key");
        $network_key = $this->request->getVar("network_key");

        $networkModel = new Network();

        $networkModel->addInstallationToNetwork($installation_key, $network_key);
        
        return $this->respond($networkModel->getResponseJSON());      
    }

    public function getNetworksByInstallationKey()
    {
        $installation_key = $this->request->getVar("installation_key");

        $networkModel = new Network();

        $networkModel->getNetworksByInstallationKey($installation_key);

        return $this->respond($networkModel->getResponseJSON());
    }

}