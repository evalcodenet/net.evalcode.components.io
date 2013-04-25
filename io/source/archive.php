<?php


namespace Components;


  /**
   * Io_Archive
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  abstract class Io_Archive extends Io_File implements \IteratorAggregate
  {
    // STATIC ACCESSORS
    /**
     * @param string $filepath_
     *
     * @return Io_Archive_Zip
     */
    public static function createZip($filepath_)
    {
      return new Io_Archive_Zip($filepath_, Io_File::CREATE|Io_File::TRUNCATE);
    }

    /**
     * @param string $filepath_
     *
     * @return Io_Archive_Zip
     */
    public static function openZip($filepath_, $accessModeMask_=Io_File::READ)
    {
      return new Io_Archive_Zip($filepath_, $accessModeMask_);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * Adds given file to this archive.
     *
     * @param Io_File $file_
     * @param string $withName_
     *
     * @return Io_Archive
     */
    public abstract function add(Io_File $file_, $withName_=null);

    /**
     * Adds contents of given directory to this archive.
     *
     * @param Io_Path $directory_
     *
     * @return Io_Archive
     */
    public abstract function addDirectory(Io_Path $directory_);

    /**
     * Extracts contents of this archive into given directory.
     *
     * @param Io_Path $directory_
     *
     * @return Io_Archive
     */
    public abstract function extract(Io_Path $directory_);


    /**
     * (non-PHPdoc)
     * @see IteratorAggregate::getIterator()
     *
     * @return Io_Archive_Iterator
     */
    public function getIterator()
    {
      return new Io_Archive_Iterator($this);
    }
    //--------------------------------------------------------------------------
  }
?>
