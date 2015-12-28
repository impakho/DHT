<?php
ini_set("display_errors","On");
include 'Init.php';

OpenDB();
$result=RunDB("select count(id) from metadata");
$row=mysql_fetch_row($result);
$allNum=$row[0];
CloseDB();
?>
<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <title>DHT 资源搜索站</title>
    <link href="static/bootstrap.css" rel="stylesheet">
    <link href="static/flat-ui.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <div style="height:180px;"></div>
      <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6"><h3 class="text-center">DHT 资源搜索站</h3></div>
        <div class="col-md-3"></div>
      </div>
      <div style="height:70px;"></div>
      <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
          <div class="input-group">
            <span class="input-group-addon">资源：</span>
            <input type="text" id="kw" placeholder="关键词" class="form-control" />
            <span class="input-group-addon" style="padding:0;">
              <a id="sbtn" class="btn btn-block btn-primary" style="width:120px;height:40px;" onclick="$.csbtn()">搜索</a>
            </span>
          </div>
        </div>
        <div class="col-md-3"></div>
      </div>
      <div style="height:30px;"></div>
      <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4" style="text-align:center;">总资源数：<?php echo $allNum; ?></div>
        <div class="col-md-4"></div>
      </div>
    </div>
    <script src="static/jquery.min.js"></script>
    <script type="text/javascript">
      $(function() {
        $("#kw").keyup(function(){
          if (event.keyCode == 13){
            $("a")[0].click();
          }
        });

        $.extend({
          'csbtn' : function(){
            window.location.href='s.php?kw='+encodeURIComponent($("#kw").val());
          }
        });
      });
    </script>
  </body>
</html>