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

class A
{
    private $class = 'A';
    
    public $blah = 0;
}

class B
    extends A
{
    private $class = 'B';
}

class Test
    extends B
{
    /**
     *
     * @field _id
     */
    private $_id = 'a';
    
    /**
     *
     * @field _rev
     */
    private $_rev;

    private $class = 'Test';
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function getRev()
    {
        return $this->_rev;
    }
}

echo '<pre>';

function create()
{
    $server = Chemisus\ODM\Server::Factory('localhost:5984');

    $server->deleteDatabase('db');

    $server->createDatabase('db');

    $database = $server->getDatabase('db');

    $database->createDocument(new Test());
}

function update()
{
    $server = Chemisus\ODM\Server::Factory('localhost:5984');

    $database = $server->getDatabase('db');
    
    $document = $database->getDocument('a');
    
    $document->blah += 1;
    
    $database->updateDocument($document);
}

function fetch()
{
    $server = Chemisus\ODM\Server::Factory('localhost:5984');

    $database = $server->getDatabase('db');
    
    $document = $database->getDocument('a');
    
    print_r($document);
}

//create();

update();

fetch();

echo '</pre>';
