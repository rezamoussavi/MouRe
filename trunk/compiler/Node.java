
public class Node {
	public String node;
	public String biz;
	public boolean isArray;
	public boolean isTemp;

	public Node(){
	}

	public Node(SecElement se){
		String s=se.data.trim();
		int spc=s.indexOf(' ');
		isTemp=false;
		if(spc>0){
			biz=s.substring(0,spc).trim();
			node=s.substring(spc).trim();
		}else{
			biz=s;
			node="";
			isTemp=true;
		}
		isArray=false;
		if(endWithBrackets(biz)){
			biz=biz.substring(0,biz.length()-2);
			isArray=true;
		}
		if(endWithBrackets(node)){
			node=node.substring(0,node.length()-2);
			isArray=true;
		}
	}

	private boolean endWithBrackets(String s){
		if(s.length()<3)
			return false;
		return s.substring(s.length()-2).equals("[]");
	}
}
