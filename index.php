<?php
if($_GET['app'] == 't'){
    gettrr();
}elseif($_GET['app'] == 'h'){
    gethash();
}elseif($_GET['app'] == 'm'){
    getmove();
}elseif($_GET['app'] == "test"){
    gettrr2();
}else{
    exit();
}

function curl($url){
    $output = file_get_contents($url);
//    $ch = curl_init();
//
//    curl_setopt($ch, CURLOPT_URL, $url);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($ch, CURLOPT_HEADER, 0);
//
//    $output = curl_exec($ch);
//
//    curl_close($ch);

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
        foreach($dejson['resp']['subfile_list'] as $key => $value){
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
    require "includes/common.php";
    $hash = $_GET['hash'];
    $index = $_GET['index'];
    $ret_seach = $DB->get_row("Select url,cookie from h where hi='".$hash.$index."'");
    if($ret_seach['cookie'] <> ""){
        $ret_rest = array(
            "cookie" => $ret_seach['cookie'],
            "url" => $ret_seach['url']
        );
        echo json_encode($ret_rest);
    }else{
    $metch = "/<cookie>([\s\S]*?)<\/cookie><\/br><url>([\s\S]*?)<\/url>/";
    $ret = curl("http://aa7761610.s180.cnaaa8.com/2944423432_11_14.php?hash=".$hash."&index=".$index);
    if($ret <> "﻿无效资源        "){
    preg_match($metch, $ret, $str1);
        $returl = array(
            'cookie' => $str1['1'],
            'url' => $str1['2']
        );
    echo json_encode($returl);
        $qurey = $DB->query("INSERT INTO h(`hash`, `hindex`, `url`, `cookie`, `hi`) VALUES ('{$hash}','{$index}', '".$returl['url']."', '".$returl['cookie']."', '".$hash.$index."')");
        if(!$qurey){
            $file = fopen('error.txt',"a");
            $data = '{"hash":"'.$hash.'","index":"'.$index.'","url":"'.$returl['cookie'].'","cookie":"'.$returl['cookie'].'","time":"'.data("Y-m-d H:i:s",time()).'"}';
            fwrite($qurey,$data);
            fclose($file);
        }
    }else{
            echo $ret." hash:".$_GET['hash']." index:".$_GET['index'];
    }
    exit();
    }
}
function gettrr2()
{
    $word = $_GET['word'];
    $page = isset($_GET['page'])?$_GET['page']:1;
    $metch1 = "/<span class=\"highlight\">([\s\S]*?)<\/span>/";
    $metch2 = "/<a class=\"title\" href=\"\/view\/([A-Za-z0-9]{40})([A-Za-z0-9]{1,9})\">([\s\S]*?)<\/a>/";
    $ret1 = curl("http://www.shenmidizhi.com/list/".$word."-hot-desc-".$page);
    $ret2 = preg_replace($metch1, "$1", $ret1);
    preg_match_all($metch2, $ret2, $ret3);
    if (count($ret3) == 0) {
        echo "没有搜索结果";
        exit();
    } else {
        $res = array();
        foreach ($ret3 as $key => $value) {
            if ($key == 0) {
                echo '';
            } else {
                foreach ($ret3[$key] as $key1 => $value1) {
                    if ($res[$key1] == "") {
                        $res[$key1] = array();
                        $res[$key1][$key] = $value1;
                    } else {
                        $res[$key1][$key] = $value1;
                    }
                }
            }
        }
    }
    echo json_encode((object)$res);
    exit();
}
/*
 * 创建表代码
 * create table h(
   hid INT(10) NOT NULL AUTO_INCREMENT,
   hname VARCHAR(255),
   hash VARCHAR(40) NOT NULL,
   hindex INT(255) NOT NULL,
   url VARCHAR(255) NOT NULL,
   cookie VARCHAR(255) not null,
   hi VARCHAR(255) NOT null,
   PRIMARY KEY (hid)
);
 */
/*
 *
 */
?>
