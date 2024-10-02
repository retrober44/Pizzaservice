<?php	// UTF-8 marker äöüÄÖÜß€
/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 * 
 * PHP Version 5
 *
 * @category File
 * @package  Pizzaservice
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 * @license  http://www.h-da.de  none 
 * @Release  1.2 
 * @link     http://www.fbi.h-da.de 
 */

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

/**
 * This is a template for top level classes, which represent 
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class. 
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking 
 * during implementation.
 
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 */
class Bestellung extends Page
{

    // to do: declare reference variables for members 
    // representing substructures/blocks
    
    /**
     * Instantiates members (to be defined above).   
     * Calls the constructor of the parent i.e. page class.
     * So the database connection is established.
     *
     * @return none
     */
    protected function __construct() 
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }
    
    /**
     * Cleans up what ever is needed.   
     * Calls the destructor of the parent i.e. page class.
     * So the database connection is closed.
     *
     * @return none
     */
    protected function __destruct() 
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is stored in an easily accessible way e.g. as associative array.
     *
     * @return none
     */
    protected function getViewData()
    {
        // to do: fetch data for this view from the database
        $sqlAbfrage = "Select * from angebot;";
        $recordSet = $this->_database->query($sqlAbfrage);
        if(!$recordSet){
            throw new Exception("Query failed: ".$this->_database->error);
        }
        
        
        $anzahlRecords = $recordSet->num_rows;
        while($record = $recordSet->fetch_assoc()){
            //echo ("<p>".htmlspecialchars($record["Preis"])."</p>");
            $this->pizzen[ htmlspecialchars($record["PizzaName"])] = htmlspecialchars($record["Preis"]);
            $this->pizzenObj [htmlspecialchars($record["PizzaName"])] = new Pizza(htmlspecialchars($record["PizzaNummer"]), 
            htmlspecialchars($record["PizzaName"]), htmlspecialchars($record["Preis"]), "");
        }
        $recordSet->free();
       
    }
    
    /**
     * First the necessary data is fetched and then the HTML is 
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if avaialable- the content of 
     * all views contained is generated.
     * Finally the footer is added.
     *
     * @return none
     */
    protected function generateView() 
    {
        $this->getViewData();
        $this->generatePageHeader('Bestellung');
        // to do: call generateView() for all members
        // to do: output view of this page
        echo <<<EOT
        <header class="header">
        <h1>
            <!-- h1 - h6 header(groß & bold) / nicht einfach nur <h1>-->
            Bestellung
        </h1>
        </header>
        <div class="bestellungWrapper">
        <section class="speisekarte">
        <h2>Speisekarte</h2>
        EOT;
        
        foreach($this->pizzenObj as $pName => $pObj){
            $preis = $pObj->getPizzaPreis();
            echo<<<EOT
            <div class = "pizza">
            <img src="img/pizza_ma.png" width="125" height="125" alt="Margherita" id="img$pName">
            <p data-preis="$preis" id="$pName">$pName</p>
            <p>$preis €</p>
            </div>
            EOT;
        }
        
        echo <<<EOT
        </section>
        <section class="warenkorb">
        <h2>
            Warenkorb
        </h2>
        <form id="ware" action="kundeTemp.php" method="POST" accept-charset="UTF-8">
            <div class="block">
            <select name="pizzen[]" size="8" multiple tabindex="0">
            </select>
            </div>
            <p id="pPreis">0€</p>
            <button type="button" tabindex="1" accesskey="l" onclick = deleteAll()>Alle Löschen</button>
            <button type="button" tabindex="2" accesskey="a" onclick = deleteFew()>Auswahl Löschen</button>
            <p><input class="inputBestellung" type="text" name="name" id="inputName" value="" placeholder="Name" ></p>
            <p><input class="inputBestellung" type="text" name="vorname" id="inputVorname" value="" placeholder="Vorname" ></p>
            <p><input class="inputBestellung" type="text" name="adresse" id="inputAdr" value="" placeholder="Straße" required></p>
            <input type="submit" tabindex="3" id="bestellButton" onclick = bestellen() value="Bestellen">
        </form>
        </section>
        </div>
        <script src="myjs.js"></script>
        <script>iinit()</script>
        <noscript>
        <p>Bitte aktivieren Sie JavaScript!</p>
        </noscript>
        EOT;
        $this->generatePageFooter();
    }
    
    /**
     * Processes the data that comes via GET or POST i.e. CGI.
     * If this page is supposed to do something with submitted
     * data do it here. 
     * If the page contains blocks, delegate processing of the 
	 * respective subsets of data to them.
     *
     * @return none 
     */
    protected function processReceivedData() 
    {
        parent::processReceivedData();
        // to do: call processReceivedData() for all members
    }

    /**
     * This main-function has the only purpose to create an instance 
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     *
     * @return none 
     */    
    public static function main() 
    {
        try {
            $page = new Bestellung();
            $page->processReceivedData();
            $page->generateView();
        }
        catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
Bestellung::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >