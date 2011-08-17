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
require_once('odm.php');

class Automobile
{
    /**
     *
     * @field _id
     */
    private $id;
    
    /**
     *
     * @field _rev
     */
    private $rev;
    
    protected function __construct($id)
    {
        $this->id = $id;
    }
}

class Car
    extends Automobile
{
    /**
     *
     * @field
     */
    private $make;
    
    /**
     *
     * @field
     */
    private $model;
    
    /**
     *
     * @field
     */
    private $year;
    
    /**
     *
     * @field
     */
    private $count = 0;
    
    public function __construct($make, $model, $year)
    {
        parent::__construct($make.'_'.$model.'_'.$year);

        $this->make = $make;
        
        $this->model = $model;
        
        $this->year = $year;
    }
}

echo '<pre>';

function create()
{
    $server = Chemisus\ODM\Server::Factory('localhost:5984');

    $server->deleteDatabase('db');

    $server->createDatabase('db');

    $database = $server->getDatabase('db');
    
    $database->createDocument(new Car('hyundai', 'elantra', '06'));
}

function update()
{
    $server = Chemisus\ODM\Server::Factory('localhost:5984');

    $database = $server->getDatabase('db');

    $document = $database->getDocument('hyundai_elantra_06');

    $document->blah += 1;
    
    $database->updateDocument($document);
}

function fetch()
{
    $server = Chemisus\ODM\Server::Factory('localhost:5984');

    $database = $server->getDatabase('db');
    
    $document = $database->getDocument('hyundai_elantra_06');
    
    print_r($document);
}

update();

fetch();

echo '</pre>';
