<?php


namespace Components;


  /**
   * Io_Archive_Passbook_Boardingpass
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage archive.passbook
   *
   * @author evalcode.net
   */
  class Io_Archive_Passbook_Boardingpass extends Io_Archive_Passbook_Generic
  {
    // PREDEFINED PROPERTIES
    const STYLE='boardingPass';

    const TYPE_FIELD_TRANSIT_TYPE='transitType';

    const TYPE_TRANSIT_AIR='PKTransitTypeAir';
    const TYPE_TRANSIT_BOAT='PKTransitTypeBoat';
    const TYPE_TRANSIT_BUS='PKTransitTypeBus';
    const TYPE_TRANSIT_GENERIC='PKTransitTypeGeneric';
    const TYPE_TRANSIT_TRAIN='PKTransitTypeTrain';
    //--------------------------------------------------------------------------


    // PROPERTIES
    public $transitType=self::TYPE_TRANSIT_GENERIC;
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Io_Archive_Passbook_Generic::getStyle() \Components\Io_Archive_Passbook_Generic::getStyle()
     */
    public function getStyle()
    {
      return self::STYLE;
    }

    /**
     * @see \Components\Io_Archive_Passbook_Generic::getFields() \Components\Io_Archive_Passbook_Generic::getFields()
     */
    public function getFields()
    {
      $fields=parent::getFields();
      $fields[self::TYPE_FIELD_TRANSIT_TYPE]=$this->transitType;

      return $fields;
    }
    //--------------------------------------------------------------------------
  }
?>
