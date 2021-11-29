### Install

```php
composer require codelint/ringo
```

### Configuration

```properties
RINGO_WECORP_CHAT_ID=ringo
RINGO_WECORP_ID=****
RINGO_WECORP_SECRET=****
RINGO_WECORP_JOB_CONNECTION=database
RINGO_ALERT_MAIL=unotseeme@foxmail.com
RINGO_MAIL_DOMAIN=example.com
RINGO_NOTIFY_MAIL=notify@example.com
```

### Usage

##### 日志文件通知

```php
Ringo::info("hello world");
Ringo::warning("hello world");
Ringo::alert("hello world"); // will trigger mail & wecorp notify
Ringo::error("hello world"); // will trigger mail & wecorp notify
```

##### 发送邮件通知

```php
Ringo::mail("message", ["ext" => "hello"], ["test@example.com"]); // no log file
Ringo::mail(new Exception("hello world"), ["test@example.com"]); // no log file
Ringo::notify(new Exception("hello world"), ["test@example.com"]); // with info log
// 给企业邮箱 $mailName@RINGO_MAIL_DOMAIN 发送notify信息
Ringo::notify$mailName("message", ["ext" => "hello"], ["test@example.com"]);	
```

##### 发送企业微信通知

```php
Ringo::weCorp("message", ["ext" => "hello"]);
Ringo::weError("message", ["ext" => "hello"]);
// 给 群组$group 发送企业微信通知
Ringo::we$group("send message to a theme", ["ext" => "hello"])
```

##### 默认发送


### Reference
* https://packagist.org/packages/codelint/ringo

