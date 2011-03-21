import java.util.ArrayList;

public class PHPClass {
	public static String Name;
	public static String Family;
	public ArrayList<Node> nodes;
	public ArrayList<Var> vars;
	public ArrayList<Message> messages;
	public ArrayList<Frame> frames;
	public String startFunName;
	public String functions;
	public String comments;

	public PHPClass(){
		nodes=new ArrayList<Node>();
		vars=new ArrayList<Var>();
		messages=new ArrayList<Message>();
		frames=new ArrayList<Frame>();
		startFunName="";
		functions="";
		comments="";
	}

	public void applySection(Section sec){
		if(sec.name.equalsIgnoreCase("biz")){
			Biz b=new Biz(sec.elements.get(0));
			Name=b.bizName;
			Family=b.Family;
		}else if(sec.name.equalsIgnoreCase("family")){
			Family=sec.elements.get(0).data.trim();
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
			Frame f=null;
			for(SecElement se:sec.elements){
				f=new Frame(se);
				if(f.Stared)
					frames.add(0,f);
				else
					frames.add(f);
			}
		}else if(sec.name.equalsIgnoreCase("phpfunction")){
			functions=sec.elements.get(0).data;
		}else if(sec.name.equalsIgnoreCase("comments")){
			comments= "/*\n\tCompiled by bizLang compiler version 3.0 (March 22 2011) By Reza Moussavi\n\n"+
			sec.elements.get(0).data+"\n*/\n";
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
				s+="require_once 'biz/"+b+"/"+b+".php';\n";
		return s;
	}

	private String ClassName(){
		String s="\nclass "+Name+" {\n\n" +
		"\t//Mandatory Variables for a biz\n" +
		"\tvar $_fullname;\n" +
		"\tvar $_curFrame;\n" +
		"\tvar $_tmpNode;\n" +
		"\tvar $_frmChanged;\n" +
		"\n\t//Variables\n";
		for(Var v:vars)
			s+="\tvar $"+v.name+";\n";
		s+="\n\t//Nodes (bizvars)\n";
		for(Node n:nodes){
			if(!n.isTemp){
				s+="\tvar $"+n.node+";";
				if(n.isArray)
					s+=" // array of biz";
				s+="\n";
			}
		}
		return s;
	}
	private String HiddenFunctions(){
		return HFConstruct()+HFMessage()+HFShow();
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
		$this->_tmpNode=false;
		$this->_frmChanged=false;//if there is any frame defined for this biz
		if($fullname==null){
			$fullname="_tmpNode_".count($_SESSION['osNodes']);
			$this->_tmpNode=true;
		}
        $this->_fullname=$fullname;
        if(!isset($_SESSION['osNodes'][$fullname])){
        	$_SESSION['osNodes'][$fullname]=array();

        	//for each inMessage as Msg
        	$_SESSION['osMsg'][Msg][$this->_fullname]=true;
        }

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']="frm";
        $this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		//handle arrays
		$this->nodes=array();
		if(!isset($_SESSION['osNodes'][$fullname]['nodes']))
			$_SESSION['osNodes'][$fullname]['nodes']=array();
		foreach($_SESSION['osNodes'][$fullname]['nodes'] as $arrfn)
			$this->nodes[]=new biz($arrfn);

        $this->node=new biz($this->_fullname.'#node');//repeat for all noTemp nodes

		if(!isset($_SESSION['osNodes'][$fullname]['VAR']))
			$_SESSION['osNodes'][$fullname]['VAR']=""/initVar;
        $this->var=&$_SESSION['osNodes'][$fullname]['VAR'];
        if(!isset($_SESSION['osNodes'][$fullname]['biz']))
        	$this->initfun();//which take from start section
	}

	function gotoSleep() {
		$_SESSION['osNodes'][$this->_fullname]['NODES']=array();
		$_SESSION['osNodes'][$this->_fullname]['sleep']=true;
		foreach($this->NODES as $node){
			$_SESSION['osNodes'][$this->_fullname]['NODES'][]=$node->_fullname;
		}
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}

		 */
		String s="\n\tfunction __construct($fullname) {\n";
		if(frames.size()>0)
			s+="\t\t$this->_frmChanged=false;\n";
		s+=	"\t\t$this->_tmpNode=false;\n"+
			"\t\tif($fullname==null){\n"+
			"\t\t\t$fullname='_tmpNode_'.count($_SESSION['osNodes']);\n"+
			"\t\t\t$this->_tmpNode=true;\n"+
			"\t\t}\n" +
			"\t\t$this->_fullname=$fullname;\n" +
			"\t\tif(!isset($_SESSION['osNodes'][$fullname])){\n" +
			"\t\t\t$_SESSION['osNodes'][$fullname]=array();\n" +
			"\t\t\t//If any message need to be registered will placed here\n";
		for(Message m:messages)
			if(m.isCallBack())
				s+="\t\t\t$_SESSION['osMsg']['"+m.msg+"'][$this->_fullname]=true;\n";
		s+="\t\t}\n\n" +
			"\t\t$_SESSION['osNodes'][$fullname]['sleep']=false;\n";
		if(frames.size()>0){
			s+= "\t\t//default frame if exists\n"+
			"\t\tif(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))\n"+
			"\t\t\t$_SESSION['osNodes'][$fullname]['_curFrame']='"+frames.get(0).Name+"';\n"+
			"\t\t$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];\n\n";
		}
		for(Node n:nodes)
			if(n.isArray){
				s+= "\t\t//handle arrays\n" +
				"\t\t$this->"+n.node+"=array();\n"+
				"\t\tif(!isset($_SESSION['osNodes'][$fullname]['"+n.node+"']))\n"+
				"\t\t\t$_SESSION['osNodes'][$fullname]['"+n.node+"']=array();\n"+
				"\t\tforeach($_SESSION['osNodes'][$fullname]['"+n.node+"'] as $arrfn)\n"+
				"\t\t\t$this->"+n.node+"[]=new "+n.biz+"($arrfn);\n\n";
			}
			else if(!n.isTemp){
				s+="\t\t$this->"+n.node+"=new "+n.biz+"($this->_fullname.'_"+n.node+"');\n\n";
			}
		for(Var v:vars){
			s+= "\t\tif(!isset($_SESSION['osNodes'][$fullname]['"+v.name+"']))\n"+
			"\t\t\t$_SESSION['osNodes'][$fullname]['"+v.name+"']="+v.init+";\n"+
			"\t\t$this->"+v.name+"=&$_SESSION['osNodes'][$fullname]['"+v.name+"'];\n\n";
		}
		if(startFunName.trim().length()>0)
			s+= "\t\tif(!isset($_SESSION['osNodes'][$fullname]['biz']))\n" +
				"\t\t\t$this->"+startFunName+"(); //Customized Initializing\n";
		s+= "\t\t$_SESSION['osNodes'][$fullname]['node']=$this;\n" +
			"\t\t$_SESSION['osNodes'][$fullname]['biz']='"+Name+"';\n" +
		"\t}\n\n" +
		"\tfunction gotoSleep() {\n";
		for(Node n:nodes)
			if(n.isArray)
				s+= "\t\t$_SESSION['osNodes'][$this->_fullname]['"+n.node+"']=array();\n" +
					"\t\t$_SESSION['osNodes'][$this->_fullname]['sleep']=true;\n"+
					"\t\tforeach($this->"+n.node+" as $node){\n" +
					"\t\t\t$_SESSION['osNodes'][$this->_fullname]['"+n.node+"'][]=$node->_fullname;\n\t\t}\n";
		s+= "\t\tif($this->_tmpNode)\n"+
			"\t\t\tunset($_SESSION['osNodes'][$this->_fullname]);\n" +
			"\t\telse\n" +
			"\t\t\tunset($_SESSION['osNodes'][$this->_fullname]['node']);\n"+
			"\t}\n\n";
		return s;
	}

