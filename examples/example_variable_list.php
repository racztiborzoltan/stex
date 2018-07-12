<?php
use Stex\VariableList;

require_once '../vendor/autoload.php';

header('Content-Type: text/plain');

$variables = new VariableList();
$variables->set('string', 'Lorem Ipsum');
$variables->set('integer', 123456);
$variables->set('float', 12345.67);
$variables->set('boolean_true', true);
$variables->set('boolean_false', false);
$variables->set('foo', 'bar');
$variables->set('var', 12345);
$variables->set('html_content', '<h3>asgagd</h3><script type="adgs">adga();</script>');
$variables->set('multilevel', [
    'level1.1' => 'value 1.1',
    'level1.2' => [
        'level2.1' => 'value 2.1',
        'level2.2' => 'value 2.2',
    ],
    'level1.3' => 'value 1.3',
    'level1.4' => new \stdClass(),
]);
$variables->set('numeric_indexed_array', [
    'numeric_indexed_value_1',
    'numeric_indexed_value_2',
    'numeric_indexed_value_3',
]);

$dom_document = $variables->toDomDocument('variables');


$dom_document->formatOutput = true;
echo $dom_document->saveXML($dom_document);
