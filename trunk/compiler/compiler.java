import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Scanner;

public class compiler {

	public ArrayList<Section> sections;
	String FileName;

	public static void main(String[] args) {
		String input="";
		while(!input.equalsIgnoreCase("exit")){
			System.out.print("command> ");
			input=getInput();
			loadAndCompile(input);
		}
	}

	public static String getInput(){
		Scanner scan= new Scanner(System.in);
		return scan.nextLine();
	}

	public static void loadAndCompile(String fname){
		BufferedReader br;
		String FileName=fname;
		if(FileName.length()<20){
			FileName="C:\\MouReBackup\\trunk\\src\\biz\\"+fname+"\\"+fname;
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
		sections=new ArrayList<Section>();
		Section sec=null;
		String line="";
		while(br.ready()){
			line=br.readLine();
			if(isNewSection(line)){
				if(sec!=null){
//					sections.add(sec);
					php.applySection(sec);
				}
				sec=new Section(line);
				continue;
			}
			if(line.length()>0)
				sec.add(line);
		}
		sections.add(sec);
		php.applySection(sec);
//		transfer();
		System.out.println("\tCompiled");
		FileWriter fw=new FileWriter(FileName);
		fw.write(php.toString());
		fw.close();
	}

//	public void transfer(){
//		PHPClass php=new PHPClass();
//		for(Section sec:sections)
//			php.applySection(sec);
//		System.out.println(php);
//	}

	public boolean isNewSection(String line){
		if(line.length()>0)
			return ((line.charAt(0)=='#'));
		return false;
	}
}
