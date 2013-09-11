<?php


namespace Components;


  /**
   * Io_Pipe_Sink
   *
   * @package net.evalcode.components
   * @subpackage io.pipe
   *
   * @author evalcode.net
   */
  class Io_Pipe_Sink extends Io_Pipe implements Io_Channel_Writable
  {
    // CONSTRUCTION
    public function __construct($descriptor_, $flags_='a')
    {
      parent::__construct($descriptor_, $flags_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**     * @see Components\Io_Channel_Writable::write() Components\Io_Channel_Writable::write()
     */
    public function write(Io_Buffer $buffer_)
    {
      $offset=$buffer_->position();

      if($buffer_ instanceof Io_Buffer_String)
      {
        if(false===@fwrite($this->m_pipe, (string)$buffer_))
          throw new Io_Exception('io/pipe/sink', sprintf('Unable to write to pipe [%s].', $this));

        $buffer_->position($buffer_->limit());
      }
      else
      {
        if(false===@fwrite($this->m_pipe, implode('', $buffer_->arrayValue())))
          throw new Io_Exception('io/pipe/sink', sprintf('Unable to write to pipe [%s].', $this));

        $buffer_->position($buffer_->limit());
      }

      return $buffer_->position()-$offset;
    }
    //--------------------------------------------------------------------------
  }
?>
