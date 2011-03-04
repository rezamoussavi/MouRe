import java.io.File;
import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.HttpVersion;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.entity.mime.content.ContentBody;
import org.apache.http.entity.mime.content.FileBody;
import org.apache.http.entity.mime.content.StringBody;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.params.CoreProtocolPNames;
import org.apache.http.util.EntityUtils;

public class PostFile {

	public static boolean Post(String FileName){
		return Post("http://isoor.com:80/uploader/uploader.php","",FileName);
	}

	public static boolean Post(String URL,String biz,String FileName){
		HttpClient httpclient = new DefaultHttpClient();
		httpclient.getParams().setParameter(CoreProtocolPNames.PROTOCOL_VERSION, HttpVersion.HTTP_1_1);

		HttpPost httppost = new HttpPost(URL);
		File file = null ;
		MultipartEntity mpEntity= null;
		ContentBody cbFile = null;

		try{

			file = new File(FileName);
			mpEntity = new MultipartEntity();
			cbFile = new FileBody(file, "image/jpeg");
			mpEntity.addPart("file", cbFile);
			mpEntity.addPart("biz", new StringBody(biz));
		}catch(Exception e){
			//System.out.println("file not found ");
			return false;
		}

		httppost.setEntity(mpEntity);
		//System.out.println("executing request " + httppost.getRequestLine());
		HttpResponse response;
		try {
			response = httpclient.execute(httppost);
		} catch (Exception e) {
			return false;
		}
		HttpEntity resEntity = response.getEntity();

		//System.out.println(response.getStatusLine());
		if (resEntity != null) {
			try {
				return EntityUtils.toString(resEntity).endsWith("ok");
			} catch (Exception e) {
				return false;
			}
	    }
		httpclient.getConnectionManager().shutdown();
		return false;
	}
}
