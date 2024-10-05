<h1 align="center"><i>✨ 翻译API封装 ✨ </i></h1>

<div align="center">
 
![PHP](https://img.shields.io/badge/PHP-7+-blue)
![Python](https://img.shields.io/badge/Python-3-blue)
![Typescript](https://img.shields.io/badge/Typescript-now-blue)

</div>

---

## *🎈*如何下载

直接下载**整个仓库**的内容

```bash
git clone https://github.com/bingqiu456/Translation_API
```

---

##  🎈如何使用

下载之后根据自己需要的语言，导入对应文件的即可

> 每个语言我都写了调用方法

第一个的参数是**翻译的内容**，第二个的参数是**翻译的目标语言**

- `Python`的调用方法

```python
a = TranslationCurl()
print(a.sougou_fanyi("你好", "en"))
```

- `PHP`的调用方法

```php
$a = new Translation;
$a->__config();
$a->__sougou($msg, $en);
```

- `Typescript`

```typescript
const test = new TranslationCurl();
test.sougou_fanyi("测试", "en").then(result => {
     console.log(result); // 输出结果
 }).catch(error => {
     console.error('Error:', error); // 捕获并处理错误
 });
```

> 注：另外一点的是，由于你需要提供`cookie`才能获取数据，自己手动打开网页抓一个填进去吧

---

##  🎈关于sign

关于这三家平台的加密参数解密方法，具体分析可以看我博客

| 平台     | 原理                                                         |
| -------- | ------------------------------------------------------------ |
| 百度翻译 | [https://blog.bingyue.top/2024/02/18/baidu_fanyi/](https://blog.bingyue.top/2024/02/18/baidu_fanyi/) |
| 有道翻译 | [https://blog.bingyue.top/2024/08/24/youdao_fanyi/](https://blog.bingyue.top/2024/08/24/youdao_fanyi/) |
| 搜狗翻译 | [https://blog.bingyue.top/2024/05/03/saogou_fanyi/](https://blog.bingyue.top/2024/05/03/saogou_fanyi/) |

---

##  🎈遇到bug？无法获取数据？

直接发`issue`，我看到之后会立即回复，如果没回复可以发邮件->`bingyuevip@gmail.com`

---

## License
```
MIT License

Copyright (c) 2024 bingyue

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
