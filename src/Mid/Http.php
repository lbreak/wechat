<?php

namespace lbreak\Wechat\Mid;

use GuzzleHttp\Client;
use Thenbsp\Wechat\Wechat\AccessToken;
use Doctrine\Common\Collections\ArrayCollection;

class Http
{
    /**
     * Request Url.
     */
    protected $uri;

    /**
     * Request Method.
     */
    protected $method;

    /**
     * Request Body.
     */
    protected $body;

    /**
     * Request Query.
     */
    protected $query = [];

    /**
     * Query With AccessToken.
     */
    protected $accessToken;

    /**
     * SSL 证书.
     */
    protected $sslCert;
    protected $sslKey;

    private static $_instance;

    /**
     * initialize.
     */
    private function __construct()
    {

    }

    /**
     * Create Client.
     * @return $this
     */
    public static function gi()
    {
        if (empty(self::$_instance[static::class]) || !self::$_instance[static::class] instanceof static) {
            self::$_instance[static::class] = new static();
        }
        return self::$_instance[static::class];
    }

    /**
     * Send GET Request.
     * @param bool $isArray
     * @return ArrayCollection
     */
    public function get($isArray = true){
        $this->method = 'GET';
        return $this->send($isArray);
    }

    /**
     * Send POST Request.
     * @param bool $isArray
     * @return ArrayCollection
     */
    public function post($isArray = true){
        $this->method = 'POST';
        return $this->send($isArray);
    }

    /**
     * Send ALL METHOD
     * @param $method
     * @param bool $isArray
     * @return ArrayCollection
     */
    public function request($method,$isArray = true){
        $this->method = strtoupper($method);
        return $this->send($isArray);
    }

    /**
     * Request UEL.
     */
    public function withUri(string $uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Request Query.
     */
    public function withQuery(array $query)
    {
        $this->query = array_merge($this->query, $query);

        return $this;
    }

    /**
     * Request Json Body.
     */
    public function withBody(array $body)
    {
        $this->body = Serializer::jsonEncode($body);

        return $this;
    }

    /**
     * Request Xml Body.
     */
    public function withXmlBody(array $body)
    {
        $this->body = Serializer::xmlEncode($body);

        return $this;
    }

    /**
     * Query With AccessToken.
     */
    public function withAccessToken(AccessToken $accessToken)
    {
        $this->query['access_token'] = $accessToken->getTokenString();

        return $this;
    }

    /**
     * Request SSL Cert.
     */
    public function withSSLCert($sslCert, $sslKey)
    {
        $this->sslCert = $sslCert;
        $this->sslKey = $sslKey;

        return $this;
    }

    /**
     * Send Request.
     */
    private function send($asArray = true)
    {
        $options = [];

        // query
        if (!empty($this->query)) {
            $options['query'] = $this->query;
        }

        // body
        if (!empty($this->body)) {
            $options['body'] = $this->body;
        }

        // ssl cert
        if ($this->sslCert && $this->sslKey) {
            $options['cert'] = $this->sslCert;
            $options['ssl_key'] = $this->sslKey;
        }

        $response = (new Client())->request($this->method, $this->uri, $options);
        $contents = $response->getBody()->getContents();

        if (!$asArray) {
            return $contents;
        }

        $array = Serializer::parse($contents);

        return new ArrayCollection($array);
    }
}
