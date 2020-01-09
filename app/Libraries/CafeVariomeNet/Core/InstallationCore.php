<?php namespace App\Libraries\CafeVariomeNet\Core;

/**
 * NetworkCore.php
 * 
 * Created: 09/01/2020
 * 
 * @author Mehdi Mehtarizadeh
 * 
 */

use App\Models\Installation;
use App\Models\Network;


class InstallationCore
{
    private $installationModelInstance;

    public function __construct() {
        $this->installationModelInstance = new Installation();
    }

    public function getInstallationsByNetworkKey(int $network_key): array
    {
        $installations = $this->installationModelInstance->getInstallationsByNetworkKey($network_key);

        return $installations;
    }
}
