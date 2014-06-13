<?php

class EmailService {
    /**
     * @var EmailService
     */
    public static $singleton = null;

    public static function changeSingleton(EmailService $s) {
        self::$singleton = $s;
    }

    /**
     * @var Swift_SmtpTransport
     */
    public $transport;

    /**
     * @var Swift_Mailer
     */
    public $mailer;

    public function __construct($properties = array()) {
            parent::__construct($properties);

        $this->transport = Swift_SmtpTransport::newInstance(Config::$Configs['email']['smtp']['host'], Config::$Configs['email']['smtp']['port']);

        if(Config::$Configs['email']['smtp']['user'] !== null) {
            $this->transport->setUsername(Config::$Configs['email']['smtp']['user'])
                ->setPassword(Config::$Configs['email']['smtp']['pass']);
        }

        $this->mailer = Swift_Mailer::newInstance($this->transport);
    }

    public function sendEmail($subject, $body, $to = array(), $bcc = array(), $cc = array(), $from = null, $from_name = null, $reply_to = null, $reply_to_name = null, $restartTransport = false) {
        $from = $from === null ? Config::$Configs['email']['from'] : $from;
        $from_name = $from_name === null ? Config::$Configs['email']['from_name'] : $from_name;

        $reply_to = $reply_to === null ? $from : $reply_to;
        $reply_to_name = $reply_to_name === null ? $from_name : $reply_to_name;

        $to = is_array($to) ? $to : array($to);
        $bcc = is_array($bcc) ? $bcc : array($bcc);
        $cc = is_array($cc) ? $cc : array($cc);

        if($restartTransport) {
            $this->transport->stop();
            $this->transport->start();
        }

        /**
         * @var Swift_Message $message
         */
        $message = Swift_Message::newInstance($subject, $body, 'text/html', 'utf-8')
            ->setCc($cc)->setBcc($bcc)->setReplyTo($reply_to, $reply_to_name)->setFrom($from, $from_name)->setTo($to);

        return $this->mailer->send($message);
    }
}

if(EmailService::$singleton === null) {
    EmailService::changeSingleton(new EmailService());
}