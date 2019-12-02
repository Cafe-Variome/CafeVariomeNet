<?php namespace App\Libraries\Net;

/**
 * NetworkAdapter.php
 * Created: 15/10/2019
 * 
 * @author Mehdi Mehtarizadeh
 * 
 */

class NetworkAdapter  implements INetworkAdapter
{
    protected $adapterInstance;

    public function Send()
    {
        $this->$adapterInstance->Send();
    }
}
