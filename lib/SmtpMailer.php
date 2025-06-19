<?php
class SmtpMailer {
    public $Host;
    public $SMTPAuth = false;
    public $Username;
    public $Password;
    public $SMTPSecure = 'tls';
    public $Port = 465;
    public $From;
    public $FromName;

    public function send($to, $subject, $body) {
        $host = $this->Host;
        $port = $this->Port;
        $fp = fsockopen($host, $port, $errno, $errstr, 30);
        if (!$fp) {
            return true;
        }
        $read = function() use ($fp) { return fgets($fp, 512); };
        $write = function($data) use ($fp) { fwrite($fp, $data."\r\n"); };
        $read();
        $write("EHLO " . $host);
        $read();
        if ($this->SMTPSecure === 'tls') {
            $write('STARTTLS');
            $read();
            stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $write("EHLO " . $host);
            $read();
        }
        if ($this->SMTPAuth) {
            $write('AUTH LOGIN');
            $read();
            $write(base64_encode($this->Username));
            $read();
            $write(base64_encode($this->Password));
            $read();
        }
        $from = $this->From ?: $this->Username;
        $write("MAIL FROM:<$from>");
        $read();
        $write("RCPT TO:<$to>");
        $read();
        $write('DATA');
        $read();
        $headers = "From: {$this->FromName} <{$from}>\r\n";
        $headers .= "To: $to\r\n";
        $headers .= "Subject: $subject\r\n";
        $write($headers."\r\n".$body."\r\n.");
        $read();
        $write('QUIT');
        fclose($fp);
        return true;
    }
}
