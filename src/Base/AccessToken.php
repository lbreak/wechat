<?php

namespace lbreak\Wechat\Base;

use lbreak\Wechat\Mid\Http;
use lbreak\Wechat\Mid\CacheTrait;
use Doctrine\Common\Collections\ArrayCollection;

class AccessToken extends ArrayCollection
{
    /*
     * Cache Trait
     */
    use CacheTrait;

    /**
     * @see https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Get_access_token.html.
     */
    const ACCESS_TOKEN = 'https://api.weixin.qq.com/cgi-bin/token';

    /**
     * 构造方法.
     * AccessToken constructor.
     * @param $appid
     * @param $appsecret
     */
    public function __construct($appid, $appsecret)
    {
        parent::__construct([
            'appid' => $appid,
            'appsecret' => $appsecret
        ]);
    }

    /**
     * 获取 AccessToken（调用缓存，返回 String）.
     * @return mixed
     * @throws \Exception
     */
    public function getTokenString()
    {
        $cacheKey = $this->getCacheKey();

        if ($this->cache && $data = $this->cache->fetch($cacheKey)) {
            return $data['access_token'];
        }

        $response = $this->getTokenResponse();

        if ($this->cache) {
            $this->cache->save($cacheKey, $response, $response['expires_in']);
        }

        return $response['access_token'];
    }

    /**
     * 获取 AccessToken（不缓存，返回原始数据）.
     * @return mixed
     * @throws \Exception
     */
    public function getTokenResponse()
    {
        $query = [
            'grant_type' => 'client_credential',
            'appid' => $this->get('appid'),
            'secret' => $this->get('appsecret'),
        ];

        $response = Http::gi()->withUri(self::ACCESS_TOKEN)->withQuery($query)->get();

        if ($response->containsKey('errcode')) {
            throw new \Exception($response['errmsg'], $response['errcode']);
        }

        return $response;
    }

    /**
     * 从缓存中清除.
     */
    public function delCache()
    {
        return $this->cache ? $this->cache->delete($this->getCacheKey()) : false;
    }

    /**
     * 获取缓存 ID.
     */
    public function getCacheKey()
    {
        return sprintf('wechat_access_token_%s', $this['appid']);
    }
}
