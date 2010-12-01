<?php

class dummie
{
   //all of our biz classes should define these three variables
   var $_fullname;
   var $_bizname;
   var $_parent;
   
   //local
   var $loggedIn;
   var $userEmail;
    
   function __construct($data)
   {
      $this->_bizname = &$data["bizname"];
      $this->_fullname = &$data["fullname"];
      
      if(!isset($data["loggedIn"]))
      {
         $data["loggedIn"] = 0;
      }
      $this->loggedIn = &$data["loggedIn"];
      $this->userEmail = &$data["userEmail"];
      
   }
   
   function message($to, $message, $info)
   {
        if ($to != $this->_fullname)
        {
            //pass msg to childs
            return;
        }
      
      $this->show();
    }

    function broadcast($msg, $info)
    {
      switch($msg)
      {
         case 'login':
            $this->loggedIn = 1;
            $this->userEmail = $info["email"];
            $this->show();
            break;
         
         case 'logout':
            $this->loggedIn = 0;
            $this->show();
            break;
      }
      
        //pass to child bizes
        //no children
        
    }
   
   function show()
   {

      $html = "";
      
      
      if($this->loggedIn > 0)
      {
         $html .= "<h2>:) </h2> " . $this->userEmail;
      }
      else
      {
         $html .= "<h2>:(</h2>";
      }
      
      osReturn($html, $this->_fullname);
   }
   
}

?>