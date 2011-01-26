
public class Message {

	public String msg;
	public String fun;

	public Message(){
		this(new SecElement());
	}

	public Message(SecElement se){
		msg="";
		fun="";
		String s=se.data.trim();
		if(s.length()>0){
			int eq=s.indexOf("=");
			if(eq==-1){
				msg=s;
			}else{
				msg=s.substring(0,eq).trim();
				fun=s.substring(eq+1).trim();
			}
		}
		msg=msg.replaceAll("->", "_").replaceAll(" _", "_").replaceAll("_ ", "_");
	}

	public boolean isCallBack(){
		return fun.length()!=0;
	}
}
