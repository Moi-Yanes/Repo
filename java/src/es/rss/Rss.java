package es.rss;

// El Día: http://eldia.es/rss/
// La Opinión: http://www.laopinion.es/servicios/rss/rss.jsp

public class Rss {
	
	String url;
	String tipoRss;
	String periodico;
	
	public String getUrl() {
		return url;
	}

	public void setUrl(String url) {
		this.url = url;
	}

	public String getTipoRss() {
		return tipoRss;
	}

	public void setTipoRss(String tipoRss) {
		this.tipoRss = tipoRss;
	}

	public String getPeriodico() {
		return periodico;
	}

	public void setPeriodico(String periodico) {
		this.periodico = periodico;
	}

	// Constantes
	public static String TITLE = "title";
	public static String LINK = "link";
	public static String PUBLIC_DATE = "pubDate";	
	public static String DESCRIPCION = "description";
	
	public Rss(String url, String tipo, String periodico) {
		this.url = url;
		this.tipoRss = tipo;
		this.periodico = periodico;
	}
	
	// RSS
	public static Rss[] vectorRss = { 
		new Rss ("http://eldia.es/rss/santacruz.rss", "Santa Cruz de Tenerife", "El Día"), 
		new Rss ("http://eldia.es/rss/sociedad.rss", "Sociedad", "El Día Sociedad"), 
		new Rss ("http://eldia.es/rss/sucesos.rss", "Sucesos", "El Día Sucesos"),
		new Rss ("http://eldia.es/rss/canarias.rss", "Canarias", "El Día Canarias"),
		new Rss ("http://eldia.es/rss/laguna.rss", "La Laguna", "El Día La Laguna"),
		new Rss ("http://eldia.es/rss/economia.rss", "Economía", "El Día La Economía"),
		new Rss ("http://eldia.es/rss/nacional.rss", "Nacional", "El Día Nacional"),
//		new Rss ("http://www.laopinion.es/elementosInt/rss/2", "Tenerife", "La Opinión"),
		new Rss ("http://www.laopinion.es/elementosInt/rss/91", "La Laguna", "La Opinión"),
		new Rss ("http://www.laopinion.es/elementosInt/rss/98", "Santa Cruz de Tenerife", "La Opinión"),
		new Rss ("http://www.laopinion.es/elementosInt/rss/42", "Cabildo de Tenerife", "La Opinión Cabildo de Tenerife")
	};
	
}
