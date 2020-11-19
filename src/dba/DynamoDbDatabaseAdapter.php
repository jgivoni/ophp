<?php

namespace Ophp\dba;

class DynamoDbDatabaseAdapter
{
    /**
     *
     * @var \Aws\DynamoDb\DynamoDbClient
     */
    protected $client, $marshaler;

    /**
     *
     * @var AmazonDbOptions
     */
    protected $options;

    /**
     *
     * @var array Total consumed capacity by table
     */
    protected $consumedCapacity;

    /**
     * 
     * @param array $options Array or array access object
     */
    public function __construct($options = [])
    {
        if (!$options instanceof \Ohp\Options) {
            $options = new AmazonDbOptions($options);
        }
        $this->options = $options;
    }

    protected function getCredentials()
    {
        if (!isset($this->credentials)) {
            $credentialsFile = $this->options->credentialsFile;

            // Don't specify a credentials file if you want to autodetect or use an EC2 embedded role
            if (!empty($credentialsFile)) {
                if (!file_exists($credentialsFile)) {
                    throw new \Exception('AWS credentials file not found');
                }
                $credentialsProvider = \Aws\Credentials\CredentialProvider::ini('default', $credentialsFile);
                $credentialsProvider = \Aws\Credentials\CredentialProvider::memoize($credentialsProvider);
                $this->credentials = $credentialsProvider;
            }
        }
        return $this->credentials;
    }

    protected function getClient()
    {
        if (!isset($this->client)) {
            $this->client = new \Aws\DynamoDb\DynamoDbClient([
                'credentials' => $this->getCredentials(),
                'region' => $this->options->region,
                'version' => 'latest',
            ]);
        }
        return $this->client;
    }

    protected function getMarshaler()
    {
        if (!isset($this->marshaler)) {
            $this->marshaler = new \Aws\DynamoDb\Marshaler;
        }
        return $this->marshaler;
    }

    /**
     * Inserts a new item into a table
     * 
     * @param string $table
     * @param array $item
     * @param string $partitionKey Name of the partition key column/attribute
     * @returns string Primary Key
     */
    public function insert($table, $item, $partitionKey)
    {
        try {
            $this->getClient()->putItem([
                'TableName' => $table,
                'Item' => $this->getMarshaler()->marshalItem($item),
                'ConditionExpression' => 'attribute_not_exists(' . $partitionKey . ')',
                'ReturnConsumedCapacity' => 'TOTAL',
            ]);
            $result = true;
        } catch (\Aws\DynamoDb\Exception\DynamoDbException $e) {
            if ($e->getAwsErrorCode() == 'ConditionalCheckFailedException') {
                // Item already exists
                $result = false;
            } else {
                throw new \Exception('Insert query failed', 0, $e);
            }
        }
        return isset($result) ? $result : null;
    }

    public function updateAttributes($table, $item, $partitionKey)
    {
        try {
            foreach ($item as $attribute => $value) {
                if ($attribute == $partitionKey) {
                    continue;
                }
                $attributeUpdates[$attribute] = [
                    'Value' => [
                        'S' => $value,
                    ],
                    'Action' => 'PUT',
                ];
            }
            $result = $this->getClient()->updateItem([
                'TableName' => $table,
                'Key' => $this->getMarshaler()->marshalItem([
                    $partitionKey => $item[$partitionKey],
                ]),
                'AttributeUpdates' => $attributeUpdates,
                'ReturnConsumedCapacity' => 'TOTAL',
            ]);
            $result = true;
        } catch (\Aws\DynamoDb\Exception\DynamoDbException $e) {
            if ($e->getAwsErrorCode() == 'ConditionalCheckFailedException') {
                // Item not found
                $result = false;
            } else {
                throw new \Exception('Update query failed', 0, $e);
            }
        }
        return $result;
    }

