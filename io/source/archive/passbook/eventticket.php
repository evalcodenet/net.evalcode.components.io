<?php


  /**
   * Io_Archive_Passbook_Eventticket
   *
   * @package net.evalcode.components
   * @subpackage io.archive.passbook
   *
   * @author evalcode.net
   */
  class Io_Archive_Passbook_Eventticket extends Io_Archive_Passbook_Generic
  {
    // PREDEFINED PROPERTIES
    const STYLE='eventTicket';
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function getStyle()
    {
      return self::STYLE;
    }
    //--------------------------------------------------------------------------
  }
?>