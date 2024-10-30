<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;

class ResultScraper {
    private $client;
    private $exam;
    private $year;
    private $board;
    private $roll;
    private $reg;

    public function __construct() {
        $this->client = new Client(['cookies' => true]);
    }

    // Setters for parameters
    public function setExam($exam) {
        $this->exam = $exam;
    }

    public function setYear($year) {
        $this->year = $year;
    }

    public function setBoard($board) {
        $this->board = $board;
    }

    public function setRoll($roll) {
        $this->roll = $roll;
    }

    public function setReg($reg) {
        $this->reg = $reg;
    }

    private function isValidMathQuestion($question) {
        return preg_match('/^\s*(\d+)\s*([+\-])\s*(\d+)\s*$/', $question);
    }

    private function solveMathQuestion($question) {
        if (!preg_match('/^\s*(\d+)\s*([+\-])\s*(\d+)\s*$/', $question, $matches)) {
            throw new Exception("Invalid math question format");
        }
        
        $a = intval($matches[1]);
        $b = intval($matches[3]);
        $op = $matches[2];
        
        switch ($op) {
            case '+': return $a + $b;
            case '-': return $a - $b;
            default: throw new Exception("Unsupported operation");
        }
    }

    private function parseResultHtml($html) {
        $crawler = new Crawler($html);
        $result = [];

        $result['student_info'] = [];
        $crawler->filter('table.black12 tr')->each(function (Crawler $row) use (&$result) {
            $cells = $row->filter('td');
            if ($cells->count() === 4) {
                $key1 = strtolower(str_replace(' ', '_', trim($cells->eq(0)->text())));
                $value1 = trim($cells->eq(1)->text());
                $key2 = strtolower(str_replace(' ', '_', trim($cells->eq(2)->text())));
                $value2 = trim($cells->eq(3)->text());
                
                $result['student_info'][$key1] = $value1;
                $result['student_info'][$key2] = $value2;
            }
        });

        $result['grade_sheet'] = [];
        $crawler->filter('table.black12:last-of-type tr')->each(function (Crawler $row, $i) use (&$result) {
            if ($i === 0) return;
            $cells = $row->filter('td');
            if ($cells->count() === 3) {
                $subject = [
                    'code' => trim($cells->eq(0)->text()),
                    'name' => trim($cells->eq(1)->text()),
                    'grade' => trim($cells->eq(2)->text())
                ];
                $result['grade_sheet'][] = $subject;
            }
        });

        return $result;
    }

    public function scrapeResults() {
        $url = "http://www.educationboardresults.gov.bd/";

        try {
            $response = $this->client->get($url);
            $html = $response->getBody()->getContents();

            $crawler = new Crawler($html);
            $mathQuestion = null;

            $crawler->filter('td')->each(function (Crawler $node) use (&$mathQuestion) {
                $text = trim($node->text());
                if ($this->isValidMathQuestion($text)) {
                    $mathQuestion = $text;
                    return false;
                }
            });

            if (!$mathQuestion) {
                return ["error" => "Valid math question not found on the page."];
            }

            try {
                $mathAnswer = $this->solveMathQuestion($mathQuestion);
            } catch (Exception $e) {
                return ["error" => "Error solving math question: " . $e->getMessage()];
            }

            $formData = [
                'sr' => '3',
                'et' => '2',
                'exam' => $this->exam,
                'year' => $this->year,
                'board' => $this->board,
                'roll' => $this->roll,
                'reg' => $this->reg,
                'value_s' => strval($mathAnswer),
                'button2' => 'Submit'
            ];

            $response = $this->client->post($url . "result.php", [
                'form_params' => $formData
            ]);

            $resultHtml = $response->getBody()->getContents();
            return $this->parseResultHtml($resultHtml);

        } catch (RequestException $e) {
            return ["error" => "An error occurred: " . $e->getMessage()];
        }
    }
}
?>
