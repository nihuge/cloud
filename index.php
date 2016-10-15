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
        echo htmlspecialchars($st = json_encode((object)$res));
        exit();
    }
}


function gethash(){
    $seed = $_GET['seed'];
    $out = curl("http://i.vod.xunlei.com/req_subBT/info_hash/".$seed."/req_num/1000/req_offset/0");
    $dejson = json_decode($out,TRUE);
    if(count($dejson['resp']['subfile_list'])>0){
        foreach($dejson['resp']['subfile_list'] as $key=>$value){
            echo "index=".$value['index']."<br>";
            echo "name=".$value['name']."<br>";
            echo "file_size=".$value['file_size']."<br>";
            echo "".$value['']."<br>";
        }
    }else{
        echo "没有数据";
    }
    exit();
}
function getmove(){
    $hash=$_GET['hash'];
    $index=$_GET['index'];
    $ret=curl("http://aa7761610.s180.cnaaa8.com/2944423432_11_14.php?hash=".$hash."&index=".$index);
}
?>
