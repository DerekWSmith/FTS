<?php
require 'vendor/autoload.php';
require 'class_job.php';

ini_set("memory_limit", "4096M");  // juggling a large spreadsheet in memory.  Let's be on the safe side

// check we have these details in the env, and if not, put them in for the life of this current script
// just absolutely defensive programming
if (getenv('DB_NAME') == false) putenv('DB_NAME=wordpress_obpdev');
if (getenv('DB_USER') == false) putenv('DB_NAME=wordpress_user');
if (getenv('DB_PASSWORD') == false) putenv('DB_PASSWORD=!QAZxsw2#EDCvfr4');
if (getenv('DB_HOST') == false) putenv('DB_HOST=localhost');
if (getenv('DB_CHARSET') == false) putenv('DB_CHARSET=');
if (getenv('DB_COLLATE') == false) putenv('DB_COLLATE=');


// See: https://github.com/symfony/panther
// kill $(lsof -i:9515)
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;

//
use Facebook\WebDriver\WebDriverExpectedCondition;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Panther\Client;


 echo "\rStarting up......";


$val = getopt('', ["spreadsheet:"]);

//
/// / DEV override
// --spreadsheet="/Volumes/Passport/CheckPoint/FTS/FTS - Job Status.xlsx"

$val['in'] = '/Volumes/Passport/Premier Location/FTS - Job Status TEST DATA.xlsx';
$val['out'] = '/Volumes/Passport/Premier Location/FTS - Spear updates.xlsx';

if (count($val) != 2)
    exit("Must give two parameter: \n eg --in=\"/Volumes/Passport/Premier Location/FTS - Job Status TEST DATA.xlsx\" --out=\"/Volumes/Passport/Premier Location/FTS - Spear updates.xlsx\"");
// '/var/lib/mysql-files/' on Debbie.
$val = (object)$val;
if (!file_exists($val->in)) {
    echo "\r{$val->in} not found.";
}

if (file_exists($val->out)) {
    echo "\r{$val->out} found.  Will not overwrite it.";
}

// test ss read

 echo "\r--Reading {$val->in}";
// input spreadsheet
$inbook = \PhpOffice\PhpSpreadsheet\IOFactory::load($val->in);


// output spreadsheet
$outbook = new \PhpOffice\PhpSpreadsheet\Spreadsheet() ;
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($outbook);


$worksheet = $inbook->getSheet(0);

// somewhere to put the results


// remove existing results
//if ($outbook->sheetNameExists('Updates')) {
//    $sheetIndex = $outbook->getIndex($outbook->getSheetByName('Updates'));
//    $outbook->removeSheetByIndex($sheetIndex);
//}
// add new results
$resultssheet = $outbook->setActiveSheetIndex(0);

// set column headings
// Basic details, from the search results screen
$resultssheet->setCellValueByColumnAndRow(1, 1, 'Property');
$resultssheet->setCellValueByColumnAndRow(2, 1, 'Responsible Authority');
$resultssheet->setCellValueByColumnAndRow(3, 1, 'Responsible Authority Ref');
$resultssheet->setCellValueByColumnAndRow(4, 1, 'Application Type');
$resultssheet->setCellValueByColumnAndRow(5, 1, 'Status');
$resultssheet->setCellValueByColumnAndRow(6, 1, 'Advertising Indicator');
$resultssheet->setCellValueByColumnAndRow(7, 1, 'Objected Indicator');
$resultssheet->setCellValueByColumnAndRow(8, 1, 'Appealed Indicator');
$resultssheet->setCellValueByColumnAndRow(9, 1, 'Spear Ref');
$resultssheet->setCellValueByColumnAndRow(10,1, 'Plan Number');
$resultssheet->setCellValueByColumnAndRow(11,1, 'Submitted Date');


