
public class Frame {
	public String Name;
	public String Style;
	public boolean Stared;

	public Frame(SecElement se){
		String s=se.data.trim();
		int i=s.indexOf("(");
		if(i>0){
			Name=s.substring(0,i);
			s=s.substring(i+1,s.length()-1);
			Style=" style=\""+s+"\" ";
		}else{
			Name=s;
			Style="";
		}
		Stared=Name.charAt(0)=='*';
		if(Stared){
			Name=Name.substring(1);
		}
	}
}
