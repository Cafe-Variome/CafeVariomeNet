<?php namespace App\Libraries\CafeVariomeNet\Email;

/**
 * IEmailAbstractFactory.php
 * 
 * Created: 19/09/2019
 * @author Mehdi Mehtarizadeh
 */


interface IEmailAbstractFactory{

    public static function NotifyAdmin($adapter): NotifyAdmin;

}