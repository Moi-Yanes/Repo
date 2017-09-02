package es.rss;

import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.List;

import org.jdom.Document;
import org.jdom.Element;
import org.jdom.input.SAXBuilder;

public class CapturarRSS {
	
	public List<Noticia> listaNoticiasClasificadas = new ArrayList<Noticia>();
	
	public static String TITLE = "title";
	public static String LINK = "link";
	public static String PUBLIC_DATE = "pubDate";

	public void obtenerInserts() {
		for (int i = 0; i < Rss.vectorRss.length; i++)
			obtenerInserts(Rss.vectorRss[i]);
	}
	
	public void obtenerInserts(Rss rss) {
		SAXBuilder builder = new SAXBuilder();
		try {
			Document document = (Document) builder.build(Utils.TransformFile(rss, StandardCharsets.ISO_8859_1));
			Element rootNode = document.getRootElement();
			List<Element> list = rootNode.getChildren(Utils.CHANNEL);
			
			for (int i = 0; i < list.size(); i++) {
				Element node = (Element) list.get(i);

				List<Element> nodeChild = (List<Element>) node.getChildren(Utils.ITEM);
				for (int j = 0; j < nodeChild.size(); j++) {
					Noticia noticia = new Noticia(rss, (Element) nodeChild.get(j));
					listaNoticiasClasificadas.add(noticia);
				}
			}
			
		}
		catch (Exception e) {
			e.printStackTrace();
			System.out.println("Exception" + e.getMessage());
		};
	}

}
