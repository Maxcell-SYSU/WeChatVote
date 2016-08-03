<?php
header("Content-type:text/html;charset=utf-8");

function checkSignature() {
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
    $token = "iwillnevertellyou";
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode($tmpArr);
    $tmpStr = sha1($tmpStr);

    if ($tmpStr == $signature) {
        return true;
    }
    else {
        return false;
    }
}

///////////////////////////////////////////////////////////

function creatResponse() {
    if (checkSignature())
    {
        $xmlStr = file_get_contents("php://input");
        $xml = simplexml_load_string($xmlStr);

        $response = simplexml_load_string("<xml></xml>");
        $response->addChild("ToUserName", $xml->FromUserName);
        $response->addChild("FromUserName", $xml->ToUserName);
        $response->addChild("CreateTime", time());
        $response->addChild("MsgType", "text");

        if($xml->MsgType == "event" && $xml->Event == "subscribe") {
            $response->addChild("Content", mb_convert_encoding("欢迎关注 Maxcell 资源在线协会！","UTF-8"));
            return $response->asXML();
        }
        else {
            include_once "database.php";
            if ($xml->MsgType=="text") {
                $query = preg_replace("/\s(?=\s)/","\\1",$xml->Content);
                $query = trim($query);
                $query = explode(" ", $query, 2);
                if ($query) {
                    $result = mysql_query("SELECT * FROM time");
                    while($row = mysql_fetch_array($result)) {
                        $timestr = $row['time'];
                    }
                    if($query[0]=="投票") {
                        $result = mysql_query("SELECT * FROM voted");
                        while ($row = mysql_fetch_array($result))
                        {
                            if ($xml->FromUserName == $row['id'])
                            {
                                $response->addChild("Content","您已经投过票了，一人只能投一次，谢谢参与!");
                                return $response->asXML();
                            }
                        }
                        $id = explode(" ", $query[1]);
                        if (count($id) == 3 && $id[0] != $id[1] && $id[1] != $id[2] && $id[2] != $id[0]) {
                            $result = mysql_query("SELECT COUNT(id) AS num FROM vote WHERE id='".$id[0]."' OR id='".$id[1]."' OR id='".$id[2]."'");
                            $num = 0;
                            while ($row = mysql_fetch_array($result)){
                                $num=$row['num'];
                            }
                            if ($num == 3) {
                                $result = mysql_query("SELECT * FROM vote WHERE id='".$id[0]."' OR id='".$id[1]."' OR id='".$id[2]."'");
                                while ($row = mysql_fetch_array($result))
                                {
                                    mysql_query("UPDATE vote SET vote=vote+1 WHERE id=".$row['id']);
                                }
                                mysql_query("INSERT INTO voted VALUES ('".$xml->FromUserName."')");
                                $content = "";
                                $content = "投票成功！\n\n".$content;
                                $content = $content."你投了".$id[0]."号，".$id[1]."号和".$id[2]."号选手，谢谢您的参与！";
                                $response->addChild("Content", $content);
                                return $response->asXML();
                            }
                        }
                        $response->addChild("Content","请输入正确的队伍编号!\n\n参赛队伍及作品：\n1.脑洞比较大<皇茶>\n2.有趣队<静心 共乐吧>\n3. 元气Te4m<鸡汁步VS翠微电脑城>\n4. 呆而萌<约跑中大app>\n5. 巫山沧海<绿草地咖啡·西餐厅>\n6. 这个组<共乐驿站>\n7. 飞花走鱼<我们的朋友，马公交>\n8. 梦璐<中大琴行>\n9. 朴奎映<臻安烘焙馆>\n10. 超能陆战队<you always here>");
                        return $response->asXML();
                    }
                }
                mysql_close($con);
            }
            else if ($xml->Content=="节目单") {
                $response->MsgType = "news";
                $response->addChild("ArticleCount", "1");
                $response->addChild("Articles", "");
                $response->Articles->addChild("item", "");
                $response->Articles->item->addChild("Title", "橙名夜节目单", "UTF-8");
                $content = "1227 橙名夜节目单\n";
                $response->Articles->item->addChild("Description",$content);
                $response->Articles->item->addChild("PicUrl","http://maxcellweixin.sinaapp.com/webShow/vote/images/head.jpg");
                $response->Articles->item->addChild("Url","http://www.dwz.cn/showList");
                return $response->asXML();
            }
            else if ($xml->Content == "弹幕") {
                $response->addChild("Content", "点击下面的链接发弹幕！\n\nhttp://danmu.maxcell.com.cn/");
                return $response->asXML();
            }
            else {
                $response->addChild("Content", "输入有误，请重试！\n\n输入投票和队伍编号可参与投票，如“投票 1 2 3”。\n\n输入“弹幕”可获取弹幕发送链接。\n\n输入“节目单”可查看橙名夜节目单。\n\n参赛队伍及作品：\n1.脑洞比较大<皇茶>\n2.有趣队<静心 共乐吧>\n3. 元气Te4m<鸡汁步VS翠微电脑城>\n4. 呆而萌<约跑中大app>\n5. 巫山沧海<绿草地咖啡·西餐厅>\n6. 这个组<共乐驿站>\n7. 飞花走鱼<我们的朋友，马公交>\n8. 梦璐<中大琴行>\n9. 朴奎映<臻安烘焙馆>\n10. 超能陆战队<you always here>");
                return $response->asXML();
            }
        }
        if($xml->MsgType != "event" && $xml->MsgType != "text")
            return "";
    }
}

echo creatResponse();

?>
