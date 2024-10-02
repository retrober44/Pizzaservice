<?php    // UTF-8 marker äöüÄÖÜß€
session_start();
$bID = session_id();

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
class KundenStatus extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks
    private $serializedData;
    private $jsonPizza = array();

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

        if (isset($_SESSION['bID'])) {
            $bID = $_SESSION["bID"];
            $_SESSION['LastAccess'] = time(); // Inaktivitätsdauer = 0


            $sqlAbfrage = "SELECT PizzaID, PizzaName, Preis, Status FROM bestelltepizza, angebot WHERE fBestellungID = " . $_SESSION["bID"] . " AND PizzaNummer = fPizzaNummer;";
            $recordSet = $this->_database->query($sqlAbfrage);
            if (!$recordSet) {
                throw new Exception("Query failed: " . $this->_database->error);
            }

            $anzahlRecords = $recordSet->num_rows;
            while ($record = $recordSet->fetch_assoc()) {
                $forJson = array();

                $forJson[] = htmlspecialchars($record["PizzaID"]);
                $forJson[] = htmlspecialchars($record["PizzaName"]);
                $forJson[] = htmlspecialchars($record["Preis"]);
                $forJson[] =  htmlspecialchars($record["Status"]);
                
                $this->jsonPizza[] = $forJson;
                
            }
            $recordSet->free();
        }
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
        //$this->generatePageHeader('KundenStatus');

        //keine HTML Seite, deswegen generatePageHeader/-footer auskommentiert

        $this->serializedData = json_encode($this->jsonPizza);
        echo $this->serializedData;

        $t = 0;
        //$this->generatePageFooter();
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
        /*
        BLEIBT BEI KUNDENSTATUS LEER!
        */
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
            $page = new KundenStatus();
            header("Content-type: application/json; charset=UTF-8");
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
KundenStatus::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >
