<?php

namespace odissey;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{

    public $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer();
        $this->mailer->isSMTP();
        $this->mailer->Debugoutput = 'html';
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Host = Configuration::MAIL_SMTP_HOST;
        $this->mailer->Port = Configuration::MAIL_SMTP_PORT;
        $this->mailer->SMTPAuth = Configuration::MAIL_SMTP_AUTH;
        $this->mailer->Username = Configuration::MAIL_SMTP_USER;
        $this->mailer->Password = Configuration::MAIL_SMTP_PASS;
        $this->mailer->SMTPSecure = Configuration::MAIL_SMTP_SECURE;
        $this->mailer->setFrom(Configuration::MAIL_FROM, "Одиссей");
        $this->mailer->isHTML(true);
    }

    public function setFrom($address, $name = '', $auto = true) {
        return $this->mailer->setFrom($address, $name, $auto);
    }

    public function addAddress($adress, $name = '') {
        return $this->mailer->addAddress($adress, $name);
    }

    public function addBCC($adress, $name = '') {
        return $this->mailer->addBCC($adress, $name);
    }

    public function setMessage($message) {
        $this->mailer->Body = $message;
    }

    public function setSubject($subject) {
        $this->mailer->Subject = $subject;
    }

    public function send() {
        return $this->mailer->send();
    }

    public function compose($subject, $template, $payload, $to = Configuration::MAIL_NOTIFY) {

        $tpl = new \Smarty();
        $tpl->template_dir = Configuration::TEMPLATE_DIR;
        $tpl->compile_dir = Configuration::TEMPLATE_CACHE;
        $tpl->cache_dir = Configuration::TEMPLATE_CACHE;
        $tpl->compile_id = 'email';
        $tpl->caching = 0;
        $tpl->debugging = Configuration::DEBUG;

        $tpl->plugins_dir[] = implode(DIRECTORY_SEPARATOR, ['app', 'addons', 'smarty-plugins']);

        foreach ($payload as $key => $data) {
            $tpl->assign($key, $data);
        }
        $domain = Helpers::getDomainURL();

        $tpl->assign('emailTemplate', ['domain' => $domain]);
        $message = $tpl->fetch($template);

        $tpl->assign('emailTemplate', ['title' => $subject, 'domain' => $domain, 'content' => $message]);
        $template = $tpl->fetch('email/email.tpl');

        $this->setSubject($subject);
        $this->setMessage($template);

        foreach ($to as $recipient) {
            $this->addAddress($recipient);
        }

        return $this->send();
    }
}
