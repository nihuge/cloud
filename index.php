<?php
include "includes/common.php";
//print_r($DB->fetch($DB->query("Select * from test")));

if($_GET['app']=='t'){
    gettrr();
}elseif($_GET['app']=='h'){
    gethash();
}elseif($_GET['app']=='m'){
    getmove();
}


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
function gettrr()
{
    $word = $_GET['word'];
    $page = $_GET['page'];
    //打印获得的数据
    $match = "/<a name='file_title' target=\"_blank\" href=\"\/xiangxi\/([\s\S]*?)\">([\s\S]*?)<span class=\"mhl\">([\s\S]*?)<\/span>([\s\S]*?)</";
    $output = curl("http://cililian.me/list/" . $word . "/" . $page . ".html");
    preg_match_all($match, $output, $str);
    if (count($str) == 0) {
        echo "没有搜索结果";
        exit();
    } else {
        $res = array();
        foreach ($str as $key => $value) {
            if ($key == 0) {
                echo '';
            } else {
                foreach ($str[$key] as $key1 => $value1) {
                    if ($res[$key1] == "") {
                        $res[$key1] = array();
                        $res[$key1][$key] = $value1;
                    } else {
                        $res[$key1][$key] = $value1;
                }
                }
            }
        }
        echo json_encode((object)$res);
        exit();
    }
}


function gethash(){
    $seed = $_GET['seed'];
    $out = curl("http://i.vod.xunlei.com/req_subBT/info_hash/".$seed."/req_num/1000/req_offset/0");
    $dejson = json_decode($out,TRUE);
    if(count($dejson['resp']['subfile_list'])>0){
        $hashs = array();
        $num = 0;
        foreach($dejson['resp']['subfile_list'] as $key=>$value){
                $hashs[$num] = array();
                $hashs[$num]["index"] = $value['index'];
                $hashs[$num]["name"] = urldecode($value['name']);
                $hashs[$num]["file_size"] = $value['file_size'];
                $hashs[$num]["hash"] = $_GET['seed'];
            $num++;
//            echo "index=".$value['index']."<br>";
//            echo "name=".$value['name']."<br>";
//            echo "file_size=".$value['file_size']."<br>";
//            echo "".$value['']."<br>";
        }
    }else{
        echo "没有数据";
    }
    echo json_encode((object)$hashs);
//    print_r($hashs);
    exit();
}


function getmove(){
    $hash=$_GET['hash'];
    $index=$_GET['index'];
    $metch = "/<cookie>([\s\S]*?)<\/cookie><\/br><url>([\s\S]*?)<\/url>/";
    $ret=curl("http://aa7761610.s180.cnaaa8.com/2944423432_11_14.php?hash=".$hash."&index=".$index);
    if($ret<>"﻿无效资源        "){
    preg_match($metch, $ret, $str1);
        $returl = array(
            'cookie' => $str1['1'],
            'url' => $str1['2']
        );
    echo json_encode($returl);
    }else{
        echo $ret." hash:".$_GET['hash']." index:".$_GET['index'];
    }
}
function gettrr2(){
    $metch1 = "/<span class=\"highlight\">(\W+)<\/span>/";
    $metch2 = "/<a class=\"title\" href=\"\/view\/([A-Za-z0-9]{40})([A-Za-z0-9]{1,9})\">([\s\S]*?)<\/a>/";
    $res=curl("http://www.shenmidizhi.com/list/".$_GET['word']."-hot-desc-".$_GET['page']);
}
?>
