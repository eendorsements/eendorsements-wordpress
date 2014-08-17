<?php

/**
 * eEndorsements API - PHP:  Wrapper for v1.0
 *
 */

class eEndorsementsAPIExchange
{
	private $apiKey;
	private $apiSecretKey;
	private $postFields;
	private $getFields;
	private $url;

	/*
	 * Create the object and pass in the api keys.
	 * Requires: apiKey and apiSecret parameters.
	 */
	public function __construct(array $settings)
	{
		if (!in_array('curl', get_loaded_extensions()))
		{
			throw new Exception('You will need to install cURL, see: http://curl.haxx.se/docs/install.html');
		}

		if(!isset($settings['apiKey']) && !isset($settings['apiSecretKey']))
		{
			throw new Exception('Make sure you pass the api Key and api Secret');
		}

		$this->apiKey = $settings['apiKey'];
		$this->apiSecretKey = $settings['apiSecretKey'];
	}

	public function setPostFields(array $postFields)
	{
		if(!is_null($this->getGetFields()))
		{
			throw new Exception('You can only send POST or GET fields');
		}

		$this->postFields = $postFields;
	}

	public function setGetFields(array $getFields)
	{
		if(!is_null($this->getGetFields()))
		{
			throw new Exception('You can only send POST or GET fields');
		}

		$search = array('#', ',', '+', ':');
		$replace = array('%23', '%2C', '%2B', '%3A');
		$string = str_replace($search, $replace, $string);

		$this->getFields = $string;
	}

	public function getGetFields()
	{
		return $this->getFields;
	}

	public function getPostFields()
	{
		return $this->postFields;
	}

	protected function getAuthorizationString()
	{
		$string = sprintf("?apiKey=%s&apiSecretKey=%s", $this->apiKey, $this->apiSecretKey);

		return $string;
	}

	public function makeRequest($url)
	{
		$getFields = $this->getGetFields();
		$postFields = $this->getPostFields();

		$this->url = $url . $this->getAuthorizationString();

		$options = array(
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HEADER => false,
			CURLOPT_URL => $this->url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 10
		);

		if(!is_null($postFields))
		{
			$postFields = http_build_query($postFields);
			$options[CURLOPT_POSTFIELDS] = $postFields;
		}
		elseif($getFields !== '')
		{
			$options[CURLOPT_URL] .= $getFields;
		}

		$feed = curl_init();
		curl_setopt_array($feed, $options);
		
		$json = curl_exec($feed);
		curl_close($feed);

		return $json;
	}
}