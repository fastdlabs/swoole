# 安装
 
### pecl

```shell
$ pecl install swoole
```

### 编译安装

[下载 Swoole](https://github.com/swoole/swoole-src/releases)

```php
$ tar zxcvf swoole-version.tar.gz
$ cd swoole-version
$ /path/to/phpize
$ ./configure --with-php-config=/path/to/php-config
$ make && make install
```

查看 php.ini 文件路径

```php
$ /pato/to/php --ini
```

追加 swoole.so 到 php.ini

```ini
extension=swoole.so
```

##### 检查 swoole 安装状态

```php
$ php --ri 'swoole'
```

下一节: [服务器](2-1-server.md)