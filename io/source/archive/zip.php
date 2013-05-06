<?php


namespace Components;


  /**
   * Io_Archive_Zip
   *
   * @package net.evalcode.components
   * @subpackage io.archive
   *
   * @author evalcode.net
   */
  class Io_Archive_Zip extends Io_Archive
  {
    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components.Io_File::open()
     *
     * @return \Components\Io_Archive_Zip
     */
    public function open()
    {
      $exists=@is_file($this->m_pathAsString);
      $status=null;

      if($exists)
      {
        if($this->m_accessMask&self::CREATE)
        {
          if($this->m_accessMask&self::TRUNCATE)
            $status=$this->archive()->open($this->m_pathAsString, \ZipArchive::OVERWRITE);
          else
            throw new Io_Exception('io/archive/zip', 'Can not create already existing archive.');
        }
        else
        {
          $status=$this->archive()->open($this->m_pathAsString);
        }
      }
      else
      {
        if($this->m_accessMask&self::CREATE)
          $status=$this->archive()->open($this->m_pathAsString, \ZipArchive::CREATE);
        else
          throw new Io_Exception('io/archive/zip', 'Can not open not existing archive.');
      }

      if($exists)
        $this->m_length=@filesize($this->m_pathAsString);
      else
        $this->m_length=0;

      $this->m_position=0;

      if(true===$status)
        $this->m_open=true;

      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Io_File::close()
     *
     * @return \Components\Io_Archive_Zip
     */
    public function close()
    {
      $this->archive()->close();
      $this->m_open=false;

      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Io_Archive::add()
     *
     * @return \Components\Io_Archive_Zip
     */
    public function add(Io_File $file_, $withName_=null)
    {
      if(false===$this->m_open)
        throw new Io_Exception('io/archive/zip', 'Archive must be open to add files.');

      $this->archive()->addFile((string)$file_, $withName_);

      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Io_Archive::addDirectory()
     *
     * @return \Components\Io_Archive_Zip
     */
    public function addDirectory(Io_Path $directory_)
    {
      if(false===$this->m_open)
        throw new Io_Exception('io/archive/zip', 'Archive must be open to add files.');

      if(false===$directory_->isDirectory())
        throw new Io_Exception('io/archive/zip', 'Given path must point to a readable directory.');

      /* @var $path \Components\Io_Path */
      foreach($directory_ as $path)
      {
        if($path->isDirectory())
        {
          $this->addDirectory($path);
        }
        else if($path->isFile() && $path->isReadable())
        {
          $file=$path->asFile();
          if($this->m_pathAsString!==$file->getPathAsString())
            $this->add($file, (string)$this->getRelativePath($file));
        }
      }
    }

    /**
     * (non-PHPdoc)
     * @see Components.Io_Archive::extract()
     *
     * @return \Components\Io_Archive_Zip
     */
    public function extract(Io_Path $directory_)
    {
      if(false===$directory_->isDirectory() || false===$directory_->isWritable())
        throw new Io_Exception('io/archive/zip', 'Destination must be a writable directory.');

      $this->archive()->extractTo((string)$directory_);

      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Io_File::delete()
     *
     * @return \Components\Io_Archive_Zip
     */
    public function delete()
    {
      parent::delete();

      return $this;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \ZipArchive
     */
    private $m_archive;
    //-----


    /**
     * @return \ZipArchive
     */
    private function archive()
    {
      if(null===$this->m_archive)
        $this->m_archive=new \ZipArchive($this->m_pathAsString);

      return $this->m_archive;
    }

    public function __destruct()
    {
      if(null!==$this->m_archive)
        @$this->m_archive->close();
    }
    //--------------------------------------------------------------------------
  }
?>
