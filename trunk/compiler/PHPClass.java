import java.util.ArrayList;

public class PHPClass {
	public static String Name;
	public ArrayList<Node> nodes;
	public ArrayList<Var> vars;
	public ArrayList<Message> messages;
	public ArrayList<String> frames;
	public String startFunName;
	public String functions;
	public String comments;

	public PHPClass(){
		nodes=new ArrayList<Node>();
		vars=new ArrayList<Var>();
		messages=new ArrayList<Message>();
		frames=new ArrayList<String>();
		startFunName="";
		functions="";
		comments="";
	}

	public void applySection(Section sec){
		if(sec.name.equalsIgnoreCase("biz")){
			Name=sec.elements.get(0).data.trim();
		}else if(sec.name.equalsIgnoreCase("node")){
			for(SecElement se:sec.elements)
				nodes.add(new Node(se));
		}else if(sec.name.equalsIgnoreCase("var")){
			for(SecElement se:sec.elements)
				vars.add(new Var(se));
		}else if(sec.name.equalsIgnoreCase("start")){
			startFunName=sec.elements.get(0).data.trim();
		}else if(sec.name.equalsIgnoreCase("message")){
			for(SecElement se:sec.elements)
				messages.add(new Message(se));			
		}else if(sec.name.equalsIgnoreCase("frame")){
			String frm="";
			for(SecElement se:sec.elements){
				frm=se.data.trim();
				if(frm.length()>0){
					if(frm.charAt(0)=='*')
						frames.add(0, frm.substring(1));
					else
						frames.add(frm);
				}
			}
		}else if(sec.name.equalsIgnoreCase("phpfunction")){
			functions=sec.elements.get(0).data;
		}else if(sec.name.equalsIgnoreCase("comments")){
			comments="/*\n\tCompiled by bizLang compiler version 1.02\n\n"+sec.elements.get(0).data+"\n*/\n";
		}
	}

/*
#######################################################################################
#######################################################################################
###################         PHP File Generator       ##################################
#######################################################################################
#######################################################################################
*/

	public String toString(){
		return Header()+ClassName()+HiddenFunctions()+Functions()+Footer();
	}

	private String Header(){
		ArrayList<String> bizes=fetchbizes();
		String s="<?PHP\n\n"+comments;
		for(String b:bizes)
			if(!Name.equalsIgnoreCase(b))
				s=s+"require_once '../biz/"+b+"/"+b+".php';\n";
		return s;
	}

	private String ClassName(){
		String s="\nclass "+Name+" {\n\n" +
				"\t//Mandatory Variables for a biz\n" +
				"\tvar $_fullname;\n" +
				"\tvar $_curFrame;\n" +
				"\n\t//Variables\n";
		for(Var v:vars)
			s=s+"\tvar $"+v.name+";\n";
		s=s+"\n\t//Nodes (bizvars)\n";
		for(Node n:nodes){
			if(!n.isTemp){
				if(n.isArray)
					s=s+"\tvar $"+n.node+"_array_data; ";
				s=s+"\tvar $"+n.node+";\n";
			}
		}
		return s;
	}
	private String HiddenFunctions(){
		return HFConstruct()+HFMessage()+HFBroadcast()+HFShow();
	}
	private String Functions(){
		return functions;
	}
	private String Footer(){
		return "\n}\n\n?>";
	}

/*
#######################################################################################
#######################################################################################
###################         Hidden Functions Generator     ############################
#######################################################################################
#######################################################################################
*/
	private String HFConstruct(){
/*
	function __construct($fullname) {
        $this->_fullname=$fullname;
        $this->_curFrame="frm";//defauls frame if exists
        $this->nodes=array(); //for each node that has been defined as array
        $this->node=new biz($this->_fullname.'_node');//repeat for all noTemp nodes
        $this->var=initVal; // for those vars which has initVal
        $this->initfun();//which take from start section
	}
	
	function _sleep(){
		return array('_fuulname', '_curFrame','node1',''node2',...,'var1','var2',...);
	}
*/
		String s="\n\tfunction __construct($fullname) {\n" +
				"\t\t$this->_fullname=$fullname;\n";
		if(frames.size()>0)
			s+="\t\t$this->_curFrame='"+frames.get(0)+"';\n";
		for(Node n:nodes)
			if(n.isArray)
				s+="\t\t$this->"+n.node+"=array();\n";
			else if(!n.isTemp)
				s+="\t\t$this->"+n.node+"=new "+n.biz+"($this->_fullname.'_"+n.node+"');\n";
		for(Var v:vars)
			if(v.isInit())
				s+="\t\t$this->"+v.name+"="+v.init+";\n";
		if(startFunName.trim().length()>0)
			s+="\t\t$this->"+startFunName+"(); //Customized Initializing\n";
		s+="\t}\n\n" +
			"\tfunction __sleep(){\n" +
			"\t\treturn array('_fullname', '_curFrame',";
		for(Var v:vars)
			s+="'"+v.name+"',";
		for(Node n:nodes)
			if(!n.isTemp)
				s+="'"+n.node+"',";
		s=s.substring(0, s.length()-1)+
			");\n\t}\n";
		return s;
	}
	private String HFMessage(){
/*
    function message($to, $message, $info) {
        if ($to != $this->_fullname) {
			$this->myCat->message($to, $message, $info);
			foreach($this->eBoards as $i=>&$eB)
				$eB->message($to, $message, $info);
            return;
        }
        switch($message){
        	case "msg1":
        		$this->msg1callbackFun($info);
        		break;
        	default:
        		break;
        }
    }
*/
		String s="\n\tfunction message($to, $message, $info) {\n" +
				"\t\tif ($to != $this->_fullname) {\n";
		for(Node n:nodes){
			if(!n.isTemp){
				if(n.isArray){
					s=s+"\t\t\tforeach($this->"+n.node+" as $i=>&$_element)\n" +
					"\t\t\t\t$_element->message($to, $message, $info);\n";
				}else
					s=s+"\t\t\t$this->"+n.node+"->message($to, $message, $info);\n";
			}
		}
		s=s+"\t\t\treturn;\n\t\t}\n" +
			"\t\tswitch($message){\n";
		for(Message m:messages)
			if(m.isCallBack()){
				s=s+"\t\t\tcase '"+m.msg+"':\n" +
					"\t\t\t\t$this->"+m.fun+"($info);\n" +
					"\t\t\t\tbreak;\n";
			}
		s=s+"\t\t\tdefault:\n\t\t\t\tbreak;\n\t\t}\n" +
			"\t}\n";
		return s;
 	}

