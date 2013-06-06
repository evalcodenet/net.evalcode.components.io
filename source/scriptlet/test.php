<?php


namespace Components;


  /**
   * Io_Scriptlet_Test
   *
   * @package net.evalcode.components
   * @subpackage io.scriptlet
   *
   * @author evalcode.net
   */
  class Io_Scriptlet_Test extends Http_Scriptlet
  {
    // ACCESSORS/MUTATORS
    public function get()
    {
      /* @var $csv Io_File_Csv */
      $csv=Io_File::forMimetype('/tmp/test.csv', Io_File::WRITE);
      $csv->setColumns(array('material_number', 'material_group', 'ean', 'name_en_us'));
      $csv->mapColumn('material_number', 'sku');
      $csv->open();


      print_r($csv->readAll());
    }
    //--------------------------------------------------------------------------
  }
?>

