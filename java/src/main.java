import es.mongodb.*;
import es.rss.*;

class Main
{
    public static void main(String args[])
    {
        Rss rss_ = new Rss ("http://eldia.es/rss/santacruz.rss", "Santa Cruz de Tenerife", "El Día");
        System.out.println("El periodico es: " + rss_.getPeriodico());
    }
}
