#!/usr/bin/env python
# encoding: utf-8

import re
import socket
import os
import torndb
import jieba

def remove_punctuation(text):
  r='[’!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~]+'
  return re.sub(r,'',text)

server = socket.socket(socket.AF_UNIX, socket.SOCK_STREAM)
if os.path.exists("./sql.sock"):
    os.unlink("./sql.sock")
server.bind("./sql.sock")
server.listen(0)
db = torndb.Connection("127.0.0.1:3306","dht",user="root",password="mysql_password")
while True:
    try:
        connection, address = server.accept()
        sql = str(connection.recv(999999999))
        connection.close()
        shash = sql.split('||@@||')[2]
        existRs = db.query("select id from metadata where `hash`='"+shash+"'")
        if len(existRs)<=0:
          db.execute(sql.split('||@@||')[0])
          for seg in jieba.cut(sql.split('||@@||')[1].replace('\'\'','\'')):
            seg = remove_punctuation(seg)
            if len(seg) > 1:
              sql = "call addSearch('%s','%s')" % (seg, shash)
              db.execute(sql)
    except Exception, e:
        print 'sql fail'
        pass
