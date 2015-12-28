<?php
include 'Init.php';

@$g_kw=trim(urldecode($_GET['kw']));
@$g_p=trim($_GET['p']);
if (!isset($g_kw)||$g_kw=="") header('Location: /');
if (!is_numeric($g_p)) $g_p=1;
if ($g_p<1) $g_p=1;
$g_p=floor($g_p);
$t1=microtime(true);
OpenDB();
$kws=getKeyword($g_kw);
$hashs=array();
if (strpos($kws,"|")!==false){
  $kws_split=split("\|",$kws);
  for ($a=0;$a<count($kws_split);$a++){
    if ($kws_split[$a]!=""){
      $result=RunDB("select hashs from search where keyword='".$kws_split[$a]."'");
      if (mysql_num_rows($result)>0){
        $row=mysql_fetch_row($result);
        $row=$row[0];
        if (strpos($row,"|")!==false){
          $row_split=split("\|",$row);
          for ($b=0;$b<count($row_split);$b++){
            array_push($hashs,$row_split[$b]);
          }
        }else{
          array_push($hashs,$row);
        }
      }
    }
  }
}else{
  $result=RunDB("select hashs from search where keyword='".$kws."'");
  if (mysql_num_rows($result)>0){
    $row=mysql_fetch_row($result);
    $row=$row[0];
    if (strpos($row,"|")!==false){
      $row_split=split("\|",$row);
      for ($c=0;$c<count($row_split);$c++){
        array_push($hashs,$row_split[$c]);
      }
    }else{
      array_push($hashs,$row);
    }
  }
}
if (count($hashs)>0){
  $hashs=array_reverse($hashs);
  $hashsl=array();
  foreach ($hashs as $hasht){
    @$hashsl[$hasht]++;
  }
  arsort($hashsl);
  $info_hash=array();
  $info_time=array();
  $info_name=array();
  $info_size=array();
  $info_files_num=array();
  $info_files_name=array();
  $info_files_size=array();
  $hashs=array_keys($hashsl);
  if ($g_p>ceil(count($hashs)/10)) $g_p=ceil(count($hashs)/10);
  for ($d=10*($g_p-1);$d<10*$g_p;$d++){
    if ($d>=count($hashs)) break;
    $result=RunDB("select * from metadata where `hash`='".$hashs[$d]."'");
    if (mysql_num_rows($result)>0){
      $row=mysql_fetch_row($result);
      array_push($info_hash,$row[1]);
      $row[2]=format_date($row[2]);
      array_push($info_time,$row[2]);
      array_push($info_name,$row[3]);
      $row[5]=format_size($row[5]);
      array_push($info_size,$row[5]);
      array_push($info_files_num,$row[6]);
      array_push($info_files_name,$row[7]);
      $row[8]=format_size($row[8]);
      array_push($info_files_size,$row[8]);
    }
  }
}
CloseDB();
$t2=microtime(true);
?>
<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <title>搜索结果：<?php echo $g_kw; ?> - DHT 资源搜索站</title>
    <link href="static/bootstrap.css" rel="stylesheet">
    <link href="static/flat-ui.css" rel="stylesheet">
    <style>
      .keyword{color:red;}
      ul{padding-left:0px;}
      li{list-style-type:none;}
      p{width:900px;line-height:6px;
        white-space:nowrap;
        text-overflow:ellipsis;}
      .p0{font-size:16px;}
      .p1{font-size:13px;}
      .p2{font-size:11px;}
      p a{font-size:16px;}
      p a:hover{text-decoration:underline;}
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
            <input type="text" id="kw" value="<?php echo $g_kw; ?>" class="form-control" />
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
          <span>共找到 <?php echo count($hashs); ?> 个关于 <?php echo $g_kw; ?> 的结果，耗时 <?php echo round($t2-$t1,3); ?> 秒</span>
          <ul>
<?php
for ($i=0;$i<count($info_hash);$i++){
  if (strpos($kws,"|")!==false){
    $kws_split=split("\|",$kws);
    for ($e=0;$e<count($kws_split);$e++){
      $info_name[$i]=str_ireplace($kws_split[$e],'<span class="keyword">'.$kws_split[$e].'</span>',$info_name[$i]);
    }
  }else{
    $info_name[$i]=str_ireplace($kws,'<span class="keyword">'.$kws.'</span>',$info_name[$i]);
  }
?>
            <hr>
            <li>
              <p class="p0"><a target="_blank" href="i.php?h=<?php echo $info_hash[$i]; ?>"><?php echo $info_name[$i]; ?></a></p>
              <div style="height:5px;"></div>
<?php
  if (strpos($info_files_size[$i],"|")===false){
    echo '              <p class="p1">'.$info_files_name[$i].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$info_files_size[$i].'</p>'."\n";
  }else{
    $info_files_name_split=split("\|",$info_files_name[$i]);
    $info_files_size_split=split("\|",$info_files_size[$i]);
    for ($ii=0;$ii<count($info_files_size_split);$ii++){
      if ($ii>=5){
        echo '              <p class="p1">....</p>'."\n";
        break;
      }
      echo '              <p class="p1">'.$info_files_name_split[$ii].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$info_files_size_split[$ii].'</p>'."\n";
    }
  }
?>
              <div style="height:10px;"></div>
              <p class="p2">文件数：<?php echo $info_files_num[$i]; ?>&nbsp;&nbsp;&nbsp;&nbsp;大小：<?php echo $info_size[$i]; ?>&nbsp;&nbsp;&nbsp;&nbsp;更新时间：
<?php
  if (strpos($info_time[$i],"秒")!==false||strpos($info_time[$i],"分钟")!==false||strpos($info_time[$i],"小时")!==false||strpos($info_time[$i],"1天")!==false||strpos($info_time[$i],"2天")!==false||strpos($info_time[$i],"3天")!==false){
    echo '<span class="keyword">'.$info_time[$i].'</span>';
  }else{
    echo $info_time[$i];
  }
  echo "&nbsp;&nbsp;&nbsp;&nbsp;";
?>
              <a class="p2" target="_blank" href="magnet:?xt=urn:btih:<?php echo $info_hash[$i]; ?>">打开链接</a></p>
            </li>
<?php } ?>
          </ul>
          <div class="pagination">
            <ul>
<?php
  if ($g_p!=1) echo '              <li class="previous"><a href="s.php?kw='.$g_kw.'&p='.($g_p-1).'" class="fui-arrow-left"></a></li>'."\n";
  $pbp=0;
  for ($pa=($g_p-4);$pa<$g_p;$pa++){
    if ($pa<=0){
      $pbp++;
      continue;
    }
    echo '              <li><a href="s.php?kw='.$g_kw.'&p='.$pa.'">'.$pa.'</a></li>'."\n";
  }
  echo '              <li class="active"><a href="javascript:void;">'.$g_p.'</a></li>'."\n";
  for ($pb=($g_p+1);$pb<=($g_p+$pbp+5);$pb++){
    if ($pb>ceil(count($hashs)/10)) break;
    echo '              <li><a href="s.php?kw='.$g_kw.'&p='.$pb.'">'.$pb.'</a></li>'."\n";
  }
  if ($g_p!=ceil(count($hashs)/10)) echo '              <li class="next"><a href="s.php?kw='.$g_kw.'&p='.($g_p+1).'" class="fui-arrow-right"></a></li>'."\n";
?>
            </ul>
          </div>
        </div>
        <div class="col-md-2"></div>
        <div style="height:10px;"></div>
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