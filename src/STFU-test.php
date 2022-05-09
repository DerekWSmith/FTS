<?php
require 'vendor/autoload.php' ;
require_once( 'obp-config.php' );

// See: https://github.com/symfony/panther
// kill $(lsof -i:9515)
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
//
use Facebook\WebDriver\WebDriverExpectedCondition;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Panther\Client;
















$val = getopt('', ["spreadsheet:"]);
class address
{
	public string $ftsjobnumber  ;
	public string $lotnumber  ;
	public string $streetno  ;
	public string $street  ;
	public string $suburb  ;




}
//
/// / DEV override
// --spreadsheet="/Volumes/My Passport/CheckPoint/FTS/FTS - Job Status.xlsx"
if (count($val) != 1)
	exit("Must give one parameter: \n eg --spreadsheet=\"/Volumes/My Passport/CheckPoint\" --tmpfolder=\"/tmp/VidcData\"\nzipfolder - dir where the zips have been downloaded\ntmpfolder - folder to place unzipped files for DB import");
// '/var/lib/mysql-files/' on Debbie.
$val = (object) $val ;
if (!file_exists($val->spreadsheet))
	echo "{$val->spreadsheet} not found." ;


// test ss read

echo "--Reading {$val->spreadsheet}" . PHP_EOL  ;
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($val->spreadsheet);

$worksheet = $spreadsheet->getSheet(0);

// Get the highest row and column numbers referenced in the worksheet
$highestRow = $worksheet->getHighestRow(); // e.g. 10
$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5
$addresses = [] ;


echo "Login starting...." . PHP_EOL;
ini_set( 'memory_limit', '2048M' );  // lot of databas


$mydb     = new mysqli( getenv('DB_HOST') , getenv('DB_USER'), getenv('DB_PASSWORD'), getenv('DB_NAME'));
$result   = $mydb->select_db( 'obpii_spatial' );
$query = $mydb->prepare("SELECT PLAN_NUMBER FROM obpii_spatial.obpSearch where lot_number=? AND EZI_Address like ?") ;


try {
// Set the environment.
	putenv( 'PANTHER_NO_HEADLESS=0' );
	$prefs = [
		'safebrowsing.disable_download_protection' => true
	];


	// on local
	$client = Client::createChromeClient( null, null, $prefs );
//	// on Linux server
//	// $client = Client::createChromeClient("/usr/bin/chromedriver", null, $prefs);
//
//
	echo 'Going to https://www.spear.land.vic.gov.au/spear/app/login' . PHP_EOL;
	$client->get( 'https://www.spear.land.vic.gov.au/spear/app/login' );
	echo "--Logging in......\n";
	$client->findElement(WebDriverBy::id('username'))->sendKeys('dersmi40') ;
	$client->findElement(WebDriverBy::id('password'))->sendKeys('Pingvin4') ;
	$client->findElement(WebDriverBy::className('spearbuttons'))->click();








	for ($row = 2; $row <= $highestRow; ++$row) {



		$a = new address() ;

	    if (strtolower($worksheet->getCellByColumnAndRow(10, $row)->getValue() ?? '') != 'pending')
	    {
			continue;
	    }
		$a->ftsjobnumber = $worksheet->getCellByColumnAndRow(2, $row)->getValue() ?? '';
		$a->lotnumber = $worksheet->getCellByColumnAndRow(3, $row)->getValue() ?? '';
		$a->streetno = $worksheet->getCellByColumnAndRow(4, $row)->getValue() ?? '';
		$a->street = $worksheet->getCellByColumnAndRow(5, $row)->getValue() ?? '';
		$a->suburb = $worksheet->getCellByColumnAndRow(6, $row)->getValue() ?? '';
		$addresses[] = $a ;

		// go find the spi
	$p1 = '' ;
		if (!empty($a->streetno))
			$p1 = "{$a->streetno} {$a->street} %" ;
		else
			$p1 = "%{$a->street}%" ;

		echo "--Seeking plan number for ($a->lotnumber) {$a->streetno} {$a->street} {$a->suburb}" . PHP_EOL ;
		$query = "SELECT PLAN_NUMBER FROM obpii_spatial.obpSearch where lot_number=\"{$a->lotnumber}\" AND EZI_Address like \"{$p1}\"" ;
	$result = $mydb->query($query) ;

//
//	$query->bind_param('ss',  $a->lotnumber, $p1 ) ;
//		$query->execute() ;
//		if ($query->num_rows() === 0 ) continue; // not found

	if($result->num_rows != 1 )
	{
		echo "----Not Found.  Next..." . PHP_EOL ;

		$worksheet->setCellValueByColumnAndRow(34, $row, 'Not Found') ;


		$writer = new Xlsx($spreadsheet);
		$writer->save('hello world.xlsx');


		continue ;
	}  // not 1 means we found zero, or several.  Ain't nobody got time for that
	$plannumber = $result->fetch_row()[0]  ;  ;
		echo "--Found. {$plannumber}." . PHP_EOL ;
	if (null == $plannumber) continue ;


		$client->waitFor('#plan-num')->isDisplayed() ;
// Enter plan num
	$client->findElement(WebDriverBy::id('plan-num'))->sendKeys($plannumber) ;
	// press search button
		echo "--Searching in SPEAR" . PHP_EOL ;
	$client->findElement(WebDriverBy::className('button__btn'))->click() ;

  $client->waitFor('.footer')->isDisplayed() ;

  $pstr = $client->getPageSource() ;
	if (str_contains($pstr, 'Current Applications - No results found for "Plan Number:')) {
		echo "----No result found for $plannumber." . PHP_EOL ;




		if (str_contains($pstr, 'Current Applications - Results for')) {
			$client = $client->get( 'https://www.spear.land.vic.gov.au/spear/app/public/application-search' );
			$client->waitForVisibility( '#plan-num' );
			continue;
		}


	}

	sleep(0) ;

	// this is not the answer, right here.
		$client = $client->get('https://www.spear.land.vic.gov.au/spear/app/public/application-search') ;
		$client->waitForVisibility('#plan-num') ;
}






}
catch (Exception $e)
{

}
finally {
	echo "Logging out...\n";

	echo "--Closed driver.\n";
}


