package es.rss;

import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;

import org.jdom.Element;

public class Noticia {
	
	final String ANYO = "2016";
	
	public String titular;
	public String descripcion;
	public String enlace;
	public String tipo_noticia;
	public String periodico;
	public Date fecha;
	
	// Tipos Noticias
	final String SUCESO = "Suceso";
	final String SOCIEDAD = "Sociedad";

	// Lugares
	String[][] Lugares = {
			{"Acorán",                                          "Acorán"},
			{"Anaga",                                           "Anaga"},
			{"La cantera de Jagua",                             "Anaga"},
			{"Las Gaviotas",                                    "Anaga"},
			{"Las Huertas",                                     "Anaga"},
			{"Roque Negro",                                     "Anaga"},
			{"Añaza",                                           "Añaza"},
			{"Luis Celso",                                      "Añaza"},
			{"Barranco Grande",                                 "Barranco Grande"},
			{"Barrio de La Alegría",                            "Barrio de La Alegría"},
			{"Barrio de Salamanca",                             "Barrio de Salamanca"},
			{"Auditorio",                                       "Cabo Llanos"},
			{"Cabo-Llanos",                                     "Cabo Llanos"},
			{"Centro comercial Meridiano",                      "Cabo Llanos"},
			{"CC Meridiano",                                    "Cabo Llanos"},
			{"La Refinería",                                    "Cabo Llanos"},
			{"Parque Científico y Tecnológico de Tenerife",     "Cabo Llanos"},
			{"Parque Marítimo",                                 "Cabo Llanos"},
			{"PCTT",                                            "Cabo Llanos"},
			{"Recinto Ferial",                                  "Cabo Llanos"},
			{"Traysesa",                                        "Cuevas Blancas"},
			{"Los Llanos",                                      "Darsena"},
			{"Duggi",                                           "Duggi"},
			{"Sobradillo",                                      "El Sobradillo"},
			{"El Tablero",                                      "El Tablero"},
			{"Toscal",                                          "El Toscal"},
			{"Escámez",                                         "García Escámez"},
			{"Escamez",                                         "García Escámez"},
			{"Ifara",                                           "Ifara"},
			{"Línea 2",                                         "La Gallega"},
			{"La Gallega",                                      "La Gallega"},
			{"Casa del Carnaval",                               "La Noria"},
			{"Fufa",                                            "La Noria"},
			{"Ni Fú-Ni Fá",                                     "La Noria"},
			{"La Noria",                                        "La Noria"},
			{"Sociedad Mamels",                                 "La Noria"},
			{"Palacio de Deportes",                             "La Salle"},
			{"Salle",                                           "La Salle"},
			{"La Salud",                                        "La Salud"},
			{"Las Delicias",                                    "Las Delicias"},
			{"Las Moraditas",                                   "Las Moraditas"},
			{"Los Gladiolos",                                   "Los Gladiolos"},
			{"Campitos",                                        "Los Campitos"},
			{"María Jiménez",                                   "María Jiménez"},
			{"Muelle Norte",                                    "Muelle Norte"},
			{"La Candelaria",                                   "Ofra"},               
			{"CIFP César Manrique",                             "Ofra"},               
			{"Ofra",                                            "Ofra"},
			{"Casa Cuna",                                       "Ofra"},
			{"Miramar",                                         "Ofra"},
			{"Barranco del Hierro",                             "Ofra"},
			{"La Campana",                                      "Polígono La Campana"},
			{"Mamotreto",                                       "San Andrés"},
			{"San Andrés",                                      "San Andrés"},
			{"Teresitas",                                       "San Andrés"},
			{"Barranco del Cercado",                            "San Andrés"},
			{"Parque La Estrella",                              "Santa María del Mar"},
			{"Santa María del Mar",                             "Santa María del Mar"},
			{"Somosierra",                                      "Somosierra"},
			{"Suroeste",                                        "Suroeste"},
			{"Montaña de Taco",                                 "Taco"},
			{"Taganana",                                        "Taganana"},
			{"Tincer",                                          "Tincer"},
			{"Tío Pino",                                        "Tío Pino"},
			{"Cueva Roja",                                      "Valle Jiménez"},
			{"Valleseco",                                       "Valleseco"},
			{"Residencia San Pancracio",                        "Vistabella"},
			{"Bravo Murillo",                                   "Zona Centro"},
			{"Calle Candelaria",                                "Zona Centro"},
			{"Carnaval 2017",                                   "Zona Centro"},
			{"La Recova",                                       "Zona Centro"},
			{"Méndez Núñez",                                    "Zona Centro"},
			{"Museo de Bellas Artes",                           "Zona Centro"},
			{"Quiosco La Paz",                                  "Zona Centro"},
			{"Plaza de Toros",                                  "Zona Centro"},
			{"Plaza del Príncipe",                              "Zona Centro"},
			{"Rambla de Pulido",                                "Zona Centro"},
			{"Real Casino",                                     "Zona Centro"},
			{"Tres de Mayo",                                    "Zona Centro"},
			{"Villalba Hervás",                                 "Zona Centro"},
			{"Sanabria",                                        "Zona Centro"},
			{"Viera y Clavijo",                                 "Zona Centro"},
			{"Weyler",                                          "Zona Centro"},
			{"Zona Comercial Tranvía",                          "Zona Centro"},
			
			{"Cruceros",                                        "Zona Portuaria"},
			{"Autoridad Portuaria",                             "Zona Portuaria"},
			{"La capital",                                      "Santa Cruz de Tenerife"},
			{"Consejo Rector del IMAS",                         "Santa Cruz de Tenerife"},
			{"Élite Taxi",                                      "Santa Cruz de Tenerife"},
			{"Emmasa",                                          "Santa Cruz de Tenerife"},
			{"Urbaser",                                         "Santa Cruz de Tenerife"},
			{"Santa Cruz",                                      "Santa Cruz de Tenerife"},
			{"Sociedad de Desarrollo",                          "Santa Cruz de Tenerife"},
			{"SCLocaliza",                                      "Santa Cruz de Tenerife"},
			{"Real Club de Golf de Tenerife",                   "Santa Cruz de Tenerife"},
			{"Oficina Municipal de Información al Consumidor",  "Santa Cruz de Tenerife"},
			{"Plan Integral de Instalaciones deportivas",       "Santa Cruz de Tenerife"},

			{"Arico",                                           "Arico"},
			{"Arona",                                           "Arona"},
			{"Barranco de Masca",                               "Masca"},
			{"Fasnia",                                          "Fasnia"},
			{"Icod de Los Vinos",                               "Icod de Los Vinos"},
			{"Los Silos",                                       "Los Silos"},
			{"Guía de Isora",                                   "Guía de Isora"},
			{"Güímar",                                          "Güímar"},
			{"Santa Úrsula",                                    "Santa Úrsula"},
			
			{"Empresa Insular de Artesanía",           "La Orotava"},
			{"San Benito",           "San Cristóbal de La Laguna"},
			{"Playa Las Vistas",         "Los Cristianos"},
			{"La Laguna",          "La Laguna"},
			{"La Orotava",         "La Orotava"},			
			{"Los Cristianos",         "Los Cristianos"},
			{"Los Abrigos",         "Los Abrigos"},
			{"Granadilla",         "Granadilla"},
			{"Tacoronte",         "Tacoronte"},
			{"Tegueste",         "Tegueste"},
			{"Puerto de La Cruz",         "Puerto de La Cruz"},
			{"El Sauzal",         "El Sauzal"},
			{"Buenavista del Norte",         "Buenavista del Norte"},
			
			{"Ingenio",         "Canarias"},
			{"Fuerteventura",        "Canarias"},
			{"Gran Canaria",        "Canarias"},
			{"Lanzarote",        "Canarias"},
			{"Canarias",        "Canarias"},
			{"La Palma",        "Canarias"},
			{"Naviera Armas",        "Canarias"},
			{"San Bartolomé de Tirajana",        "Canarias"},
			{"Telde",        "Canarias"},
			
			{"Álava",                                           "Nacional"},
			{"Badajoz",                                         "Nacional"},
			{"Cataluña",                                        "Nacional"},
			{"Ceuta",                                           "Nacional"},
			{"Cifuentes",                                       "Nacional"},
			{"Diana Quer",                                      "Nacional"},
			{"Guadalajara",                                     "Nacional"},
			{"Ibiza",                                           "Nacional"},
			{"Lomce",                                           "Nacional"},
			{"Madrid",                                          "Nacional"},
			{"Molina de Segura",                                "Nacional"},
			{"Vallecas",                                        "Nacional"},
			{"Cantabria",             "Nacional"},
			{"Penélope Cruz",        "Nacional"},
			{"Llobregat",                 "Nacional"},
			{"León",                 "Nacional"},
			{"Hospitalet",                 "Nacional"},
			{"Santander",            "Nacional"},
			{"Murcia",               "Nacional"},
			{"Valencia",               "Nacional"},
			{"Fesvial",               "Nacional"},
			{"Pontevedra",               "Nacional"},
			{"Coruña",               "Nacional"},
			{"Córdoba",               "Nacional"},
			{"Ministerio de Sanidad",               "Nacional"},
			{"Fiscal General del Estado",               "Nacional"},
			{"Instituto Catalán de Paleontología",               "Nacional"},
			{"España",               "Nacional"},
			{"Mallorca",               "Nacional"},
			{"Barcelona",               "Nacional"},
			{"Galicia",               "Nacional"},
			{"Navarra",               "Nacional"},
			{"Almería",               "Nacional"},
			{"Huelva",               "Nacional"},
			{"Lugo",               "Nacional"},
			{"Playa de La Concha",               "Nacional"},
			{"Sevilla",               "Nacional"},
			{"Jaén",               "Nacional"},
			{"Zamora",               "Nacional"},
			{"Organización Médica Colegial",               "Nacional"},
			{"La Plataforma Estatal",               "Nacional"},
			{"O Porriño",               "Nacional"},
			{"Alicante",               "Nacional"},
			{"Málaga",               "Nacional"},
			{"Tarragona",               "Nacional"},
			{"Bilbao",               "Nacional"},
			{"Mossos",               "Nacional"},
			{"Granada",               "Nacional"},
			{"Toledo",               "Nacional"},
			{"Dirección General de Tráfico",               "Nacional"},
			{"Audiencia Nacional",               "Nacional"},
			{"Melilla",               "Nacional"},
			{"Albacete",               "Nacional"},
			{"Península",               "Nacional"},
			{"Oviedo",               "Nacional"},
			{"Cádiz",               "Nacional"},
			{"Castellón",               "Nacional"},
			{"Leganés",               "Nacional"},
			{"Cáceres",               "Nacional"},
			{"Ciudad Real",               "Nacional"},
			{"Tarifa",               "Nacional"},
			{"El Ejido",               "Nacional"},
			{"Ávila",               "Nacional"},
			
			{"África",                               "Internacional"},
			{"Agencia Espacial Europea",             "Internacional"},
			{"Alemania",                             "Internacional"},
			{"Arabia Saudí",                         "Internacional"},
			{"Argentina",                            "Internacional"},
			{"Bangladesh",                           "Internacional"},
			{"Bruselas",                             "Internacional"},
			{"China",                                "Internacional"},
			{"Crimea-Congo",                         "Internacional"},
			{"Estocolmo",                            "Internacional"},
			{"Guinness",                             "Internacional"},
			{"Honduras",                             "Internacional"},			
			{"Indonesia",                            "Internacional"},
			{"Institución Carnegie para la Ciencia", "Internacional"},
			{"La Meca",                              "Internacional"},
			{"Marruecos",                            "Internacional"},
			{"Marsella",                             "Internacional"},
			{"México",                               "Internacional"},
			{"Mont Blanc",                           "Internacional"},
			{"Polonia",                              "Internacional"},
			{"Rotterdam",                            "Internacional"},
			{"Saint Etienne",                        "Internacional"},
			{"Save The Children",                    "Internacional"},
			{"Viena",                                "Internacional"},
			{"Yucatán",                              "Internacional"},
			{"Eslovaquia",            "Internacional"},
			{"Francia",              "Internacional"},
			{"Colombia",             "Internacional"},
			{"Bolivia",             "Internacional"},
			{"India",             "Internacional"},
			{"Teresa de Calcuta",             "Internacional"},
			{"Bélgica",             "Internacional"},
			{"Harvard",             "Internacional"},
			{"Antártida",             "Internacional"},
			{"Nicaragua",             "Internacional"},
			{"Vietnam",             "Internacional"},
			{"Unicef",             "Internacional"},
			{"Consejo Europeo de Investigaciones",             "Internacional"},
			{"Estados Unidos",             "Internacional"},
			{"EEUU",             "Internacional"},
			{"Suiza",             "Internacional"},
			{"Malasia",             "Internacional"},
			{"Lille",             "Internacional"},
			{"Reino Unido",             "Internacional"},
			
			{"La Nasa",        "Internacional"},
			{"Voyager 1",        "Internacional"},
			{"La sonda Osiris",        "Internacional"},
			{"Ondas Gravitacionales",        "Internacional"},
			{"Soyuz TMA-20M",        "Internacional"},
			{"Vulcan",        "Internacional"},
			{"Rosetta",        "Internacional"},
			{"Júpiter",        "Internacional"},
			{"Cambio Climático",        "Internacional"},
			{"Observatorio de rayos X Chandra",        "Internacional"},
			{"Curiosity",        "Internacional"},
			{"Hubble",        "Internacional"},
			{"Vía Láctea",             "Internacional"}
			
	};
	
