<?php


  /**
   * Io_Scriptlet_Test
   *
   * @package net.evalcode.components
   * @subpackage io.scriptlet
   *
   * @author evalcode.net
   */
  class Io_Scriptlet_Test extends Scriptlet
  {
    // OVERRIDES/IMPLEMENTS
    public function post()
    {
      define('PASSBOOK_CERTIFICATE', Environment::pathComponent('io').'/certificate/pass.net.evalcode.eval.moc.pem');
      define('PASSBOOK_CERTIFICATE_KEY', Environment::pathComponent('io').'/certificate/pass.net.evalcode.eval.moc.key');
      define('PASSBOOK_CERTIFICATE_KEY_PASSPHRASE', 'com.mo-commerce.eval.passbook');

      define('PASSBOOK_CERTIFICATE_APPLE_INTERMEDIATE', Environment::pathComponent('io').'/certificate/AppleWWDRCA.pem');

      define('PASSBOOK_ORGANIZATION_NAME', 'net.evalcode');

      define('PASSBOOK_TEAM_IDENTIFIER', 'KE2H6SKV9C');
      define('PASSBOOK_PASS_TYPE_IDENTIFIER', 'pass.net.evalcode.eval.moc');
      define('PASSBOOK_PASS_TYPE_DESCRIPTION', 'moc evaluation');

      define('PASSBOOK_PASS_ICON_DEFAULT', Environment::pathResource().'/passbook/icon.png');
      define('PASSBOOK_PASS_LOGO_DEFAULT', Environment::pathResource().'/passbook/logo.png');

      $pass=new Io_Archive_Passbook_Coupon(
        Io::tmpFileName(),
        PASSBOOK_PASS_TYPE_IDENTIFIER,
        PASSBOOK_PASS_TYPE_DESCRIPTION,
        PASSBOOK_ORGANIZATION_NAME,
        PASSBOOK_TEAM_IDENTIFIER
      );

      $pass->open();

      $code=Io_Archive_Passbook_Coupon::generateSerialNumber();
      $pass->setBarcode($code, $code);

      $pass->addPrimaryField('customer', 'Kevin Whooo', 'Customer');
      $pass->addSecondaryField('merchant', 'Room 2403, 495 Jiangning Road, Jingan District, 200041 Shanghai', 'Redeem at');
      $pass->addBackField('copyright', 'Copyright (C) evalcode.net', 'Copyright');

      $pass->setIcon(Io::image(PASSBOOK_PASS_ICON_DEFAULT));
      $pass->setLogo(Io::image(PASSBOOK_PASS_LOGO_DEFAULT));
      $pass->enableStripShine();

      $pass->close();

      $pass->sign(
        PASSBOOK_CERTIFICATE_APPLE_INTERMEDIATE,
        PASSBOOK_CERTIFICATE,
        PASSBOOK_CERTIFICATE_KEY,
        PASSBOOK_CERTIFICATE_KEY_PASSPHRASE
      );

      readfile($pass);
    }

    public function get()
    {
      return $this->post();
    }
    //--------------------------------------------------------------------------
  }
?>
