<?php namespace App\Libraries\Net;

/**
 * APIResponse.php
 * Created: 19/11/2019
 * @author Mehdi Mehtarizadeh
 * 
 * This entity class forms the basic data structure for responses in API requests.
 * 
 */

class APIResponse
{
    private $status;
    private $message;

    public function __construct(int $status = null, string $message = null) {
        $this->status = $status;
        $this->message = $message;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): int
    {
        $this->status = $status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): int
    {
        $this->message = $message;
    }
}
