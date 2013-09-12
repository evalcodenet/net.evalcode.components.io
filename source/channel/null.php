<?php


namespace Components;


  /**
   * Io_Channel_Null
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage channel
   *
   * @author evalcode.net
   */
  class Io_Channel_Null implements Object, Io_Channel_Readable, Io_Channel_Writable
  {
    // OVERRIDES
    /**
     * @see \Components\Io_Channel_Readable::read() \Components\Io_Channel_Readable::read()
     */
    public function read(Io_Buffer $buffer_, $interrupt_=null)
    {
      return -1;
    }

    /**
     * @see \Components\Io_Channel_Writable::write() \Components\Io_Channel_Writable::write()
     */
    public function write(Io_Buffer $buffer_)
    {
      return 0;
    }

    /**
     * @see \Components\Io_Channel::isOpen() \Components\Io_Channel::isOpen()
     */
    public function isOpen()
    {
      return true;
    }

    /**
     * @see \Components\Io_Channel::open() \Components\Io_Channel::open()
     */
    public function open()
    {
      return true;
    }

    /**
     * @see \Components\Io_Channel_Closeable::close() \Components\Io_Channel_Closeable::close()
     */
    public function close()
    {
      return true;
    }

    /**
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * @see \Components\Object::equals() \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------
  }
?>
