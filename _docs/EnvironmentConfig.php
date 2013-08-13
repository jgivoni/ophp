<?php

class EnvironmentConfig extends Replanner\DevelopmentConfig
{
	protected $baseUrl = "http://replanner.local/";
	protected $databaseConnections = array(
		'replanner' => array(
			'host' => 'localhost',
			'database' => 'replanner',
			'user' => 'webapp',
			'password' => 'BFvmny5awwFvbvRt',
		),
	);
}