    public function delete($table, $item, $partitionKey)
    {
        try {
            $result = $this->getClient()->deleteItem([
                'TableName' => $table,
                'Key' => $this->getMarshaler()->marshalItem([
                    $partitionKey => $item[$partitionKey],
                ]),
                'ConditionExpression' => 'attribute_exists(' . $partitionKey . ')',
                'ReturnConsumedCapacity' => 'TOTAL',
            ]);
            $result = true;
        } catch (\Aws\DynamoDb\Exception\DynamoDbException $e) {
            if ($e->getAwsErrorCode() == 'ConditionalCheckFailedException') {
                // Item not found
                $result = false;
            } else {
                throw new \Exception('Delete query failed', 0, $e);
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $table
     * @param array $key attribute=>value pairs
     * @return type
     * @throws \Exception
     */
    public function get($table, $key)
    {
        try {
            $result = $this->getClient()->getItem([
                'TableName' => $table,
                'Key' => $this->getMarshaler()->marshalItem($key),
                'ReturnConsumedCapacity' => 'TOTAL',
            ]);
            $this->addConsumedCapacity($result->get('ConsumedCapacity'));
            if ($result->get('Item') === null) {
                throw new \Exception('Item not found: ' . key($key) . ' = ' . current($key) . '  in table ' . $table);
            }
            $result = $this->getMarshaler()->unmarshalItem($result->get('Item'));
        } catch (\Aws\DynamoDb\Exception\DynamoDbException $e) {
            throw new \Exception('Get query failed', 0, $e);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $result = null;
        }
        return $result;
    }

    /**
     * 
     * @param string $table
     * @param array $key attribute=>value pairs
     * @return type
     * @throws \Exception
     */
    public function getBatch($table, $keys)
    {
        $keysMarshalled = array_map(function($key) {
            return $this->getMarshaler()->marshalItem($key);
        }, $keys);
        try {
            $result = $this->getClient()->batchGetItem([
                'RequestItems' => [
                    $table => [
                        'Keys' => $keysMarshalled,
                    ]
                ],
                'ReturnConsumedCapacity' => 'TOTAL',
            ]);
            $items = $result->get('Responses');
            if (isset($items[$table])) {
                $results = array_map(function($item) {
                    return $this->getMarshaler()->unmarshalItem($item);
                }, $items[$table]);
            } else {
                $results = [];
            }
        } catch (\Aws\DynamoDb\Exception\DynamoDbException $e) {
            throw new \Exception('GetBatch query failed', 0, $e);
        } catch (\Exception $e) {
            $results = null;
        }
        return $results;
    }

    /**
     * Adds elements to a string set or number set
     * 
     * All the elements for each attribute (1st level only) must be of the same type
     * 
     * @param type $table
     * @param type $key
     * @param type $elements
     * @return boolean
     * @throws \Exception
     */
    public function addSetElements($table, $key, $elements)
    {
        try {
            $attributeUpdates = [];
            foreach ($elements as $attribute => $values) {
                $type = is_string(current($values)) ? 'SS' : 'NS';
                $values = array_map(function($value) use ($type) {
                    return $type == 'SS' ? (string) $value : (float) $value;
                }, $values);
                $attributeUpdates[$attribute] = [
                    'Value' => [
                        $type => $values,
                    ],
                    'Action' => 'ADD',
                ];
            }
            $result = $this->getClient()->updateItem([
                'TableName' => $table,
                'Key' => $this->getMarshaler()->marshalItem($key),
                'AttributeUpdates' => $attributeUpdates,
                'ReturnConsumedCapacity' => 'TOTAL',
            ]);
            $result = true;
        } catch (\Aws\DynamoDb\Exception\DynamoDbException $e) {
            if ($e->getAwsErrorCode() == 'ConditionalCheckFailedException') {
                // Item not found
                $result = false;
            } else {
                throw new \Exception('Update query failed', 0, $e);
            }
        }
        return $result;
    }

    /**
     * Remove elements to a string set or number set
     * 
     * All the elements for each attribute (1st level only) must be of the same type
     * 
     * @param type $table
     * @param type $key
     * @param type $elements
     * @return boolean
     * @throws \Exception
     */
    public function removeSetElements($table, $key, $elements)
    {
        try {
            $attributeUpdates = [];
            foreach ($elements as $attribute => $values) {
                $type = is_string(current($values)) ? 'SS' : 'NS';
                $values = array_map(function($value) use ($type) {
                    return $type == 'SS' ? (string) $value : (float) $value;
                }, $values);
                $attributeUpdates[$attribute] = [
                    'Value' => [
                        $type => $values,
                    ],
                    'Action' => 'DELETE',
                ];
            }
            $result = $this->getClient()->updateItem([
                'TableName' => $table,
                'Key' => $this->getMarshaler()->marshalItem($key),
                'AttributeUpdates' => $attributeUpdates,
                'ReturnConsumedCapacity' => 'TOTAL',
            ]);
            $result = true;
        } catch (\Aws\DynamoDb\Exception\DynamoDbException $e) {
            if ($e->getAwsErrorCode() == 'ConditionalCheckFailedException') {
                // Item not found
                $result = false;
            } else {
                throw new \Exception('Update query failed', 0, $e);
            }
        }
        return $result;
    }

    protected function addConsumedCapacity($consumedCapacityResult)
    {
        $table = $consumedCapacityResult['TableName'];
        $capacityUnits = $consumedCapacityResult['CapacityUnits'];
        if (!isset($this->consumedCapacity[$table])) {
            $this->consumedCapacity[$table] = 0;
        }
        $this->consumedCapacity[$table] += $capacityUnits;
    }

    public function getConsumedCapacity($table)
    {
        return isset($this->consumedCapacity[$table]) ? $this->consumedCapacity[$table] : 0;
    }

}
