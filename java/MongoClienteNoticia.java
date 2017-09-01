package es.mongodb;

import com.mongodb.BasicDBObject;
import com.mongodb.DB;
import com.mongodb.DBCollection;
import com.mongodb.DBCursor;
import com.mongodb.DBObject;
import com.mongodb.MongoClient;

import es.dbpedia.DBpedia;
import es.rss.Noticia;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.net.UnknownHostException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Date;
import java.util.List;
import java.util.Set;

/**
 * Prueba para realizar conexión con MongoDB.
 * @author j
 *
 */
public class MongoClienteNoticia {
	
	private MongoClient mongo;
	private DB db;
	private DBCollection table;
	
	public static int PERIODICO = 0;
	public static int TITULAR = 1;
	public static int DESCRIPCION = 2;
	public static int LINK = 3;
	public static int RSS = 4;
	public static int FECHA = 5;
	
	public static String DATABASE_PUBLIC = "public";
	public static String TABLE_NOTICIAS = "noticia";
	
	public static String ELEMS_TABLE_NOTICIAS[] = {"periodico", "titular", "descripcion", "link", "rss", "fecha"}; 
 
    /**
     * Clase para crear una conexión a MongoDB.
     * @return MongoClient conexión
     */
    public MongoClienteNoticia() {
        this.mongo = new MongoClient("localhost", 27017);
        db = mongo.getDB(DATABASE_PUBLIC);
        table = db.getCollection(TABLE_NOTICIAS);
    }
 
    /**
     * Clase que imprime por pantalla todas las bases de datos MongoDB.
     * @param mongo conexión a MongoDB
     */
    public void printDatabases() {
        List dbs = this.mongo.getDatabaseNames();
        for (Object database : dbs) {
        	DB db = mongo.getDB((String) database);
        	Set<String> collections = db.getCollectionNames();

        	for (String collectionName : collections) {
        		System.out.println("Base de datos: " + (String) database + " Tabla: " + collectionName);
        	}
        }
    }
    
    public void mostrarTabla () {
	    //Listar la tabla "trabajador"
    	int numero = 0;
	    System.out.println("Listar los registros de la tabla: " + TABLE_NOTICIAS + " de la BD: " + DATABASE_PUBLIC);
	    DBCursor cur = table.find();
	    while (cur.hasNext()) {
	    	numero++;
	    	System.out.println(cur.next());
	    }
	    System.out.println("El listado de elementos es: " + numero);
	    System.out.println();
    }
    
    public Collection<String> TablatoArray () {
    	Collection<String> tabla = new ArrayList<String>();
    	
	    //Listar la tabla "trabajador"
    	int numero = 0;
	    System.out.println("Listar los registros de la tabla: " + TABLE_NOTICIAS + " de la BD: " + DATABASE_PUBLIC);
	    DBCursor cur = table.find();
	    while (cur.hasNext()) {
	    	
	    	DBObject noticia = cur.next();
	    	Noticia n = new Noticia(
		    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[TITULAR]),
		    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[DESCRIPCION]),
		    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[LINK]),
		    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[RSS]),
		    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[PERIODICO]),
		    		(Date) noticia.get(ELEMS_TABLE_NOTICIAS[FECHA]));     
	    	
	    	String contenido = n.titular + ". " + n.descripcion;
	    	tabla.add(contenido);
	    	// System.out.println(numero + " : " + contenido);
	    	numero++;
	    }
	    System.out.println("El listado de elementos es: " + numero);
	    System.out.println();
	    return tabla;
    }        
    
    public void mostrarTablaFichero (String nombreFichero) throws IOException {
		String ruta = nombreFichero;
        File archivo = new File(ruta);
        BufferedWriter bw;
        bw = new BufferedWriter(new FileWriter(archivo));
        
	    //Listar la tabla "trabajador"
    	int numero = 0;
	    System.out.println("Listar los registros de la tabla: " + TABLE_NOTICIAS + " de la BD: " + DATABASE_PUBLIC);
	    DBCursor cur = table.find();
	    while (cur.hasNext()) {
	    	numero++;
	    	System.out.println(numero);
	    	
	    	DBObject noticia = cur.next();
	    	Noticia n = new Noticia(
		    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[PERIODICO]),
		    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[TITULAR]),
		    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[DESCRIPCION]),
		    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[LINK]),
		    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[RSS]),
		    		(Date) noticia.get(ELEMS_TABLE_NOTICIAS[FECHA]));    	
	    	bw.write(n.toString("ç") + "\n");
	    }
	    System.out.println("El listado de elementos es: " + numero);
	    System.out.println();
	    bw.close();
    }
	     
    
    public boolean find (String titular) {
	    BasicDBObject whereQuery = new BasicDBObject();
	    whereQuery.put(ELEMS_TABLE_NOTICIAS[TITULAR], titular);
	    DBCursor cursor = table.find(whereQuery);
	    return cursor.hasNext();
    }
    
    public Noticia findObject (String titular) {
	    BasicDBObject whereQuery = new BasicDBObject();
	    whereQuery.put(ELEMS_TABLE_NOTICIAS[TITULAR], titular);
	    DBCursor cursor = table.find(whereQuery);
	    if (cursor.hasNext()) {
	    	DBObject noticia = cursor.next();
	    	Noticia n = new Noticia(
	    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[PERIODICO]),
	    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[TITULAR]),
	    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[DESCRIPCION]),
	    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[LINK]),
	    		(String) noticia.get(ELEMS_TABLE_NOTICIAS[RSS]),
	    		(Date) noticia.get(ELEMS_TABLE_NOTICIAS[FECHA]));
	    	return n;
	    }
	    return null;
    }    
    
    public boolean insertNoticia(Noticia n) {
    	if (find(n.titular))
    		return false;
    	
        //Crea los objectos básicos
        BasicDBObject document = new BasicDBObject();
        document.put(ELEMS_TABLE_NOTICIAS[0], n.periodico);
        document.put(ELEMS_TABLE_NOTICIAS[TITULAR], n.titular);
        document.put(ELEMS_TABLE_NOTICIAS[2], n.descripcion);
        document.put(ELEMS_TABLE_NOTICIAS[3], n.enlace);
        document.put(ELEMS_TABLE_NOTICIAS[4], n.tipo_noticia);
        document.put(ELEMS_TABLE_NOTICIAS[5], n.fecha);
        
        //Insertar tablas
        this.table.insert(document);
        return true;
    }
 
    public  void borrarNoticia(String titular) {
    	//Crea los objectos básicos
        BasicDBObject document = new BasicDBObject();
        document.remove(ELEMS_TABLE_NOTICIAS[TITULAR], titular);
        
        //Insertar tablas
        this.table.remove(document);
    }
    
    public  void borrarAll() {
        //Insertar tablas
        this.table.drop();
    }        
    
}