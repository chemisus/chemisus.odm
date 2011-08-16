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
require_once('document.php');

$connection = \Chemisus\ODM\Connection::Factory();

function test($message, $transaction, $error=false)
{
    echo '<div class="test">';
    echo "<h3>{$message}</h3>";
    if (!$error && !$transaction['response']['error'])
    {
        echo '<p class="success">No unexpected error returned</p>';
    }
    else if ($error && $error === $transaction['response']['error']['error'])
    {
        echo '<p class="success">Error returned as expected.</p>';
    }
    else if ($error && $error !== $transaction['response']['error']['error'])
    {
        echo '<p class="error">Expected error did not return.</p>';
    }
    else
    {
        echo '<h4 class="error">'.$transaction['response']['error']['error'].'</h4>';
        echo '<p class="error">';
        echo $transaction['response']['error']['reason'];
        echo '</p>';
    }
    echo '<pre class="request">';
    print_r($transaction['request']);
    echo '</pre>';
    echo '<pre class="head">';
    print_r($transaction['response']['head']);
    echo '</pre>';
    echo '<pre class="body">';
    print_r($transaction['response']['body']);
    echo '</pre>';
    echo '</div>';
}



test(
    'Get MOTD without Slashes',
    $connection->get(),
    false
);

test(
    'Get MOTD with Slashes',
    $connection->get('/////'),
    false
);

test(
    'List All Databases without Slashes',
    $connection->get('_all_dbs'),
    false
);

test(
    'List All Databases with Slashes',
    $connection->get('/_all_dbs/'),
    false
);

test(
    'Get Non-Existent Database',
    $connection->get('/db2/'),
    'not_found'
);

test(
    'Delete Non-Existent Database',
    $connection->delete('/db2/'),
    'not_found'
);

test(
    'Get Document from Non-Existent Database',
    $connection->get('/db2/doc2/'),
    'not_found'
);

test(
    'Put Document to Non-Existent Database',
    $connection->put('/db2/doc2/', array('a'=>'A')),
    'not_found'
);

test(
    'Post Document to Non-Existent Database',
    $connection->post('/db2/', array('a'=>'A')),
    'not_found'
);

test(
    'Delete Document from Non-Existent Database',
    $connection->get('/db2/doc2/'),
    'not_found'
);

test(
    'Create Database',
    $connection->put('/db1'),
    false
);

test(
    'Create Database Again',
    $connection->put('/db1'),
    'file_exists'
);

test(
    'Put Document',
    $connection->put('/db1/a', array('a'=>'a')),
    false
);

test(
    'Put Document',
    $connection->put('/db1/a', array('a'=>'a')),
    'conflict'
);

test(
    'Post Document',
    $connection->post('/db1/', array('b'=>'b')),
    false
);

test(
    'Put Document with ID',
    $connection->post('/db1/', array('_id'=>'c')),
    false
);

test(
    'Post Document with ID',
    $connection->post('/db1/', array('_id'=>'b')),
    false
);

test(
    'Get Document',
    $document = $connection->get('/db1/a'),
    false
);

test(
    'Delete Document',
    $connection->delete('/db1/a?rev=1-71b1114b184818dd4bbebe5ff10f7ba4'),
    false
);

class A
{
    private $a = 1;
}

class B
    extends A
{
    private $_id = 'ba';
    
    private $a = 2;
    public $c;
}

class C
    extends B
{
    private $a = 3;
    
    public function A()
    {
        return $this->a;
    }

    public function B($a)
    {
        return $this->c;
    }
}

$b = new B();

$b->c = new C();


test(
    'Create Document BA',
    $connection->post('/db1/', \Chemisus\ODM\Document::Convert($b)),
    false
);
echo '<pre>';
print_r(\Chemisus\ODM\Document::Convert($b));

test(
    'Get Document BA',
    $document = $connection->get('/db1/ba'),
    false
);

$data = $document['response']['body'];
echo '<pre>';
print_r($data);
echo '</pre>';

test(
    'Delete Database',
    $connection->delete('/db1'),
    false
);
