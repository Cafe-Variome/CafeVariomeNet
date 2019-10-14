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

    public function initCotroller(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
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



}