// milestones from the summary screen
$resultssheet->setCellValueByColumnAndRow(12, 1, 'Application Submission');
$resultssheet->setCellValueByColumnAndRow(13, 1, 'Referral');
$resultssheet->setCellValueByColumnAndRow(14, 1, 'Final Referral Response (Cert)');
$resultssheet->setCellValueByColumnAndRow(15, 1, 'Original Certification Date');
$resultssheet->setCellValueByColumnAndRow(16, 1, 'Re Certification Date (most recent)');
$resultssheet->setCellValueByColumnAndRow(17, 1, 'Street Addressing (Submitted on M1)');
$resultssheet->setCellValueByColumnAndRow(18, 1, 'Statement Of Compliance');
$resultssheet->setCellValueByColumnAndRow(19, 1, 'Released for Lodgement');
$resultssheet->setCellValueByColumnAndRow(20, 1, 'Lodged at Land Use Victoria');
$resultssheet->setCellValueByColumnAndRow(21, 1, 'Registered at Land Use Victoria');


// Get the highest row and column numbers referenced in the worksheet
$highestRow = $worksheet->getHighestRow(); // e.g. 10
$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5
$results_row = 1 ;


 echo "\rLogin starting....";
ini_set('memory_limit', '2048M');  // lot of databas


$mydb = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASSWORD'), getenv('DB_NAME'));
$result = $mydb->select_db('obpii_spatial');
$query = $mydb->prepare("SELECT PLAN_NUMBER FROM obpii_spatial.obpSearch where lot_number=? AND EZI_Address like ?");


