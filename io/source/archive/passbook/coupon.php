<?php


  /**
   * Io_Archive_Passbook_Coupon
   *
   * @package net.evalcode.components
   * @subpackage io.archive.passbook
   *
   * @author evalcode.net
   */
  class Io_Archive_Passbook_Coupon extends Io_Archive_Passbook_Generic
  {
    // PREDEFINED PROPERTIES
    const STYLE='coupon';
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function getStyle()
    {
      return self::STYLE;
    }
    //--------------------------------------------------------------------------
  }
?>
