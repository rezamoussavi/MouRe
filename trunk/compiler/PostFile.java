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

	public static boolean Regdb(String URL, String biz){
		URL+="?regdb="+biz;
		HttpClient httpclient = new DefaultHttpClient();
		httpclient.getParams().setParameter(CoreProtocolPNames.PROTOCOL_VERSION, HttpVersion.HTTP_1_1);

		HttpPost httppost = new HttpPost(URL);
		MultipartEntity mpEntity= null;
		try{
			mpEntity = new MultipartEntity();
		}catch(Exception e){
			return false;
		}

		httppost.setEntity(mpEntity);
		HttpResponse response;
		try {
			response = httpclient.execute(httppost);
		} catch (Exception e) {
			return false;
		}
		HttpEntity resEntity = response.getEntity();
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

	public static boolean AppendJS(String URL, String biz){
		URL+="?appendJS="+biz;
		HttpClient httpclient = new DefaultHttpClient();
		httpclient.getParams().setParameter(CoreProtocolPNames.PROTOCOL_VERSION, HttpVersion.HTTP_1_1);

		HttpPost httppost = new HttpPost(URL);
		MultipartEntity mpEntity= null;
		try{
			mpEntity = new MultipartEntity();
		}catch(Exception e){
			return false;
		}

		httppost.setEntity(mpEntity);
		HttpResponse response;
		try {
			response = httpclient.execute(httppost);
		} catch (Exception e) {
			return false;
		}
		HttpEntity resEntity = response.getEntity();
		if (resEntity != null) {
			try {
				boolean ret=EntityUtils.toString(resEntity).endsWith("ok");
				return ret;
			} catch (Exception e) {
				return false;
			}
	    }
		httpclient.getConnectionManager().shutdown();
		return false;
	}

	public static boolean AppendCSS(String URL, String biz){
		URL+="?appendCSS="+biz;
		HttpClient httpclient = new DefaultHttpClient();
		httpclient.getParams().setParameter(CoreProtocolPNames.PROTOCOL_VERSION, HttpVersion.HTTP_1_1);

		HttpPost httppost = new HttpPost(URL);
		MultipartEntity mpEntity= null;
		try{
			mpEntity = new MultipartEntity();
		}catch(Exception e){
			return false;
		}

		httppost.setEntity(mpEntity);
		HttpResponse response;
		try {
			response = httpclient.execute(httppost);
		} catch (Exception e) {
			return false;
		}
		HttpEntity resEntity = response.getEntity();
		if (resEntity != null) {
			try {
				boolean ret=EntityUtils.toString(resEntity).endsWith("ok");
				return ret;
			} catch (Exception e) {
				return false;
			}
	    }
		httpclient.getConnectionManager().shutdown();
		return false;
	}

	public static boolean Post(String URL,String biz,String FileName,String FileExtension){
		HttpClient httpclient = new DefaultHttpClient();
		httpclient.getParams().setParameter(CoreProtocolPNames.PROTOCOL_VERSION, HttpVersion.HTTP_1_1);

		HttpPost httppost = new HttpPost(URL);
		File file = null ;
		MultipartEntity mpEntity= null;
		ContentBody cbFile = null;
		try{
			file = new File(FileName+"."+FileExtension);
			mpEntity = new MultipartEntity();
			cbFile = new FileBody(file, "application/octet-stream");
			mpEntity.addPart("file", cbFile);
			mpEntity.addPart("biz", new StringBody(biz));
			mpEntity.addPart("ext", new StringBody(FileExtension));
		}catch(Exception e){
			return false;
		}

		httppost.setEntity(mpEntity);
		HttpResponse response;
		try {
			response = httpclient.execute(httppost);
		} catch (Exception e) {
			return false;
		}
		HttpEntity resEntity = response.getEntity();
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