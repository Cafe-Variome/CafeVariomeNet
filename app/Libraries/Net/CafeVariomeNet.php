<?php namespace App\Libraries\Net;

/**
 * CafeVariomeNet.php
 * 
 * Created 14/10/2019
 * 
 * @author Mehdi Mehtarizadeh
 * 
 */

use App\Models\Installation;

class CafeVariomeNet
{

    public static function validateInstallation(string $key = null): bool{
        $installationModel = new Installation();
        $installation = $installationModel->getInstallations('base_url', ["installation_key" => $key, "status" => 1]);

        if (count($installation) == 1) {
           return true;
        }
        return false;
    }

}
 