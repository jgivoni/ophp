<?php

class EnvironmentConfig extends AppNamespace\DevelopmentConfig
{
    /**
     * Root url string
     * This cannot be autodetected yet. Used for links and internal requests and redirects
     * @var string
     */
    protected $baseUrl = "http://hostname/";

    /**
     * List of database connections
     * - each named coonnection needs to specify host, db name, user and password
     * @var array
     */
    protected $databaseConnections = array(
        'replanner' => array(
            'host' => 'localhost',
            'database' => 'myapp',
            'user' => 'user',
            'password' => '',
        ),
    );

}