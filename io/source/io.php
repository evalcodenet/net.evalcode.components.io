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
     * @return Io_Console
     */
    public static function console()
    {
      if(null===self::$m_console)
      {
        self::$m_console=new Io_Console();
        self::$m_console->attach(new Io_Pipe_Stdin(), new Io_Pipe_Stdout(), new Io_Pipe_Stderr());
      }

      return self::$m_console;
    }

    /**
    * @param string $filepath_
    *
    * @return Io_File
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
     * @param string $filepath_
     *
     * @return Io_MimeType
     */
    public static function fileMimeType($filepath_)
    {
      return Io_MimeType::forFilePath($filepath_);
    }

    /**
     * @param int $bytes_
     *
     * @return Io_Filesize
     */
    public static function fileSize($path_)
    {
      if(false===($filesize=@filesize($path_)))
        throw new Io_Exception('io/file', sprintf('Unable to determine filesize for given path [%1$s].', $path_));

      return new Io_Filesize($filesize);
    }

    /**
     * @param int $bytes_
     *
     * @return Io_Filesize
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
    public static function sanitizeFileName($filename_)
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
     * @param string $path_
     *
     * @return Io_Image
     */
    public static function image($path_)
    {
      return Io_Image::forPath($path_);
    }

    /**
     * @param string $path_
     * @param int $width_
     * @param int $height_
     *
     * @return Io_Image
     */
    public static function createImage($path_, $width_, $height_)
    {
      return Io_Image::create($path_, new Point($widh_, $height_));
    }

    /**
     * @param string $name_
     *
     * @return Io_MimeType
     */
    public static function mimeType($filename_)
    {
      return Io_MimeType::forFilePath($filename_);
    }

    /**
     * @param string $filename_
     *
     * @return Io_Charset
     */
    public static function charset($filename_)
    {
      return Io_Charset::forFilePath($filename_);
    }

    /**
     * @param string $path_
     *
     * @return Io_Path
     */
    public static function path($path_)
    {
      return new Io_Path($path_);
    }

    /**
     * @param string $prefix_
     * @param string $path_
     *
     * @return Io_File
     */
    public static function tmpFile($prefix_=null, $path_=null, $global_=true, $accessModeMask_=Io_File::WRITE)
    {
      return self::file(self::tmpFileName($prefix_, $path_, $global_), $accessModeMask_);
    }

    /**
     * @param string $prefix_
     * @param string $path_
     *
     * @return string
     */
    public static function tmpFileName($prefix_=null, $path_=null, $global_=true)
    {
      if(null===$path_)
      {
        $path_=self::tmpPathName(null, $global_);
        self::createDirectory($path_);
      }

      return tempnam($path_, $prefix_);
    }

    /**
     * @return Io_Path
     */
    public static function tmpPath($directory_=null, $global_=true)
    {
      return self::path(self::tmpPathName($directory_, $global_));
    }

    /**
     * @return string
     */
    public static function tmpPathName($directory_=null, $global_=true)
    {
      if(!$global_)
      {
        if(!$sessionId=session_id())
        {
          if(Environment::isCli())
            $sessionId=md5(get_current_user());
          else
            $sessionId=md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
        }

        if(null===$directory_)
        {
          return sys_get_temp_dir().
            DIRECTORY_SEPARATOR.
            Runtime::getInstanceNamespace().
            DIRECTORY_SEPARATOR.
            date('YW', time()).
            DIRECTORY_SEPARATOR.
            'session'.
            DIRECTORY_SEPARATOR.
            $sessionId;
        }

        return sys_get_temp_dir().
          DIRECTORY_SEPARATOR.
          Runtime::getInstanceNamespace().
          DIRECTORY_SEPARATOR.
          date('YW', time()).
          DIRECTORY_SEPARATOR.
          'session'.
          DIRECTORY_SEPARATOR.
          $sessionId.
          DIRECTORY_SEPARATOR.
          $directory_;
      }

      if(null===$directory_)
      {
        return sys_get_temp_dir().
          DIRECTORY_SEPARATOR.
          Runtime::getInstanceNamespace().
          DIRECTORY_SEPARATOR.
          date('YW', time()).
          DIRECTORY_SEPARATOR.
          'global';
      }

      return sys_get_temp_dir().
        DIRECTORY_SEPARATOR.
        Runtime::getInstanceNamespace().
        DIRECTORY_SEPARATOR.
        date('YW', time()).
        DIRECTORY_SEPARATOR.
        'global'.
        DIRECTORY_SEPARATOR.
        $directory_;
    }

    public static function createDirectory($path_, $umask_=0775)
    {
      $create=array();
      $segments=explode(DIRECTORY_SEPARATOR, $path_);
      while(0<count($segments))
      {
        if(@is_dir(implode(DIRECTORY_SEPARATOR, $segments)))
          break;

        $create[]=array_pop($segments);
      }

      while(0<count($create))
      {
        $segments[]=array_pop($create);

        $path=implode(DIRECTORY_SEPARATOR, $segments);

        @mkdir($path, $umask_, true);
        @chmod($path, $umask_);
      }

      return true;
    }

    public static function copyDirectory($pathSource_, $pathTarget_)
    {
      if(false===@is_dir($pathSource_))
        throw new Io_Exception('io', 'Source path must be an accessible directory.');

      if(false===@is_dir($pathTarget_))
      {
        if(@file_exists($pathTarget_))
          throw new Io_Exception('io', 'Target path must not be an existing file.');

        self::createDirectory($pathTarget_);
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
            self::createDirectory("$pathTarget_/".dirname($subPath));

          @copy($entryPath, "$pathTarget_/$subPath");
        }
      }
    }

    public static function clearPath($path_)
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

    public static function deletePath($path_, $recursive_=false)
    {
      if(@is_dir($path_) && @is_writeable($path_))
      {
        if($recursive_)
          self::clearPath($path_);

        return @rmdir($path_);
      }

      return @unlink($path_);
    }

    /**
     * @param string $path_
     *
     * @return Io_Archive
     */
    public static function openArchive($path_)
    {
      return Io_Archive::openZip($path_);
    }

    /**
     * @param string $path_
     *
     * @return Io_Archive
     */
    public static function createArchive($path_)
    {
      return Io_Archive::createZip($path_);
    }

    public static function systemName()
    {
      return PHP_OS;
    }

    public static function systemIsWindows()
    {
      return 'win'===strtolower(substr(PHP_OS, 0, 3));
    }

    public static function systemIsX()
    {
      // TODO Verify name for OSX
      return 'linux'===strtolower(PHP_OS) || 'unix'===strtolower(PHP_OS)  || 'macosx'===strtolower(PHP_OS);
    }
    //--------------------------------------------------------------------------
  }
?>
