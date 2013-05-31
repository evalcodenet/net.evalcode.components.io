<?php


namespace Components;


  /**
   * Io_Archive_Passbook_Storecard
   *
   * @package net.evalcode.components
   * @subpackage io.archive.passbook
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
     * (non-PHPdoc)
     * @see Components\Io_Archive_Passbook_Generic::getStyle()
     */
    public function getStyle()
    {
      return self::STYLE;
    }
    //--------------------------------------------------------------------------
  }
?>