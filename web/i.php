<?php
include 'Init.php';

@$g_h=trim($_GET['h']);
if (!isset($g_h)||$g_h=="") header('Location: /');
if (!ereg("^[0-9a-zA-Z]*$",$g_h)){
  header('Location: /');
}else{
  OpenDB();
  $result=RunDB("select * from metadata where `hash`='".$g_h."'");
  if (mysql_num_rows($result)<=0){
    header('Location: /');
  }else{
    $row=mysql_fetch_row($result);
    $info_hash=$row[1];
    $row[2]=format_date_utc($row[2]);
    $info_time=$row[2];
    $info_name=$row[3];
    $row[4]=format_date_utc($row[4]);
    $info_created=$row[4];
    $row[5]=format_size($row[5]);
    $info_size=$row[5];
    $info_files_num=$row[6];
    $info_files_name=$row[7];
    $row[8]=format_size($row[8]);
    $info_files_size=$row[8];

    $info_keyword=getKeyword($info_name);
    if (strpos($info_keyword,"|")===false){
      $info_keyword='                  <a target="_blank" href="s.php?kw='
        .urlencode($info_keyword).'">'.$info_keyword.'</a>&nbsp;&nbsp;&nbsp;'."\n";
    }else{
      $info_keyword_split=split("\|",$info_keyword);
      $info_keyword='';
      for ($a=0;$a<count($info_keyword_split);$a++){
        $info_keyword.='                  <a target="_blank" href="s.php?kw='
          .urlencode($info_keyword_split[$a]).'">'.$info_keyword_split[$a].'</a>';
        if ($a!=(count($info_keyword_split)-1)) $info_keyword.="&nbsp;&nbsp;&nbsp;\n";
      }
    }
  }
  CloseDB();
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <title><?php echo $info_name; ?> - DHT 资源搜索站</title>
    <link href="static/bootstrap.css" rel="stylesheet">
    <link href="static/flat-ui.css" rel="stylesheet">
    <style>
      span{width:900px;white-space:nowrap;
        text-overflow:ellipsis;}
      ul{padding-left:0px;margin-left:25px;}
      li{list-style-type:none;
        font-size:16px;line-height:24px;
        white-space:nowrap;text-overflow:ellipsis;}
      p{white-space:nowrap;text-overflow:ellipsis;}
      li a:hover{text-decoration:underline;}
      .bold{font-weight:bold;}
      .p0{font-size:14px;margin-left:25px;
        line-height:10px;}
      .btna{width:140px;height:50px;
        font-size:18px;line-height:30px;}
    </style>
  </head>
  <body>
    <div class="container">
      <div style="height:20px;"></div>
      <div class="row">
        <div class="col-xs-2"></div>
        <div class="col-md-3"><h6><a title="返回首页" href="/">DHT 资源搜索站</a></h6></div>
        <div class="col-md-5">
          <div class="input-group" style="margin-top:10px;">
            <input type="text" id="kw" class="form-control" />
            <span class="input-group-addon" style="padding:0;">
              <a id="sbtn" class="btn btn-block btn-primary" style="width:120px;height:40px;" onclick="$.csbtn()">搜索</a>
            </span>
          </div>
        </div>
        <div class="col-xs-2"></div>
      </div>
      <div style="height:40px;"></div>
      <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <span><?php echo $info_name; ?></span>
          <div style="height:15px;"></div>
          <div class="row">
            <div class="col-xs-2">
              <ul class="bold">
                <li>关键词</li>
                <li>创建时间</li>
                <li>更新时间</li>
                <li>文件大小</li>
                <li>文件数</li>
                <li>哈希值</li>
              </ul>
            </div>
            <div class="col-xs-10">
              <ul>
                <li>
<?php echo $info_keyword; ?>
                </li>
                <li><?php echo $info_created; ?></li>
                <li><?php echo $info_time; ?></li>
                <li><?php echo $info_size; ?></li>
                <li><?php echo $info_files_num; ?></li>
                <li><?php echo $info_hash; ?></li>
              </ul>
            </div>
          </div>
          <div style="height:15px;"></div>
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-8">
              <div class="input-group">
                <span class="input-group-addon" style="background:#ecf0f1;padding-left:45px;">
                  <a class="btn btn-block btn-lg btn-success btna" target="_blank" href="magnet:?xt=urn:btih:<?php echo $info_hash; ?>">下载资源</a>
                </span>
                <span class="input-group-addon" style="background:#ecf0f1;padding-left:45px;">
                  <a class="btn btn-block btn-lg btn-info btna" target="_blank" href="https://www.haosou.com/s?q=<?php echo urlencode($info_name); ?>">搜索相关</a>
                </span>
              </div>
            </div>
            <div class="col-xs-2"></div>
          </div>
          <div class="row">
            <div class="col-md-12">
            <hr>
              <p>文件列表：</p>
<?php
  if (strpos($info_files_size,"|")===false){
    echo '              <p class="p0">•&nbsp;&nbsp;&nbsp;'.$info_files_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$info_files_size.'</p>'."\n";
  }else{
    $info_files_name_split=split("\|",$info_files_name);
    $info_files_size_split=split("\|",$info_files_size);
    for ($b=0;$b<count($info_files_size_split);$b++){
      echo '              <p class="p0">•&nbsp;&nbsp;&nbsp;'.$info_files_name_split[$b].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$info_files_size_split[$b].'</p>'."\n";
    }
  }
?>
            </div>
          </div>
          <div style="height:20px;"></div>
        </div>
        <div class="col-md-2"></div>
      </div>
    </div>
    <script src="static/jquery.min.js"></script>
    <script type="text/javascript">
      $(function() {
        $("#kw").keyup(function(){
          if (event.keyCode == 13){
            $("a")[1].click();
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