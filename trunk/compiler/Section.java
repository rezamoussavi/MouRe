import java.util.ArrayList;

public class Section {
	String name;
	ArrayList<SecElement> elements;
	private boolean cs;//Comma Separated
	private static String[] csTypes={"node","var","message","frame","db"};// Comma separated sections

	public Section(String line){
		elements=new ArrayList<SecElement>();
		line=line.trim();

		int spc=line.indexOf(' ');// first Space Index
		if(spc==-1)	name=line.substring(1);
		else		name=line.substring(1, spc);
		name=name.toLowerCase();
		cs=false;
		for(String t:csTypes)
			if(name.equalsIgnoreCase(t))
				cs=true;
		if(name.equals("phpfunction"))
			add("\n\n//########################################" +
				"\n//         YOUR FUNCTIONS GOES HERE" +
				"\n//########################################\n\n");
		if(spc!=-1)
			add(line.substring(spc).trim());

	}

	public void add(String line){
		if(cs){
			String[] els=line.trim().split(",");
			for(String e:els){
				String data=e.trim();
				if(data.length()>0)
					elements.add(new SecElement(data));
			}
		}else{
			append(line);
		}
	}

	public void append(String line){
		if(name.equals("phpfunction"))
			line=PHPLine.parse(line);
		if(elements.size()==0)
			elements.add(new SecElement(line+"\n"));
		else
			elements.get(0).append(line+"\n");
	}

	@Override
	public String toString(){
		return "\n["+name+"]\n"+elements+"\n#################################\n";
	}
}
