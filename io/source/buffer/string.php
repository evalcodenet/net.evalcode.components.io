<?php


namespace Components;


  /**
   * Io_Buffer_String
   *
   * @package net.evalcode.components
   * @subpackage io.buffer
   *
   * @author evalcode.net
   */
  class Io_Buffer_String extends Io_Buffer_Char
  {
    // STATIC ACCESSORS
    /**
     * @param integer $capacity_
     *
     * @return Components\Io_Buffer_String
     */
    public static function allocate($capacity_=4096)
    {
      return new static($capacity_);
    }

    /**
     * @param string $buffer_
     *
     * @return Components\Io_Buffer_String
     */
    public static function wrap(&$buffer_)
    {
      $buffer=new static(String::length($buffer_));
      $buffer->m_stringBuffer=&$buffer_;

      return $buffer;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function substring($offset_, $length_)
    {
      return String::substring($this->m_stringBuffer, $offset_, $length_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see Components.Io_Buffer::next()
     *
     * @return string
     */
    public function next()
    {
      return $this->m_stringBuffer[$this->m_position++];
    }

    /**
     * @see Components.Io_Buffer::get()
     *
     * @return string
     */
    public function get($position_=null)
    {
      if(null===$position_)
        return $this->m_stringBuffer[$this->m_position];

      return $this->m_stringBuffer[$position_];
    }

    /**
     * @see Components.Io_Buffer::append()
     *
     * @return Components\Io_Buffer_String
     */
    public function append($value_)
    {
      $this->m_stringBuffer=mb_substr($this->m_stringBuffer, 0, $this->m_position).$value_;
      $this->m_position+=String::length($value_);

      return $this;
    }

    /**
     * @see Components.Io_Buffer::appendBuffer()
     *
     * @param Io_Buffer_String $source_
     *
     * @return Components\Io_Buffer_String
     */
    public function appendBuffer(Io_Buffer $source_)
    {
      if(!$source_ instanceof self)
        throw new Exception_IllegalArgument('io/buffer/string', 'Expected instance of '.__CLASS__.'.');

      return parent::appendBuffer($source_);
    }

    /**
     * @see Components.Io_Buffer::flip()
     *
     * @return Components\Io_Buffer_String
     */
    public function flip()
    {
      return parent::flip();
    }

    /**
     * @see Components.Io_Buffer::rewind()
     *
     * @return Components\Io_Buffer_String
     */
    public function rewind()
    {
      return parent::rewind();
    }

    /**
     * @see Components.Io_Buffer::clear()
     *
     * @return Components\Io_Buffer_String
     */
    public function clear()
    {
      $this->m_position=0;
      $this->m_limit=$this->m_capacity;
      $this->m_stringBuffer='';

      return $this;
    }

    /**
     * @see Components.Io_Buffer::mark()
     *
     * @return Components\Io_Buffer_String
     */
    public function mark()
    {
      return parent::mark();
    }

    /**
     * @see Components.Io_Buffer::reset()
     *
     * @return Components\Io_Buffer_String
     */
    public function reset()
    {
      return parent::reset();
    }

    /**
     * @see Components.Io_Buffer::isArray()
     */
    public function isArray()
    {
      return false;
    }

    /**
     * @see Components.Io_Buffer::arrayValue()
     */
    public function arrayValue()
    {
      return null;
    }

    /**
     * @see Components.Cloneable::__clone()
     *
     * @return Components\Io_Buffer_String
     */
    public function __clone()
    {
      $buffer=new self($this->m_capacity);
      $buffer->m_limit=$this->m_limit;
      $buffer->m_mark=$this->m_mark;
      $buffer->m_position=$this->m_position;
      $buffer->m_stringBuffer=&$this->m_stringBuffer;

      return $buffer;
    }

    /**
     * @see Components.Object::hashCode()
     */
    public function hashCode()
    {
      return string_hash($this->m_stringBuffer);
    }

    /**
     * @see Components.Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return String::equal($this->m_stringBuffer, $object_->m_stringBuffer);

      return false;
    }

    /**
     * @see Components.Object::__toString()
     */
    public function __toString()
    {
      return $this->m_stringBuffer;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string
     */
    protected $m_stringBuffer='';
    //--------------------------------------------------------------------------
  }
?>
