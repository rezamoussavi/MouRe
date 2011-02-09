<?php

class Biz
{
   public $_bizname;
   public $_fullname;
   public $_parent;
   public $_biznodes;
   public $_html;
   
   function __construct($data, $bizes)
   {
      $this->_bizname = &$data["bizname"];
      $this->_fullname = &$data["fullname"];
      $this->_parent = &$data["parent"];
      
      foreach($bizes as $bizname=>$biz)
      {
          if (!(isset($data[$bizname])))
          { 
            $data[$bizname]['fullname'] = ($this->_bizname . "_" . $bizname); //$this->user
            $data[$bizname]['bizname'] = $bizname;
            $data[$bizname]['parent'] = $this;
          }
          $this->_biznodes[$bizname] = new $biz(&$data[$bizname]);
      } 
   }
}

?>