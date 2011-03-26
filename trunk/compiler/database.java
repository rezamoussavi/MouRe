
public class database {

	public String Name="";
	public String Info="";

	public database(SecElement se){
		String s=se.data.trim();
		int i=s.indexOf("(");
		if(i>0){
			Name=s.substring(0,i).trim();
			s=s.substring(i+1,s.length()-1);
			Info= s.replace(';', ',').replace('\'', '`');
		}
	}
}
