<?php
header('Content-Type:text/html;charset=utf-8');
require 'MyPdo.class.php';

    define("ALL_PS", "pencil");
    $action = isset($_GET['action'])?$_GET['action']:"";
    $userinfo = isset($_POST['action'])?$_POST['action']:"";
    $error = "false";
    $dsn = 'localhost';
    $username = 'root';
    $password = 'root';
    $dbName = 'test';
    $dbChar = 'UTF8';
    $database = 'mysql';
    $pdo = MyPdo::getInstance($dsn,$username,$password,$dbName,$dbChar,$database);
    
switch ($action) {

    //注册会员
    case"adduserinfo";
        $username = lib_replace_end_tag(trim($_POST['username']));
        $password2 = lib_replace_end_tag(trim($_POST['password']));
        $password = md5("$password2" . ALL_PS);
        $email = lib_replace_end_tag(trim($_POST['email']));

        if ($username == '' || $password2 == '' || $password == '' || $email == '') {
            $res = urlencode("参数有误");
            $resinfo = array('status'=>'1','info'=>'参数有误'); 
            exit(json_encode($resinfo)); //有空信息
        }

        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        if (!preg_match($regex, $email)) {
            $resinfo = array('status'=>'1','info'=>'电子邮件格式不正确'); 
            exit(json_encode($resinfo)); 
        }

        $sql = "select username from `userinfo` where username='$username'";
        $count = count($pdo->getInfo($sql));

        $sql = "select email from `userinfo` where email='$email'";
        $count2 = count($pdo->getInfo($sql));

        if ($count > 0) {
            $resinfo = array('status'=>'1','info'=>'用户名已存在'); 
            exit(json_encode($resinfo)); 
        }else if ($count2 > 0){
            $resinfo = array('status'=>'1','info'=>'邮箱已使用，请使用别的邮箱注册'); 
            exit(json_encode($resinfo)); 
        } else {
            
            $arr = array('username'=>$username,'password'=>$password,'email'=>$email,'registertime'=>time());
            $pdo->insert('userinfo',$arr);

            $resinfo = array('status'=>'0','info'=>'注册成功'); 
            exit(json_encode($resinfo));  //返回0表示注册成功
        }
        break;


    //查询用户信息
    case"selectuserinfo";
        $username = lib_replace_end_tag($_POST['username']);
        $sql = "select userid,username,email from `userinfo` where username='$username'";
        $row = $pdo->getInfo($sql,'ROW');
        foreach ($row as $key => $v) {
            $res[$key] = urlencode($v);
        }
        $resinfo = array('status'=>'0','info'=>$res); 
        exit(json_encode($resinfo)); 
        break;


    //会员登录
    case"userlogin";
        $username = lib_replace_end_tag($_POST['username']);
        $password2 = lib_replace_end_tag(trim($_POST['password']));
        $password = md5("$password2" . ALL_PS);
        $sqluser = "select username,password,email from `userinfo` where email='" . $username . "' and password='" . $password . "'";
        $rowuser = $pdo->getInfo($sqluser,'ROW');

        if ($rowuser && is_array($rowuser) && !empty($rowuser)) {
            if ($rowuser['username'] == $username || $rowuser['email'] == $username) {
                if ($rowuser['password'] == $password) {
                    $resinfo = array('status'=>'0','info'=>'登录成功'); 
                    exit(json_encode($resinfo)); 
                } else {
                    $resinfo = array('status'=>'1','info'=>'密码错误'); 
                    exit(json_encode($resinfo)); 
                }
            } else {
                $resinfo = array('status'=>'1','info'=>'用户名不存在'); 
                exit(json_encode($resinfo)); 
            }
        } else {
            $resinfo = array('status'=>'1','info'=>'用户名密码错误'); 
            exit(json_encode($resinfo)); 
        }
       
        break;

    default:
        $resinfo = array('status'=>'1','info'=>'地址错误'); 
        exit(json_encode($resinfo));
}


function lib_replace_end_tag($str) { 
    if (empty($str)) return false; 
    $str = htmlspecialchars($str); 
    $str = str_replace( '/', "", $str); 
    $str = str_replace("\\", "", $str); 
    $str = str_replace(">", "", $str); 
    $str = str_replace("<", "", $str); 
    $str = str_replace("<SCRIPT>", "", $str); 
    $str = str_replace("</SCRIPT>", "", $str); 
    $str = str_replace("<script>", "", $str); 
    $str = str_replace("</script>", "", $str); 
    $str=str_replace("select","select",$str); 
    $str=str_replace("join","join",$str); 
    $str=str_replace("union","union",$str); 
    $str=str_replace("where","where",$str); 
    $str=str_replace("insert","insert",$str); 
    $str=str_replace("delete","delete",$str); 
    $str=str_replace("update","update",$str); 
    $str=str_replace("like","like",$str); 
    $str=str_replace("drop","drop",$str); 
    $str=str_replace("create","create",$str); 
    $str=str_replace("modify","modify",$str); 
    $str=str_replace("rename","rename",$str); 
    $str=str_replace("alter","alter",$str); 
    $str=str_replace("cas","cast",$str); 
    $str=str_replace("&","&",$str); 
    $str=str_replace(">",">",$str); 
    $str=str_replace("<","<",$str); 
    $str=str_replace(" ",chr(32),$str); 
    $str=str_replace(" ",chr(9),$str); 
    $str=str_replace(" ",chr(9),$str); 
    $str=str_replace("&",chr(34),$str); 
    $str=str_replace("'",chr(39),$str); 
    $str=str_replace("<br />",chr(13),$str); 
    $str=str_replace("''","'",$str); 
    $str=str_replace("css","'",$str); 
    $str=str_replace("CSS","'",$str); 
    return $str; 
} 


?>