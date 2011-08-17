<style>
    .test {
        background-color: seashell;
        border: 1px black solid;
    }
    
    .test h3 {
        margin: 0px;
    }
    
    .test:nth-child(even) {
        background-color: beige;
    }
    
    .test pre {
        border: 1px black dotted;
        display: none;
    }
    
    .test .success {
        background-color: lightgreen;
        display: none;
    }
    
    .test .error {
        background-color: lightgray;
    }
    
    .test p {
        font-family: monospace;
        padding: 5px;
    }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.15/jquery-ui.min.js"></script>
<script>
    $('h3').live('click', function () {
        $(this).parents('.test:first').find('.success').slideToggle();
        $(this).parents('.test:first').find('pre').slideToggle();
    });
</script>
<?php
require_once('connection.php');
require_once('server.php');
require_once('database.php');
require_once('document.php');

$server = Chemisus\ODM\Server::Factory('localhost:5984');

print_r($server->deleteDatabase('db'));

print_r($server->createDatabase('db'));

$database = $server->getDatabase('db');

class Test
{
    private $_id = 'a';
    
    private $_rev;
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function getRev()
    {
        return $this->_rev;
    }
}

print_r($database->createDocument(new Test()));

echo '<pre>';
print_r($document = $database->getDocument('a'));

print_r($database->updateDocument($document));
print_r($document = $database->getDocument('a'));

print_r($database->updateDocument($document));
print_r($document = $database->getDocument('a'));

print_r($database->updateDocument($document));
print_r($document = $database->getDocument('a'));

print_r($database->updateDocument($document));
print_r($document = $database->getDocument('a'));

print_r($database->updateDocument($document));
print_r($document = $database->getDocument('a'));

print_r($database->updateDocument($document));
print_r($document = $database->getDocument('a'));

echo '</pre>';

print_r($database->deleteDocument($document));
