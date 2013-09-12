<?php


namespace Components;


  /**
   * Io_Pipe
   *
   * @api
   * @package net.evalcode.components.io
   *
   * @author evalcode.net
   */
  abstract class Io_Pipe implements Object, Io_Channel
  {
    // CONSTRUCTION
    public function __construct($descriptor_, $flags_)
    {
      $this->m_descriptor=$descriptor_;
      $this->m_flags=$flags_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Io_Channel::isOpen() \Components\Io_Channel::isOpen()
     */
    public function isOpen()
    {
      return $this->m_isOpen;
    }

    /**
     * @see \Components\Io_Channel::open() \Components\Io_Channel::open()
     */
    public function open()
    {
      if(false===($this->m_pipe=@fopen($this->m_descriptor, $this->m_flags)))
      {
        throw new Io_Exception('io/pipe', sprintf(
          'Unable to open pipe [%s].', $this
        ));
      }

      return $this->m_isOpen=true;
    }

    /**
     * @see \Components\Io_Channel_Closeable::close() \Components\Io_Channel_Closeable::close()
     */
    public function close()
    {
      if(false===@fclose($this->m_pipe))
      {
        throw new Io_Exception('io/pipe', sprintf(
          'Unable to close pipe [%s].', $this
        ));
      }

      $this->m_isOpen=false;

      return true;
    }

    /**
     * @see Components\Object::hashCode() Components\Object::hashCode()
     */
    public function hashCode()
    {
      return String::hash($this->m_descriptor);
    }

    /**
     * @see Components\Object::equals() Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof static)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see Components\Object::__toString() Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{descriptor: %s, is_open: %s}',
        get_class($this),
        $this->hashCode(),
        $this->m_descriptor,
        true===$this->m_isOpen?'true':'false'
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var boolean
     */
    protected $m_isOpen=false;
    /**
     * @var string
     */
    protected $m_descriptor;
    /**
     * @var resource
     */
    protected $m_pipe;
    //--------------------------------------------------------------------------


    // DESTRUCTION
    public function __destruct()
    {
      if($this->m_isOpen)
        $this->close();
    }
    //--------------------------------------------------------------------------
  }
?>
