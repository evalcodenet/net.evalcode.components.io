<?php


  class Io_Scriptlet_Test extends Scriptlet
  {
    public function get()
    {
      return $this->post();
    }

    public function post()
    {

    }
  }
?>
