<?php

namespace Ophp\dba;

class DynamoDbDatabaseAdapter
{
    /**
     *
     * @var \Aws\DynamoDb\DynamoDbClient
     */
    protected $client, $marshaler;
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    protected function getCredentials()
    {
        if (!isset($this->credentials)) {
            $credentialsFile = $this->params['credentialsFile'];

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
                'region' => $this->params['region'],
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
     * @param string $table
     * @param array $item
     * @returns string Primary Key
     */
    public function insert($table, $primaryKey, $item)
    {
        if (!isset($item[$primaryKey])) {
            $item[$primaryKey] = $this->getUniqueId();
        }
        try {
            $this->getClient()->putItem([
                'TableName' => $table,
                'Item' => $this->getMarshaler()->marshalItem($item),
                'ConditionExpression' => 'attribute_not_exists(' . $primaryKey . ')',
                'ReturnConsumedCapacity' => 'TOTAL',
            ]);
            $result = [$primaryKey => $item[$primaryKey]];
        } catch (\Aws\DynamoDb\Exception\DynamoDbException $e) {
            if ($e->getAwsErrorCode() == 'ConditionalCheckFailedException') {
                // Item already exists
                $result = false;
            } else {
                throw new \Exception('Insert query failed', 0, $e);
            }
        }
        return $result;
    }

    public function updateAttributes($table, $primaryKey, $item)
    {
        try {
			foreach ($item as $attribute => $value) {
				if ($attribute == $primaryKey) {
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
                    $primaryKey => $item[$primaryKey],
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

    public function delete($table, $primaryKey, $item)
    {
        try {
            $result = $this->getClient()->deleteItem([
                'TableName' => $table,
                'Key' => $this->getMarshaler()->marshalItem([
                    $primaryKey => $item[$primaryKey],
                ]),
                'ConditionExpression' => 'attribute_exists(' . $primaryKey . ')',
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

    public function get($table, $primaryKey, $item)
    {
        try {
            $result = $this->getClient()->getItem([
                'TableName' => $table,
                'Key' => $this->getMarshaler()->marshalItem([
                    $primaryKey => $item[$primaryKey],
                ]),
                'ReturnConsumedCapacity' => 'TOTAL',
            ]);
            if ($result->get('Item') === null) {
                throw new \Exception('Item not found');
            }
            $result = $this->getMarshaler()->unmarshalItem($result->get('Item'));
        } catch (\Aws\DynamoDb\Exception\DynamoDbException $e) {
            throw new \Exception('Get query failed', 0, $e);
        } catch (\Exception $e) {
            $result = null;
        }
        return $result;
    }

    /**
     * Adds elements to a string set or number set
     * 
     * All the elements for each attribute (1st level only) must be of the same type
     * 
     * @param type $table
     * @param type $primaryKey
     * @param type $item
     * @param type $elements
     * @return boolean
     * @throws \Exception
     */
    public function addSetElements($table, $primaryKey, $item, $elements)
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
                'Key' => $this->getMarshaler()->marshalItem([
                    $primaryKey => $item[$primaryKey],
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
    
    public function getUniqueId($length = 31)
    {
        // @todo Add a server unique prefix to uniqid
        return substr(base_convert(hash('ripemd160', uniqid(true)), 16, 36), 0, $length);
    }

}
