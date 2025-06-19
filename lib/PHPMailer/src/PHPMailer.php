<?php
namespace PHPMailer\PHPMailer;

class PHPMailer {
    public $isSMTP = false;
    public $Host;
    public $SMTPAuth = false;
    public $Username;
    public $Password;
    public $SMTPSecure = 'tls';
    public $Port = 587;
    public $From;
    public $FromName;
    public $Subject = '';
    public $Body = '';
    public $isHTML = false;
    private $addresses = [];

    public function isSMTP() { $this->isSMTP = true; }
    public function setFrom($address, $name = '') { $this->From = $address; $this->FromName = $name; }
    public function addAddress($address) { $this->addresses[] = $address; }

    public function send() {
        $to = implode(',', $this->addresses);
        $headers = '';
        if ($this->From) {
            $fromName = $this->FromName ?: $this->From;
            $headers .= "From: {$fromName} <{$this->From}>\r\n";
        }
        if ($this->isHTML) {
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        }
        return mail($to, $this->Subject, $this->Body, $headers);
    }
}
