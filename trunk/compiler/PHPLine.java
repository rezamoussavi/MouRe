
public class PHPLine {

	private static String[] toThisTags={"var","node","fun"};
	private static String[] toStringTags={"frame","msg"};
	private static String validVarChars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_";

	public static String parse(String line){
		line=nodeFrame(line);//Should be before toThisTags
		line=nodeArray(line);//Should be before toThisTags
		line=messageInForms(line);//Should be before toThisTags
		line=toThisTags(line);
		line=dbTags(line);
		line=toStringTags(line);
		line=nodeIDTag(line);
		line=replace(line, "_bookframe", "$this->_bookframe",false);
//		line=replace(line, "_backframe", "$this->_backframe",false);
		return line;
	}

	private static String messageInForms(String line){
		int eom=0,i=line.indexOf("<#msg.");
		String msg="";
		while(i!=-1){//there is a "<#msg."
			eom=endOfWordIndex(line.substring(i+6))+i+6;
			msg=line.substring(i+6,eom);
			line=line.substring(0,i)
				+"<input type=\"hidden\" name=\"_message\" value=\""+msg+"\" />"
				+"<input type = \"hidden\" name=\"_target\" value=\"' . $this->_fullname . '\" />"
				+line.substring(eom+1);
			i=line.indexOf("<#msg.");
		}
		return line;
	}
	private static String nodeFrame(String line){
		int framei,nodeTagi=line.indexOf("#node.");
		String node="";
		if(nodeTagi!=-1){//if there is a '#node.'
			framei=line.substring(nodeTagi+6).indexOf(".frame");
			if(framei!=-1){//if there is a '.frame' after '#node.xxx'
				framei+=nodeTagi+6;
				node=line.substring(nodeTagi+6,framei);
				line=line.substring(0,nodeTagi)
					+" ' . $this->"+node+"->show(false) . ' "
					+line.substring(framei+6);
			}
		}
		return line;
	}

	private static String nodeArray(String line){
		int tabs=0,doti=-1,EOLi,EQi,nodeTagi=line.indexOf("#node.");
		String nodeAr="",afterEq="";
		if(nodeTagi==-1)//if there is no '#node.'
			return line;
		EOLi=line.indexOf(";",nodeTagi);
		EQi=line.indexOf("=", nodeTagi);
		if(nodeTagi!=-1)
			doti=line.substring(nodeTagi).indexOf(".")+nodeTagi+1;
		if(EOLi!=-1 && EQi<EOLi && EQi>nodeTagi && doti!=-1){//there is #node.XXXXX=YYYY;
			tabs=countTabs(line);
			afterEq=line.substring(EQi+1,EOLi).trim();
			nodeAr=line.substring(doti,EQi).trim();
			if(nodeAr.endsWith("[]"))
				nodeAr=nodeAr.substring(0,nodeAr.length()-2);
			if(afterEq.equalsIgnoreCase("null")){
				line=line.substring(0,nodeTagi)+"\n"+tabs(tabs)+"// Empty the array\n"+tabs(tabs)+
					"$this->"+nodeAr+"_array_data=array();\n"+tabs(tabs)
					+"$this->"+nodeAr+"=array();"
					+line.substring(EOLi+1);
			}else if(afterEq.startsWith("new")){
				String biz=afterEq.substring(3).substring(0,afterEq.indexOf("(")-3);
				String codes="\n"+tabs(tabs)+"// Add new Node to the array\n" +
								tabs(tabs)+"$_index=count($this->"+nodeAr+"_array_data);\n" +
								tabs(tabs)+"$_data=array();\n" +
								tabs(tabs)+"$_data['parent']=$this;\n" +
								tabs(tabs)+"$_data['bizname']=$_index;\n" +
								tabs(tabs)+"$_data['fullname']=$this->_fullname.'_'.$_index;\n" +
								tabs(tabs)+"$this->"+nodeAr+"_array_data[]=$_data;\n" +
								tabs(tabs)+"$this->"+nodeAr+"[]=new "+biz+"($this->"+nodeAr+"_array_data[$_index]);";
				line=line.substring(0,nodeTagi)+codes+line.substring(EOLi+1);
			}
		}

		return line;
	}

	private static int countTabs(String line){
		int tabs=0;
		while(line.substring(tabs).startsWith("\t"))
			tabs++;
		return tabs;
	}

	private static String tabs(int count){
		String tabs="";
		for(int i=0;i<count;i++)
			tabs=tabs+"\t";
		return tabs;
	}
	private static String nodeIDTag(String line){
		return replace(line,"#nodeID","$this->_fullname");
	}

	private static String toStringTags(String line){
		int i,wi;
		for(String s:toStringTags){
			i=line.indexOf("#"+s+".");
			while(i!=-1){
				wi=endOfWordIndex(line.substring(i+2+s.length()))+i+2+s.length();
				line=line.substring(0,i)+"\""+line.substring(i+2+s.length(),wi)+"\""+line.substring(wi);
				i=line.indexOf("#"+s+".");
			}
		}
		return line;
	}

	private static int endOfWordIndex(String line){
		int i=0;
		for(;i<line.length();i++)
			if(validVarChars.indexOf(line.charAt(i))==-1)
				break;
		return i;
	}

	private static String dbTags(String line){
		line=replace(line,"#db.",PHPClass.Name+"_");
		return line;
	}

	private static String toThisTags(String line){
		for(String s:toThisTags)
			line=replace(line,"#"+s+".","$this->");
		return line;
	}

	private static String replace(String line,String exp,String rep){
		return replace(line,exp,rep,true);
	}

	private static String replace(String line,String exp,String rep,boolean repeat){
		int i=line.indexOf(exp);
		while(i!=-1){
			line=line.substring(0,i)+rep+line.substring(i+exp.length());
			i=line.indexOf(exp);
			if(!repeat)
				i=-1;
		}
		return line;
	}
}
