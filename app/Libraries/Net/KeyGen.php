<?php namespace App\Libraries\Net;

/**
 * KeyGen.php
 * 
 * Created: 14/10/2019
 * 
 * @author Mehdi Mehtarizadeh
 */


class KeyGen 
{

    public function generateInstallationKey()
    {
        return md5($this->generateMD5());
    }

    public function generateNetworkRequestToken()
    {
        return $this->generateMD5();
    }

    private function generateMD5() {
        $mdstring = md5(uniqid(rand(), true));
        return $mdstring;
    }
}
