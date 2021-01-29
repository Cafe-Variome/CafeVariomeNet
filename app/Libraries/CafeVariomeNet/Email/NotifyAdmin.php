<?php namespace App\Libraries\CafeVariomeNet\Email;

/**
 * CredentialsEmail.php
 * 
 * Created: 19/09/2019
 * @author Mehdi Mehtarizadeh
 */

class NotifyAdmin extends CafeVariomeEmail
{

    public function __construct($adapter){
        parent::__construct($adapter);
        $this->message = new EmailMessage();
    }

    public function notifyAdmin(string $user_email, string $admin_email, string $first_name, string $last_name, string $installation_key){
        $this->message->setFromAddress("noreply@cafevariome.org");
        $this->message->setSenderName("Cafe Variome Email Notification Centre");
        $subject = "New User for Keycloak";

        $body = "New user requested to be added to Keycloak by: " . $admin_email . " from installation: " .$installation_key . " " . PHP_EOL;
        $body .= "Email: " . $user_email . PHP_EOL;
        $body .= "First Name: " . $first_name . PHP_EOL;
        $body .= "Last Name: " . $last_name . PHP_EOL;
        // $local_admin_emails =
        // Generate emails who to send to from a secure location?
        $this->message->compose($local_admin_emails, $subject, $body);
    }

    public function send()
    {
        parent::send();
    }
}
