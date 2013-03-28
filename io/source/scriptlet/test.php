<?php


  // INCLUDES
  Environment::includeConfig('passbook.php');


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
      $pass=new Io_Archive_Passbook_Coupon(
        '/tmp/coupon',
        PASSBOOK_PASS_TYPE_IDENTIFIER,
        PASSBOOK_PASS_TYPE_DESCRIPTION,
        PASSBOOK_ORGANIZATION_NAME,
        PASSBOOK_TEAM_IDENTIFIER
      );

      $pass->open();
      $pass->close();
    }

    public function get()
    {
      return $this->post();
    }
    //--------------------------------------------------------------------------
  }
?>