	private String HFBroadcast(){
/*
	function broadcast($message, $info) {
		$this->myCat->broadcast($message, $info);
		foreach($this->eBoards as $i=>&$eB)
			$eB->message($to, $message, $info);

        switch($message){
        	case "msg1":
        		$this->msg1callbackFun($info);
        		break;
        	default:
        		break;
        }
	}
*/
		String s="\n\tfunction broadcast($message, $info) {\n";
		for(Node n:nodes)
			if(!n.isTemp)
				if(n.isArray){
					s=s+"\t\tforeach($this->"+n.node+" as $i=>&$_element)\n" +
					"\t\t\t$_element->broadcast($message, $info);\n";
				}else
					s=s+"\t\t$this->"+n.node+"->broadcast($message, $info);\n";
		s=s+"\t\tswitch($message){\n";
		for(Message m:messages)
			if(m.isCallBack()){
				s=s+"\t\t\tcase '"+m.msg+"':\n" +
					"\t\t\t\t$this->"+m.fun+"($info);\n" +
					"\t\t\t\tbreak;\n";
			}
		s=s+"\t\t\tdefault:\n\t\t\t\tbreak;\n\t\t}\n" +
			"\t}\n";
		return s;
	}

	private String HFShow(){
/*

	function _bookframe($frame){
		$this->_curFrame=$frame;
		$this->show(true);
	}

	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$html='<div id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
		if($echo)
            echo $this;
        else
            return $this;
    }
*/
		String s="";
		s=s+"\n\tfunction _bookframe($frame){\n" +
			"\t\t$this->_curFrame=$frame;\n" +
			"\t\t$this->show(true);\n" +
			"\t}\n" +
			"\tfunction _backframe(){\n" +
			"\t\treturn $this->show(false);\n" +
			"\t}\n" +
			"\n\tfunction show($echo){\n" +
			"\t\t$html='<div id=\"' . $this->_fullname . '\">'.call_user_func(array($this, $this->_curFrame)).'</div>';\n" +
			"\t\tif($echo)\n" +
			"\t\t\techo $html;\n" +
			"\t\telse\n" +
			"\t\t\treturn $html;\n" +
			"\t}\n";
		return s;
	}

/*
#######################################################################################
#######################################################################################
###################         Helping Functions        ##################################
#######################################################################################
#######################################################################################
*/

	public ArrayList<String> fetchbizes(){
		ArrayList<String> bizes=new ArrayList<String>();
		boolean added=false;
		for(Node n:nodes){
			added=false;
			for(String s:bizes)
				if(s.equalsIgnoreCase(n.biz))
					added=true;
			if(!added)
				bizes.add(n.biz);
		}
		return bizes;
	}
}
