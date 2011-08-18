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
     * @field _id
     */
    private $_id;
    
    /**
     *
     * @var string
     * @field _rev
     */
    private $_rev;
    
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
    
    public function __construct($wheels, $color)
    {
        $this->wheels = $wheels;
        
        $this->color = $color;
    }
}

class Car
    extends Automobile
{
    public function __construct($color)
    {
        parent::__construct(4, $color);
    }
}

class Motorcycle
    extends Automobile
{
    public function __construct($color)
    {
        parent::__construct(2, $color);
    }
}

$server = Chemisus\ODM\Server::Factory('localhost:5984');

$database = $server->getDatabase('db');

$database->createDocument(new Car('red'));

$database->createDocument(new Motorcycle('black'));
