<?php


namespace Components;


  /**
   * Io_Archive
   *
   * @api
   * @package net.evalcode.components.io
   *
   * @author evalcode.net
   */
  abstract class Io_Archive extends Io_File implements \IteratorAggregate
  {
    // STATIC ACCESSORS
    /**
     * @param string $filepath_
     *
     * @return \Components\Io_Archive_Zip
     */
    public static function createZip($filepath_)
    {
      return new Io_Archive_Zip($filepath_, Io_File::CREATE|Io_File::TRUNCATE);
    }

    /**
     * @param string $filepath_
     *
     * @return \Components\Io_Archive_Zip
     */
    public static function openZip($filepath_, $accessModeMask_=Io_File::READ)
    {
      return new Io_Archive_Zip($filepath_, $accessModeMask_);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * Adds given file to this archive.
     *
     * @param \Components\Io_File $file_
     * @param string $withName_
     *
     * @return \Components\Io_Archive
     */
    public abstract function add(Io_File $file_, $withName_=null);

    /**
     * Adds contents of given directory to this archive.
     *
     * @param \Components\Io_Path $directory_
     *
     * @return \Components\Io_Archive
     */
    public abstract function addDirectory(Io_Path $directory_);

    /**
     * Extracts contents of this archive into given directory.
     *
     * @param \Components\Io_Path $directory_
     *
     * @return \Components\Io_Archive
     */
    public abstract function extract(Io_Path $directory_);

    /**
     * @see \IteratorAggregate::getIterator() \IteratorAggregate::getIterator()
     *
     * @return \Components\Io_Archive_Iterator
     */
    public function getIterator()
    {
      return new Io_Archive_Iterator($this);
    }
    //--------------------------------------------------------------------------
  }
?>
