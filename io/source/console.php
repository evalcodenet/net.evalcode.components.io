<?php


namespace Components;


  /**
   * Io_Console
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  class Io_Console implements Io_Channel_Writable, Io_Channel_Readable, Io_Channel_Flushable
  {
    // PROPERTIES
    /**
     * @var \Components\Io_Channel_Readable
     */
    public $in;
    /**
     * @var \Components\Io_Channel_Writable
     */
    public $out;
    /**
     * @var \Components\Io_Channel_Writable
     */
    public $err;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct()
    {
      $this->in=new Io_Channel_Null();
      $this->out=new Io_Channel_Null();
      $this->err=new Io_Channel_Null();

      $this->m_buffer=Io_Buffer_String::allocate();
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param string $string_
     *
     * @return \Components\Io_Console
     */
    public function append($string_)
    {
      $this->m_buffer->append($string_);

      return $this;
    }

    /**
     * @param string $string_
     *
     * @return \Components\Io_Console
     */
    public function appendLine($string_='')
    {
      $this->append($string_.Io::LINE_SEPARATOR_DEFAULT);

      return $this;
    }

    public function appendOptions()
    {
      $this->appendLine("Usage: \t[executable] [option] ...");

      $this->appendLine("Options:");
      foreach($this->m_options as $option)
      {
        if(isset($option['character']))
        {
          $this->appendLine(sprintf('%s%-5.5s%s%-20.20s%-70.70s',
            "\t", $option['character']?"-$option[character]":'', "\t", "--$option[extended]", $option['description']
          ));
        }
        else
        {
          $this->appendLine();
        }
      }

      $this->appendLine();

      return $this;
    }

    public function appendInfo()
    {
      $this->appendLine($this->m_info);

      return $this;
    }

    public function appendLicense()
    {
      $this->appendLine($this->m_license);

      return $this;
    }

    public function isAttached()
    {
      return $this->m_isAttached;
    }

    /**
     * @param \Components\Io_Channel_Readable $stdin_
     * @param \Components\Io_Channel_Writable $stdout_
     * @param \Components\Io_Channel_Writable $stderr_
     *
     * @return \Components\Io_Console
     *
     * @throws \Components\Io_Exception
     */
    public function attach(Io_Channel_Readable $stdin_, Io_Channel_Writable $stdout_,
      Io_Channel_Writable $stderr_=null)
    {
      if($this->m_isAttached)
        throw new Io_Exception('io/console', 'Console is already attached.');

      $this->in=$stdin_;
      $this->out=$stdout_;

      if(null!==$stderr_)
        $this->err=$stderr_;

      $options=array();
      $optionsExtended=array();
      foreach($this->m_options as $option)
      {
        if(false===isset($option['character']))
            continue;

        $append=0;
        if(isset($option['holdsvalue']) && $option['holdsvalue'])
          $append++;

        if(isset($option['default']) && $option['default'])
          $optionsDefaults[$option['character']]=$option['default'];

        $options[$option['character']]=$option['character'].($suffix=str_repeat(':', $append));
        if(isset($option['extended']) && $option['extended'])
          $optionsExtended[$option['character']]=$option['extended'].$suffix;
      }

      $this->m_arguments=getopt(implode('', $options), $optionsExtended);

      foreach($this->m_options as $option)
      {
        if(0===count($option))
            continue;

        if(isset($this->m_arguments[$option['character']]) || isset($this->m_arguments[$option['extended']]))
        {
          if(false===isset($this->m_arguments[$option['character']]) && isset($this->m_arguments[$option['extended']]))
            $this->m_arguments[$option['character']]=$this->m_arguments[$option['extended']];
          if(isset($this->m_arguments[$option['character']]) && false==isset($this->m_arguments[$option['extended']]))
            $this->m_arguments[$option['extended']]=$this->m_arguments[$option['character']];
        }
        else
        {
          $this->m_arguments[$option['character']]=$option['default'];
          $this->m_arguments[$option['extended']]=$option['default'];
        }
      }

      $this->m_isAttached=true;

      return $this;
    }

    public function addOption($character_, $holdsValue_=false, $defaultValue_=null, $description_=null, $nameExtended_=null)
    {
      array_push($this->m_options, array(
        'character'=>$character_,
        'holdsvalue'=>$holdsValue_,
        'default'=>$defaultValue_,
        'description'=>$description_,
        'extended'=>$nameExtended_,
      ));

      return $this;
    }

    public function addEmptyOption()
    {
      array_push($this->m_options, array());

      return $this;
    }

    public function setInfo($info_)
    {
      $this->m_info=$info_;

      return $this;
    }

    public function setLicense($license_)
    {
      $this->m_license=$license_;

      return $this;
    }

    public function getArguments()
    {
      return $this->m_arguments;
    }

    public function hasArgument($name_)
    {
      if(isset($this->m_arguments[$name_]))
        return true;

      return false;
    }

    public function getArgument($name_, $defaultValue_=null)
    {
      if(isset($this->m_arguments[$name_]))
        return $this->m_arguments[$name_];

      return $defaultValue_;
    }

    public function setArgument($name_, $value_)
    {
      $this->m_arguments[$name_]=$value_;

      return $this;
    }

    public function getWorkingDirectory()
    {
      return $this->m_workingDirectory;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components.Io_Channel_Readable::read()
     */
    public function read(Io_Buffer $buffer_, $interrupt_=null)
    {
      return $this->in->read($buffer_, $interrupt_);
    }

    /**
     * (non-PHPdoc)
     * @see Components.Io_Channel_Writable::write()
     */
    public function write(Io_Buffer $buffer_)
    {
      return $this->out->write($buffer_);
    }

    /**
     * (non-PHPdoc)
     * @see Components.Io_Channel_Flushable::flush()
     *
     * @return \Components\Io_Console
     */
    public function flush()
    {
      $this->m_buffer->flip();
      $this->out->write($this->m_buffer);
      $this->m_buffer->clear();

      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Io_Channel::isOpen()
     */
    public function isOpen()
    {
      return $this->m_isOpen;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Io_Channel::open()
     *
     * @return \Components\Io_Console
     */
    public function open()
    {
      if(false===$this->in->isOpen())
        $this->in->open();
      if(false===$this->out->isOpen())
        $this->out->open();
      if(false===$this->err->isOpen())
        $this->err->open();

      $this->m_isOpen=true;

      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Io_Channel_Closeable::close()
     *
     * @return \Components\Io_Console
     */
    public function close()
    {
      if($this->in->isOpen())
        $this->in->close();
      if($this->out->isOpen())
        $this->out->close();
      if($this->err->isOpen())
        $this->err->close();

      $this->m_isOpen=false;

      return $this;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_arguments=array();
    private $m_options=array();
    private $m_isAttached=false;
    private $m_isOpen=false;
    /**
     * @var \Components\Io_Buffer
     */
    private $m_buffer;
    private $m_info;
    private $m_license;
    private $m_workingDirectory;
    //--------------------------------------------------------------------------
  }
?>
