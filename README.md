# BDResultScrapper  

## Usage 
```
<?php
include("ResultScraper.php");
// Set the response header to JSON
header('Content-Type: application/json');

// Example usage
$scraper = new ResultScraper();
$scraper->setExam('ssc');
$scraper->setYear('2020');
$scraper->setBoard('rajshahi');
$scraper->setRoll('124858');
$scraper->setReg('1712684463');

$result = $scraper->scrapeResults();
echo json_encode($result, JSON_PRETTY_PRINT);

?>
```
