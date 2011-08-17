chemisus.odm
============

PHP Object Document Mapper (ODM) for [CouchDB](http://couchdb.apache.org/).

---

Summary
-------

CouchDB is a no-sql database. Instead of having tables and records like MySql,
it instead stores everything as a document with an id. As a result, there is
no specific structure to these documents, and the structure of a single document
will come from the data that is saved at the time of creation. It is up to the
developer to make sure that some form of structure is kept. CouchDB stores each
document as a JSON object.

What chemisus.odm will attempt to do is make it easier for the developer to
store and retrieve these documents. If the document happens to be a PHP object,
then chemisus.odm will automatically "serialize" and "unserialize" the object to
and from a JSON object. Quotes are on serialize and unseralize, because these
processes are different then just calling php's serialize() and unseralize()
functions, which return a string that is hard to read and even harder to manipulate.

How chemisus.odm serializes differently than PHP is that it first creates an
array with at least two key value set to the class name of the object. These keys
have unique names "$$object" and "$$base". $$object is a string value representing
the class name of the object being saved. $$base is an array value, where each key
in the array is a name of a class that the object inherits, and each value is an
array of variables and values that are defined in that class. Methods that do
not require any parameters can also be stored, as they will have no effect upon
unserialization of the object.

Additionally, any field that has @field in its DocComment will be set in the
top level of the JSON object.

## Example

### PHP Code:

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

### PHP Object:

    Car Object
    (
        [make:Car:private] => hyundai
        [model:Car:private] => elantra
        [year:Car:private] => 06
        [count:Car:private] => 0
        [id:Object:private] => hyundai_elantra_06
        [rev:Object:private] => 5-6d01dc52df805f82febac528ac1074a7
    )

### JSON Object (After saving a few times):

    {
       "_id": "hyundai_elantra_06",
       "_rev": "5-6d01dc52df805f82febac528ac1074a7",
       "$$object": "Car",
       "$$base": {
           "Car": {
               "make": "hyundai",
               "model": "elantra",
               "year": "06",
               "count": 0
           },
           "Automobile": {
               "id": "hyundai_elantra_06",
               "rev": "4-f6fb4ec4d69d0870dd78efee99e134f6"
           }
       },
       "make": "hyundai",
       "model": "elantra",
       "year": "06",
       "count": 0
    }

---

Requirements
------------
chemisus.odm so far has the following requirements:

* PHP 5.3 (namespaces)
* php5-curl installed (connections)

---

Installing
----------

For the moment, there really isn't any sort of installation
process. Just include odm.php where appropriate, and it
will include the files necessary for chemisus.odm.

To include odm:

    require_once('odm.php');

---

Classes
-------

There are currently four main objects in chemisus.odm:

* \Chemisus\ODM\Connection
* \Chemisus\ODM\Server
* \Chemisus\ODM\Database
* \Chemisus\ODM\Document

To actually use chemisus.odm, you really only need to interact
with the server and database. The server will handle the connections,
and the database will handle the documents.

--- 

Examples
--------

The following examples will assume you have a couchdb server
running on localhost at default port 5984.

### Server and Database Objects

    $server = \Chemisus\ODM\Server::Factory('localhost:5984');
    $database = $server->getDatabase('dbname');

### Database Creation, Deletion

    $server->createDatabase('dbname');
    $server->deleteDatabase('dbname');

### Document Read, Creation, Deletion

    $database->getDocument('docid');
    $database->createDocument($object);
    $database->deleteDocument($object);

