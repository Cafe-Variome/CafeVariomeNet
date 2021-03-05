<?php namespace App\Controllers;

/**
 * Admin.php
 * Created 05/03/2021
 * 
 * @author Mehdi Mehtarizadeh
 */

use App\Models\UIData;
use CodeIgniter\Config\Services;

class Admin extends CVUI_Controller{

    /**
	 * Constructor
	 *
	 */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger){
        parent::setProtected(false);
        parent::setIsAdmin(false);
        parent::initController($request, $response, $logger);

		$this->session = Services::session();
		$this->db = \Config\Database::connect();
        //$this->setting =  Settings::getInstance($this->db);

        $this->validation = Services::validation();

    }

    public function Index()
    {
        $uidata = new UIData();
        $uidata->title = "Index";

        $data = $this->wrapData($uidata);
        
        return view($this->viewDirectory. '/Index', $data);

    }

}