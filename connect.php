<?php
include('config.php');
$DBManager = new MongoDB\Driver\Manager(server);
$bulk = new MongoDB\Driver\BulkWrite;

// foreach($_POST['check_list'] as $exam) {
//     $exams->addChild('exame', $exam);
// }

$doc = [
    "id" => 12134,
    'name' => 123,
    'exames' => ["vlau", "sdasq", 'sadq!!!']
];

$bulk->insert($doc);


$doc = [
    "ied" => 1211241434,
    'naeeeme' => 151515152523,
];

$bulk->insert($doc);

$DBManager->executeBulkWrite('planoSaude.laboratorios', $bulk);
?>