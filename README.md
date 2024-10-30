# BDResultScrapper  

BDResultScrapper is a  PHP-based script which scrapes Bangladeshi board examination results from an external source, formats them into JSON, and displays them. The script is designed to scrape results based on specific parameters such as exam type, year, board, roll number, and registration number.

## Features

- **Flexible Scraping**: Set various exam parameters (exam type, year, board, roll number, and registration number) to fetch accurate result data.
- **JSON Response**: Results are output as JSON, making it easy to integrate into other applications.

## Getting Started

### Prerequisites

- PHP installed on your server or local machine.
- `ResultScraper.php` file, which contains the necessary scraping functions.

### Installation

1. Clone or download this repository.
2. Place `ResultScraper.php` in the same directory as your main script.

### Usage

```php
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

This script does the following:

1. Includes `ResultScraper.php`, where the main scraping logic is defined.
2. Sets the response header to JSON.
3. Creates an instance of the `ResultScraper` class and sets the exam details.
4. Calls `scrapeResults()` to fetch the data, which is then displayed as formatted JSON.

### Example Output

```json
{
    "exam": "ssc",
    "year": "2020",
    "board": "rajshahi",
    "roll": "124858",
    "registration": "1712684463",
    "result": {
        // Result details here
    }
}
```

## License

This project is licensed under the MIT License. 

---

Feel free to modify the `ResultScraper.php` to suit your requirements and integrate this scraper API into your larger application.

## Readme Credit
The Credit of this README.md goes to ChatGPT. 🥹
