<?php namespace App\Libraries\CafeVariomeNet\Email;

/**
 * IEmail.php
 * 
 * Created: 19/09/2019
 * @author Mehdi Mehtarizadeh
 */

interface IEmail {

    public function setEmailAdapter($emailAdapter);

    public function send();
}