// another : bingyue
import { createHash,createDecipheriv } from 'crypto';

interface cookie_info{
    baidu_cookie : string ,
    sougou_cookie : string ,
    youdao_cookie : string
}

// 为了拿数据 请手动配置好cookie
var ck : cookie_info = {
    baidu_cookie : "",
    sougou_cookie : "",
    youdao_cookie : ""
}

class TranslationCurl{
    async baidu_fanyi(msg: string, en: string): Promise<any> {
        var url : string = "https://fanyi.baidu.com/v2transapi?from=auto&to="+en;
        const { get_sign } = require('./baidu_sign.js');
        const headers = {
            method: 'POST',
            headers :{
                "Referer":"https://fanyi.baidu.com/?aldtype=16047&ext_channel=Aldtype",
                'Content-Type' : 'application/x-www-form-urlencoded',
                "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36",
                'Cookie' : ck.baidu_cookie
            },
            body:
                `from=auto&to=${en}&query=${msg}&transtype=realtime&simple_means_flag=3&sign=${get_sign(msg)}&token=520924a5b96e002d783193cbfa03f0d9&domain=common&ts=1728046766455`,
        }
        const resp : any = await fetch(url, headers);
        const result : any = await resp.json();
        if(result["code"]){
            return {
                "code": 101,
                "msg": "请求错误"
            }
        }else{
            return {
                "code": 200,
                "result" : result["trans_result"]["data"][0]["dst"]
            }
        }
    }

    async youdao_fanyi(msg : string, en : string): Promise<any>{
        var headers : any = {
            "Referer": "https://fanyi.youdao.com/",
            "User-agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36",
        }
        var mysticTime : number = Math.floor(Date.now())
        var sign: string = createHash('md5').update(`client=fanyideskweb&mysticTime=${mysticTime}&product=webfanyi&key=asdjnjfenknafdfsdfsd`).digest("hex")
        var url : string =  "https://dict.youdao.com/webtranslate/key?" +
            "keyid=webfanyi-key-getter"+
            `&sign=${sign}`+
            "&client=fanyideskweb"+
            "&product=webfanyi"+
            "&appVersion=1.0.0&vendor=web"+
            "&pointParam=client,mysticTime,product"+
            `&mysticTime=${mysticTime}&keyfrom=fanyi.web`+
            "&mid=1"+
            "&screen=1"+
            "&model=1"+
            "&network=wifi"+
            "&abtest=0"+
            "&yduuid=abcdefg"
        
        var requset = await fetch(url, headers)
        var json_data = await requset.json()
        
        var aeskey : string = json_data["data"]["aesKey"]
        var aesiv : string = json_data["data"]["aesIv"]
        var url : string = "https://dict.youdao.com/webtranslate"
        var mysticTime : number = Math.floor(Date.now())
        var sign: string = createHash('md5').update(`client=fanyideskweb&mysticTime=${mysticTime}&product=webfanyi&key=fsdsogkndfokasodnaso`).digest("hex")
        var m : string = `i=${msg}&from=auto&to=${en}&useTerm=false&dictResult=true&keyid=webfanyi&sign=${sign}&client=fanyideskweb&product=webfanyi&appVersion=1.0.0&vendor=web&pointParam=client%2CmysticTime%2Cproduct&mysticTime=${mysticTime}&keyfrom=fanyi.web&mid=1&screen=1&model=1&network=wifi&abtest=0&yduuid=abcdefg`
        var header : any = {
            method : "POST",
            headers : {
                "Content-type": "application/x-www-form-urlencoded",
                "User-agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36",
                "Referer": "https://fanyi.youdao.com/",
                "Cookie": ck.youdao_cookie
            },
            body:
                m
        }
        try{
            const resp = await fetch(url, header)
            const resp_text = await resp.text()
            const a = Buffer.from(createHash('md5').update(aeskey).digest().toJSON().data)
            const n = Buffer.from(createHash('md5').update(aesiv).digest().toJSON().data)
            const r = createDecipheriv('aes-128-cbc', a, n);
            let l = r.update(resp_text, 'base64', 'utf-8'); 
            l += r.final('utf-8');
            var t = JSON.parse(l)
            return {"result":t["translateResult"][0][0]["tgt"], "code": 200}
        }catch{
            return {"code" : 101, "msg": "解密失败"}
        }
        
    }

    async sougou_fanyi(msg: string, en : string) : Promise<any>{
        var url : string = "https://fanyi.sogou.com/api/transpc/text/result";
        var data : any = {
            "from" : "auto",
            "to" : en,
            "text" :msg,
            "client" : "pc",
            "fr" : "browser_pc",
            "needQc" : 1,
            "s" : createHash('md5').update("auto" + en + msg + "109984457").digest("hex"),
            "uuid" : "1c8d59af-95b7-46f7-8a1c-b68235313389",
            "exchange" : false
        }
        
        var header : any = {
            method : "POST",
            headers : {
                "Content-type": "application/json",
                "User-agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36",
                "Referer": `https://fanyi.sogou.com/text?keyword=${encodeURIComponent(msg)}&transfrom=auto&transto=en&model=general`,
                "Cookie": ck.sougou_cookie
            },
            body:
                JSON.stringify(data)
        }
        const resp = await fetch(url, header)
        const result = await resp.json()
        if(result['data']['translate']['errorCode'] == 's10'){
            return {
                "code" : 101,
                "msg" : "解密失败"
            }
        }else{
            return {
                "code": 200,
                "result" : result['data']['translate']['dit']
            }
        }
    }
}


// 调用方法

// const test = new TranslationCurl();
// test.sougou_fanyi("伴你左右", "en").then(result => {
//     console.log(result); // 输出结果
// }).catch(error => {
//     console.error('Error:', error); // 捕获并处理错误
// });


