<?php


namespace Components;


  /**
   * Io_Pipe_Source
   *
   * @package net.evalcode.components
   * @subpackage io.pipe
   *
   * @author evalcode.net
   */
  class Io_Pipe_Source extends Io_Pipe implements Io_Channel_Readable
  {
    // CONSTRUCTION
    public function __construct($descriptor_, $flags_='r')
    {
      parent::__construct($descriptor_, $flags_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**     * @see Components\Io_Channel_Readable::read() Components\Io_Channel_Readable::read()
     */
    public function read(Io_Buffer $buffer_, $interrupt_=null)
    {
      $offset=$buffer_->position();

      if($buffer_ instanceof Io_Buffer_String)
      {
        // TODO Implement $interrupt_.
        while($buffer_->limit()>$buffer_->position())
        {
          if(false===($read=@fread($this->m_pipe, $buffer_->limit())))
            throw new Io_Exception('io/pipe/source', sprintf('Unable to read from pipe [%s].', $this));

          $buffer_->append($read);
        }
      }
      else
      {
        while($buffer_->limit()>$buffer_->position())
        {
          if(false===($read=@fread($this->m_pipe, 1)))
            throw new Io_Exception('io/pipe/source', sprintf('Unable to read from pipe [%s].', $this));

          $buffer_->append($read=ord($read));

          if($interrupt_===$read)
            break;
        }
      }

      return $buffer_->position()-$offset;
    }
    //--------------------------------------------------------------------------
  }
?>
