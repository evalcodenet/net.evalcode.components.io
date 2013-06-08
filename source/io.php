<?php


namespace Components;


  /**
   * Io
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  class Io
  {
    // PREDEFINED PROPERTIES
    const LINE_SEPARATOR_X="\n";
    const LINE_SEPARATOR_WINDOWS="\r\n";
    const LINE_SEPARATOR_DEFAULT=self::LINE_SEPARATOR_X;

    const PATH_SEPARATOR=PATH_SEPARATOR;
    const DIRECTORY_SEPARATOR=DIRECTORY_SEPARATOR;
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return \Components\Io_Console
     */
    public static function console()
    {
      return new Io_Console();
    }

    /**
     * @return \Components\Io_Pipe_Stdin
     */
    public static function stdin()
    {
      return new Io_Pipe_Stdin();
    }

    /**
     * @return \Components\Io_Pipe_Stdout
     */
    public static function stdout()
    {
      return new Io_Pipe_Stdout();
    }

    /**
     * @return \Components\Io_Pipe_Stderr
     */
    public static function stderr()
    {
      return new Io_Pipe_Stderr();
    }

    /**
     * @param string... $path0_
     *
     * @return \Components\Io_Path
     */
    public static function path($path0_/*, $path1_, $path2_..*/)
    {
      $args=func_get_args();

      $prepend='';
      if(Io::DIRECTORY_SEPARATOR===$args[0])
        $prepend=array_shift($args);

      return new Io_Path($prepend.implode(Io::DIRECTORY_SEPARATOR, $args));
    }

    /**
     * @param string $path_
     *
     * @return \Components\Io_Archive
     */
    public static function archive($path_)
    {
      return Io_Archive::openZip($path_);
    }

    /**
     * @param string $path_
     *
     * @return \Components\Io_Archive
     */
    public static function archiveCreate($path_)
    {
      return Io_Archive::createZip($path_);
    }

    /**
     * @param string $filepath_
     * @param integer $accessModeMask_
     *
     * @return \Components\Io_File
     */
    public static function file($filepath_, $accessModeMask_=Io_File::READ)
    {
      return new Io_File($filepath_, $accessModeMask_);
    }

    /**
     * @param string $filename_
     *
     * @return string
     */
    public static function fileExtension($filename_)
    {
      return substr($filename_, strrpos($filename_, '.')+1);
    }

    /**
     * @param string $path_
     *
     * @return \Components\Io_Filesize
     */
    public static function fileSize($path_)
    {
      if(false===($filesize=@filesize($path_)))
        throw new Io_Exception('io', sprintf('Unable to determine filesize for given path [%1$s].', $path_));

      return new Io_Filesize($filesize);
    }

    /**
     * @param integer $bytes_
     *
     * @return \Components\Io_Filesize
     */
    public static function fileSizeForBytes($bytes_)
    {
      return new Io_Filesize($bytes_);
    }

    /**
     * @param string $filename_
     *
     * @return string
     */
    public static function fileNameSanitize($filename_)
    {
      if(false===($pos=mb_strpos($filename_, '.')))
      {
        $name=$filename_;
        $extension='';
      }
      else
      {
        $name=mb_substr($filename_, 0, $pos);
        $extension=mb_strtolower(mb_substr($filename_, $pos));
      }

      $matches=array();
      preg_match_all('/[\x2B\x2E\x30-\x39\x41-x5B]+/mi', $name, $matches);

      if(isset($matches[0]) && 0<count($matches[0]))
        return preg_replace('/[_-]+/', '-', strtolower(implode('-', reset($matches)))).$extension;

      return md5($name).$extension;
    }

    /**
     * @param string $fileId_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public static function fileUpload($fileId_=null, Io_Path $destination_=null, array $permittedMimetypes_=array(), array $permittedFileExtensions_=array())
    {
      if(null===$fileId_ && is_array($_FILES) && 0<count($_FILES))
        $fileId_=key($_FILES);

      if(false===isset($_FILES[$fileId_]))
      {
        throw new Io_Exception('io', sprintf(
          'Upload for given id does not exist [file: %s].',
            $fileId_
        ));
      }

      if(isset($_FILES[$fileId_]['error']) && 0<(int)$_FILES[$fileId_]['error'])
      {
        throw new Io_Exception('io', sprintf(
          'File upload failed [file: %s, error: %s].',
            $fileId_, $_FILES[$fileId_]['error']
        ));
      }

      if(null===$destination_)
        $destination_=static::tmpPath('upload', false);

      if(false===$destination_->exists())
        $destination_->create();

      $file=$destination_->getFile(static::fileNameSanitize($_FILES[$fileId_]['name']));

      if($file->exists())
        $file->delete();

      if(false===@move_uploaded_file($_FILES[$fileId_]['tmp_name'], $file->getPathAsString()))
      {
        throw new Io_Exception('io', sprintf(
          'Failed to stash uploaded file [file: %s, destination: %s].',
            $fileId_, $file
        ));
      }

      return $file;
    }

    /**
     * @param \Components\Io_Path $destination_
     *
     * @return array|\Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public static function fileUploadAll(Io_Path $destination_=null)
    {
      $files=array();
      foreach($_FILES as $key=>$file)
        $files[$key]=static::fileUpload($key, $destination_);

      return $files;
    }

    /**
     * @param string $path_
     *
     * @return \Components\Io_Image
     */
    public static function image($path_)
    {
      return Io_Image::forPath($path_);
    }

    /**
     * @param string $path_
     * @param integer $width_
     * @param integer $height_
     *
     * @return \Components\Io_Image
     */
    public static function imageCreate($path_, $width_, $height_)
    {
      return Io_Image::create($path_, new Point($widh_, $height_));
    }

    /**
     * @param string $filename_
     *
     * @return \Components\Io_Charset
     */
    public static function charset($filename_)
    {
      return Io_Charset::forFilePath($filename_);
    }

    /**
     * @param string $filename_
     *
     * @return \Components\Io_Mimetype
     */
    public static function mimeType($filename_)
    {
      return Io_Mimetype::forFilePath($filename_);
    }

    /**
     * @param string $path_
     * @param string $umask_
     *
     * @return boolean
     */
    public static function directoryCreate($path_, $umask_=0775)
    {
      $create=array();
      $segments=explode(DIRECTORY_SEPARATOR, $path_);
      while(0<count($segments))
      {
        if(is_dir(implode(DIRECTORY_SEPARATOR, $segments)))
          break;

        $create[]=array_pop($segments);
      }

      while(0<count($create))
      {
        $segments[]=array_pop($create);

        $path=implode(DIRECTORY_SEPARATOR, $segments);

        if(false===@mkdir($path, $umask_, true))
          return false;

        @chmod($path, $umask_);
      }

      return true;
    }

    /**
     * @param string $pathSource_
     * @param string $pathTarget_
     *
     * @throws \Components\Io_Exception
     */
    public static function directoryCopy($pathSource_, $pathTarget_)
    {
      if(false===@is_dir($pathSource_))
        throw new Io_Exception('io', 'Source path must be an accessible directory.');

      if(false===@is_dir($pathTarget_))
      {
        if(@file_exists($pathTarget_))
          throw new Io_Exception('io', 'Target path must not be an existing file.');

        static::directoryCreate($pathTarget_);
      }

      if(false===@is_dir($pathTarget_))
        throw new Io_Exception('io', 'Failed to copy directory. Unable to create target directory.');

      $iterator=new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator(
          $pathSource_, \RecursiveDirectoryIterator::SKIP_DOTS|\RecursiveDirectoryIterator::KEY_AS_PATHNAME
        ),
        \RecursiveIteratorIterator::CHILD_FIRST
      );

      foreach($iterator as $entryPath=>$entryInfo)
      {
        if($entryInfo->isFile())
        {
          $subPath=String::replace($entryPath, "$pathSource_/", '');
          if(false===@is_dir("$pathTarget_/".dirname($subPath)))
            static::directoryCreate("$pathTarget_/".dirname($subPath));

          @copy($entryPath, "$pathTarget_/$subPath");
        }
      }
    }

    /**
     * @param string $path_
     */
    public static function directoryClear($path_)
    {
      $iterator=new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator(
          $path_, \RecursiveDirectoryIterator::SKIP_DOTS|\RecursiveDirectoryIterator::KEY_AS_PATHNAME
        ),
        \RecursiveIteratorIterator::CHILD_FIRST,
        \RecursiveIteratorIterator::CATCH_GET_CHILD
      );

      foreach($iterator as $entryPath=>$entryInfo)
      {
        if($entryInfo->isFile())
          @unlink($entryPath);
        else
          @rmdir($entryPath);
      }
    }

    /**
     * @param string $path_
     * @param boolean $recursive_
     *
     * @return boolean
     */
    public static function directoryDelete($path_, $recursive_=false)
    {
      if(@is_dir($path_) && @is_writeable($path_))
      {
        if($recursive_)
          static::directoryClear($path_);

        return @rmdir($path_);
      }

      return @unlink($path_);
    }

    /**
     * @param string $prefix_
     * @param string $path_
     * @param boolean $global_
     * @param integer $accessModeMask_
     *
     * @return \Components\Io_File
     */
    public static function tmpFile($prefix_=null, $path_=null, $global_=true, $accessModeMask_=Io_File::WRITE)
    {
      return new Io_File(static::tmpFileName($prefix_, $path_, $global_), $accessModeMask_);
    }

    /**
     * @param string $prefix_
     * @param string $path_
     * @param boolean $global_
     *
     * @return string
     */
    public static function tmpFileName($prefix_=null, $path_=null, $global_=true)
    {
      if(null===$path_)
      {
        $path_=static::tmpPathName(null, $global_);

        static::directoryCreate($path_);
      }

      return tempnam($path_, $prefix_);
    }

    /**
     * @param string $directory_
     * @param boolean $global_
     *
     * @return \Components\Io_Path
     */
    public static function tmpPath($directory_=null, $global_=true)
    {
      return new Io_Path(static::tmpPathName($directory_, $global_));
    }

    /**
     * @param string $directory_
     * @param boolean $global_
     *
     * @return string
     */
    public static function tmpPathName($directory_=null, $global_=true)
    {
      if(null===self::$m_tmpPathNameRoot)
        static::tmpPathNameRoot();

      if(!$global_)
      {
        if(!$sessionId=session_id())
        {
          if(Environment::isCli())
            $sessionId=md5(get_current_user());
          else
            $sessionId=md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].date('Ymd'));
        }

        if(null===$directory_)
        {
          return self::$m_tmpPathNameRoot.
            DIRECTORY_SEPARATOR.
            'session'.
            DIRECTORY_SEPARATOR.
            $sessionId;
        }

        return self::$m_tmpPathNameRoot.
          DIRECTORY_SEPARATOR.
          'session'.
          DIRECTORY_SEPARATOR.
          $sessionId.
          DIRECTORY_SEPARATOR.
          $directory_;
      }

      if(null===$directory_)
        return self::$m_tmpPathNameRoot.DIRECTORY_SEPARATOR.'global';

      return self::$m_tmpPathNameRoot.DIRECTORY_SEPARATOR.'global'.DIRECTORY_SEPARATOR.$directory_;
    }

    /**
     * @return string
     */
    public static function tmpPathNameRoot()
    {
      if(null===self::$m_tmpPathNameRoot)
      {
        self::$m_tmpPathNameRoot=sys_get_temp_dir().
          DIRECTORY_SEPARATOR.
          Runtime::getInstanceNamespace().
          DIRECTORY_SEPARATOR.
          date('YW');
      }

      return self::$m_tmpPathNameRoot;
    }

    /**
     * @return string
     */
    public static function tmpPathNameSystem()
    {
      return sys_get_temp_dir();
    }

    /**
     * @return \Components\Io_Path
     */
    public static function tmpPathRoot()
    {
      return new Io_File(static::tmpPathNameRoot());
    }

    /**
     * @return \Components\Io_Path
     */
    public static function tmpPathSystem()
    {
      return new Io_Path(static::tmpPathNameSystem());
    }

    /**
     * @return string
     */
    public static function systemName()
    {
      return PHP_OS;
    }

    /**
     * @return boolean
     */
    public static function systemIsWindows()
    {
      return 'win'===strtolower(substr(PHP_OS, 0, 3));
    }

    /**
     * @return boolean
     */
    public static function systemIsX()
    {
      // TODO Verify name for OSX
      return 'linux'===strtolower(PHP_OS) || 'unix'===strtolower(PHP_OS)  || 'macosx'===strtolower(PHP_OS);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string
     */
    private static $m_tmpPathNameRoot;
    //--------------------------------------------------------------------------
  }
?>
