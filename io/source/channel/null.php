<?php


  /**
   * Io_Channel_Null
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  class Io_Channel_Null implements Object, Io_Channel_Readable, Io_Channel_Writable
  {
    // OVERRIDES/IMPLEMENTS
    /**
     * @see Io_Channel_Readable::read()
     */
    public function read(Io_Buffer $buffer_, $interrupt_=null)
    {
      return -1;
    }

    /**
     * @see Io_Channel_Writable::write()
     */
    public function write(Io_Buffer $buffer_)
    {
      return 0;
    }

    /**
     * @see Io_Channel::isOpen()
     */
    public function isOpen()
    {
      return true;
    }

    /**
     * @see Io_Channel::open()
     */
    public function open()
    {
      return true;
    }

    /**
     * @see Io_Channel_Closeable::close()
     */
    public function close()
    {
      return true;
    }

    /**
     * @see Object::hashCode()
     */
    public function hashCode()
    {
      return spl_object_hash($this);
    }

    /**
     * @see Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------
  }
?>
