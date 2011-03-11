
public class Biz {

	public String bizName;
	public String Family;

	public Biz(SecElement se){
		Family="NULL";
		String s=se.data.trim();
		int i=s.indexOf(":");
		if(i>0){
			bizName=s.substring(0, i);
			Family=s.substring(i+1);
		}else{
			bizName=s;
		}
	}
}
