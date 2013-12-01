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
require_once('Odm.php');

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
     */
    private $license;
    
    /**
     *
     * @var string
     */
    private $make;
    
    /**
     *
     * @var string
     */
    private $model;
    
    /**
     *
     * @var int
     */
    private $year;
    
    /**
     *
     * @var string
     */
    private $wheels;
    
    /**
     * 
     * @var int
     */
    private $miles = 0;

    /**
     *
     * @var string
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

    /**
     * @field _id
     * @return string
     */
    public function getLicense()
    {
        return $this->license;
    }
    
    /**
     * @field make
     * @return string
     */
    public function getMake()
    {
        return $this->make;
    }
    
    /**
     * @field model
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }
    
    /**
     * @field year
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }
    
    /**
     * @field color
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @field miles
     * @return int
     */
    public function getMiles()
    {
        return $this->miles;
    }
    
    /**
     * @field wheels
     * @return string
     */
    public function getWheels()
    {
        return $this->wheels;
    }
    
    public function addMiles($miles)
    {
        $this->miles += $miles;
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
echo '<pre>';

$server = Chemisus\ODM\Server::Factory('localhost:5984');

$server->deleteDatabase('db');

$server->createDatabase('db');

$database = \Chemisus\ODM\Database::Factory('localhost:5984', 'db', true);

$model = \Chemisus\ODM\Model::Factory('Car');

$model->install();

$database->createDocument(new Car('ABC 123', 'Toyota', 'Echo', '2002', 'Silver'));
$database->createDocument(new Car('DEF 456', 'Hyundai', 'Elantra', '2004', 'Red'));
$database->createDocument(new Car('GHI 789', 'Chevrolet', 'Camaro', '2007', 'Black'));
$database->createDocument(new Car('JKL 147', 'Jeep', 'Range Rover', '2009', 'Blue'));

$view = $database->getView('Car', 'all');

foreach ($view as $key=>$value)
{
    $value['value']->addMiles(25);
    
    $database->updateDocument($value['value']);
}

$view = $database->getView('Car', 'count');



print_r($view);
/**/