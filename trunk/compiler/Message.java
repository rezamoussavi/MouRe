
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
			int eq=s.indexOf("->");
			if(eq==-1){
				msg=s;
			}else{
				msg=s.substring(0,eq).trim();
				fun=s.substring(eq+2).trim();
			}
		}
	}

	public boolean isCallBack(){
		return fun.length()!=0;
	}
}
