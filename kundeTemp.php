<?php    // UTF-8 marker äöüÄÖÜß€
session_start();

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
class Kunde extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks
    private $pizzen = array();
    private $bestellId = array();

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

        if (isset($_SESSION['LastAccess']) && time() - $_SESSION['LastAccess'] < 60) {
            $bID = $_SESSION["bID"];
            $_SESSION['LastAccess'] = time(); // Inaktivitätsdauer = 0


            $sqlAbfrage = "SELECT PizzaID, PizzaName, Preis, Status FROM bestelltepizza, angebot WHERE fBestellungID = " . $_SESSION["bID"] . " AND PizzaNummer = fPizzaNummer;";
            $recordSet = $this->_database->query($sqlAbfrage);
            if (!$recordSet) {
                throw new Exception("Query failed: " . $this->_database->error);
            }

            $anzahlRecords = $recordSet->num_rows;
            while ($record = $recordSet->fetch_assoc()) {
                $this->pizzenObj[htmlspecialchars($record["PizzaID"])] = new Pizza(
                    htmlspecialchars($record["PizzaID"]),
                    htmlspecialchars($record["PizzaName"]),
                    htmlspecialchars($record["Preis"]),
                    htmlspecialchars($record["Status"])
                );
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
        $this->generatePageHeader('to do: change headline');
        // to do: call generateView() for all members
        // to do: output view of this page
        echo <<<EOT
        <h1>
           Ihre Bestellung
        </h1>
        <script src="StatusUpdate.js"></script>
        <script>
        requestData();
        </script>
        <section class="meineBestellungen">
        EOT;
        foreach ($this->pizzenObj as $key => $obj) {
            $nameId = $obj->getPizzaName() . (string) $obj->getId();
            $this->showBestellung($obj->getPizzaName(), $nameId, $obj->getPizzaStatus());
        }
        echo "<script>init();</script>";
        echo "</section>";

        $this->generatePageFooter();
    }

    private function showBestellung($pName, $inputName, $s)
    {


        echo <<<EOT
        <div class="table">
        <fieldset>
        <legend>$pName</legend>
        <div class="tr">
        EOT;
        echo ("<label for='mar'>Bestellt</label> <input id='bestellt$inputName' type='radio' name='status$inputName' " . (($s == "bestellt") ?  "checked"   : "")  . " value='fertig'>");
        echo ("<label for='mar'>Im Ofen</label> <input id='imOfen$inputName' type='radio' name='status$inputName' " . (($s == "imOfen") ?  "checked"   : "")  . " value='fertig'>");
        echo ("<label for='mar'>Fertig</label> <input id='fertig$inputName' type='radio' name='status$inputName' " . (($s == "fertig") ?  "checked"   : "")  . " value='fertig'>");
        echo ("<label for='mar'>Unterwegs</label> <input id='unterwegs$inputName' type='radio' name='status$inputName' " . (($s == "unterwegs") ?  "checked"   : "")  . " value='fertig'>");
        echo ("<label for='mar'>Geliefert</label> <input id='geliefert$inputName' type='radio' name='status$inputName' " . (($s == "geliefert") ?  "checked"   : "")  . " value='fertig'> </fieldset> </div>");
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

        if (isset($_POST["pizzen"]) && isset($_POST["adresse"])) {
            $this->pizzen = $_POST["pizzen"];
            $adresse = htmlspecialchars($_POST["adresse"]);

            $sqlInsertBestellung = "INSERT INTO bestellung (BestellungID, Adresse, Bestellzeitpunkt) values (DEFAULT, '$adresse', DEFAULT);";
            $this->_database->query($sqlInsertBestellung);
            $fBestellungId = $this->_database->insert_id;

            if (isset($_POST['adresse'])) {
                $_SESSION['bID'] = $fBestellungId;
                $_SESSION['LastAccess'] = time();
            }




            for ($i = 0; $i < count($this->pizzen); $i++) {
                $sqlAbfrage = "Select PizzaNummer from angebot where PizzaName ='" . $this->pizzen[$i] . "';";
                $recordSet = $this->_database->query($sqlAbfrage);
                if (!$recordSet) {
                    throw new Exception("Query failed: " . $this->_database->error);
                }

                $recordId = $recordSet->fetch_assoc();
                $sqlInsertBesPizza = "INSERT INTO bestelltepizza(PizzaID, fBestellungID, fPizzaNummer, Status) VALUES (DEFAULT, $fBestellungId , " . $recordId["PizzaNummer"] . ", 'bestellt');";
                $this->_database->query($sqlInsertBesPizza);
            }


            $recordSet->free();
        }
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
            $page = new Kunde();
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
Kunde::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >
