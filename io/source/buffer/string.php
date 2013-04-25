<?php


namespace Components;


  /**
   * Io_Buffer_String
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  class Io_Buffer_String extends Io_Buffer_Char
  {
    // STATIC ACCESSORS
    /**
     * @param int $capacity_
     *
     * @return Io_Buffer_String
     */
    public static function allocate($capacity_=4096)
    {
      return new static($capacity_);
    }

    /**
     * @param string $buffer_
     *
     * @return Io_Buffer_String
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
     * @see Io_Buffer::next()
     *
     * @return string
     */
    public function next()
    {
      return $this->m_stringBuffer[$this->m_position++];
    }

    /**
     * @see Io_Buffer::get()
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
     * @see Io_Buffer::append()
     *
     * @return Io_Buffer_String
     */
    public function append($value_)
    {
      $this->m_stringBuffer=mb_substr($this->m_stringBuffer, 0, $this->m_position).$value_;
      $this->m_position+=String::length($value_);

      return $this;
    }

    /**
     * @see Io_Buffer::appendBuffer()
     *
     * @param Io_Buffer_String $source_
     *
     * @return Io_Buffer_String
     */
    public function appendBuffer(Io_Buffer $source_)
    {
      if(!$source_ instanceof static)
        throw new Exception_IllegalArgument('io/buffer/string', 'Expected instance of '.__CLASS__.'.');

      return parent::appendBuffer($source_);
    }

    /**
     * @see Io_Buffer::flip()
     *
     * @return Io_Buffer_String
     */
    public function flip()
    {
      return parent::flip();
    }

    /**
     * @see Io_Buffer::rewind()
     *
     * @return Io_Buffer_String
     */
    public function rewind()
    {
      return parent::rewind();
    }

    /**
     * @see Io_Buffer::clear()
     *
     * @return Io_Buffer_String
     */
    public function clear()
    {
      $this->m_position=0;
      $this->m_limit=$this->m_capacity;
      $this->m_stringBuffer='';

      return $this;
    }

    /**
     * @see Io_Buffer::mark()
     *
     * @return Io_Buffer_String
     */
    public function mark()
    {
      return parent::mark();
    }

    /**
     * @see Io_Buffer::reset()
     *
     * @return Io_Buffer_String
     */
    public function reset()
    {
      return parent::reset();
    }

    /**
     * @see Io_Buffer::isArray()
     */
    public function isArray()
    {
      return false;
    }

    /**
     * @see Io_Buffer::arrayValue()
     */
    public function arrayValue()
    {
      return null;
    }

    /**
     * @see Io_Buffer::__clone()
     *
     * @return Io_Buffer_String
     */
    public function __clone()
    {
      $buffer=new static($this->m_capacity);
      $buffer->m_limit=$this->m_limit;
      $buffer->m_mark=$this->m_mark;
      $buffer->m_position=$this->m_position;
      $buffer->m_stringBuffer=&$this->m_stringBuffer;

      return $buffer;
    }

    /**
     * @see Object::hashCode()
     */
    public function hashCode()
    {
      return String::hash($this->m_stringBuffer);
    }

    /**
     * @see Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof static)
        return String::equal($this->m_stringBuffer, $object_->m_stringBuffer);

      return false;
    }

    /**
     * @see Object::__toString()
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
