
public class PHPLine {

	private static String[] toThisTags={"var","node","fun","function","phpfunction"};
	private static String[] toStringTags={"frame","frm","msg","message"};
	private static String validVarChars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_";

	public static String parse(String line){
		line=messageInForms(line);//Should be before toThisTags
		line=msgInForms(line);//Should be before toThisTags
		line=msgToEvent(line);
		line=toThisTags(line);
		line=dbTags(line);
		line=toStringTags(line);
		line=replace(line,"#nodeID","$this->_fullname");
		line=replace(line, "<PHTML>", "<<<PHTMLCODE\n");
		line=replace(line, "</PHTML>;", "\nPHTMLCODE;\n");
		line=replace(line, "</PHTML>", "\nPHTMLCODE;\n");
		line=replace(line, "_bookframe", "$this->_bookframe");
		line=replace(line, "_bookFrame", "$this->_bookframe");
		line=replace(line, "_backFrame", "_backframe");
		line=replace(line, "osBackLink(", "osBackLink($this->_fullname,");
		line=replace(line, "osBackLink (", "osBackLink($this->_fullname,");
		line=replace(line, "osBackLinkInfo(", "osBackLinkInfo($this->_fullname,");
		line=replace(line, "osBackLinkInfo (", "osBackLinkInfo($this->_fullname,");
		line=replace(line, "osLog(", "osLog('"+PHPClass.Name+"',$this->_fullname,");
		line=replace(line, "osLog (", "osLog('"+PHPClass.Name+"',$this->_fullname,");
		return line;
	}

	private static String msgToEvent(String line){
		line=replace(line, "sndmsg (", "sndmsg(");
		line=replace(line, "sndmsg( '", "sndmsg('");
		line=replace(line, "sndmsg( \"", "sndmsg(\"");
		line=replace(line, "sndmsg('#msg->", "sndevent('{$this->_fullname}','frame_");
		line=replace(line, "sndmsg(\"#msg->", "sndevent(\"{$this->_fullname}\",\"frame_");
		return line;
	}
	private static String messageInForms(String line){
		int eom=0,i=line.indexOf("<#message->");
		String msg="";
		while(i!=-1){//there is a "<#message->"
			eom=endOfWordIndex(line.substring(i+11))+i+11;
			msg=line.substring(i+11,eom);
			line=line.substring(0,i)
				+"<input type=\"hidden\" name=\"_message\" value=\"frame_"+msg+"\" />"
				+"<input type = \"hidden\" name=\"_target\" value=\"{$this->_fullname}\" />"
				+line.substring(eom+1);
			i=line.indexOf("<#message->");
		}
		return line;
	}

	private static String msgInForms(String line){
		int eom=0,i=line.indexOf("<#msg->");
		String msg="";
		while(i!=-1){//there is a "<#msg->"
			eom=endOfWordIndex(line.substring(i+7))+i+7;
			msg=line.substring(i+7,eom);
			line=line.substring(0,i)
				+"<input type=\"hidden\" name=\"_message\" value=\"frame_"+msg+"\" />"
				+"<input type = \"hidden\" name=\"_target\" value=\"{$this->_fullname}\" />"
				+line.substring(eom+1);
			i=line.indexOf("<#msg->");
		}
		return line;
	}

	private static String toStringTags(String line){
		int i,wi;
		String family="";
		for(String s:toStringTags){
			if(s.equalsIgnoreCase("msg"))
				family=PHPClass.Family+"_";
			else
				family="";
			i=line.indexOf("#"+s+"->");
			while(i!=-1){
				wi=endOfWordIndex(line.substring(i+3+s.length()))+i+3+s.length();
				line=line.substring(0,i)+"\""+family+line.substring(i+3+s.length(),wi)+"\""+line.substring(wi);
				i=line.indexOf("#"+s+"->");
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
		line=replace(line,"#db->",PHPClass.Name+"_");
		return line;
	}

	private static String toThisTags(String line){
		for(String s:toThisTags)
			line=replace(line,"#"+s+"->","$this->");
		return line;
	}

	private static String replace(String line,String exp,String rep){
		return replace(line,exp,rep,true);
	}

	private static String replace(String line,String exp,String rep,boolean repeat){
		int i=line.indexOf(exp,0);
		while(i!=-1){
			line=line.substring(0,i)+rep+line.substring(i+exp.length());
			i=line.indexOf(exp,i+rep.length());
			if(!repeat)
				i=-1;
		}
		return line;
	}
}
