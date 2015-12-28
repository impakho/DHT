# DHT
A DHT crawler with WebUI

## Dependency
- bencode
- BitVector
- torndb
- libtorrent
- jieba

## How to use it?

Step 1: import `Database.sql` into a MySQL database named `dht` (utf8mb4_general_ci).

Step 2: config your MySQL database connection information

file `Sql.py`
```
db = torndb.Connection("127.0.0.1:3306","dht",user="root",password="mysql_password")
```

file `Init.php` in `web` directory
```
@$GLOBALS['sql_con']=mysql_connect("localhost","root","mysql_password");
```

Step 3: copy all files in `web` directory to the root directory of your website.

Step 4: make sure `torrent` directory is exist and all python dependencies are solved.

Step 5: run `DHT.py` and `Sql.py` in background.

## Tips
You can control the speed of DHT crawler simply by modifying `max_node_qsize` option in `DHT.py` file.

```
dht = DHTServer(master, "0.0.0.0", 6882, max_node_qsize=200)
```

## License
DHT is published under GPLv2 License. See the LICENSE file for more.

<hr>

# DHT
一只DHT网络爬虫，带Web界面

## 依赖
- bencode
- BitVector
- torndb
- libtorrent
- jieba

## 怎样使用？

第一步：把 `Database.sql` 文件导入到MySQL数据库，数据库名为 `dht` (utf8mb4_general_ci).

第二步：修改你的MySQL数据库连接信息

文件 `Sql.py`
```
db = torndb.Connection("127.0.0.1:3306","dht",user="root",password="mysql_password")
```

文件 `Init.php` （在 `web` 目录下）
```
@$GLOBALS['sql_con']=mysql_connect("localhost","root","mysql_password");
```

第三步：将 `web` 目录下的所有文件复制到你的网站根目录

第四步：确保存在 `torrent` 目录而且所有Python依赖已经被正确安装

第五步：后台运行 `DHT.py` 和 `Sql.py`

## 提示
如果要调节DHT爬虫的速度，你只需要修改 `DHT.py` 文件里的 `max_node_qsize` 属性

```
dht = DHTServer(master, "0.0.0.0", 6882, max_node_qsize=200)
```

## 许可协议
DHT采用GPLv2许可协议。查看LICENSE文件了解更多。