import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.Arrays;
import java.util.List;
import java.util.Scanner;

public class compiler {

	String FileName;

	public static void main(String[] args) {
		List<String> bizes=Arrays.asList("adminpanel","history","mainviewer","multipageviewer","product","productlistviewer","productviewer","profile","purchase","purchaseviewer","referal","subscribe","tab","tabbank","userpanel","userpanelviewer","usertab","usertabbank","purchaseviewer","login","user");
		String input="";
		while(!input.equalsIgnoreCase("exit")){
			System.out.print("command> ");
			input=getInput();
			if(input.equalsIgnoreCase("all"))
				for(String s:bizes)
					loadAndCompile(s);
			else
				loadAndCompile(input);
		}
	}

	public static String getInput(){
		Scanner scan= new Scanner(System.in);
		return scan.nextLine();
	}

	public static void loadAndCompile(String fname){
		System.out.print("\t"+fname+"...");
		BufferedReader br;
		String FileName=fname;
		if(FileName.length()<20){
			FileName="C:\\MouRe\\trunk\\kopon\\biz\\"+fname+"\\"+fname;
		}
		try {
			br=new BufferedReader(new FileReader( new File(FileName+".biz")));
		} catch (Exception e) {
			System.out.println("ERROR Reading File");
			return;
		}
		try {
			new compiler(br,FileName);
			br.close();
		} catch (IOException e) {
			System.out.println("ERROR while reading file: "+e.toString());
		}		
	}
	public compiler(BufferedReader br,String File) throws IOException{
		this.FileName=File+".php";
		PHPClass php=new PHPClass();
		Section sec=null;
		String line="";
		while(br.ready()){
			line=br.readLine();
			if(isNewSection(line)){
				if(sec!=null){
					php.applySection(sec);
				}
				sec=new Section(line);
				continue;
			}
			if(line.length()>0)
				sec.add(line);
		}
		php.applySection(sec);
		System.out.println(" Compiled!");
		FileWriter fw=new FileWriter(FileName);
		fw.write(php.toString());
		fw.close();
	}

	public boolean isNewSection(String line){
		if(line.length()>0)
			return ((line.charAt(0)=='#'));
		return false;
	}
}
