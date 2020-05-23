<?php
namespace lbreak\Wechat\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use lbreak\Wechat\Mid\Http;

class Customer extends ArrayCollection {

    const CUSTOMER_LIST = 'https://api.weixin.qq.com/cgi-bin/user/get';
    const CUSTOMER_INFO = 'https://api.weixin.qq.com/cgi-bin/user/info';

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
     * 获取用户列表
     * @param string $nextOpenId
     * @return ArrayCollection
     * @throws \Exception
     */
    public function getCustomerList($nextOpenId = ''){
        $query = [
            'appid' => $this['appid'],
            'secret' => $this['appsecret'],
            'next_openid'=> $nextOpenId
        ];

        $client = Http::gi()->withUri(self::CUSTOMER_LIST );
        $nextOpenId && $client->withQuery($query);

        $response = $client->get();

        if ($response->containsKey('errcode')) {
            throw new \Exception($response['errmsg'], $response['errcode']);
        }

        return $response;
    }

    /**
     * 获取用户详情
     * @param $openId
     * @return ArrayCollection
     * @throws \Exception
     */
    public function getCustomerInfo($openId){
        $query = [
            'openid' => $openId,
            'lang' => 'zh_CN',
            'appid' => $this->get('appid'),
            'secret' => $this->get('appsecret'),
        ];

        $response = Http::gi()->withUri(self::CUSTOMER_INFO )->withQuery($query)->get();

        if ($response->containsKey('errcode')) {
            throw new \Exception($response['errmsg'], $response['errcode']);
        }

        return $response;
    }
}