<?php
// another : bingyue

$msg = $_GET["msg"];
$en = $_GET["en"];

class Translation{

    var $youdao_cookie;
    var $baidu_cookie;
    var $sougou_cookie;

    public function __config(){
        /**
         * 这里是各家翻译的cookie
         * 为了防止拿不到数据 需要你手动获取一下
         */
        $youdao_cookie = '';
        $baidu_cookie = '';
        $sougou_cookie = '';
        $this->baidu_cookie = $baidu_cookie;
        $this->youdao_cookie = $youdao_cookie;
        $this->sougou_cookie = $sougou_cookie;
    }

    public function __youdao($msg, $to_la){
        /**
         * 有道网易翻译
         * $msg string 翻译内容
         * $to_la 输出的语言
         * json 输出格式
         */
        /**
         * 由于新版有道翻译改了api 这里也跟着更新
         */
        $mysticTime = floor(microtime(true) * 1000);
        $sign = md5("client=fanyideskweb&mysticTime=".$mysticTime."&product=webfanyi&key=asdjnjfenknafdfsdfsd");
        $en_sign = $this->__curl(
            $url = "https://dict.youdao.com/webtranslate/key?keyid=webfanyi-key-getter".
            "&sign=".$sign.
            "&client=fanyideskweb".
            "&mysticTime=".$mysticTime.
            "&product=webfanyi".
            "&appVersion=1.0.0".
            "&vendor=web".
            "&pointParam=client,mysticTime,product".
            "&keyfrom=fanyi.web".
            "&mid=1&screen=1&model=1&network=wifi&abtest=0&yduuid=abcdefg"
            ,
            $cookie = $this->youdao_cookie,
            $header =  array('content-type: text/plain;charset=UTF-8'),
            $data = "{}",
            $re = "https://fanyi.youdao.com/"
        );
        $en_sign_json = json_decode($en_sign, true);

        if($en_sign_json["code"] == 1){
            echo json_encode(array(
                "code"=>101,
                "msg"=>"解析失败，密钥获取失败"
            ),JSON_UNESCAPED_UNICODE);
        }
        $new_sign = md5("client=fanyideskweb&mysticTime=".($mysticTime+1)."&product=webfanyi&key=fsdsogkndfokasodnaso");
        $w = $this->__curl(
            $url = "https://dict.youdao.com/webtranslate",
            $cookie = $this->youdao_cookie,
            $header = array("Content-Type: application/x-www-form-urlencoded"),
            $data = 'i='.$msg.'&from=auto&to='.$to_la.'&useTerm=false&dictResult=true&keyid=webfanyi&sign='.$new_sign.'&client=fanyideskweb&product=webfanyi&appVersion=1.0.0&vendor=web&pointParam=client%2CmysticTime%2Cproduct&mysticTime='.($mysticTime+1).'&keyfrom=fanyi.web&mid=1&screen=1&model=1&network=wifi&abtest=0&yduuid=abcdefg',
            $re = "https://fanyi.youdao.com/",
            
        );
        function T($o) {
            return md5($o, true);
        }
        $a = T($en_sign_json["data"]["aesKey"]);
        $n = T($en_sign_json["data"]["aesIv"]);
        $encryptedData = base64_decode(str_replace(['-', '_'], ['+', '/'], $w));
        $decrypted = json_decode(openssl_decrypt($encryptedData, 'aes-128-cbc', $a, true, $n), true);
        if($decrypted["code"]!=0){
            echo json_encode(array(
                "code"=>102,
                "msg"=>"数据解密失败"
            ));
        }
        echo json_encode(
            array(
                "code"=>200,
                "result"=>$decrypted["translateResult"][0][0]["tgt"],
                "tip"=>"感谢使用"
            )
        );
        
    }

    public function __baidu($msg, $en){
        /**
         * 百度翻译
         * $msg string 翻译内容
         * $to_la 输出的语言
         * json 输出格式
         */
        /**
         * 无
         */
        $get_sign = shell_exec("node need.js ".$msg);
        $w = $this->__curl(
            "https://fanyi.baidu.com/v2transapi?from=auto&to=".$en,
            $this->baidu_cookie,
            array(
                "Content-Type:application/x-www-form-urlencoded",
            ),
            'from=auto&to='.$en.'&query='.urlencode($msg).'&transtype=realtime&simple_means_flag=3&sign='.rtrim($get_sign).'&token=520924a5b96e002d783193cbfa03f0d9&domain=common&ts=1728046766455',
            "https://fanyi.baidu.com/?aldtype=16047&ext_channel=Aldtype"
        );
        $b = json_decode($w, true);
        if(in_array("code", $b)){
            echo json_encode(
                array(
                    "code"=>103,
                    "msg"=>"解析失败"
                )
            );
        }else{
            echo json_encode(
                array(
                    "code"=>200,
                    "result"=>$b["trans_result"]["data"][0]["dst"]
                )
            );
        }
    }

    public function __sougou($msg, $en){
        /**
         * 搜狗翻译
         * $msg string 翻译内容
         * $to_la 输出的语言
         * json 输出格式
         */
        /**
         * 无
         */
        $get_sign = md5("auto".$en.$msg."109984457");
        $dictionary = [
            "from" => "auto",
            "to" => $en,
            "text" => $msg,
            "client" => "pc",
            "fr" => "browser_pc",
            "needQc" => 1,
            "s" => $get_sign,
            "uuid" => "1c8d59af-95b7-46f7-8a1c-b68235313389",
            "exchange" => false
        ];
        $r = $this->__curl(
            "https://fanyi.sogou.com/api/transpc/text/result",
            $this->sougou_cookie,
            array(
                'Content-Type: application/json'
                ),
            json_encode($dictionary, JSON_UNESCAPED_UNICODE),
            "https://fanyi.sogou.com/text?keyword=".$msg."&transfrom=auto&transto=en&model=general"
        );
        // echo $r;
        $r = json_decode($r, true);
        if($r["status"]==0){
            echo json_encode(array(
                "code"=>200,
                "result"=>$r["data"]["translate"]["dit"],
            ));
        }else{
            echo json_encode(
                array(
                    "code"=>102,
                    "msg"=>"翻译错误"
                )
            );
        }
    }

    public function __curl($url, $cookie, $header, $data, $re){
        /**
         * 构建请求包头
         */
        $ua = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        if($data!="{}"){
            curl_setopt($curl, CURLOPT_POST, true); // 发送POST请求
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // POST字段
        }
        curl_setopt($curl, CURLOPT_COOKIE,$cookie);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl,CURLOPT_REFERER,$re);
        curl_setopt($curl, CURLOPT_USERAGENT, $ua);
        $a = curl_exec($curl);
        curl_close($curl);
        return $a;
    }
}
// 调用方法
// $a = new Translation;
// $a->__config();
// $a->__sougou($msg, $en);
?>