	private String HFMessage(){
		/*
	    function message($message, $info) {
	        switch($message){
	        	case "msg1":
	        		$this->msg1callbackFun($info);
	        		break;
	        	default:
	        		break;
	        }
	    }
		 */
		String s="\n\tfunction message($message, $info) {\n" +
		"\t\tswitch($message){\n";
		for(Message m:messages)
			if(m.isCallBack()){
				s+="\t\t\tcase '"+m.msg+"':\n" +
				"\t\t\t\t$this->"+m.fun+"($info);\n" +
				"\t\t\t\tbreak;\n";
			}
		s+="\t\t\tdefault:\n\t\t\t\tbreak;\n\t\t}\n" +
		"\t}\n";
		return s;
	}


	private String HFShow(){
		/*

	function _bookframe($frame){
		$this->_frmChanged=true;//if there is any frm defined for this biz
		$this->_curFrame=$frame;
		//$this->show(true);
	}

	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$html='<div id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
		if($_SESSION['silentmode'])
			return;
		if($echo)
            echo $this;
        else
            return $this;
    }
		 */
		String s="";
		s+="\n\tfunction _bookframe($frame){\n";
		if(frames.size()>0)
			s+="\t\t$this->_frmChanged=true;\n";
		s+="\t\t$this->_curFrame=$frame;\n" +
		"\t\t//$this->show(true);\n" +
		"\t}\n" +
		"\tfunction _backframe(){\n" +
		"\t\treturn $this->show(false);\n" +
		"\t}\n" +
		"\n\tfunction show($echo){\n";
		if(frames.size()>0){
			s+="\t\t$_style='';\n" +
			"\t\tswitch($this->_curFrame){\n";
			for(Frame f:frames)
				s+=	"\t\t\tcase '"+f.Name+"':\n" +
				"\t\t\t\t$_style='"+f.Style+"';\n" +
				"\t\t\t\tbreak;\n";
			s+="\t\t}\n" +
			"\t\t$html='<div '.$_style.' id=\"' . $this->_fullname . '\">'.call_user_func(array($this, $this->_curFrame)).'</div>';\n" +
			"\t\tif($_SESSION['silentmode'])\n" +
			"\t\t\treturn;\n" +
			"\t\tif($echo)\n" +
			"\t\t\techo $html;\n" +
			"\t\telse\n" +
			"\t\t\treturn $html;\n";
		}
		s+="\t}\n";
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
