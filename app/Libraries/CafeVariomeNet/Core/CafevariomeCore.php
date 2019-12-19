<?php namespace App\Libraries\CafeVariomeNet\Core;

/**
 * CafevariomeCore.php
 * 
 * Created: 13/12/2019
 * 
 * @author Mehdi Mehtarizadeh
 * 
 */

use App\Models\Installation;


class CafeVariomeCore
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