try {
// Set the environment.
    putenv('PANTHER_NO_HEADLESS=1');
    putenv('PANTHER_DEVTOOLS=0');
    putenv('PANTHER_NO_SANDBOX=1');
    $prefs = [
        'safebrowsing.disable_download_protection' => true,
        'headless' => true,
        'start-maximized' => true,
        'disable-gpu' => true,
        'disable-extensions' => true,
        "download.prompt_for_download" => false,
        "download.directory_upgrade" => true,
        "safebrowsing.enabled" => true,
        "download.default_directory" => "~/Downloads",
    ];


    // on local
    $client = Client::createChromeClient(null, [], $prefs);
//	// on Linux server
//	// $client = Client::createChromeClient("/usr/bin/chromedriver", null, $prefs);
//
//
     echo "\rGoing to https://www.spear.land.vic.gov.au/spear/app/login";
    $client->get('https://www.spear.land.vic.gov.au/spear/app/login');
     echo "\r--Logging in.....";
    $client->findElement(WebDriverBy::id('username'))->sendKeys('dersmi40');
    $client->findElement(WebDriverBy::id('password'))->sendKeys('Lapwing.d0l0mite.fling');
    $client->findElement(WebDriverBy::className('spearbuttons'))->click();


    for ($row = 2; $row <= $highestRow; ++$row) {


        // a new row form the spreadsheet, means we are resetting to start again at the data rows from the top on the screen
        $screendatarow = -1;  // zero indexed array - we increment, first thing


        $a = new class_job();

        if (strtolower($worksheet->getCellByColumnAndRow(10, $row)->getValue() ?? '') != 'pending') {
            continue;
        }
        $a->Metricon_Job_No = $worksheet->getCellByColumnAndRow(1, $row)->getValue() ?? '';
        $a->FTS_Job_No = $worksheet->getCellByColumnAndRow(2, $row)->getValue() ?? '';
        $a->Lot_No = $worksheet->getCellByColumnAndRow(3, $row)->getValue() ?? '';
        $a->Street_No = $worksheet->getCellByColumnAndRow(4, $row)->getValue() ?? '';
        $a->Street_Name = $worksheet->getCellByColumnAndRow(5, $row)->getValue() ?? '';
        $a->Suburb = $worksheet->getCellByColumnAndRow(6, $row)->getValue() ?? '';


        // go find the plan number
        // using either LOT + Street Name
        // or
        // Street Number + Street name
        $p1 = '';
        if (!empty($a->Street_No))
            $p1 = "{$a->Street_No} {$a->Street_Name} %";
        else
            $p1 = "%{$a->Street_Name}%";

         echo "\r--Looking in database to get the plan number for (Lot:$a->Lot_No) {$p1}";
        $query = "SELECT PLAN_NUMBER FROM obpii_spatial.obpSearch where lot_number=\"{$a->Lot_No}\" AND EZI_Address like \"{$p1}\"";
        $result = $mydb->query($query);

        if ($result->num_rows != 1) {
             echo "\r----Not Found.  Next...";
            continue;
        }  // not 1 means we found zero, or several.  Ain't nobody got time for that


        // founf in DB.  We have a plan number
        $plannumber = $result->fetch_row()[0];;
         echo "\r--Found {$plannumber} in database.";
        if (null == $plannumber) continue;

        $client->get('https://www.spear.land.vic.gov.au/spear/app/public/application-search');
        $client->waitFor('#plan-num')->isDisplayed();
        // Enter plan num on Spear Search screen
        $client->findElement(WebDriverBy::id('plan-num'))->sendKeys($plannumber);
        // press search button
         echo "\r--Searching in SPEAR for {$plannumber} (Lot:$a->Lot_No) {$a->Street_No} {$a->Street_Name} {$a->Suburb}";
        $client->findElement(WebDriverBy::className('button__btn'))->click();

        $client->waitForVisibility('body > app-root > div > main > div > div > sp-public-application-list > div > div > sp-application-search-result > section > strong');

        $pstr = $client->getPageSource();

        if (str_contains($pstr, 'Current Applications - No results found for "Plan Number:')) {
            echo "\r----No data found in Spear for $plannumber";
            continue;
        }


        // WE FOUND SOME DATA IN SPEAR

        // get the links from the property column
        // $links = $client->findElements(WebDriverBy::className('PROPERTY_CellClass'));

// chop it up into lines of data
        $re = '/<datatable-row-wrapper(.*?)<\/datatable-row-wrapper>/msi';
        preg_match_all($re, $pstr, $datalines, PREG_SET_ORDER, 0);


        // foreach TR line of data on the screen
        // $links = $client->findElements(WebDriverBy::className('PROPERTY_CellClass'));
        foreach ($datalines as $line)  // data irrelevant.  Just a convenient way to count the lines
        {


            // for each dataline, there are 11 pieces of data in cloumns
            // split the line into the data cells
            $re = '/(\w*)_CellClass.*?>.*?>(?:<!---->){0,4}(?:<span title=")?(.*?)(?:">|<!---->)/msi';
            preg_match_all($re, $line[1], $columns, PREG_SET_ORDER, 0);


            // lets throw that data into the spreadsheet
            // first the summary from the search screen, then the milestones from the summary screen
            // for each column of data starting with 1
            $results_row++;  // new data line on screen - new row in the output.
            $screendatarow = 0;
            $sumdatacol = 0;  // start with summary data in the leftmost column (index 1-based)
            foreach ($columns as $data) {
                $sumdatacol++;
                $resultssheet->setCellValueByColumnAndRow($sumdatacol, $results_row, $data[2]);
            }  // fort each data columns
            // Get the link corresponding to the data row and go there
            // we need to get the links afresh, as we have navigated back to this page
            $links = $client->findElements(WebDriverBy::className('PROPERTY_CellClass'));
            $links[$screendatarow]->click();
            $client->waitForVisibility('.section-header__title');
            $statusstring = $client->getPageSource();

            // Get the milestones data
            $re = '/application-milestones__name">(.*?)<\/div>.*?__status">(.*?)<\/div>/msi';
            preg_match_all($re, $statusstring, $milestones, PREG_SET_ORDER, 0);
            foreach ($milestones as $stone) {
                switch ($stone[1]) {
                    case 'Application Submission' :
                        $resultssheet->setCellValueByColumnAndRow(13, $results_row, $stone[2]);
                        break;
                    case 'Referral' :
                        $resultssheet->setCellValueByColumnAndRow(14, $results_row, $stone[2]);
                        break;
                    case 'Final Referral Response (Cert)' :
                        $resultssheet->setCellValueByColumnAndRow(15, $results_row, $stone[2]);
                        break;
                    case 'Original Certification Date' :
                        $resultssheet->setCellValueByColumnAndRow(16, $results_row, $stone[2]);
                        break;
                    case 'Re-certification (Most Recent)' :
                        $resultssheet->setCellValueByColumnAndRow(17, $results_row, $stone[2]);
                        break;

                    case 'Street Addressing (Submitted on M1)' :
                        $resultssheet->setCellValueByColumnAndRow(18, $results_row, $stone[2]);
                        break;
                    case 'Statement of Compliance' :
                        $resultssheet->setCellValueByColumnAndRow(18, $results_row, $stone[2]);
                        break;
                    case 'Released for Lodgement' :
                        $resultssheet->setCellValueByColumnAndRow(19, $results_row, $stone[2]);
                        break;
                    case 'Lodged at Land Use Victoria' :
                        $resultssheet->setCellValueByColumnAndRow(20, $results_row, $stone[2]);
                        break;
                    case 'Registered at Land Use Victoria' :
                        $resultssheet->setCellValueByColumnAndRow(21, $results_row, $stone[2]);
                        break;
                    default:
                        $resultssheet->setCellValueByColumnAndRow(23, 1, $stone[2]);
                        $resultssheet->setCellValueByColumnAndRow(23, $results_row, $stone[2]);



                    // do nothing
                }
            }  // for each milestone
            // we need to return to search results.
            $client->findElement(WebDriverBy::partialLinkText('Search Results'))->click();
            $client->waitForVisibility('ngx-datatable');

        } // for each line of data on screen
        // save the data

        // well, this is disturbing
        $resultssheet->getColumnDimension('A')->setAutoSize(true);
        $resultssheet->getColumnDimension('B')->setAutoSize(true);
        $resultssheet->getColumnDimension('C')->setAutoSize(true);
        $resultssheet->getColumnDimension('D')->setAutoSize(true);
        $resultssheet->getColumnDimension('E')->setAutoSize(true);
        $resultssheet->getColumnDimension('F')->setAutoSize(true);
        $resultssheet->getColumnDimension('G')->setAutoSize(true);
        $resultssheet->getColumnDimension('H')->setAutoSize(true);
        $resultssheet->getColumnDimension('I')->setAutoSize(true);
        $resultssheet->getColumnDimension('J')->setAutoSize(true);
        $resultssheet->getColumnDimension('K')->setAutoSize(true);
        $resultssheet->getColumnDimension('L')->setAutoSize(true);
        $resultssheet->getColumnDimension('M')->setAutoSize(true);
        $resultssheet->getColumnDimension('M')->setAutoSize(true);
        $resultssheet->getColumnDimension('O')->setAutoSize(true);
        $resultssheet->getColumnDimension('P')->setAutoSize(true);
        $resultssheet->getColumnDimension('Q')->setAutoSize(true);
        $resultssheet->getColumnDimension('R')->setAutoSize(true);
        $resultssheet->getColumnDimension('S')->setAutoSize(true);
        $resultssheet->getColumnDimension('T')->setAutoSize(true);
        $resultssheet->getColumnDimension('U')->setAutoSize(true);
        $resultssheet->getColumnDimension('V')->setAutoSize(true);



        $writer->save($val->out);
        // Reset to Spear search form
        $client = $client->get('https://www.spear.land.vic.gov.au/spear/app/public/application-search');
        $client->waitForVisibility('#plan-num');
        continue;
    } // for each row in thre spreadsheet
} // try

catch
(Exception $e) {

} finally {
     echo "\nLogging out...\n";
    if ($client) $client->close();
     echo "Finished.";
}


