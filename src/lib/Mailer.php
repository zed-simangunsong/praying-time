<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\Lib;


use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    protected $recipients = [];

    protected $subject;

    protected $usingHtml = true;

    /**
     * Create a new Mailer object.
     *
     * @return static
     */
    public static function instance()
    {
        return new static();
    }

    /**
     * Add email recipient(s).
     *
     * @param $to
     * @param string $name
     * @return $this
     */
    public function setTo($to, $name = '')
    {
        $this->recipients[] = [$to, $name];

        return $this;
    }

    /**
     * Set the email subject.
     *
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Did the email allow HTML or not?
     *
     * @param bool $state
     * @return $this
     */
    public function setUsingHtml($state = true)
    {
        $this->usingHtml = $state;

        return $this;
    }

    /**
     * Send the email.
     *
     * @param $view
     * @param array $context
     * @return array
     */
    public function send($view, array $context = [])
    {
        $mailer = new PHPMailer(true);

        try {
            $this->setServerConfiguration($mailer);

            // Sender.
            $this->setSender($mailer);

            // Recipient.
            $this->setRecipient($mailer);

            // Subject & body.
            $mailer->isHTML($this->usingHtml);
            $mailer->Subject = $this->subject;
            $mailer->Body = view($view, $context);

            // Send the email now.
            $mailer->send();

            $status = [
                'status' => 'OK',
                'message' => 'Success. Email send',
            ];
        } catch (\Exception $e) {
            $status = [
                'status' => 'KO',
                'message' => $mailer->ErrorInfo,
            ];
        }

        return $status;
    }

    /**
     * SMTP server connection info.
     *
     * @param PHPMailer $mailer
     */
    protected function setServerConfiguration(PHPMailer $mailer)
    {
        $mailer->isSMTP();
        $mailer->Host = env('SMTP_HOST', 'localhost');
        $mailer->Port = env('SMTP_PORT');

        if (env('SMTP_USERNAME') && env('SMTP_PASSWORD')) {
            $mailer->SMTPAuth = true;
            $mailer->Username = env('SMTP_USERNAME');
            $mailer->Password = env('SMTP_PASSWORD');
        }
    }

    /**
     * @param PHPMailer $mailer
     * @throws \PHPMailer\PHPMailer\Exception
     */
    protected function setSender(PHPMailer $mailer)
    {
        $mailer->setFrom(
            env('MAIL_FROM_ADDRESS', 'auto-email@example.com'),
            env('MAIL_FROM_NAME', 'Automail'));
    }

    /**
     * @param PHPMailer $mailer
     * @throws \PHPMailer\PHPMailer\Exception
     */
    protected function setRecipient(PHPMailer $mailer)
    {
        foreach ($this->recipients as $recipient) {
            [$email, $name] = $recipient;

            $mailer->addAddress($email, $name);
        }
    }
}