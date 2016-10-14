<?php
include "includes/common.php";
print_r($DB->fetch($DB->query("Select * from test")));
$match="/<a name='file_title' target=\"_blank\" href=\"([\s\S]*?)\">([\s\S]*?)<span class=\"mhl\">([\s\S]*?)<\/span>([\s\S]*?)<\/a><\/div>\n<dl class=\"BotInfo\">\n<dt>种子大小:<span>([\s\S]*?)<\/span> &nbsp;&nbsp;&nbsp;&nbsp;\n文件数量:<span> ([\s\S]*?)<\/span> &nbsp;&nbsp;&nbsp;&nbsp;\n创建时间:<span> ([\s\S]*?) <\/span>\n<\/dt>\n<\/dl>\n<div class=\"dInfo\">\n请使用uTorrent,迅雷,旋风,百度115网盘等工具下载！<a href=\"([\s\S]*?) \">/";

$output = curl("http://cililian.me/list/1/1.html");

preg_match_all($match,$output,$str);

//打印获得的数据
$res = array();
foreach($str as $key => $value){
  if($key == 0){
        echo "<br>";
    }else{
      foreach($str[$key] as $key1 => $value1) {
          if($res[$key1]==""){
            $res[$key1] = array();
            $res[$key1][$key] = $value1;
          }else{
              $res[$key1][$key] = $value1;
          }
      }
    }
}
echo json_encode((object)$res)."<br>";
$seed = $_GET['seed'];
$out = curl("http://i.vod.xunlei.com/req_subBT/info_hash/".$seed."/req_num/1000/req_offset/0");
$dejson = json_decode($out,TRUE);
if(count($dejson['resp']['subfile_list'])>0){
    foreach($dejson['resp']['subfile_list'] as $key=>$value){
        echo $key."index:".$value['index']."<br>";
        echo $key."name:".$value['name']."<br>";
        echo $key."file_size:".$value['file_size']."<br>";
    }
}else{
    echo "没有数据";
}

//print_r($dejson);

function curl($url){
//初始化
    $ch = curl_init();

//设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);

//执行并获取HTML文档内容
    $output = curl_exec($ch);

//释放curl句柄
    curl_close($ch);

    return $output;
}


