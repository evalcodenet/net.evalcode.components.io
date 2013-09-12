<?php


namespace Components;


  /**
   * Io_Archive_Passbook_Storecard
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage archive.passbook
   *
   * @author evalcode.net
   */
  class Io_Archive_Passbook_Storecard extends Io_Archive_Passbook_Generic
  {
    // PREDEFINED PROPERTIES
    const STYLE='storeCard';
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
