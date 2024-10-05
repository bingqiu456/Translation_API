from Crypto.Cipher import AES
import urllib.parse
import httpx
import execjs
import time
import hashlib
import base64
import json

"""
使用之前填写一下cookie信息
防止获取不到数据
"""
baidu_cookie = ""
sougou_cookie = ""
youdao_cookie = ""

class TranslationCurl():
    
    def baidu_fanyi(self, msg, en):
        with open("./need.js", "r+" , encoding="utf_8") as f:
            w = f.read()
            f.close()
        url = "https://fanyi.baidu.com/v2transapi?from=auto&to=" + en
        js = execjs.compile(w)
        sign = js.eval(f'get_sign("{msg}")')
        headers = {
            "Referer":"https://fanyi.baidu.com/?aldtype=16047&ext_channel=Aldtype",
            'Content-Type' : 'application/x-www-form-urlencoded',
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36",
            "Cookie" : baidu_cookie
        }
        data = f'from=auto&to={en}&query={msg}&transtype=realtime&simple_means_flag=3&sign={sign}&token=520924a5b96e002d783193cbfa03f0d9&domain=common&ts=1728046766455'
        b = httpx.post(
            url=url,
            headers=headers,
            data=data
        ).json()
        if ("errno" in b):
            return {
                "code" : 101,
                "msg" : "解密失败"
            }
        else:
            return {
                "code" : 200,
                "result" : b["trans_result"]["data"][0]["dst"]
            }
    
    def youdao_fanyi(self, msg, en):
        headers = {
            "Referer": "https://fanyi.youdao.com/",
            "User-agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36",
        }
        mysticTime = int(time.time() * 1000)
        sign = md5_encryption(f"client=fanyideskweb&mysticTime={mysticTime}&product=webfanyi&key=asdjnjfenknafdfsdfsd")
        url = "https://dict.youdao.com/webtranslate/key?" + \
            "keyid=webfanyi-key-getter"+ \
            f"&sign={sign}"+ \
            "&client=fanyideskweb"+ \
            "&product=webfanyi"+ \
            "&appVersion=1.0.0&vendor=web"+ \
            "&pointParam=client,mysticTime,product"+ \
            f"&mysticTime={mysticTime}&keyfrom=fanyi.web"+ \
            "&mid=1"+ \
            "&screen=1"+ \
            "&model=1"+ \
            "&network=wifi"+ \
            "&abtest=0"+ \
            "&yduuid=abcdefg" 
        get_key = httpx.get(url, headers=headers).json()
        aeskey = T(get_key["data"]["aesKey"])
        aesiv = T(get_key["data"]['aesIv'])
        url = "https://dict.youdao.com/webtranslate"
        sign = md5_encryption(f"client=fanyideskweb&mysticTime={mysticTime+1}&product=webfanyi&key=fsdsogkndfokasodnaso")
        headers["Content-type"] = "application/x-www-form-urlencoded"
        headers["Cookie"] = youdao_cookie
        data = f"i={msg}&from=auto&to={en}&useTerm=false&dictResult=true&keyid=webfanyi&sign={sign}&client=fanyideskweb&product=webfanyi&appVersion=1.0.0&vendor=web&pointParam=client%2CmysticTime%2Cproduct&mysticTime={mysticTime+1}&keyfrom=fanyi.web&mid=1&screen=1&model=1&network=wifi&abtest=0&yduuid=abcdefg"
        get_data = httpx.post(url=url, headers=headers, data=data).text
        cipher = AES.new(aeskey, AES.MODE_CBC, aesiv)
        ciphertext = base64.b64decode(get_data.replace("-", "+").replace("_", "/"))
        decrypted = cipher.decrypt(ciphertext).decode('utf-8')
        padding_len = ord(decrypted[-1])
        decrypted = json.loads(decrypted[:-padding_len])
        if decrypted['code'] != 0:
            return {
                "code" : 101,
                "msg" : "解密失败"
            }
        else:
            return {
                "code" : 200,
                "result" : decrypted['translateResult'][0][0]['tgt']
            }
    
    def sougou_fanyi(self, msg, en):
        url = "https://fanyi.sogou.com/api/transpc/text/result"
        data = {
            "from" : "auto",
            "to" : en,
            "text" :msg,
            "client" : "pc",
            "fr" : "browser_pc",
            "needQc" : 1,
            "s" : md5_encryption("auto" + en + msg + "109984457"),
            "uuid" : "57dc375a-e191-403b-94cb-487bafa50d63",
            "exchange" : False
        }
        headers = {
                "Content-type": "application/json;charset=UTF-8",
                "User-agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36",
                "Referer": f'https://fanyi.sogou.com/text?keyword={urllib.parse.quote(msg)}&transfrom=auto&transto=en&model=general',
                "Cookie": sougou_cookie
            }
        get_result = httpx.post(url=url, headers=headers, data=data)
        if get_result.status_code != 200:
            return {
                "code" : 101,
                "msg" :"解密失败"
            }
        m = get_result.json()
        if m['data']['translate']['errorCode'] == 's10':
            return {
                "code" : 102,
                "msg" : "获取信息失败"
            }
        else:
            return {
                "code" : 200,
                "result" : m['data']['translate']['dit']
            }

def md5_encryption(data):
    md5 = hashlib.md5()
    md5.update(data.encode('utf-8'))
    return md5.hexdigest() 

def T(data: str):
    return hashlib.md5(data.encode()).digest()

# 调用方法  
# a = TranslationCurl()
# print(a.sougou_fanyi("你好", "en"))
