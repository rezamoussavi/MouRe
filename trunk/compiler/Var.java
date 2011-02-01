
public class Var {
	public String name;
	public String init;

	public Var(){
		this(new SecElement());
	}

	public Var(SecElement se){
		name="";
		init="''";
		String s=se.data.trim();
		if(s.length()>0){
			int eq=s.indexOf('=');
			if(eq==-1){
				name=s;
			}else{
				name=s.substring(0,eq).trim();
				init=s.substring(eq+1).trim();
			}
		}
	}

	public boolean isInit(){
		return init.length()!=0;
	}
}
