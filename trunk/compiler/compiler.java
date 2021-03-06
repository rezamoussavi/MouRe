import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Scanner;

public class compiler {

	public static String UpServer;
	public static String Server;
	public static String BizFolder;
	public static boolean DoUpload;

	public static void main(String[] args) {
		DoUpload=true;
		if(!LoadConfig())
			return;
		File Dir=new File(BizFolder);
		String Dirs[]=Dir.list();
		ArrayList<String> bizes=new ArrayList<String>();
		for(int i=0;i<Dirs.length;i++){
			bizes.add(Dirs[i]);
			System.out.println("Found ... "+Dirs[i]);
		}
		String input="";
		while(!input.equalsIgnoreCase("exit")){
			System.out.print("\ncommand> ");
			input=getInput();if(input.equalsIgnoreCase("exit")) break;
			int ok=0;int err=0;
			if(input.equalsIgnoreCase("re")){
				for(String s:bizes)
					if(loadAndCompile(s))	ok++; else err++;
				System.out.print("\n\n\t"+(ok+err)+"\tTotal\n\t"+ok+"\tCompiled/Uploaded\n\t"+err+"\tError");
			}else if(input.equalsIgnoreCase("all")){
				for(String s:bizes)
					if(hasChanged(s)) if(loadAndCompile(s))	ok++; else err++;
				System.out.print("\n\n\t"+(ok+err)+"\tTotal\n\t"+ok+"\tCompiled/Uploaded\n\t"+err+"\tError");
			}else if(input.equalsIgnoreCase("help") || input.equalsIgnoreCase("?")){
				System.out.println("\n--- HELP ----");
				System.out.println("\tbizname: compile and upload the biz");
				System.out.println("\t-bizname: compile but not upload the biz");
				System.out.println("\tall: compile and upload all changed bizes since last compile");
				System.out.println("\t-all: compile but not upload all changed bizes since last compile");
				System.out.println("\tre: compile and upload all(old and new) bizes");
				System.out.println("\t-re: compile but not upload all(old and new) bizes");
				System.out.println("--- HELP ---");
			}
			else
				loadAndCompile(input);
		}
	}

	public static boolean hasChanged(String fname){
		String bizFileName=BizFolder+fname+"/"+fname+".biz";
		String phpFileName=BizFolder+fname+"/"+fname+".php";
		File bizFile=new File(bizFileName);
		File phpFile=new File(phpFileName);
		return bizFile.lastModified()>phpFile.lastModified();
	}

	public static boolean LoadConfig(){
		String line="";
		try {
			BufferedReader br=new BufferedReader(new FileReader(new File("config.ini")));
			while(line!=null){
				line= br.readLine();
				if(line!=null)
					if(line.trim().equalsIgnoreCase("[server]")){
						compiler.Server= br.readLine();
						compiler.UpServer=compiler.Server+"uploader.php";
					}
					else if(line.trim().equalsIgnoreCase("[bizfolder]"))
						compiler.BizFolder= br.readLine();
			}
		} catch (FileNotFoundException e) {
			System.out.println("config.ini: file not found / content error!\n"+e.getMessage());
			return false;
		} catch (IOException e) {
			System.out.println("config.ini: reading error!\n"+e.getMessage());
			return false;
		}
		return true;
	}

	public static String getInput(){
		Scanner scan= new Scanner(System.in);
		String s=scan.nextLine();
		DoUpload=true;
		if(s.length()>0) if(s.charAt(0)=='-'){
			DoUpload=false;
			s=s.substring(1);
		}
		return s;
	}

	public static boolean loadAndCompile(String fname){
		System.out.print("\n\t"+fname+"...\n\t\t");
		BufferedReader br;
		String FileName=BizFolder+fname+"/"+fname;
		try {
			br=new BufferedReader(new FileReader( new File(FileName+".biz")));
		} catch (Exception e) {
			System.out.print("ERROR Reading File");
			return false;
		}
		try {
			new compiler(br,FileName);
			br.close();
			return true;
		} catch (IOException e) {
			System.out.print("ERROR: "+e.getMessage());
			return false;
		}		
	}
	@SuppressWarnings("static-access")
	public compiler(BufferedReader br,String File) throws IOException{
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
		System.out.print(" Compiled!");
		//
		// Saving .php Compiled File
		//
		FileWriter fw=new FileWriter(File+".php");
		fw.write(php.toString());
		fw.close();
		//
		// Saving .sql Compiled File
		//
		if(php.hasSql()){
			FileWriter qfw=new FileWriter(File+".sql");
			qfw.write(php.sqlString());
			qfw.close();
		}
		//
		// Saving .js Compiled File
		//
		if(php.hasJS()){
			FileWriter jfw=new FileWriter(File+".js");
			jfw.write(php.jsString());
			jfw.close();
		}
		//
		// Saving .css Compiled File
		//
		if(php.hasCSS()){
			FileWriter cfw=new FileWriter(File+".css");
			cfw.write(php.cssString());
			cfw.close();
		}
		if(DoUpload){
			//
			// Uploading .php file
			//
			System.out.print(" [up php]");
			if(PostFile.Post(UpServer, php.Name, File,"php"))
				System.out.print(" ok!");
			else{
				throw new IOException("Cannot upload php to Server: <"+UpServer+">");
			}
			//
			// Uploading .biz file
			//
			System.out.print(" [up biz]");
			if(PostFile.Post(UpServer, php.Name, File,"biz"))
				System.out.print(" ok!");
			else{
				throw new IOException("Cannot upload biz to Server: <"+UpServer+">");
			}
			//
			// Uploading .css file
			//
			if(php.hasCSS()){
				System.out.print(" [up css]");
				if(PostFile.Post(UpServer, php.Name, File,"css"))
					System.out.print(" ok!");
				else{
					throw new IOException("Cannot upload CSS to Server: <"+UpServer+">");
				}
				//
				// Append to main .css
				//
				System.out.print(" [apnd CSS]");
				if(PostFile.AppendCSS(Server, php.Name))
					System.out.print(" ok!");
				else{
					throw new IOException("Cannot Append CSS on Server: <"+Server+">");
				}
			}
			//
			// Uploading .js file
			//
			if(php.hasJS()){
				System.out.print(" [up JS]");
				if(PostFile.Post(UpServer, php.Name, File,"js"))
					System.out.print(" ok!");
				else{
					throw new IOException("Cannot upload JS to Server: <"+UpServer+">");
				}
				//
				// Append to main .JS
				//
				System.out.print(" [apnd JS]");
				if(PostFile.AppendJS(Server, php.Name))
					System.out.print(" ok!");
				else{
					throw new IOException("Cannot Append JS on Server: <"+Server+">");
				}
			}
			//
			// Uploading .sql file
			//
			if(php.hasSql()){
				System.out.print(" [up DB]");
				if(PostFile.Post(UpServer, php.Name, File,"sql"))
					System.out.print(" ok!");
				else{
					throw new IOException("Cannot upload DB to Server: <"+UpServer+">");
				}
				//
				// Creating Database
				//
				System.out.print(" [bld DB]");
				if(PostFile.Regdb(Server, php.Name))
					System.out.print(" ok!");
				else{
					throw new IOException("Cannot Create DB on Server: <"+Server+">");
				}
			}
		}
	}

	public boolean isNewSection(String line){
		if(line.length()>0)
			return ((line.charAt(0)=='#'));
		return false;
	}
}