	Date extraerFecha (String in) throws ParseException {
//		try {
			String out;
			int indiceFecha = in.indexOf(ANYO);
			
			if (indiceFecha == -1) {
				Date fecha_Actual = new Date();
				int anyo = 1900 + fecha_Actual.getYear();
				String fechaFormat = "" + anyo + "-";
				int mes = 1 + fecha_Actual.getMonth();
				String aux = "" + mes;
				if (aux.length() == 1)
					fechaFormat += "0" + aux + "-";
				else
					fechaFormat += aux + "-";
				aux = "" + fecha_Actual.getDate();
				if (aux.length() == 1)
					fechaFormat += "0" + aux;
				else
					fechaFormat += aux;
				return new SimpleDateFormat("yyyy-MM-dd").parse(fechaFormat);
			}
			
			out = in.substring(indiceFecha, indiceFecha+10);
			out = out.replaceAll("/", "-");
			
			return new SimpleDateFormat("yyyy-MM-dd").parse(out);
	}	

	public Noticia (Rss rss, Element node) throws ParseException {
		this.titular = Utils.tratar(node.getChildText(Rss.TITLE));
		this.descripcion = Utils.tratar(node.getChildText(Rss.DESCRIPCION));
		this.enlace = node.getChildText(Rss.LINK);
		this.tipo_noticia = rss.tipoRss;
		this.periodico = rss.periodico;
		this.fecha = extraerFecha (this.enlace);		
	}
	
	public Noticia (String titular, String descripcion, String enlace, String tipo_noticia, String periodico, Date fecha) {
		this.titular = titular;
		this.descripcion = descripcion;
		this.enlace = enlace;
		this.tipo_noticia = tipo_noticia;
		this.periodico = periodico;
		this.fecha = fecha;
	}	
	
	@Override
	public String toString () {
		return this.periodico + "," + this.titular + "," + this.descripcion + "," + this.enlace + "," + this.tipo_noticia + "," + this.fecha;
	}
	
	public String toString (String delimitador) {
		return this.periodico + delimitador + this.titular + delimitador + this.descripcion + delimitador + this.enlace + delimitador + this.tipo_noticia + delimitador + this.fecha;
	}	
	
}
