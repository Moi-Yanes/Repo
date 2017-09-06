import es.mongodb.*;
import es.rss.*;

import org.jdom.input.SAXBuilder;


import java.util.Date;
import java.util.ArrayList;
import java.util.List;


class Main
{

	public void testCrearRSS(){
		Rss rss_ = new Rss ("http://eldia.es/rss/santacruz.rss", "Santa Cruz de Tenerife", "El Día");
		System.out.println("El periodico es: " + rss_.getPeriodico());
	}


	public void testInsertarNoticia(){
		MongoClienteNoticia cliente = new MongoClienteNoticia();
		Noticia n = new Noticia("Probando insert noticia 2", "En Santa cruz", "http://jaja", "Rss El dia", "El dia", new Date());

		cliente.insertNoticia(n);
		System.out.println("Noticia: " + cliente.findObject (n.titular));
	}


	public void testFecha()
	{
		MongoClienteNoticia cliente = new MongoClienteNoticia();
		Noticia n = new Noticia("Probando insert noticia 2", "En Santa cruz", "http://jaja", "Rss El dia", "El dia", new Date());

		System.out.println("Fecha: " + n.fecha);
	}


	public void InsertarRss()
	{
		MongoClienteNoticia cliente = new MongoClienteNoticia();
		CapturarRSS captura = new CapturarRSS();
		int contador = 0;
		
		captura.obtenerInserts();
		List<Noticia> listaClasificados = captura.listaNoticiasClasificadas; 
		for (Noticia node : listaClasificados) {
		    if (cliente.insertNoticia(node))
			contador++;
		}
		System.out.println("*********** Se han insertado: " + contador + " elementos.");
	} 


	public static void main(String args[])
	{
		Main main = new Main();

		//main.testCrearRSS();
		//main.testInsertarNoticia();
		//main.testFecha();

		main.InsertarRss();
	}
}
