<?php

require_once(APPPATH.'data/notifications.php');
require_once(APPPATH.'config/creds.php');

class SendGridHelper {

  const FROM = 'community@4pfoods.com';

  private $_view;
  private $_file;
  private $_subject;
  private $_html;
  private $_body;
  private $_emailObj;

  function __construct($to, $emailView) {

    global $_notifications;

    if (SKIP_EMAILS)
      return $this;

    $viewDir = APPPATH.'views/notifications/';

    $viewFile = $viewDir.$emailView.'.php';
    if (!file_exists($viewFile))
      throw new Exception('Invalid email view.');

    $this->_view = $emailView;
    $this->_file = $viewFile;
    $this->_to = $to;

    $headerFile = $viewDir.'_header.php';
    $footerFile = $viewDir.'_footer.php';

    $headerHTML = file_get_contents($headerFile);
    $footerHTML = file_get_contents($footerFile);
    $this->_body = $bodyHTML = file_get_contents($viewFile);

    $this->_html = $headerHTML."\n".$bodyHTML."\n".$footerHTML;
    $this->_subject = $_notifications[$emailView];
  }

  function merge($dataSet) {

    foreach ($dataSet as $key => $value) {
      $this->_subject = str_replace('['.$key.']', $value, $this->_subject);
      $this->_html = str_replace('['.$key.']', $value, $this->_html);
      $this->_body = str_replace('['.$key.']', $value, $this->_body);
    }

  }

  function send($withBcc = NULL) {

    if (SKIP_EMAILS)
      return true;

    $sendgrid = new SendGrid(SENDGRID_USERNAME, SENDGRID_PASSWD);
    $mail = new SendGrid\Email();
    $mail->addTo($this->_to)
      ->setFrom(self::FROM)
      ->setSubject($this->_subject)
      ->setHtml($this->_html);

    if ($withBcc)
      $mail->addTo($withBcc);

    $sendgrid->send($mail);

  }

  function toArray() {

    return array(
      'To'      => $this->_to,
      'From'    => self::FROM,
      'Subject' => $this->_subject,
      'Body'    => str_replace(array("\t", "\n"), '', strip_tags($this->_body))
    );

  }
}