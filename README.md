### Install

```php
composer require codelint/ringo
```

### Configuration

```properties
# add to .env file
# use laravel global mail function to mail
MAIL_DRIVER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=465
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_NAME=
MAIL_FROM_ADDRESS=
MAIL_ENCRYPTION=ssl

RINGO_WECORP_CHAT_ID=ringo
# must uid must more than 1 (at least 2), else can't not create the chat[ringo] auto
RINGO_WECORP_CHAT_UIDS=gzhang,dev
RINGO_WECORP_ID=****
RINGO_WECORP_SECRET=****
RINGO_ALERT_MAIL=unotseeme@foxmail.com
RINGO_MAIL_DOMAIN=example.com
RINGO_NOTIFY_MAIL=notify@example.com
RINGO_JOB_CONNECTION=sync
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

