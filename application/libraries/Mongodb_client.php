<?php
// require_once(APPPATH . 'libraries/mongo-php-library-1.x/src/Client.php');
// use MongoDB\Client;
use MongoDB\Client as MongoClient;
use MongoDB\Driver\Session;

class mongodb_client {
    public $client;
    public $database;

    public function __construct() {
        // Load the configuration for MongoDB connection
        $CI =& get_instance();
        $CI->config->load('mongo_db');  // Load config file
        $mongo_config = $CI->config->item('mongo_db');

        $config = [
            'hostname' => $mongo_config['default']['hostname'],
            'port' => $mongo_config['default']['port'],
            'username' => $mongo_config['default']['username'],
            'password' => $mongo_config['default']['password'],
            'database' => $mongo_config['default']['database']
        ];

        // MongoDB URI (use authentication if required)
        $uri = "mongodb://{$config['hostname']}:{$config['port']}/{$config['database']}?authSource={$config['database']}";

        try {
            // Create MongoDB Client
            $this->client = new MongoClient($uri); // Use the new MongoDB\Client class
            $this->database = $this->client->{$config['database']}; // Access the specified database

        } catch (Exception $e) {
            log_message('error', "MongoDB connection failed: " . $e->getMessage());
            show_error('Could not connect to MongoDB');
        }
    }

    // Start MongoDB Session for Transaction
    public function startSession() {
        try {
            // Starting the session
            return $this->client->startSession();
        } catch (Exception $e) {
            log_message('error', "Failed to start MongoDB session: " . $e->getMessage());
            throw new Exception("Failed to start session: " . $e->getMessage());
        }
    }

    // Insert document method which returns the inserted ID
    public function insertDocument($collectionName, $document, $session) {
        $collection = $this->database->$collectionName;

        try {
            // Insert the document within a transaction
            $insertResult = $collection->insertOne($document, ['session' => $session]);
            
            $document['_id'] = $insertResult->getInsertedId(); // Return inserted document's ID
            // $idString  = (string) $insertedId;
            return $document;
        } catch (Exception $e) {
            log_message('error', "Insert failed: " . $e->getMessage());
            throw new Exception("Insert failed: " . $e->getMessage());
        }
    }

    // Commit Transaction after operations
    public function commitTransaction($session) {
        try {
            $session->commitTransaction();
        } catch (Exception $e) {
            log_message('error', "Failed to commit MongoDB transaction: " . $e->getMessage());
            throw new Exception("Failed to commit transaction: " . $e->getMessage());
        }
    }

    // Abort Transaction in case of errors
    public function abortTransaction($session) {
        try {
            $session->abortTransaction();
        } catch (Exception $e) {
            log_message('error', "Failed to abort MongoDB transaction: " . $e->getMessage());
            throw new Exception("Failed to abort transaction: " . $e->getMessage());
        }
    }

    // get document method which returns the document data
    public function getDocument($action='single', $collectionName, $filter=[], $session=null, $skip=0, $limit=100, $projection=null, $sort=null) {
        $collection = $this->database->$collectionName;
        $options = ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']];
        if ($session !== null) {
            $options['session'] = $session;
        }

        if ($action == 'single') {
            $result = $collection->findOne($filter, $options);
            if ($result && isset($result['_id']) && $result['_id'] instanceof MongoDB\BSON\ObjectId) {
                $result['_id'] = ['$id' => (string) $result['_id']];
            }
            return $result;
        }
        if ($action == 'multiple') {
            if ((int)$skip > 0) {
                $options['skip'] = (int)$skip;
            }
            if ((int)$limit > 0) {
                $options['limit'] = (int)$limit;
            }
            if (is_array($projection) && !empty($projection)) {
                if (array_values($projection) === $projection) {
                    $projectionFields = array();
                    foreach ($projection as $field) {
                        $projectionFields[$field] = 1;
                    }
                    $options['projection'] = $projectionFields;
                } else {
                    $options['projection'] = $projection;
                }
            }
            if (is_array($sort) && !empty($sort)) {
                $options['sort'] = $sort;
            }

            $cursor = $collection->find($filter, $options);
            $results = array();
            foreach ($cursor as $doc) {
                if (isset($doc['_id']) && $doc['_id'] instanceof MongoDB\BSON\ObjectId) {
                    $doc['_id'] = array('$id' => (string)$doc['_id']);
                }
                $results[] = $doc;
            }
            return $results;
        }
        if ($action == 'count') {
            return $collection->countDocuments($filter, $options);
        }
        return false;
    }


    // Update document method which returns update result details
    public function updateDocument($collectionName, $filter, $updateData, $session) {
        $collection = $this->database->$collectionName;

        try {
            // Perform the update operation within the transaction session
            $updateResult = $collection->updateMany(
                $filter,
                $updateData,
                ['session' => $session]
            );

            // Prepare the result summary
            $result = [
                'matchedCount'   => $updateResult->getMatchedCount(),
                'modifiedCount'  => $updateResult->getModifiedCount(),
                'upsertedId'     => $updateResult->getUpsertedId(),
                'success'        => ($updateResult->getModifiedCount() > 0)
            ];

            return $result;

        } catch (Exception $e) {
            log_message('error', "Update failed: " . $e->getMessage());
            throw new Exception("Update failed: " . $e->getMessage());
        }
    }

}
