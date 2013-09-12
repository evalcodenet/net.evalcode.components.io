<?php


namespace Components;


  /**
   * Io_Archive_Passbook_Coupon
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage archive.passbook
   *
   * @author evalcode.net
   */
  class Io_Archive_Passbook_Coupon extends Io_Archive_Passbook_Generic
  {
    // PREDEFINED PROPERTIES
    const STYLE='coupon';
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Io_Archive_Passbook_Generic::getStyle() \Components\Io_Archive_Passbook_Generic::getStyle()
     */
    public function getStyle()
    {
      return self::STYLE;
    }
    //--------------------------------------------------------------------------
  }
?>
