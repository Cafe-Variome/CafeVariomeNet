<?php namespace App\Libraries\CafeVariomeNet\Email;

/**
 * EmailFactory.php
 * 
 * Created: 19/09/2019
 * @author Mehdi Mehtarizadeh
 */


class EmailFactory implements IEmailAbstractFactory
{
    public static function NotifyAdmin($adapter):NotifyAdmin
    {
        return new NotifyAdmin($adapter);
    }
}
