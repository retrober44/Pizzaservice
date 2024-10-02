<?php    // UTF-8 marker äöüÄÖÜß€
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
class PageTemplate extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks

    private $bestellungInfo;

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
        $tmpArray;
        //$sqlBestellung = "SELECT BestellungID, Adresse, PizzaID,PizzaNummer, PizzaName, Preis, Status FROM bestellung, bestelltepizza, angebot WHERE PizzaNummer = fPizzaNummer AND BestellungID = fBestellungID;";
        $sqlBestellung = "SELECT BestellungID, Adresse , Status FROM bestellung, bestelltepizza where BestellungID =fBestellungID  AND Status in ('fertig' , 'unterwegs') and Status != 'geliefert';";
        $recordSet = $this->_database->query($sqlBestellung);
        if (!$recordSet) {
            throw new Exception("Query failed: " . $this->_database->error);
        }

        $anzahlRecords = $recordSet->num_rows;
        while ($record = $recordSet->fetch_assoc()) {
            /* $this->pizzenObj [htmlspecialchars($record["PizzaID"])] = new Pizza(htmlspecialchars($record["PizzaID"]), 
            htmlspecialchars($record["PizzaName"]), htmlspecialchars($record["Preis"]), htmlspecialchars($record["Status"]));*/
            $beObj = new BestellungObj($record["BestellungID"], htmlspecialchars($record["Adresse"]), $record["Status"]);
            $this->bestellung[$record["BestellungID"]] = $beObj;
        }
        $recordSet->free();
        if ($this->bestellung != null) {
            foreach ($this->bestellung as $key => $obj) {
                $sqlBestellung = "SELECT PizzaID,PizzaNummer, PizzaName, Preis, Status FROM bestellung, bestelltepizza, angebot WHERE PizzaNummer = fPizzaNummer AND BestellungID = fBestellungID AND BestellungID = $key;";
                $recordSet = $this->_database->query($sqlBestellung);
                $pizzen = array();
                if (!$recordSet) {
                    throw new Exception("Query failed: " . $this->_database->error);
                }

                $anzahlRecords = $recordSet->num_rows;
                while ($record = $recordSet->fetch_assoc()) {
                    $pizzen[] = new Pizza($record["PizzaID"], $record["PizzaName"], $record["Preis"], $record["Status"]);
                }

                $obj->addPizzaPreis($pizzen);
                $obj->allePizzenFertig();
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
        echo "<meta http-equiv='refresh' content='5' />";
        echo "<h1>Fahrer</h1>";
        echo ("<section id=\"fahrerbereich\">");
        
        if ($this->bestellung != null) {
            $i = 0;
            foreach ($this->bestellung as $bID => $bObj) {
                if($bObj->bestellStatus == "fertig" || $bObj->bestellStatus == "unterwegs"){
                echo ("<form action='fahrerTemp.php' method='POST' id='test$i'>");
                echo <<<EOT
            <div id="$bID">
            <p>$bID, $bObj->adresse, $bObj->preis € </p>
            <p>$bObj->pizzaToString</p>
            <span >fertig</span>
            <span>unterwegs</span>
            <span>geliefert</span>
            <br>
EOT;
                echo "<span><input type='radio' onclick=\"document.getElementById('test$i').submit();\" name='$bID' value='fertig' " . ($bObj->bestellStatus == "fertig" ? "checked" : "") . "></span>";
                echo "<span><input type='radio' onclick=\"document.getElementById('test$i').submit();\" name='$bID' value='unterwegs' " . ($bObj->bestellStatus == "unterwegs" ? "checked" : "") . "></span>";
                echo "<span><input type='radio' onclick=\"document.getElementById('test$i').submit();\" name='$bID' value='geliefert'" . ($bObj->bestellStatus == "geliefert" ? "checked" : "") . "></span>";
                echo <<<EOT
            </div>
            </form>
EOT;
            $i++;

            }
            echo ("</section>");
        }
        } else {
            echo ("Keine Lieferungen.");
        }
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

        $pizzaID;
        $pizzaStatus;

        //UPDATE `bestelltepizza` SET `PizzaID`=[value-1],`fBestellungID`=[value-2],`fPizzaNummer`=[value-3],`Status`=[value-4] WHERE 1

        foreach ($_POST as $param_name => $param_val) {
            if (isset($_POST[$param_name])) {
                $pizzaID = $param_name;
                $pizzaStatus = $param_val;

                $sqlInsertBestellung = "UPDATE bestelltepizza set Status = '$pizzaStatus' where fBestellungID = $pizzaID;";
                $this->_database->query($sqlInsertBestellung);
            }
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
            $page = new PageTemplate();
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
PageTemplate::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >
