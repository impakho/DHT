#!/usr/bin/env python
# encoding: utf-8

import requests
import libtorrent as lt
import os, time
import socket

def init(address, infohash):
    global times
    times = 0
    down(address, infohash)

def down(address, infohash, timeout=10):
    start = infohash[0:2]
    end = infohash[-2:]
    url = "http://bt.box.n0808.com/%s/%s/%s.torrent" % (start, end, infohash)
    headers = {
        "Referer": "http://bt.box.n0808.com"
    }
    try:
        r = requests.get(url, headers=headers, timeout=timeout)
        if r.status_code == 200:
            f = open('./torrent/' + infohash + '.torrent','wb')
            f.write(r.content)
            f.close()
            info(infohash)
    except Exception, e:
        global times
        times += 1
        if times < 3:
            down(address, infohash)

def info(infohash):
    try:
        info = lt.torrent_info('./torrent/' + infohash + '.torrent')
        info_hash = infohash
        info_time = int(time.time())
        info_name = info.name()
        info_created = info.creation_date()
        info_size = info.total_size()
        info_files_num = info.num_files()
        files = info.files()
        info_files_name = ''
        info_files_size = ''
        first = 0
        for file in files:
            if first == 1:
                info_files_name += '|'
                info_files_size += '|'
            first = 1
            info_files_name += os.path.split(file.path)[-1]
            info_files_size += str(file.size)
        try:
            info_name = info_name.replace('\'','\'\'')
            info_files_name = info_files_name.replace('\'','\'\'')
            sql = "call addMetadata('%s',%d,'%s','%s',%d,%d,'%s','%s')||@@||%s||@@||%s" % (info_hash, info_time, info_name, info_created, info_size, info_files_num, info_files_name, info_files_size, info_name, info_hash)
            client = socket.socket(socket.AF_UNIX, socket.SOCK_STREAM)
            client.connect("./sql.sock")
            client.send(sql)
            client.close()
            print infohash
        except Exception, e:
            print 'send fail'
            pass
    except Exception, e:
        print 'info fail'
        pass
    if os.path.exists('./torrent/' + infohash + '.torrent'):
      os.unlink('./torrent/' + infohash + '.torrent')
