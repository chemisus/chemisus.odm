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
     * @var string
     * @field _rev
     */
    private $_rev;

    /**
     * 
     * @var string
     * @field _id
     */
    private $license;
    
    /**
     *
     * @var string
     * @field
     */
    private $make;
    
    /**
     *
     * @var string
     * @field
     */
    private $model;
    
    /**
     *
     * @var int
     * @field
     */
    private $year;
    
    /**
     *
     * @var string
     * @field
     */
    private $wheels;

    /**
     *
     * @var string
     * @field
     */
    private $color;
    
    public function __construct($license, $make, $model, $year, $wheels, $color)
    {
        $this->make = $make;
        
        $this->model = $model;
        
        $this->year = $year;
        
        $this->license = $license;
        
        $this->wheels = $wheels;
        
        $this->color = $color;
    }
}

class Car
    extends Automobile
{
    public function __construct($license, $make, $model, $year, $color)
    {
        parent::__construct($license, $make, $model, $year, 4, $color);
    }
}

class Motorcycle
    extends Automobile
{
    public function __construct($license, $make, $model, $year, $color)
    {
        parent::__construct($make, $model, $year, 2, $color);
    }
}

$server = Chemisus\ODM\Server::Factory('localhost:5984');

$server->deleteDatabase('db');

$server->createDatabase('db');

$database = $server->getDatabase('db');

$database->createDocument(new Car('HYZ 778', 'Toyota', 'Echo', '2002', 'Silver'));

$car = $database->getDocument('HYZ 778');
