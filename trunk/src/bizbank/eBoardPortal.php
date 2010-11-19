    <?php

    //DB Info : Specify what informaton this BIZ is going to use from DB



    require_once '../biz/user/login.php';

    class eBoardPortal{


            //all of our biz classes should define these two variable
            var $_bizbankname;

            /****************************FIELDS****************************/
            //CALSS FIELDS




            //FIELDS WHICH HAVE TYPE OF OTHER BIZES
            var $login;


            /****************************CONSTRUCTOR****************************/

            //function __construct($data){
            //SET CLASS FIELDS WITH $data


            //IF REQUIRED BIZES HAVE NOT BEEN USED,INITIALISE
            /*if(!(isset($data["bizVN"]))){
            $data[$bizVN][$_fullname] = (this->$_fullname."_".$bizVN);
            $data[$bizVN][$_bizname] = $_bizname;
            }
            this->$bizVN = new "$bizVN"(&$data[$bizVN]);
            } */



            //}
            function __construct($data) {
                $this->_bizbankname = "eBoardPortal";
                    if(!(isset ($data['login']))){
                        $data['login']['fullname'] = ($this->_bizbankname."_"."login");//$this->user
                        $data['login']['bizname'] = 'login';
                        $data['login']['parent'] = $this;
                    }
                    $this->login = new user(&$data['login']);

            }

            /****************************MESSAGE HANDELING****************************/

            /*function message($to,$message,$info) {
             this->$bizVN->message(&$to,&$message,&$info);
             if($to != this->$-fullname){
                    return;
                    }
                    show_content();
                    }*/
            function message($to,$message,$info) {
                $this->login->message(&$to,&$message,&$info);
                if($to != $this->_bizbankname){
                    return ;
                    }
                    // handle possible messages for this
                    show();
            }
            function broadcast($msg,$info) {
                $this->login->broadcast(&$msg,&$info);
            }

            /****************************HTML HANDELING****************************/
            function show() {
                $this->login->sho;


            }


    }
    ?>