<?php

namespace app\common\services;

use app\common\services\aliyunoss\OssClient;
use app\common\services\aliyunoss\Core\OssException;

class AliOss
{
    protected $accessKeyId;
    protected $accessKeySecret;
    protected $endpoint;
    protected $bucket;
    protected $ossClient;

    public function __construct($accessKeyId, $accessKeySecret, $endpoint)
    {
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        $this->endpoint = $endpoint;
        // $bucket = $bucket;
    }
    public function upload($object, $content, $bucket)
    {
        try{
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);

            $res = $ossClient->putObject($bucket, $object, $content);
            if ($res['info']) {

            }
            $data = $ossClient->getObjectMeta($bucket, $object);

            return $data;

        } catch(OssException $e) {

            printf($e->getMessage() . "\n");
            return $e->getMessage();
        }
    }

    public function checkBucket($bucket)
    {
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);

        $res = $ossClient->listBuckets($bucket);

        return $res;
    }

    public function getObject($object)
    {
        try {
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $res = $ossClient->getobject($bucket, $object);
            return $res;
        } catch (OssException $e) {
            return $e->getMessage();
        }
    }

    public function PutBucketPublicRead()
    {
        try {
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $res = $ossClient->putBucketAcl($bucket, 'public-read');
            return $res;
        } catch (OssException $e) {
            return $e->getMessage();
        }
    }
    public function PutBucketPrivate()
    {
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        $res = $ossClient->putBucketAcl($bucket, 'private');
        return $res;
    }
    public function upCallBack($object)
    {
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);

        // ??????????????????????????????
        // callbackUrl??????????????????????????????http://oss-demo.aliyuncs.com:23450???http://127.0.0.1:9090???
        //callbackHost???????????????????????????Host????????????oss-cn-hangzhou.aliyuncs.com???
        $url =
            '{
            "callbackUrl": {$callbackUrl}",
            "callbackHost":"{$callbackHost}",
            "callbackBody":
                "bucket={$bucket}&object={$object}&etag=${etag}&size=${size}&mimeType=${mimeType}&imageInfo.height=${imageInfo.height}&imageInfo.width=${imageInfo.width}&imageInfo.format=${imageInfo.format}&my_var1=${x:var1}&my_var2=${x:var2}",
            "callbackBodyType":"application/x-www-form-urlencoded"
        }';

    // ????????????????????????????????????????????????Key???Value?????????Key?????????x:?????????
        $var =
            '{
                "x:var1":"value1",
                "x:var2":"???2"
            }';
        $options = array(OssClient::OSS_CALLBACK => $url,
            OssClient::OSS_CALLBACK_VAR => $var
        );
        $result = $ossClient->putObject($bucket, $object, file_get_contents(__FILE__), $options);
        print_r($result['body']);
        print_r($result['info']['http_code']);
    }

}