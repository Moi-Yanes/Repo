package es.rss;

import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLConnection;
import java.nio.charset.Charset;
import java.nio.charset.StandardCharsets;

public class Utils {
	
	public static String CHANNEL = "channel";
	public static String ITEM = "item";	
	
	public static ByteArrayInputStream TransformFile(Rss rss, Charset charter) throws IOException, InterruptedException {
		System.out.println("Url: " + rss.url);
		URL url = new URL(rss.url);
	    URLConnection conn = url.openConnection();
	    InputStream io = conn.getInputStream();
	    System.out.println("Inpute Stream: " + io + " : " + rss.url);
	    BufferedReader br = new BufferedReader(new InputStreamReader(io, "Cp1252"));
	    System.out.println("br: " + br);
		StringBuilder sb = new StringBuilder();

		String line;
		while ((line = br.readLine()) != null) {
			sb.append(Utils.tratar(line));
		}
		System.out.println("sb: " + sb);
//		File file = new File(rss.periodico + ".txt");
//		FileOutputStream fop = new FileOutputStream(file, false);
//		fop.write(sb.toString().getBytes());
//		fop.flush();
//		fop.close();
		
//		InputStreamReader targetStream = new InputStreamReader(new FileInputStream(file), "Cp1252");
		return new ByteArrayInputStream(sb.toString().getBytes(charter));
	}		

	public static String tratar (String in) {
		String out = in;
		out = out.replaceAll("<p>", "");
		out = out.replaceAll("</p>", "");
		out = out.replaceAll("'", "");
		out = out.replaceAll("&agrave;","à");
		out = out.replaceAll("&aacute;","á");
		out = out.replaceAll("&Aacute;","Á");
		out = out.replaceAll("&Agrave;","À");
		out = out.replaceAll("&eacute;","é");
		out = out.replaceAll("&Eacute;","É");
		out = out.replaceAll("&iacute;","í");
		out = out.replaceAll("&Iacute;","Í");
		out = out.replaceAll("&oacute;","ó");
		out = out.replaceAll("&Oacute;","Ó");
		out = out.replaceAll("&uacute;","ú");
		out = out.replaceAll("&Uacute;","Ú");
		out = out.replaceAll("&ntilde;","ñ");
		out = out.replaceAll("&Ntilde;","Ñ");
		out = out.replaceAll("&iquest;","¿");
		out = out.replaceAll("&egrave;","è");		
		out = out.replaceAll("\n", "");
		return out;
	}
	
}
