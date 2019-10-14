<?php namespace App\Controllers;

/**
 * CLI.php
 * 
 * Created: 14/10/2019
 * 
 * @author Mehdi Mehtarizadeh
 * 
 * CLI accessible functions 
 */

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class CLI extends Controller
{
    public function initCotroller(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    public function addInstallation()
    {        
        $name = readline("Please enter installation name: ");
        $url = readline("Please enter the base url of the installation: ");

        print("Creating new installation..." . PHP_EOL);

        $installationModel = new \App\Models\Installation();
        $key = $installationModel->createInstallation($name, $url);

        print("Installation created." . PHP_EOL);
        print("Installation Key is : ". $key);
    }
}
