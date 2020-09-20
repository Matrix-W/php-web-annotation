<?php
    require 'mysql_fun.php';
    if ($_COOKIE['role'] != 'user') {
        header('location: login.php');
        exit;
    }
    $id = $_GET['id'];
    $user_name = trim($_COOKIE['user_name']);
    if (empty($id)) {
        // $sql = "select * from tagging where user_name = '$user_name' order by paragraph_id asc limit 1";
        $sql = "select * from tagging where paragraph_id = (select paragraph_id from (select paragraph_id,user_name from tagging where user_name = '$user_name' group by paragraph_id ) as o  order by o.paragraph_id desc limit 0, 1)";
    }
    else {
        $sql = "select * from tagging where paragraph_id = (select paragraph_id from (select paragraph_id,user_name from tagging where user_name = '$user_name' group by paragraph_id ) as o  order by o.paragraph_id desc limit $id, 1)";
    }
    $res = custom_query($sql);
    $sql_count = "select count(*) as count from tagging where user_name = '$user_name'";
    $res_count = custom_query($sql_count);
    $all_sum = 0;   // 一共有多少段
    foreach ($res_count as $row) {
        $all_sum = $row->count;
    }
    $current_paragraph_id_res = custom_query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Theme Made By www.w3schools.com - No Copyright -->
    <meta charset="utf-8">
    <title>主页</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <script src="jquery-2.1.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <style>
        body {
            font: 20px Montserrat, sans-serif;
            line-height: 1.8;
        }

        p {
            font-size: 16px;
        }

        .margin {
            margin-bottom: 45px;
        }

        .bg-1 {
            background-color: #1abc9c;
            /* Green */
            color: #ffffff;
        }

        .bg-2 {
            background-color: #474e5d;
            /* Dark Blue */
            color: #ffffff;
        }

        .bg-3 {
            background-color: #ffffff;
            /* White */
            color: #555555;
        }

        .bg-4 {
            background-color: #2f2f2f;
            /* Black Gray */
            color: #fff;
        }

        .container-fluid {
            padding-top: 70px;
            padding-bottom: 70px;
        }

        .navbar {
            padding-top: 15px;
            padding-bottom: 15px;
            border: 0;
            border-radius: 0;
            margin-bottom: 0;
            font-size: 12px;
            letter-spacing: 5px;
        }

        .navbar-nav li a:hover {
            color: #1abc9c !important;
        }

        .container_main {
            padding-top: 70px;
            padding-bottom: 70px;
            width: 80%;
            min-height: 500px;
            color: black;

        }

        .alert-success {
            display: none;
        }

        .alert-danger {
            display: none;
        }

        #f_menu {
            position: fixed;
            bottom: 300px;
            right: 15px;
            width: 100px;
            height: 100px;
            opacity: 0.75;
            text-align: center;
            line-height: 100px;
            cursor: pointer;
        }

        .under_line_word {
            border-bottom: 3px solid red;
        }

        .red {
            color: red;
        }
        .content{
            width:70%
        }
    </style>
    <script>
    function trim(val) {
        var str = val.replace(/(^\s*)|(\s*$)/g, '');
        return str;
    }

    function initContent() {
        var html = document.querySelectorAll('.content')[0].innerHTML.split(" ");
        var arr = html.map((x) => ' <span>' + x + '</span> ');
        document.querySelectorAll('.content')[0].innerHTML = arr.join("");
    }
    //写cookies
    function setCookie(name,value) {
        var Days = 30;
        var exp = new Date();
        exp.setTime(exp.getTime() + Days*24*60*60*1000);
        document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
    }
    // 读取cookies
    function getCookie(name) {
        var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
        if(arr=document.cookie.match(reg))
        return unescape(arr[2]);
        else
        return null;
    }
    // 删除cookies
    function delCookie(name) {
        var exp = new Date();
        exp.setTime(exp.getTime() - 1);
        var cval=getCookie(name);
        if(cval!=null)
        document.cookie= name + "="+cval+";expires="+exp.toGMTString();
    }
    // 获取当前时间并格式化
    function getNowFormatDate() {
        var date = new Date();
        var seperator1 = "-";
        var seperator2 = ":";
        var month = date.getMonth() + 1;
        var strDate = date.getDate();
        if (month >= 1 && month <= 9) {
            month = "0" + month;
        }
        if (strDate >= 0 && strDate <= 9) {
            strDate = "0" + strDate;
        }
        var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
                + " " + date.getHours() + seperator2 + date.getMinutes()
                + seperator2 + date.getSeconds();
        return currentdate;
    }

    $(document).ready(function() {
        // initContent();
        $(".content span").on('click', function() {
            $(this).toggleClass('red');
        })
        $("#tagging").on('click', function() {
            var str = "";
            var sentence_id = '';
            $("[class='red']").each(function() {
                str += " " + $(this).html();
                sentence_id = $(this).attr("value");
            });
            if (str != '') {
                var word_id = $('#word_id').val();
                // var data = {
                //     paragraph_id: paragraph_id,
                //     mytext: str,
                //     sentence_id: sentence_id,
                // };
                $("#tagging_words").html(str);
                $('#myModal_tagging').modal({
                    backdrop: 'static'
                });
                $('#myModal_tagging').modal('show');

            } else {
                alert("请选择标注内容");
            }
        });
        // 点击提交按钮后执行如下代码
        $("#submit_btn").on('click', function() {
            var str = "";
            // 获取所有标红的动词
            var sentence_id = '';
            $("[class='red']").each(function() {
                str += " " + $(this).html();
                sentence_id = $(this).attr("value");
            });
            var word_id = $('#word_id').val();
            var verbs_classification_select = $("#verbs-classification-select option:selected").html();
            var speech_attr_select = $("#speech-attr-select option:selected").html();
            var one_select = $("#one-select option:selected").html();
            var two_select = $("#two-select option:selected").html();
            var data = {
                user_name: "<?php echo $user_name; ?>",
                tagging_id: word_id,
                sentence_id: sentence_id,
                tagging_words: str,
                verbs_classification_select: verbs_classification_select,
                speech_attr_select: speech_attr_select,
                one_select: one_select,
                two_select: two_select
            };

            $.ajax({
                type: "POST",
                url: 'ajax.php',
                data: data,
                success: function(data, textStatus, jQxhr) {
                    if (data == 'succeed') {
                        alert('标注成功');
                        if (getCookie("tagging_id") == null) {
                            setCookie("tagging_id", word_id);
                        }
                        else {
                            setCookie("tagging_id", getCookie("tagging_id") + "," + word_id);
                        }
                        location.reload();
                        $("[class='red']").removeAttr("class"); // 点击下面还原按钮，之前选择的选项全部还原
                    }
                    else if (data == 'error') {
                        alert('标注失败');
                    }
                    else {
                        alert('操作失败');
                    }
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    alert('操作无效');
                }
            });
        });
        // 添加空行
        $("#add_null").on('click', function() {
            var word_id = $('#word_id').val();
            var data = {
                user_name: "<?php echo $user_name; ?>",
                tagging_id: word_id,
                sentence_id: "-1",
                tagging_words: '',
                verbs_classification_select: '',
                speech_attr_select: '',
                one_select: '',
                two_select: ''
            };
            console.log(data);
            $.ajax({
                type: "POST",
                url: 'ajax.php',
                data: data,
                success: function(data, textStatus, jQxhr) {
                    if (data == 'succeed') {
                        alert('添加空行成功');
                        location.reload();
                    }
                    else if (data == 'error') {
                        alert('添加空行失败');
                    }
                    else {
                        alert('操作失败');
                    }
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    alert('操作无效');
                }
            });
        });
        $("#all_tagging").on('click', function() {
            $('#myModal').modal({
                backdrop: 'static'
            });
            $('#myModal').modal('show')
        });
        // 点击开始标注按钮
        $("#tagging_start").on('click', function() {
            $('#tagging').show();
            $("#tagging_start").hide();
            $("#tagging_end").show();
            // 将开始标识的信息存入浏览器的 cookie 中
            var mytime = getNowFormatDate();     //获取当前时间
            setCookie("tagging_now", 1);
            setCookie("tagging_time", mytime);
        });
        // 点击结束标注按钮
        $("#tagging_end").on('click', function() {
            $('#tagging').hide();
            $("#tagging_end").hide();
            $("#tagging_start").show();
            // 获取 cookie 中的开始时间后，清空浏览器 cookie 信息
            var tagging_start_time = getCookie("tagging_time");
            var tagging_end_time = getNowFormatDate();
            var user_name = getCookie("user_name");
            var tagging_id = getCookie("tagging_id");
            var data = {
                user_name: user_name,
                start_time: tagging_start_time,
                end_time: tagging_end_time,
                tagging_id: tagging_id
            };
            console.log(data);
            $.ajax({
                type: "POST",
                url: 'ajax/tagging_time.php',
                data: data,
                success: function(data, textStatus, jQxhr) {
                    if (data == 'succeed') {
                        // alert('标注结束');
                    }
                    else {
                        alert('标注计时出错，请联系管理员处理！');
                    }
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    alert('计时操作无效');
                }
            });

            delCookie("tagging_now");
            delCookie("tagging_time");
            delCookie("tagging_id");
            // 将信息存入数据表 user_tagging_time

        });
        $("#restore").click(function() {
            $("[class='red']").removeAttr("class"); // 点击下面还原按钮，之前选择的选项全部还原
        });
        
        // 第一级下拉菜单：词性
        $('#speech-attr-select').on('change', function() {
            if (this.value == '动词') {
                // 修改动词分类下拉菜单
                $("#verbs-classification-select").html('<option value="口头合作">口头合作</option><option value="实际合作">实际合作</option><option value="口头敌对">口头敌对</option><option value="实际敌对">实际敌对</option>');
                // 修改一级分类下拉菜单
                $("#one-select").html('<option value="公开声明">公开声明</option><option value="呼吁">呼吁</option><option value="合作意愿">合作意愿</option><option value="要求">要求</option>');
                // 修改二级分类下拉菜单
                $("#two-select").html('<option value="声明">声明</option><option value="拒绝评论">拒绝评论</option><option value="悲观评论">悲观评论</option><option value="乐观评论">乐观评论</option><option value="考虑政策选择">考虑政策选择</option><option value="承认或声称负责">承认或声称负责</option><option value="拒绝指控、否认责任">拒绝指控、否认责任</option><option value="参与象征性活动">参与象征性活动</option><option value="善意评论">善意评论</option><option value="表示一致性">表示一致性</option>');
            }
            else if (this.value == '施动者') {
                // 修改动词分类下拉菜单
                $("#verbs-classification-select").html('<option value="国家代码">国家代码</option><option value="国际代码">国际代码</option>');
                // 修改一级分类下拉菜单
                $("#one-select").html('<option value="阿富汗 AFG">阿富汗 AFG</option><option value="奥兰群岛 ALA">奥兰群岛 ALA</option><option value="阿尔巴尼亚 ALB">阿尔巴尼亚 ALB</option><option value="阿尔及利亚 DZA">阿尔及利亚 DZA</option><option value="美属萨摩亚 ASM">美属萨摩亚 ASM</option><option value="安道尔 AND">安道尔 AND</option><option value="安哥拉 AGO">安哥拉 AGO</option><option value="安圭拉 AIA">安圭拉 AIA</option><option value="南极洲 ATA">南极洲 ATA</option><option value="安提瓜和巴布达 ATG">安提瓜和巴布达 ATG</option><option value="阿根廷 ARG">阿根廷 ARG</option><option value="亚美尼亚 ARM">亚美尼亚 ARM</option><option value="阿鲁巴 ABW">阿鲁巴 ABW</option><option value="澳大利亚 AUS">澳大利亚 AUS</option><option value="奥地利 AUT">奥地利 AUT</option><option value="阿塞拜疆 AZE">阿塞拜疆 AZE</option><option value="巴哈马 BHS">巴哈马 BHS</option><option value="巴林 BHR">巴林 BHR</option><option value="孟加拉国 BGD">孟加拉国 BGD</option><option value="巴巴多斯 BRB">巴巴多斯 BRB</option><option value="白俄罗斯 BLR">白俄罗斯 BLR</option><option value="比利时 BEL">比利时 BEL</option><option value="伯利兹 BLZ">伯利兹 BLZ</option><option value="贝宁 BEN">贝宁 BEN</option><option value="百慕大 BMU">百慕大 BMU</option><option value="不丹 BTN">不丹 BTN</option><option value="玻利维亚 BOL">玻利维亚 BOL</option><option value="波黑 BIH">波黑 BIH</option><option value="博茨瓦纳 BWA">博茨瓦纳 BWA</option><option value="布维岛 BVT">布维岛 BVT</option><option value="巴西 BRA">巴西 BRA</option><option value="英属印度洋领地 IOT">英属印度洋领地 IOT</option><option value="文莱 BRN">文莱 BRN</option><option value="保加利亚 BGR">保加利亚 BGR</option><option value="布基纳法索 BFA">布基纳法索 BFA</option><option value="布隆迪 BDI">布隆迪 BDI</option><option value="柬埔寨 KHM">柬埔寨 KHM</option><option value="喀麦隆 CMR">喀麦隆 CMR</option><option value="加拿大 CAN">加拿大 CAN</option><option value="佛得角 CPV">佛得角 CPV</option><option value="开曼群岛 CYM">开曼群岛 CYM</option><option value="中非 CAF">中非 CAF</option><option value="乍得 TCD">乍得 TCD</option><option value="智利 CHL">智利 CHL</option><option value="中国 CHN">中国 CHN</option><option value="圣诞岛 CXR">圣诞岛 CXR</option><option value="科科斯（基林）群岛 CCK">科科斯（基林）群岛 CCK</option><option value="哥伦比亚 COL">哥伦比亚 COL</option><option value="科摩罗 COM">科摩罗 COM</option><option value="刚果（布） COG">刚果（布） COG</option><option value="刚果（金） COD">刚果（金） COD</option><option value="库克群岛 COK">库克群岛 COK</option><option value="哥斯达黎加 CRI">哥斯达黎加 CRI</option><option value="科特迪瓦 CIV">科特迪瓦 CIV</option><option value="克罗地亚 HRV">克罗地亚 HRV</option><option value="古巴 CUB">古巴 CUB</option><option value="塞浦路斯 CYP">塞浦路斯 CYP</option><option value="捷克 CZE">捷克 CZE</option><option value="丹麦 DNK">丹麦 DNK</option><option value="吉布提 DJI">吉布提 DJI</option><option value="多米尼克 DMA">多米尼克 DMA</option><option value="多米尼加 DOM">多米尼加 DOM</option><option value="厄瓜多尔 ECU">厄瓜多尔 ECU</option><option value="埃及 EGY">埃及 EGY</option><option value="萨尔瓦多 SLV">萨尔瓦多 SLV</option><option value="赤道几内亚 GNQ">赤道几内亚 GNQ</option><option value="厄立特里亚 ERI">厄立特里亚 ERI</option><option value="爱沙尼亚 EST">爱沙尼亚 EST</option><option value="埃塞俄比亚 ETH">埃塞俄比亚 ETH</option><option value="福克兰群岛（马尔维纳斯） FLK">福克兰群岛（马尔维纳斯） FLK</option><option value="法罗群岛 FRO">法罗群岛 FRO</option><option value="斐济 FJI">斐济 FJI</option><option value="芬兰 FIN">芬兰 FIN</option><option value="法国 FRA">法国 FRA</option><option value="法属圭亚那 GUF">法属圭亚那 GUF</option><option value="法属波利尼西亚 PYF">法属波利尼西亚 PYF</option><option value="法属南部领地 ATF">法属南部领地 ATF</option><option value="加蓬 GAB">加蓬 GAB</option><option value="冈比亚 GMB">冈比亚 GMB</option><option value="格鲁吉亚 GEO">格鲁吉亚 GEO</option><option value="德国 DEU">德国 DEU</option><option value="加纳 GHA">加纳 GHA</option><option value="直布罗陀 GIB">直布罗陀 GIB</option><option value="希腊 GRC">希腊 GRC</option><option value="格陵兰 GRL">格陵兰 GRL</option><option value="格林纳达 GRD">格林纳达 GRD</option><option value="瓜德罗普 GLP">瓜德罗普 GLP</option><option value="关岛 GUM">关岛 GUM</option><option value="危地马拉 GTM">危地马拉 GTM</option><option value="格恩西岛 GGY">格恩西岛 GGY</option><option value="几内亚 GIN">几内亚 GIN</option><option value="几内亚比绍 GNB">几内亚比绍 GNB</option><option value="圭亚那 GUY">圭亚那 GUY</option><option value="海地 HTI">海地 HTI</option><option value="赫德岛和麦克唐纳岛 HMD">赫德岛和麦克唐纳岛 HMD</option><option value="梵蒂冈 VAT">梵蒂冈 VAT</option><option value="洪都拉斯 HND">洪都拉斯 HND</option><option value="中国香港 HKG">中国香港 HKG</option><option value="匈牙利 HUN">匈牙利 HUN</option><option value="冰岛 ISL">冰岛 ISL</option><option value="印度 IND">印度 IND</option><option value="印度尼西亚 IDN">印度尼西亚 IDN</option><option value="伊朗 IRN">伊朗 IRN</option><option value="伊拉克 IRQ">伊拉克 IRQ</option><option value="爱尔兰 IRL">爱尔兰 IRL</option><option value="英国属地曼岛 IMN">英国属地曼岛 IMN</option><option value="以色列 ISR">以色列 ISR</option><option value="意大利 ITA">意大利 ITA</option><option value="牙买加 JAM">牙买加 JAM</option><option value="日本 JPN">日本 JPN</option><option value="泽西岛 JEY">泽西岛 JEY</option><option value="约旦 JOR">约旦 JOR</option><option value="哈萨克斯坦 KAZ">哈萨克斯坦 KAZ</option><option value="肯尼亚 KEN">肯尼亚 KEN</option><option value="基里巴斯 KIR">基里巴斯 KIR</option><option value="朝鲜 PRK">朝鲜 PRK</option><option value="韩国 KOR">韩国 KOR</option><option value="科威特 KWT">科威特 KWT</option><option value="吉尔吉斯斯坦 KGZ">吉尔吉斯斯坦 KGZ</option><option value="老挝 LAO">老挝 LAO</option><option value="拉脱维亚 LVA">拉脱维亚 LVA</option><option value="黎巴嫩 LBN">黎巴嫩 LBN</option><option value="莱索托 LSO">莱索托 LSO</option><option value="利比里亚 LBR">利比里亚 LBR</option><option value="利比亚 LBY">利比亚 LBY</option><option value="列支敦士登 LIE">列士敦士登 LIE</option><option value="立陶宛 LTU">立陶宛 LTU</option><option value="卢森堡 LUX">卢森堡 LUX</option><option value="中国澳门 MAC">中国澳门 MAC</option><option value="前南马其顿 MKD">前南马其顿 MKD</option><option value="马达加斯加 MDG">马达加斯加 MDG</option><option value="马拉维 MWI">马拉维 MWI</option><option value="马来西亚 MYS">马来西亚 MYS</option><option value="马尔代夫 MDV">马尔代夫 MDV</option><option value="马里 MLI">马里 MLI</option><option value="马其他 MLT">马其他 MLT</option><option value="马绍尔群岛 MHL">马绍尔群岛 MHL</option><option value="马提尼克 MTQ">马提尼克 MTQ</option><option value="毛利塔尼亚 MRT">毛利塔尼亚 MRT</option><option value="毛里求斯 MUS">毛里求斯 MUS</option><option value="马约特 MYT">马约特 MYT</option><option value="墨西哥 MEX">墨西哥 MEX</option><option value="密克罗尼西亚联邦 FSM">密克罗尼西亚联邦 FSM</option><option value="摩尔多瓦 MDA">摩尔多瓦 MDA</option><option value="摩纳哥 MCO">摩纳哥 MCO</option><option value="蒙古 MNG">蒙古 MNG</option><option value="黑山 MNE">黑山 MNE</option><option value="蒙特塞拉特 MSR">蒙特塞拉特 MSR</option><option value="摩洛哥 MAR">摩洛哥 MAR</option><option value="莫桑比克 MOZ">莫桑比克 MOZ</option><option value="缅甸 MMR">缅甸 MMR</option><option value="纳米比亚 NAM">纳米比亚 NAM</option><option value="瑙鲁 NRU">瑙鲁 NRU</option><option value="尼泊尔 NPL">尼泊尔 NPL</option><option value="荷兰 NLD">荷兰 NLD</option><option value="荷属安的列斯 ANT">荷属安的列斯 ANT</option><option value="新喀里多尼亚 NCL">新喀里多尼亚 NCL</option><option value="新西兰 NZL">新西兰 NZL</option><option value="尼加拉瓜 NIC">尼加拉瓜 NIC</option><option value="尼日尔 NER">尼日尔 NER</option><option value="尼日利亚 NGA">尼日利亚 NGA</option><option value="纽埃 NIU">纽埃 NIU</option><option value="诺福克岛 NFK">诺福克岛 NFK</option><option value="北马里亚纳 MNP">北马里亚纳 MNP</option><option value="挪威 NOR">挪威 NOR</option><option value="阿曼 OMN">阿曼 OMN</option>	<option value="巴基斯坦 PAK">巴基斯坦 PAK</option><option value="帕劳 PLW">帕劳 PLW</option><option value="巴勒斯坦 PSE">巴勒斯坦 PSE</option><option value="巴拿马 PAN">巴拿马 PAN</option>	<option value="巴布亚新几内亚 PNG">巴布亚新几内亚 PNG</option><option value="巴拉圭 PRY">巴拉圭 PRY</option><option value="秘鲁 PER">秘鲁 PER</option><option value="菲律宾 PHL">菲律宾 PHL</option>	<option value="皮特凯恩 PCN">皮特凯恩 PCN</option><option value="波兰 POL">波兰 POL</option><option value="葡萄牙 PRT">葡萄牙 PRT</option><option value="波多黎各 PRI">波多黎各 PRI</option>	<option value="卡塔尔 QAT">卡塔尔 QAT</option><option value="留尼汪 REU">留尼汪 REU</option><option value="罗马尼亚 ROU">罗马尼亚 ROU</option><option value="俄罗斯联邦 RUS">俄罗斯联邦 RUS</option>	<option value="卢旺达 RWA">卢旺达 RWA</option><option value="圣赫勒拿 SHN">圣赫勒拿 SHN</option><option value="圣基茨和尼维斯 KNA">圣基茨和尼维斯 KNA</option><option value="圣卢西亚 LCA">圣卢西亚 LCA</option>	<option value="圣皮埃尔和密克隆 SPM">圣皮埃尔和密克隆 SPM</option><option value="圣文森特和格林纳丁斯 VCT">圣文森特和格林纳丁斯 VCT</option><option value="萨摩亚 WSM">萨摩亚 WSM</option><option value="圣马力诺 SMR">圣马力诺 SMR</option>	<option value="圣多美和普林西比 STP">圣多美和普林西比 STP</option><option value="沙特阿拉伯 SAU">沙特阿拉伯 SAU</option><option value="塞内加尔 SEN">塞内加尔 SEN</option><option value="塞尔维亚 SRB">塞尔维亚 SRB</option>	<option value="塞舌尔 SYC">塞舌尔 SYC</option><option value="塞拉利昂 SLE">塞拉利昂 SLE</option><option value="新加坡 SGP">新加坡 SGP</option><option value="斯洛伐克 SVK">斯洛伐克 SVK</option>	<option value="斯洛文尼亚 SVN">斯洛文尼亚 SVN</option><option value="所罗门群岛 SLB">所罗门群岛 SLB</option><option value="索马里 SOM">索马里 SOM</option><option value="南非 ZAF">南非 ZAF</option>	<option value="南乔治亚岛和南桑德韦奇岛 SGS">南乔治亚岛和南桑德韦奇岛 SGS</option><option value="西班牙 ESP">西班牙 ESP</option><option value="斯里兰卡 LKA">斯里兰卡 LKA</option><option value="苏丹 SDN">苏丹 SDN</option>	<option value="苏里南 SUR">苏里南 SUR</option><option value="斯瓦尔巴岛和扬马延岛 SJM">斯瓦尔巴岛和扬马延岛 SJM</option><option value="斯威士兰 SWZ">斯威士兰 SWZ</option><option value="瑞典 SWE">瑞典 SWE</option>	<option value="瑞士 CHE">瑞士 CHE</option><option value="叙利亚 SYR">叙利亚 SYR</option><option value="中国台湾 TWN">中国台湾 TWN</option><option value="塔吉克斯坦 TJK">塔吉克斯坦 TJK</option>	<option value="坦桑尼亚 TZA">坦桑尼亚 TZA</option><option value="泰国 THA">泰国 THA</option><option value="东帝汶 TLS">东帝汶 TLS</option><option value="多哥 TGO">多哥 TGO</option>	<option value="托克劳 TKL">托克劳 TKL</option><option value="汤加 TON">汤加 TON</option><option value="特立尼达和多巴哥 TTO">特立尼达和多巴哥 TTO</option><option value="突尼斯 TUN">突尼斯 TUN</option>	<option value="土耳其 TUR">土耳其 TUR</option><option value="土库曼斯坦 TKM">土库曼斯坦 TKM</option><option value="特克斯和凯科斯群岛 TCA">特克斯和凯科斯群岛 TCA</option><option value="图瓦卢 TUV">图瓦卢 TUV</option><option value="乌干达 UGA">乌干达 UGA</option><option value="乌克兰 UKR">乌克兰 UKR</option><option value="阿联酋 ARE">阿联酋 ARE</option><option value="英国 GBR">英国 GBR</option><option value="美国 USA">美国 USA</option><option value="美国本土外小岛屿 UMI">美国本土外小岛屿 UMI</option><option value="乌拉圭 URY">乌拉圭 URY</option><option value="乌兹别克斯坦 UZB">乌兹别克斯坦 UZB</option><option value="瓦努阿图 VUT">瓦努阿图 VUT</option><option value="委内瑞拉 VEN">委内瑞拉 VEN</option><option value="越南 VNM">越南 VNM</option><option value="英属维尔京群岛 VGB">英属维尔京群岛 VGB</option><option value="美属维尔京群岛 VIR">美属维尔京群岛 VIR</option><option value="瓦利斯和富图纳 WLF">瓦利斯和富图纳 WLF</option><option value="西撒哈拉 ESH">西撒哈拉 ESH</option><option value="也门 YEM">也门 YEM</option><option value="赞比亚 ZMB">赞比亚 ZMB</option><option value="津巴布韦 ZWE">津巴布韦 ZWE</option>');
                // 修改二级分类下拉菜单
                $("#two-select").html('<option value="不明NA N/A">不明NA N/A</option><option value="警察、警官、犯罪调查、保护机构 COP">警察、警官、犯罪调查、保护机构 COP</option><option value="政府：执政内阁、执政党、联合政府、行政区 GOV">政府：执政内阁、执政党、联合政府、行政区 GOV</option><option value="造反（反叛者） INS">造反（反叛者） INS</option>  <option value="司法：法官、法庭 JUD">司法：法官、法庭 JUD</option><option value="武装力量 MIL">武装力量 MIL</option><option value="政治反对派 OPP">政治反对派 OPP</option><option value="反叛者 REB">反叛者 REB</option>  <option value="分离主义反叛者 SEP">分离主义反叛者 SEP</option><option value="国家情报机构和成员 SPY">国家情报机构和成员 SPY</option><option value="武装部队（中立） UAF">武装部队（中立） UAF</option><option value="恐怖分子 TER">恐怖分子 TER</option>  <option value="农业：个人，团体，政府机构 AGR">农业：个人，团体，政府机构 AGR</option><option value="商业：商人、公司和企业，不包含MNC BUS">商业：商人、公司和企业，不包含MNC BUS</option><option value="刑事犯罪：个人 CRM">刑事犯罪：个人 CRM</option><option value="不明确的平民个体或团体 CVL">不明确的平民个体或团体 CVL</option>  <option value="发展：发展的个人或团体，如基础设施建设、民主化等 DEV">发展：发展的个人或团体，如基础设施建设、民主化等 DEV</option><option value="教育：教育者、学生、教育机构 EDU">教育：教育者、学生、教育机构 EDU</option><option value="精英：前政府官员、名人、无组织分类发言人 ELI">精英：前政府官员、名人、无组织分类发言人 ELI</option><option value="环境 ENV">环境 ENV</option>  <option value="健康：个人，团体和组织（无国界医生） HLH">健康：个人，团体和组织（无国界医生） HLH</option><option value="人权 HRI">人权 HRI</option><option value="劳工：个人或组织 LAB">劳工：个人或组织 LAB</option>  <option value="立法机构 LEG">立法机构 LEG</option><option value="媒体 MED">媒体 MED</option><option value="难民：也指机构或跨国公司 REF">难民：也指机构或跨国公司 REF</option>  <option value="温和派：“温和”，“主流”等 MOD">温和派：“温和”，“主流”等 MOD</option><option value="激进派：“激进”，极端主义，“原教旨主义”等 RAD">激进派：“激进”，极端主义，“原教旨主义”等 RAD</option>');
            }
            else if (this.value == '受动者') {
                // 修改动词分类下拉菜单
                $("#verbs-classification-select").html('<option value="国家代码">国家代码</option><option value="国际代码">国际代码</option>');
                // 修改一级分类下拉菜单
                $("#one-select").html('<option value="阿富汗 AFG">阿富汗 AFG</option><option value="奥兰群岛 ALA">奥兰群岛 ALA</option><option value="阿尔巴尼亚 ALB">阿尔巴尼亚 ALB</option><option value="阿尔及利亚 DZA">阿尔及利亚 DZA</option><option value="美属萨摩亚 ASM">美属萨摩亚 ASM</option><option value="安道尔 AND">安道尔 AND</option><option value="安哥拉 AGO">安哥拉 AGO</option><option value="安圭拉 AIA">安圭拉 AIA</option><option value="南极洲 ATA">南极洲 ATA</option><option value="安提瓜和巴布达 ATG">安提瓜和巴布达 ATG</option><option value="阿根廷 ARG">阿根廷 ARG</option><option value="亚美尼亚 ARM">亚美尼亚 ARM</option><option value="阿鲁巴 ABW">阿鲁巴 ABW</option><option value="澳大利亚 AUS">澳大利亚 AUS</option><option value="奥地利 AUT">奥地利 AUT</option><option value="阿塞拜疆 AZE">阿塞拜疆 AZE</option><option value="巴哈马 BHS">巴哈马 BHS</option><option value="巴林 BHR">巴林 BHR</option><option value="孟加拉国 BGD">孟加拉国 BGD</option><option value="巴巴多斯 BRB">巴巴多斯 BRB</option><option value="白俄罗斯 BLR">白俄罗斯 BLR</option><option value="比利时 BEL">比利时 BEL</option><option value="伯利兹 BLZ">伯利兹 BLZ</option><option value="贝宁 BEN">贝宁 BEN</option><option value="百慕大 BMU">百慕大 BMU</option><option value="不丹 BTN">不丹 BTN</option><option value="玻利维亚 BOL">玻利维亚 BOL</option><option value="波黑 BIH">波黑 BIH</option><option value="博茨瓦纳 BWA">博茨瓦纳 BWA</option><option value="布维岛 BVT">布维岛 BVT</option><option value="巴西 BRA">巴西 BRA</option><option value="英属印度洋领地 IOT">英属印度洋领地 IOT</option><option value="文莱 BRN">文莱 BRN</option><option value="保加利亚 BGR">保加利亚 BGR</option><option value="布基纳法索 BFA">布基纳法索 BFA</option><option value="布隆迪 BDI">布隆迪 BDI</option><option value="柬埔寨 KHM">柬埔寨 KHM</option><option value="喀麦隆 CMR">喀麦隆 CMR</option><option value="加拿大 CAN">加拿大 CAN</option><option value="佛得角 CPV">佛得角 CPV</option><option value="开曼群岛 CYM">开曼群岛 CYM</option><option value="中非 CAF">中非 CAF</option><option value="乍得 TCD">乍得 TCD</option><option value="智利 CHL">智利 CHL</option><option value="中国 CHN">中国 CHN</option><option value="圣诞岛 CXR">圣诞岛 CXR</option><option value="科科斯（基林）群岛 CCK">科科斯（基林）群岛 CCK</option><option value="哥伦比亚 COL">哥伦比亚 COL</option><option value="科摩罗 COM">科摩罗 COM</option><option value="刚果（布） COG">刚果（布） COG</option><option value="刚果（金） COD">刚果（金） COD</option><option value="库克群岛 COK">库克群岛 COK</option><option value="哥斯达黎加 CRI">哥斯达黎加 CRI</option><option value="科特迪瓦 CIV">科特迪瓦 CIV</option><option value="克罗地亚 HRV">克罗地亚 HRV</option><option value="古巴 CUB">古巴 CUB</option><option value="塞浦路斯 CYP">塞浦路斯 CYP</option><option value="捷克 CZE">捷克 CZE</option><option value="丹麦 DNK">丹麦 DNK</option><option value="吉布提 DJI">吉布提 DJI</option><option value="多米尼克 DMA">多米尼克 DMA</option><option value="多米尼加 DOM">多米尼加 DOM</option><option value="厄瓜多尔 ECU">厄瓜多尔 ECU</option><option value="埃及 EGY">埃及 EGY</option><option value="萨尔瓦多 SLV">萨尔瓦多 SLV</option><option value="赤道几内亚 GNQ">赤道几内亚 GNQ</option><option value="厄立特里亚 ERI">厄立特里亚 ERI</option><option value="爱沙尼亚 EST">爱沙尼亚 EST</option><option value="埃塞俄比亚 ETH">埃塞俄比亚 ETH</option><option value="福克兰群岛（马尔维纳斯） FLK">福克兰群岛（马尔维纳斯） FLK</option><option value="法罗群岛 FRO">法罗群岛 FRO</option><option value="斐济 FJI">斐济 FJI</option><option value="芬兰 FIN">芬兰 FIN</option><option value="法国 FRA">法国 FRA</option><option value="法属圭亚那 GUF">法属圭亚那 GUF</option><option value="法属波利尼西亚 PYF">法属波利尼西亚 PYF</option><option value="法属南部领地 ATF">法属南部领地 ATF</option><option value="加蓬 GAB">加蓬 GAB</option><option value="冈比亚 GMB">冈比亚 GMB</option><option value="格鲁吉亚 GEO">格鲁吉亚 GEO</option><option value="德国 DEU">德国 DEU</option><option value="加纳 GHA">加纳 GHA</option><option value="直布罗陀 GIB">直布罗陀 GIB</option><option value="希腊 GRC">希腊 GRC</option><option value="格陵兰 GRL">格陵兰 GRL</option><option value="格林纳达 GRD">格林纳达 GRD</option><option value="瓜德罗普 GLP">瓜德罗普 GLP</option><option value="关岛 GUM">关岛 GUM</option><option value="危地马拉 GTM">危地马拉 GTM</option><option value="格恩西岛 GGY">格恩西岛 GGY</option><option value="几内亚 GIN">几内亚 GIN</option><option value="几内亚比绍 GNB">几内亚比绍 GNB</option><option value="圭亚那 GUY">圭亚那 GUY</option><option value="海地 HTI">海地 HTI</option><option value="赫德岛和麦克唐纳岛 HMD">赫德岛和麦克唐纳岛 HMD</option><option value="梵蒂冈 VAT">梵蒂冈 VAT</option><option value="洪都拉斯 HND">洪都拉斯 HND</option><option value="中国香港 HKG">中国香港 HKG</option><option value="匈牙利 HUN">匈牙利 HUN</option><option value="冰岛 ISL">冰岛 ISL</option><option value="印度 IND">印度 IND</option><option value="印度尼西亚 IDN">印度尼西亚 IDN</option><option value="伊朗 IRN">伊朗 IRN</option><option value="伊拉克 IRQ">伊拉克 IRQ</option><option value="爱尔兰 IRL">爱尔兰 IRL</option><option value="英国属地曼岛 IMN">英国属地曼岛 IMN</option><option value="以色列 ISR">以色列 ISR</option><option value="意大利 ITA">意大利 ITA</option><option value="牙买加 JAM">牙买加 JAM</option><option value="日本 JPN">日本 JPN</option><option value="泽西岛 JEY">泽西岛 JEY</option><option value="约旦 JOR">约旦 JOR</option><option value="哈萨克斯坦 KAZ">哈萨克斯坦 KAZ</option><option value="肯尼亚 KEN">肯尼亚 KEN</option><option value="基里巴斯 KIR">基里巴斯 KIR</option><option value="朝鲜 PRK">朝鲜 PRK</option><option value="韩国 KOR">韩国 KOR</option><option value="科威特 KWT">科威特 KWT</option><option value="吉尔吉斯斯坦 KGZ">吉尔吉斯斯坦 KGZ</option><option value="老挝 LAO">老挝 LAO</option><option value="拉脱维亚 LVA">拉脱维亚 LVA</option><option value="黎巴嫩 LBN">黎巴嫩 LBN</option><option value="莱索托 LSO">莱索托 LSO</option><option value="利比里亚 LBR">利比里亚 LBR</option><option value="利比亚 LBY">利比亚 LBY</option><option value="列支敦士登 LIE">列士敦士登 LIE</option><option value="立陶宛 LTU">立陶宛 LTU</option><option value="卢森堡 LUX">卢森堡 LUX</option><option value="中国澳门 MAC">中国澳门 MAC</option><option value="前南马其顿 MKD">前南马其顿 MKD</option><option value="马达加斯加 MDG">马达加斯加 MDG</option><option value="马拉维 MWI">马拉维 MWI</option><option value="马来西亚 MYS">马来西亚 MYS</option><option value="马尔代夫 MDV">马尔代夫 MDV</option><option value="马里 MLI">马里 MLI</option><option value="马其他 MLT">马其他 MLT</option><option value="马绍尔群岛 MHL">马绍尔群岛 MHL</option><option value="马提尼克 MTQ">马提尼克 MTQ</option><option value="毛利塔尼亚 MRT">毛利塔尼亚 MRT</option><option value="毛里求斯 MUS">毛里求斯 MUS</option><option value="马约特 MYT">马约特 MYT</option><option value="墨西哥 MEX">墨西哥 MEX</option><option value="密克罗尼西亚联邦 FSM">密克罗尼西亚联邦 FSM</option><option value="摩尔多瓦 MDA">摩尔多瓦 MDA</option><option value="摩纳哥 MCO">摩纳哥 MCO</option><option value="蒙古 MNG">蒙古 MNG</option><option value="黑山 MNE">黑山 MNE</option><option value="蒙特塞拉特 MSR">蒙特塞拉特 MSR</option><option value="摩洛哥 MAR">摩洛哥 MAR</option><option value="莫桑比克 MOZ">莫桑比克 MOZ</option><option value="缅甸 MMR">缅甸 MMR</option><option value="纳米比亚 NAM">纳米比亚 NAM</option><option value="瑙鲁 NRU">瑙鲁 NRU</option><option value="尼泊尔 NPL">尼泊尔 NPL</option><option value="荷兰 NLD">荷兰 NLD</option><option value="荷属安的列斯 ANT">荷属安的列斯 ANT</option><option value="新喀里多尼亚 NCL">新喀里多尼亚 NCL</option><option value="新西兰 NZL">新西兰 NZL</option><option value="尼加拉瓜 NIC">尼加拉瓜 NIC</option><option value="尼日尔 NER">尼日尔 NER</option><option value="尼日利亚 NGA">尼日利亚 NGA</option><option value="纽埃 NIU">纽埃 NIU</option><option value="诺福克岛 NFK">诺福克岛 NFK</option><option value="北马里亚纳 MNP">北马里亚纳 MNP</option><option value="挪威 NOR">挪威 NOR</option><option value="阿曼 OMN">阿曼 OMN</option>	<option value="巴基斯坦 PAK">巴基斯坦 PAK</option><option value="帕劳 PLW">帕劳 PLW</option><option value="巴勒斯坦 PSE">巴勒斯坦 PSE</option><option value="巴拿马 PAN">巴拿马 PAN</option>	<option value="巴布亚新几内亚 PNG">巴布亚新几内亚 PNG</option><option value="巴拉圭 PRY">巴拉圭 PRY</option><option value="秘鲁 PER">秘鲁 PER</option><option value="菲律宾 PHL">菲律宾 PHL</option>	<option value="皮特凯恩 PCN">皮特凯恩 PCN</option><option value="波兰 POL">波兰 POL</option><option value="葡萄牙 PRT">葡萄牙 PRT</option><option value="波多黎各 PRI">波多黎各 PRI</option>	<option value="卡塔尔 QAT">卡塔尔 QAT</option><option value="留尼汪 REU">留尼汪 REU</option><option value="罗马尼亚 ROU">罗马尼亚 ROU</option><option value="俄罗斯联邦 RUS">俄罗斯联邦 RUS</option>	<option value="卢旺达 RWA">卢旺达 RWA</option><option value="圣赫勒拿 SHN">圣赫勒拿 SHN</option><option value="圣基茨和尼维斯 KNA">圣基茨和尼维斯 KNA</option><option value="圣卢西亚 LCA">圣卢西亚 LCA</option>	<option value="圣皮埃尔和密克隆 SPM">圣皮埃尔和密克隆 SPM</option><option value="圣文森特和格林纳丁斯 VCT">圣文森特和格林纳丁斯 VCT</option><option value="萨摩亚 WSM">萨摩亚 WSM</option><option value="圣马力诺 SMR">圣马力诺 SMR</option>	<option value="圣多美和普林西比 STP">圣多美和普林西比 STP</option><option value="沙特阿拉伯 SAU">沙特阿拉伯 SAU</option><option value="塞内加尔 SEN">塞内加尔 SEN</option><option value="塞尔维亚 SRB">塞尔维亚 SRB</option>	<option value="塞舌尔 SYC">塞舌尔 SYC</option><option value="塞拉利昂 SLE">塞拉利昂 SLE</option><option value="新加坡 SGP">新加坡 SGP</option><option value="斯洛伐克 SVK">斯洛伐克 SVK</option>	<option value="斯洛文尼亚 SVN">斯洛文尼亚 SVN</option><option value="所罗门群岛 SLB">所罗门群岛 SLB</option><option value="索马里 SOM">索马里 SOM</option><option value="南非 ZAF">南非 ZAF</option>	<option value="南乔治亚岛和南桑德韦奇岛 SGS">南乔治亚岛和南桑德韦奇岛 SGS</option><option value="西班牙 ESP">西班牙 ESP</option><option value="斯里兰卡 LKA">斯里兰卡 LKA</option><option value="苏丹 SDN">苏丹 SDN</option>	<option value="苏里南 SUR">苏里南 SUR</option><option value="斯瓦尔巴岛和扬马延岛 SJM">斯瓦尔巴岛和扬马延岛 SJM</option><option value="斯威士兰 SWZ">斯威士兰 SWZ</option><option value="瑞典 SWE">瑞典 SWE</option>	<option value="瑞士 CHE">瑞士 CHE</option><option value="叙利亚 SYR">叙利亚 SYR</option><option value="中国台湾 TWN">中国台湾 TWN</option><option value="塔吉克斯坦 TJK">塔吉克斯坦 TJK</option>	<option value="坦桑尼亚 TZA">坦桑尼亚 TZA</option><option value="泰国 THA">泰国 THA</option><option value="东帝汶 TLS">东帝汶 TLS</option><option value="多哥 TGO">多哥 TGO</option>	<option value="托克劳 TKL">托克劳 TKL</option><option value="汤加 TON">汤加 TON</option><option value="特立尼达和多巴哥 TTO">特立尼达和多巴哥 TTO</option><option value="突尼斯 TUN">突尼斯 TUN</option>	<option value="土耳其 TUR">土耳其 TUR</option><option value="土库曼斯坦 TKM">土库曼斯坦 TKM</option><option value="特克斯和凯科斯群岛 TCA">特克斯和凯科斯群岛 TCA</option><option value="图瓦卢 TUV">图瓦卢 TUV</option><option value="乌干达 UGA">乌干达 UGA</option><option value="乌克兰 UKR">乌克兰 UKR</option><option value="阿联酋 ARE">阿联酋 ARE</option><option value="英国 GBR">英国 GBR</option><option value="美国 USA">美国 USA</option><option value="美国本土外小岛屿 UMI">美国本土外小岛屿 UMI</option><option value="乌拉圭 URY">乌拉圭 URY</option><option value="乌兹别克斯坦 UZB">乌兹别克斯坦 UZB</option><option value="瓦努阿图 VUT">瓦努阿图 VUT</option><option value="委内瑞拉 VEN">委内瑞拉 VEN</option><option value="越南 VNM">越南 VNM</option><option value="英属维尔京群岛 VGB">英属维尔京群岛 VGB</option><option value="美属维尔京群岛 VIR">美属维尔京群岛 VIR</option><option value="瓦利斯和富图纳 WLF">瓦利斯和富图纳 WLF</option><option value="西撒哈拉 ESH">西撒哈拉 ESH</option><option value="也门 YEM">也门 YEM</option><option value="赞比亚 ZMB">赞比亚 ZMB</option><option value="津巴布韦 ZWE">津巴布韦 ZWE</option>');
                // 修改二级分类下拉菜单
                $("#two-select").html('<option value="不明NA N/A">不明NA N/A</option><option value="警察、警官、犯罪调查、保护机构 COP">警察、警官、犯罪调查、保护机构 COP</option><option value="政府：执政内阁、执政党、联合政府、行政区 GOV">政府：执政内阁、执政党、联合政府、行政区 GOV</option><option value="造反（反叛者） INS">造反（反叛者） INS</option>                                    <option value="司法：法官、法庭 JUD">司法：法官、法庭 JUD</option><option value="武装力量 MIL">武装力量 MIL</option><option value="政治反对派 OPP">政治反对派 OPP</option><option value="反叛者 REB">反叛者 REB</option>                                    <option value="分离主义反叛者 SEP">分离主义反叛者 SEP</option><option value="国家情报机构和成员 SPY">国家情报机构和成员 SPY</option><option value="武装部队（中立） UAF">武装部队（中立） UAF</option><option value="恐怖分子 TER">恐怖分子 TER</option>                                    <option value="农业：个人，团体，政府机构 AGR">农业：个人，团体，政府机构 AGR</option><option value="商业：商人、公司和企业，不包含MNC BUS">商业：商人、公司和企业，不包含MNC BUS</option><option value="刑事犯罪：个人 CRM">刑事犯罪：个人 CRM</option><option value="不明确的平民个体或团体 CVL">不明确的平民个体或团体 CVL</option>                                    <option value="发展：发展的个人或团体，如基础设施建设、民主化等 DEV">发展：发展的个人或团体，如基础设施建设、民主化等 DEV</option><option value="教育：教育者、学生、教育机构 EDU">教育：教育者、学生、教育机构 EDU</option><option value="精英：前政府官员、名人、无组织分类发言人 ELI">精英：前政府官员、名人、无组织分类发言人 ELI</option><option value="环境 ENV">环境 ENV</option>                                    <option value="健康：个人，团体和组织（无国界医生） HLH">健康：个人，团体和组织（无国界医生） HLH</option><option value="人权 HRI">人权 HRI</option><option value="劳工：个人或组织 LAB">劳工：个人或组织 LAB</option>                                    <option value="立法机构 LEG">立法机构 LEG</option><option value="媒体 MED">媒体 MED</option><option value="难民：也指机构或跨国公司 REF">难民：也指机构或跨国公司 REF</option>                                    <option value="温和派：“温和”，“主流”等 MOD">温和派：“温和”，“主流”等 MOD</option><option value="激进派：“激进”，极端主义，“原教旨主义”等 RAD">激进派：“激进”，极端主义，“原教旨主义”等 RAD</option>');
            }
        });
        // 第二级下拉菜单：动词分类
        $('#verbs-classification-select').on('change', function() {
            if (this.value == '口头合作') {
                $("#one-select").html('<option value="公开声明">公开声明</option><option value="呼吁">呼吁</option><option value="合作意愿">合作意愿</option><option value="要求">要求</option>');
            }
            else if (this.value == '实际合作') {
                $("#one-select").html('<option value="磋商">磋商</option><option value="外交合作">外交合作</option><option value="实质合作">实质合作</option><option value="援助">援助</option><option value="调查">调查</option><option value="政策">政策</option>');
            }
            else if (this.value == '口头敌对') {
                $("#one-select").html('<option value="反对">反对</option><option value="拒绝">拒绝</option><option value="威胁">威胁</option><option value="援助">援助</option><option value="调查">调查</option><option value="政策">政策</option>');
            }
            else if (this.value == '实际敌对') {
                $("#one-select").html('<option value="抗议">抗议</option><option value="军事展示">军事展示</option><option value="降低关系">降低关系</option><option value="强迫">强迫</option><option value="袭击">袭击</option><option value="战斗">战斗</option><option value="非常规大规模暴力">非常规大规模暴力</option>');
            }
            else if (this.value == '国家代码') {
                $("#one-select").html('<option value="阿富汗 AFG">阿富汗 AFG</option><option value="奥兰群岛 ALA">奥兰群岛 ALA</option><option value="阿尔巴尼亚 ALB">阿尔巴尼亚 ALB</option><option value="阿尔及利亚 DZA">阿尔及利亚 DZA</option><option value="美属萨摩亚 ASM">美属萨摩亚 ASM</option><option value="安道尔 AND">安道尔 AND</option><option value="安哥拉 AGO">安哥拉 AGO</option><option value="安圭拉 AIA">安圭拉 AIA</option><option value="南极洲 ATA">南极洲 ATA</option><option value="安提瓜和巴布达 ATG">安提瓜和巴布达 ATG</option><option value="阿根廷 ARG">阿根廷 ARG</option><option value="亚美尼亚 ARM">亚美尼亚 ARM</option><option value="阿鲁巴 ABW">阿鲁巴 ABW</option><option value="澳大利亚 AUS">澳大利亚 AUS</option><option value="奥地利 AUT">奥地利 AUT</option><option value="阿塞拜疆 AZE">阿塞拜疆 AZE</option><option value="巴哈马 BHS">巴哈马 BHS</option><option value="巴林 BHR">巴林 BHR</option><option value="孟加拉国 BGD">孟加拉国 BGD</option><option value="巴巴多斯 BRB">巴巴多斯 BRB</option><option value="白俄罗斯 BLR">白俄罗斯 BLR</option><option value="比利时 BEL">比利时 BEL</option><option value="伯利兹 BLZ">伯利兹 BLZ</option><option value="贝宁 BEN">贝宁 BEN</option><option value="百慕大 BMU">百慕大 BMU</option><option value="不丹 BTN">不丹 BTN</option><option value="玻利维亚 BOL">玻利维亚 BOL</option><option value="波黑 BIH">波黑 BIH</option><option value="博茨瓦纳 BWA">博茨瓦纳 BWA</option><option value="布维岛 BVT">布维岛 BVT</option><option value="巴西 BRA">巴西 BRA</option><option value="英属印度洋领地 IOT">英属印度洋领地 IOT</option><option value="文莱 BRN">文莱 BRN</option><option value="保加利亚 BGR">保加利亚 BGR</option><option value="布基纳法索 BFA">布基纳法索 BFA</option><option value="布隆迪 BDI">布隆迪 BDI</option><option value="柬埔寨 KHM">柬埔寨 KHM</option><option value="喀麦隆 CMR">喀麦隆 CMR</option><option value="加拿大 CAN">加拿大 CAN</option><option value="佛得角 CPV">佛得角 CPV</option><option value="开曼群岛 CYM">开曼群岛 CYM</option><option value="中非 CAF">中非 CAF</option><option value="乍得 TCD">乍得 TCD</option><option value="智利 CHL">智利 CHL</option><option value="中国 CHN">中国 CHN</option><option value="圣诞岛 CXR">圣诞岛 CXR</option><option value="科科斯（基林）群岛 CCK">科科斯（基林）群岛 CCK</option><option value="哥伦比亚 COL">哥伦比亚 COL</option><option value="科摩罗 COM">科摩罗 COM</option><option value="刚果（布） COG">刚果（布） COG</option><option value="刚果（金） COD">刚果（金） COD</option><option value="库克群岛 COK">库克群岛 COK</option><option value="哥斯达黎加 CRI">哥斯达黎加 CRI</option><option value="科特迪瓦 CIV">科特迪瓦 CIV</option><option value="克罗地亚 HRV">克罗地亚 HRV</option><option value="古巴 CUB">古巴 CUB</option><option value="塞浦路斯 CYP">塞浦路斯 CYP</option><option value="捷克 CZE">捷克 CZE</option><option value="丹麦 DNK">丹麦 DNK</option><option value="吉布提 DJI">吉布提 DJI</option><option value="多米尼克 DMA">多米尼克 DMA</option><option value="多米尼加 DOM">多米尼加 DOM</option><option value="厄瓜多尔 ECU">厄瓜多尔 ECU</option><option value="埃及 EGY">埃及 EGY</option><option value="萨尔瓦多 SLV">萨尔瓦多 SLV</option><option value="赤道几内亚 GNQ">赤道几内亚 GNQ</option><option value="厄立特里亚 ERI">厄立特里亚 ERI</option><option value="爱沙尼亚 EST">爱沙尼亚 EST</option><option value="埃塞俄比亚 ETH">埃塞俄比亚 ETH</option><option value="福克兰群岛（马尔维纳斯） FLK">福克兰群岛（马尔维纳斯） FLK</option><option value="法罗群岛 FRO">法罗群岛 FRO</option><option value="斐济 FJI">斐济 FJI</option><option value="芬兰 FIN">芬兰 FIN</option><option value="法国 FRA">法国 FRA</option><option value="法属圭亚那 GUF">法属圭亚那 GUF</option><option value="法属波利尼西亚 PYF">法属波利尼西亚 PYF</option><option value="法属南部领地 ATF">法属南部领地 ATF</option><option value="加蓬 GAB">加蓬 GAB</option><option value="冈比亚 GMB">冈比亚 GMB</option><option value="格鲁吉亚 GEO">格鲁吉亚 GEO</option><option value="德国 DEU">德国 DEU</option><option value="加纳 GHA">加纳 GHA</option><option value="直布罗陀 GIB">直布罗陀 GIB</option><option value="希腊 GRC">希腊 GRC</option><option value="格陵兰 GRL">格陵兰 GRL</option><option value="格林纳达 GRD">格林纳达 GRD</option><option value="瓜德罗普 GLP">瓜德罗普 GLP</option><option value="关岛 GUM">关岛 GUM</option><option value="危地马拉 GTM">危地马拉 GTM</option><option value="格恩西岛 GGY">格恩西岛 GGY</option><option value="几内亚 GIN">几内亚 GIN</option><option value="几内亚比绍 GNB">几内亚比绍 GNB</option><option value="圭亚那 GUY">圭亚那 GUY</option><option value="海地 HTI">海地 HTI</option><option value="赫德岛和麦克唐纳岛 HMD">赫德岛和麦克唐纳岛 HMD</option><option value="梵蒂冈 VAT">梵蒂冈 VAT</option><option value="洪都拉斯 HND">洪都拉斯 HND</option><option value="中国香港 HKG">中国香港 HKG</option><option value="匈牙利 HUN">匈牙利 HUN</option><option value="冰岛 ISL">冰岛 ISL</option><option value="印度 IND">印度 IND</option><option value="印度尼西亚 IDN">印度尼西亚 IDN</option><option value="伊朗 IRN">伊朗 IRN</option><option value="伊拉克 IRQ">伊拉克 IRQ</option><option value="爱尔兰 IRL">爱尔兰 IRL</option><option value="英国属地曼岛 IMN">英国属地曼岛 IMN</option><option value="以色列 ISR">以色列 ISR</option><option value="意大利 ITA">意大利 ITA</option><option value="牙买加 JAM">牙买加 JAM</option><option value="日本 JPN">日本 JPN</option><option value="泽西岛 JEY">泽西岛 JEY</option><option value="约旦 JOR">约旦 JOR</option><option value="哈萨克斯坦 KAZ">哈萨克斯坦 KAZ</option><option value="肯尼亚 KEN">肯尼亚 KEN</option><option value="基里巴斯 KIR">基里巴斯 KIR</option><option value="朝鲜 PRK">朝鲜 PRK</option><option value="韩国 KOR">韩国 KOR</option><option value="科威特 KWT">科威特 KWT</option><option value="吉尔吉斯斯坦 KGZ">吉尔吉斯斯坦 KGZ</option><option value="老挝 LAO">老挝 LAO</option><option value="拉脱维亚 LVA">拉脱维亚 LVA</option><option value="黎巴嫩 LBN">黎巴嫩 LBN</option><option value="莱索托 LSO">莱索托 LSO</option><option value="利比里亚 LBR">利比里亚 LBR</option><option value="利比亚 LBY">利比亚 LBY</option><option value="列支敦士登 LIE">列士敦士登 LIE</option><option value="立陶宛 LTU">立陶宛 LTU</option><option value="卢森堡 LUX">卢森堡 LUX</option><option value="中国澳门 MAC">中国澳门 MAC</option><option value="前南马其顿 MKD">前南马其顿 MKD</option><option value="马达加斯加 MDG">马达加斯加 MDG</option><option value="马拉维 MWI">马拉维 MWI</option><option value="马来西亚 MYS">马来西亚 MYS</option><option value="马尔代夫 MDV">马尔代夫 MDV</option><option value="马里 MLI">马里 MLI</option><option value="马其他 MLT">马其他 MLT</option><option value="马绍尔群岛 MHL">马绍尔群岛 MHL</option><option value="马提尼克 MTQ">马提尼克 MTQ</option><option value="毛利塔尼亚 MRT">毛利塔尼亚 MRT</option><option value="毛里求斯 MUS">毛里求斯 MUS</option><option value="马约特 MYT">马约特 MYT</option><option value="墨西哥 MEX">墨西哥 MEX</option><option value="密克罗尼西亚联邦 FSM">密克罗尼西亚联邦 FSM</option><option value="摩尔多瓦 MDA">摩尔多瓦 MDA</option><option value="摩纳哥 MCO">摩纳哥 MCO</option><option value="蒙古 MNG">蒙古 MNG</option><option value="黑山 MNE">黑山 MNE</option><option value="蒙特塞拉特 MSR">蒙特塞拉特 MSR</option><option value="摩洛哥 MAR">摩洛哥 MAR</option><option value="莫桑比克 MOZ">莫桑比克 MOZ</option><option value="缅甸 MMR">缅甸 MMR</option><option value="纳米比亚 NAM">纳米比亚 NAM</option><option value="瑙鲁 NRU">瑙鲁 NRU</option><option value="尼泊尔 NPL">尼泊尔 NPL</option><option value="荷兰 NLD">荷兰 NLD</option><option value="荷属安的列斯 ANT">荷属安的列斯 ANT</option><option value="新喀里多尼亚 NCL">新喀里多尼亚 NCL</option><option value="新西兰 NZL">新西兰 NZL</option><option value="尼加拉瓜 NIC">尼加拉瓜 NIC</option><option value="尼日尔 NER">尼日尔 NER</option><option value="尼日利亚 NGA">尼日利亚 NGA</option><option value="纽埃 NIU">纽埃 NIU</option><option value="诺福克岛 NFK">诺福克岛 NFK</option><option value="北马里亚纳 MNP">北马里亚纳 MNP</option><option value="挪威 NOR">挪威 NOR</option><option value="阿曼 OMN">阿曼 OMN</option>	<option value="巴基斯坦 PAK">巴基斯坦 PAK</option><option value="帕劳 PLW">帕劳 PLW</option><option value="巴勒斯坦 PSE">巴勒斯坦 PSE</option><option value="巴拿马 PAN">巴拿马 PAN</option>	<option value="巴布亚新几内亚 PNG">巴布亚新几内亚 PNG</option><option value="巴拉圭 PRY">巴拉圭 PRY</option><option value="秘鲁 PER">秘鲁 PER</option><option value="菲律宾 PHL">菲律宾 PHL</option>	<option value="皮特凯恩 PCN">皮特凯恩 PCN</option><option value="波兰 POL">波兰 POL</option><option value="葡萄牙 PRT">葡萄牙 PRT</option><option value="波多黎各 PRI">波多黎各 PRI</option>	<option value="卡塔尔 QAT">卡塔尔 QAT</option><option value="留尼汪 REU">留尼汪 REU</option><option value="罗马尼亚 ROU">罗马尼亚 ROU</option><option value="俄罗斯联邦 RUS">俄罗斯联邦 RUS</option>	<option value="卢旺达 RWA">卢旺达 RWA</option><option value="圣赫勒拿 SHN">圣赫勒拿 SHN</option><option value="圣基茨和尼维斯 KNA">圣基茨和尼维斯 KNA</option><option value="圣卢西亚 LCA">圣卢西亚 LCA</option>	<option value="圣皮埃尔和密克隆 SPM">圣皮埃尔和密克隆 SPM</option><option value="圣文森特和格林纳丁斯 VCT">圣文森特和格林纳丁斯 VCT</option><option value="萨摩亚 WSM">萨摩亚 WSM</option><option value="圣马力诺 SMR">圣马力诺 SMR</option>	<option value="圣多美和普林西比 STP">圣多美和普林西比 STP</option><option value="沙特阿拉伯 SAU">沙特阿拉伯 SAU</option><option value="塞内加尔 SEN">塞内加尔 SEN</option><option value="塞尔维亚 SRB">塞尔维亚 SRB</option>	<option value="塞舌尔 SYC">塞舌尔 SYC</option><option value="塞拉利昂 SLE">塞拉利昂 SLE</option><option value="新加坡 SGP">新加坡 SGP</option><option value="斯洛伐克 SVK">斯洛伐克 SVK</option>	<option value="斯洛文尼亚 SVN">斯洛文尼亚 SVN</option><option value="所罗门群岛 SLB">所罗门群岛 SLB</option><option value="索马里 SOM">索马里 SOM</option><option value="南非 ZAF">南非 ZAF</option>	<option value="南乔治亚岛和南桑德韦奇岛 SGS">南乔治亚岛和南桑德韦奇岛 SGS</option><option value="西班牙 ESP">西班牙 ESP</option><option value="斯里兰卡 LKA">斯里兰卡 LKA</option><option value="苏丹 SDN">苏丹 SDN</option>	<option value="苏里南 SUR">苏里南 SUR</option><option value="斯瓦尔巴岛和扬马延岛 SJM">斯瓦尔巴岛和扬马延岛 SJM</option><option value="斯威士兰 SWZ">斯威士兰 SWZ</option><option value="瑞典 SWE">瑞典 SWE</option>	<option value="瑞士 CHE">瑞士 CHE</option><option value="叙利亚 SYR">叙利亚 SYR</option><option value="中国台湾 TWN">中国台湾 TWN</option><option value="塔吉克斯坦 TJK">塔吉克斯坦 TJK</option>	<option value="坦桑尼亚 TZA">坦桑尼亚 TZA</option><option value="泰国 THA">泰国 THA</option><option value="东帝汶 TLS">东帝汶 TLS</option><option value="多哥 TGO">多哥 TGO</option>	<option value="托克劳 TKL">托克劳 TKL</option><option value="汤加 TON">汤加 TON</option><option value="特立尼达和多巴哥 TTO">特立尼达和多巴哥 TTO</option><option value="突尼斯 TUN">突尼斯 TUN</option>	<option value="土耳其 TUR">土耳其 TUR</option><option value="土库曼斯坦 TKM">土库曼斯坦 TKM</option><option value="特克斯和凯科斯群岛 TCA">特克斯和凯科斯群岛 TCA</option><option value="图瓦卢 TUV">图瓦卢 TUV</option><option value="乌干达 UGA">乌干达 UGA</option><option value="乌克兰 UKR">乌克兰 UKR</option><option value="阿联酋 ARE">阿联酋 ARE</option><option value="英国 GBR">英国 GBR</option><option value="美国 USA">美国 USA</option><option value="美国本土外小岛屿 UMI">美国本土外小岛屿 UMI</option><option value="乌拉圭 URY">乌拉圭 URY</option><option value="乌兹别克斯坦 UZB">乌兹别克斯坦 UZB</option><option value="瓦努阿图 VUT">瓦努阿图 VUT</option><option value="委内瑞拉 VEN">委内瑞拉 VEN</option><option value="越南 VNM">越南 VNM</option><option value="英属维尔京群岛 VGB">英属维尔京群岛 VGB</option><option value="美属维尔京群岛 VIR">美属维尔京群岛 VIR</option><option value="瓦利斯和富图纳 WLF">瓦利斯和富图纳 WLF</option><option value="西撒哈拉 ESH">西撒哈拉 ESH</option><option value="也门 YEM">也门 YEM</option><option value="赞比亚 ZMB">赞比亚 ZMB</option><option value="津巴布韦 ZWE">津巴布韦 ZWE</option>');
                $("#two-select").html('<option value="不明NA N/A">不明NA N/A</option><option value="警察、警官、犯罪调查、保护机构 COP">警察、警官、犯罪调查、保护机构 COP</option><option value="政府：执政内阁、执政党、联合政府、行政区 GOV">政府：执政内阁、执政党、联合政府、行政区 GOV</option><option value="造反（反叛者） INS">造反（反叛者） INS</option>  <option value="司法：法官、法庭 JUD">司法：法官、法庭 JUD</option><option value="武装力量 MIL">武装力量 MIL</option><option value="政治反对派 OPP">政治反对派 OPP</option><option value="反叛者 REB">反叛者 REB</option>  <option value="分离主义反叛者 SEP">分离主义反叛者 SEP</option><option value="国家情报机构和成员 SPY">国家情报机构和成员 SPY</option><option value="武装部队（中立） UAF">武装部队（中立） UAF</option><option value="恐怖分子 TER">恐怖分子 TER</option>  <option value="农业：个人，团体，政府机构 AGR">农业：个人，团体，政府机构 AGR</option><option value="商业：商人、公司和企业，不包含MNC BUS">商业：商人、公司和企业，不包含MNC BUS</option><option value="刑事犯罪：个人 CRM">刑事犯罪：个人 CRM</option><option value="不明确的平民个体或团体 CVL">不明确的平民个体或团体 CVL</option>  <option value="发展：发展的个人或团体，如基础设施建设、民主化等 DEV">发展：发展的个人或团体，如基础设施建设、民主化等 DEV</option><option value="教育：教育者、学生、教育机构 EDU">教育：教育者、学生、教育机构 EDU</option><option value="精英：前政府官员、名人、无组织分类发言人 ELI">精英：前政府官员、名人、无组织分类发言人 ELI</option><option value="环境 ENV">环境 ENV</option>  <option value="健康：个人，团体和组织（无国界医生） HLH">健康：个人，团体和组织（无国界医生） HLH</option><option value="人权 HRI">人权 HRI</option><option value="劳工：个人或组织 LAB">劳工：个人或组织 LAB</option>  <option value="立法机构 LEG">立法机构 LEG</option><option value="媒体 MED">媒体 MED</option><option value="难民：也指机构或跨国公司 REF">难民：也指机构或跨国公司 REF</option>  <option value="温和派：“温和”，“主流”等 MOD">温和派：“温和”，“主流”等 MOD</option><option value="激进派：“激进”，极端主义，“原教旨主义”等 RAD">激进派：“激进”，极端主义，“原教旨主义”等 RAD</option>');
            }
            else if (this.value == '国际代码') {
                $("#one-select").html('<option value="不明（NA） N/A">不明（NA） N/A</option><option value="国际或区域的政府间组织 IGO">国际或区域的政府间组织 IGO</option><option value="国际或区域的军事集团 IMG">国际或区域的军事集团 IMG</option><option value="非IGO,UIS,NGO,NGM,or MNC国际参与者 INT">非IGO,UIS,NGO,NGM,or MNC国际参与者 INT</option>                                    <option value="跨国企业 MNC">跨国企业 MNC</option><option value="非政府运动 NGM">非政府运动 NGM</option><option value="非政府组织 NGO">非政府组织 NGO</option><option value="不明国家参与者 UIS">不明国家参与者 UIS/option>                                    <option value="非洲开发银行 IGOAFB">非洲开发银行 IGOAFB</option><option value="非洲经济发展阿拉伯银行 IGOABD">非洲经济发展阿拉伯银行 IGOABD</option><option value="中非国家银行 IGOBCA">中非国家银行 IGOBCA</option><option value="东非共同市场 IGOCEM">东非共同市场 IGOCEM</option>                                    <option value="萨赫勒-撒哈拉国家共同体 IGOCSS">萨赫勒-撒哈拉国家共同体 IGOCSS</option><option value="东部和南部非洲贸易发展银行 IGOATD">东部和南部非洲贸易发展银行 IGOATD</option><option value="西非经济和货币联盟 IGOUEM">西非经济和货币联盟 IGOUEM</option><option value="中非经济共同体 IGOECA">中非经济共同体 IGOECA</option>                                    <option value="西非国家经济共同体 IGOWAS">西非国家经济共同体 IGOWAS</option><option value="非洲法郎区金融共同体 IGOCFA">非洲法郎区金融共同体 IGOCFA</option><option value="非洲国家咖啡组织 IGOIAC">非洲国家咖啡组织 IGOIAC</option><option value="政府间发展管理局 IGOIAD">政府间发展管理局 IGOIAD</option>                                    <option value="中非货币经济共同体 IGOCEM">中非货币经济共同体 IGOCEM</option><option value="非洲发展新经济伙伴关系 IGONEP">非洲发展新经济伙伴关系 IGONEP</option><option value="非洲团结组织 IGOOAU">非洲团结组织 IGOOAU</option><option value="泛非会议 IGOPAP">泛非会议 IGOPAP</option>                                    <option value="南非发展共同体 IGOSAD">南非发展共同体 IGOSAD</option><option value="西非开发银行 IGOWAD">西非开发银行 IGOWAD</option><option value="西非货币经济联盟 IGOWAM">西非货币经济联盟 IGOWAM/option><option value="阿拉伯合作委员会 IGOACC">阿拉伯合作委员会 IGOACC</option>                                    <option value="阿拉伯经济团结理事会 IGOAEU">阿拉伯经济团结理事会 IGOAEU</option><option value="阿拉伯国家联盟 IGOARL">阿拉伯国家联盟 IGOARL</option><option value="阿拉伯马格里布联盟 IGOAMU">阿拉伯马格里布联盟 IGOAMU</option><option value="阿拉伯经济和社会发展货币基金 IGOAMF">阿拉伯经济和社会发展货币基金 IGOAMF</option>                                    <option value="海湾合作委员会 IGOGCC">海湾合作委员会 IGOGCC</option><option value="阿拉伯石油输出国组织 IGOAPE">阿拉伯石油输出国组织 IGOAPE</option><option value="亚洲开发银行 IGOADB">亚洲开发银行 IGOADB</option><option value="东南亚国家联盟 IGOASN">东南亚国家联盟 IGOASN</option>                                    <option value="独联体 IGOCIS">独联体 IGOCIS</option><option value="欧洲委员会 IGOCOE">欧洲委员会 IGOCOE</option><option value="欧洲安全合作委员会 IGOSCE">欧洲安全合作委员会 IGOSCE</option><option value="欧洲重建与发展银行 IGOEBR">欧洲重建与发展银行 IGOEBR</option>                                    <option value="欧洲自由贸易联盟 IGOEFT">欧洲自由贸易联盟 IGOEFT</option><option value="欧盟 IGOEEC">欧盟 IGOEEC</option><option value="南亚联盟 IGOSAA">南亚联盟 IGOSAA</option><option value="东南亚共同防御公约组织 IGOSOT">东南亚共同防御公约组织 IGOSOT</option>                                    <option value="大赦国际 NGOAMN">大赦国际 NGOAMN</option><option value="咖啡产国协会 IGOCPC">咖啡产国协会 IGOCPC</option><option value="国际结算银行 IGOBIS">国际结算银行 IGOBIS</option><option value="可可生产者联盟 IGOCPA">可可生产者联盟 IGOCPA</option>                                    <option value="英联邦 IGOCWN">英联邦 IGOCWN</option><option value="八国集团 IGOGOE">八国集团 IGOGOE</option><option value="七国集团 IGOGOS">七国集团 IGOGOS</option><option value="七十七国集团 IGOGSS">七十七国集团 IGOGSS</option>                                    <option value="重债国 IGOHIP">重债国 IGOHIP</option><option value="人权观察 NGOHRW">人权观察 NGOHRW</option><option value="国际原子能组织 IGOUNOIAE">国际原子能组织 IGOUNOIAE</option><option value="国际可可组织 IGOICO">国际可可组织 IGOICO</option>                                    <option value="国际法律家委员会 NGOJUR">国际法律家委员会 NGOJUR</option><option value="国际法院 IGOUNOICJ">国际法院 IGOUNOICJ</option><option value="国际刑事法院 IGOICC">国际刑事法院 IGOICC</option><option value="国际危机组织 NGOICG">国际危机组织 NGOICG</option>                                    <option value="国际人权联合会 NGOFID">国际人权联合会 NGOFID</option><option value="国际红十字会和新月会 NGOCRC">国际红十字会和新月会 NGOCRC</option><option value="国际谷物协会 IGOIGC">国际谷物协会 IGOIGC</option><option value="国际赫尔辛基人权联盟 NGOIHF">国际赫尔辛基人权联盟 NGOIHF</option>                                    <option value="国际劳工组织 IGOUNOILO">国际劳工组织 IGOUNOILO</option><option value="国际货币基金组织 IGOIMF">国际货币基金组织 IGOIMF</option><option value="国际移民组织 NGOIOM">国际移民组织 NGOIOM</option><option value="国际战争罪行法庭 IGOUNOWCT">国际战争罪行法庭 IGOUNOWCT</option>                                    <option value="各国会议联盟 IGOIPU">各国会议联盟 IGOIPU</option><option value="国际刑警组织 IGOITP">国际刑警组织 IGOITP</option><option value="伊斯兰开发银行 IGOIDB">伊斯兰开发银行 IGOIDB</option><option value="无国界医生组织 NGOMSF">无国界医生组织 NGOMSF</option>                                    <option value="北大西洋公约组织 IGONAT">北大西洋公约组织 IGONAT</option><option value="每周国家组织 IGOOAS">每周国家组织 IGOOAS</option><option value="伊斯兰会议组织 IGOOIC">伊斯兰会议组织 IGOOIC</option><option value="不结盟国家组织 IGONON">不结盟国家组织 IGONON</option>                                    <option value="欧佩克 IGOOPC">欧佩克 IGOOPC</option><option value="乐施会 NGOXFM">乐施会 NGOXFM</option><option value="二十国委员会 IGOPRC">二十国委员会 IGOPRC</option><option value="红十字会 NGOIRC">红十字会 NGOIRC</option>                                    <option value="红新月会 NGORCR">红新月会 NGORCR</option><option value="联合国 IGOUNO">联合国 IGOUNO</option><option value="联合国儿童基金会 IGOUNOKID">联合国儿童基金会 IGOUNOKID</option><option value="联合国粮农组织 IGOUNOFAO">联合国粮农组织 IGOUNOFAO</option>                                    <option value="联合国人员高级委员会 IGOUNOHCH">联合国人员高级委员会 IGOUNOHCH</option><option value="联合国高级难民委员会 IGOUNOHCR">联合国高级难民委员会 IGOUNOHCR</option><option value="世界银行 IGOUNOWBK">世界银行 IGOUNOWBK</option><option value="世界经济论坛 NGOWEF">世界经济论坛 NGOWEF</option>                                    <option value="世界粮食计划署 IGOUNOWFP">世界粮食计划署 IGOUNOWFP</option><option value="世界卫生组织 IGOUNOWHO">世界卫生组织 IGOUNOWHO</option><option value="世界贸易组织 IGOWTO">世界贸易组织 IGOWTO</option>');
                $("#two-select").html('<option value="不明NA N/A">不明NA N/A</option><option value="警察、警官、犯罪调查、保护机构 COP">警察、警官、犯罪调查、保护机构 COP</option><option value="政府：执政内阁、执政党、联合政府、行政区 GOV">政府：执政内阁、执政党、联合政府、行政区 GOV</option><option value="造反（反叛者） INS">造反（反叛者） INS</option>  <option value="司法：法官、法庭 JUD">司法：法官、法庭 JUD</option><option value="武装力量 MIL">武装力量 MIL</option><option value="政治反对派 OPP">政治反对派 OPP</option><option value="反叛者 REB">反叛者 REB</option>  <option value="分离主义反叛者 SEP">分离主义反叛者 SEP</option><option value="国家情报机构和成员 SPY">国家情报机构和成员 SPY</option><option value="武装部队（中立） UAF">武装部队（中立） UAF</option><option value="恐怖分子 TER">恐怖分子 TER</option>  <option value="农业：个人，团体，政府机构 AGR">农业：个人，团体，政府机构 AGR</option><option value="商业：商人、公司和企业，不包含MNC BUS">商业：商人、公司和企业，不包含MNC BUS</option><option value="刑事犯罪：个人 CRM">刑事犯罪：个人 CRM</option><option value="不明确的平民个体或团体 CVL">不明确的平民个体或团体 CVL</option>  <option value="发展：发展的个人或团体，如基础设施建设、民主化等 DEV">发展：发展的个人或团体，如基础设施建设、民主化等 DEV</option><option value="教育：教育者、学生、教育机构 EDU">教育：教育者、学生、教育机构 EDU</option><option value="精英：前政府官员、名人、无组织分类发言人 ELI">精英：前政府官员、名人、无组织分类发言人 ELI</option><option value="环境 ENV">环境 ENV</option>  <option value="健康：个人，团体和组织（无国界医生） HLH">健康：个人，团体和组织（无国界医生） HLH</option><option value="人权 HRI">人权 HRI</option><option value="劳工：个人或组织 LAB">劳工：个人或组织 LAB</option>  <option value="立法机构 LEG">立法机构 LEG</option><option value="媒体 MED">媒体 MED</option><option value="难民：也指机构或跨国公司 REF">难民：也指机构或跨国公司 REF</option>  <option value="温和派：“温和”，“主流”等 MOD">温和派：“温和”，“主流”等 MOD</option><option value="激进派：“激进”，极端主义，“原教旨主义”等 RAD">激进派：“激进”，极端主义，“原教旨主义”等 RAD</option>');
            }
        });
        // 第三级下拉菜单：一级分类
        $('#one-select').on('change', function() {
            if (this.value == '公开声明') {
                $("#two-select").html('<option value="声明">声明</option><option value="拒绝评论">拒绝评论</option><option value="悲观评论">悲观评论</option><option value="乐观评论">乐观评论</option><option value="考虑政策选择">考虑政策选择</option><option value="承认或声称负责">承认或声称负责</option><option value="拒绝指控、否认责任">拒绝指控、否认责任</option><option value="参与象征性活动">参与象征性活动</option><option value="善意评论">善意评论</option><option value="表示一致性">表示一致性</option>');
            }
            else if (this.value == '呼吁') {
                $("#two-select").html('<option value="呼吁或请求">呼吁或请求</option><option value="实质合作">实质合作</option><option value="外交合作">外交合作</option><option value="实质援助">实质援助</option><option value="政治改革">政治改革</option><option value="屈服">屈服</option><option value="他人开会或谈判">他人开会或谈判</option><option value="他人解决纠纷">他人解决纠纷</option><option value="他人参与或接受调停">他人参与或接受调停</option>');
            }
            else if (this.value == '合作意愿') {
                $("#two-select").html('<option value="合作意愿">合作意愿</option><option value="实质合作">实质合作</option><option value="外交合作">外交合作</option><option value="实质援助">实质援助</option><option value="政治改革">政治改革</option><option value="屈服">屈服</option><option value="会见或谈判">会见或谈判</option><option value="解决争议">解决争议</option><option value="接受调停">接受调停</option><option value="充当调停人">充当调停人</option>');
            }
            else if (this.value == '要求') {
                $("#two-select").html('<option value="要求">要求</option><option value="实质合作">实质合作</option><option value="外交合作">外交合作</option><option value="实质援助">实质援助</option><option value="政治改革">政治改革</option><option value="屈服">屈服</option><option value="会见或谈判">会见或谈判</option><option value="解决争议">解决争议</option><option value="调停">调停</option>');
            }

            else if (this.value == '反对') {
                $("#two-select").html('<option value="反对">反对</option><option value="批评或谴责">批评或谴责</option><option value="指控">指控</option><option value="召集反对行动">召集反对行动</option><option value="官方抱怨">官方抱怨</option><option value="提起诉讼">提起诉讼</option><option value="发现有罪或有责任的（合法的）">发现有罪或有责任的（合法的）</option>');
            }
            else if (this.value == '拒绝') {
                $("#two-select").html('<option value="拒绝">拒绝</option><option value="实质合作">实质合作</option><option value="实质援助">实质援助</option><option value="政治改革">政治改革</option><option value="屈服">屈服</option><option value="拒绝会见、讨论或谈判">拒绝会见、讨论或谈判</option><option value="调停">调停</option><option value="解决争议的计划和协议">解决争议的计划和协议</option><option value="挑战准则和法律">挑战准则和法律</option><option value="否决">否决</option>');
            }
            else if (this.value == '威胁') {
                $("#two-select").html('<option value="威胁">威胁</option><option value="非武力威胁">非武力威胁</option><option value="行政约束">行政约束</option><option value="政治分歧">政治分歧</option><option value="停止谈判">停止谈判</option><option value="终止调停">终止调停</option><option value="停止国际干预">停止国际干预</option><option value="镇压">镇压</option><option value="军事力量威胁">军事力量威胁</option><option value="下达最后通牒">下达最后通牒</option>');
            }
            else if (this.value == '要求') {
                $("#two-select").html('<option value="要求">要求</option><option value="实质合作">实质合作</option><option value="外交合作">外交合作</option><option value="实质援助">实质援助</option><option value="政治改革">政治改革</option><option value="屈服">屈服</option><option value="会见或谈判">会见或谈判</option><option value="解决争议">解决争议</option><option value="调停">调停</option>');
            }
            else if (this.value == '磋商') {
                $("#two-select").html('<option value="磋商">磋商</option><option value="电话">电话</option><option value="出访">出访</option><option value="来访">来访</option><option value="第三地点会见">第三地点会见</option><option value="参与调解">参与调解</option><option value="参与谈判">参与谈判</option>');
            }
            else if (this.value == '外交合作') {
                $("#two-select").html('<option value="外交合作">外交合作</option><option value="赞美或赞同">赞美或赞同</option><option value="口头防卫">口头防卫</option><option value="为某人利益召集的支持">为某人利益召集的支持</option><option value="予以外交承认">予以外交承认</option><option value="道歉">道歉</option><option value="原谅">原谅</option><option value="签署正式协议">签署正式协议</option>');
            }
            else if (this.value == '实质合作') {
                $("#two-select").html('<option value="实质合作">实质合作</option><option value="经济合作">经济合作</option><option value="军事合作">军事合作</option><option value="此法合作">此法合作</option><option value="情报合作">情报合作</option>');
            }
            else if (this.value == '援助') {
                $("#two-select").html('<option value="援助">援助</option><option value="经济援助">经济援助</option><option value="军事援助">军事援助</option><option value="人道主义援助">人道主义援助</option><option value="军事保护、维和">军事保护、维和</option><option value="基于庇护">基于庇护</option><option value="屈服">屈服</option><option value="行政约束">行政约束</option><option value="政治分歧">政治分歧</option> <option value="政治改革">政治改革</option><option value="返还、释放">返还、释放</option><option value="解除经济制裁、联合抵制或禁运">解除经济制裁、联合抵制或禁运</option><option value="允许国际干预">允许国际干预</option><option value="降低军事参与">降低军事参与</option>');
            }
            else if (this.value == '调查') {
                $("#two-select").html('<option value="调查">调查</option><option value="犯罪和腐败">犯罪和腐败</option><option value="侵犯人权行为">侵犯人权行为</option><option value="军事行动">军事行动</option><option value="战争罪">战争罪</option>');
            }
            else if (this.value == '政策') {
                $("#two-select").html('<option value="出台，修改政策">出台，修改政策</option><option value="取消政策">取消政策</option>');
            }
            else if (this.value == '抗议') {
                $("#two-select").html('<option value="参与不同政见活动">参与不同政见活动</option><option value="示威或集会">示威或集会</option><option value="绝食">绝食</option><option value="罢工或抵制">罢工或抵制</option><option value="封锁">封锁</option><option value="骚乱">骚乱</option>');
            }
            else if (this.value == '军事展示') {
                $("#two-select").html('<option value="展示军警力量">展示军警力量</option><option value="提高警察警戒级别">提高警察警戒级别</option><option value="提高军事警戒级别">提高军事警戒级别</option><option value="动员或增加警力">动员或增加警力</option><option value="动员或增加武装力量">动员或增加武装力量</option><option value="动员或增加网络部队">动员或增加网络部队</option>');
            }
            else if (this.value == '降低关系') {
                $("#two-select").html('<option value="降低关系">降低关系</option><option value="降低或终止外交">降低或终止外交</option><option value="减少或停止实质援助">减少或停止实质援助</option><option value="施加经济制裁、联合抵制和禁运">施加经济制裁、联合抵制和禁运</option><option value="停止谈判">停止谈判</option><option value="终止调解">终止调解</option><option value="驱逐或撤离">驱逐或撤离</option>');
            }
            else if (this.value == '强迫') {
                $("#two-select").html('<option value="强迫">强迫</option><option value="掠夺或损坏财产">掠夺或损坏财产</option><option value="实施行政限制">实施行政限制</option><option value="逮捕和拘留">逮捕和拘留</option><option value="驱逐个人或驱逐出境">驱逐个人或驱逐出境</option><option value="镇压">镇压</option><option value="网络攻击">网络攻击</option>');
            }
            else if (this.value == '袭击') {
                $("#two-select").html('<option value="非常规攻击">非常规攻击</option><option value="诱拐、绑架、劫持人质">诱拐、绑架、劫持人质</option><option value="人身攻击">人身攻击</option><option value="爆炸">爆炸</option><option value="人体盾牌">人体盾牌</option><option value="试图暗杀">试图暗杀</option><option value="暗杀">暗杀</option>');
            }
            else if (this.value == '战斗') {
                $("#two-select").html('<option value="常规军事力量">常规军事力量</option><option value="军事封锁">军事封锁</option><option value="占领领土">占领领土</option><option value="小型和轻型武器作战">小型和轻型武器作战</option><option value="大炮和坦克作战">大炮和坦克作战</option><option value="航空武器作战">航空武器作战</option><option value="违反停火">违反停火</option>');
            }
            else if (this.value == '非常规大规模暴力') {
                $("#two-select").html('<option value="大规模非常规武力">大规模非常规武力</option><option value="大规模驱逐">大规模驱逐</option><option value="大规模杀戮">大规模杀戮</option><option value="种族清洗">种族清洗</option><option value="大规模破坏武器">大规模破坏武器</option>');
            }
        });
    });
    </script>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">主页</a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="upload.php">上传数据</a></li>
                    <li><a href="index.php">查看数据</a></li>
                    <!-- <li><a href="login.php">登录</a></li> -->
                    <li><a><?php echo "welcome, <span style='color:red'>".$_COOKIE['user_name']; ?></span></a></li>
                    <li><a href="logout.php">退出</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- First Container -->
    <center>
        <div class="container_main">
            <div style="width:80%;min-height:300px">
                <?php
                    $current_paragraph_id = '';
                    foreach ($current_paragraph_id_res as $current_paragraph_id_res) {
                        $current_paragraph_id = $current_paragraph_id_res->paragraph_id;
                        break;
                    }
                ?>
                <button type="button" class="btn btn-danger" id="tagging_start" <?php if (!empty($_COOKIE['tagging_now']) && $_COOKIE['tagging_now'] == 1) { echo "style='display:none;'";} ?>>点击开始标注</button>
                <button type="button" class="btn btn-danger" id="tagging_end" <?php if (empty($_COOKIE['tagging_now'])) { echo "style='display:none;'";} ?>>点击结束标注</button>
                <h3><?php echo "Paragraph ID: <label id='current_paragraph_id'>" . $current_paragraph_id; ?></label></h3>
                <h3><?php echo "Paragraph COUNT: " . $all_sum; ?></h3><br>
                <div id="selected_text">
                    <?php foreach ($res as $row) { 
                            echo '<p class="content"> ';
                            $split_span = explode(" ", $row->sentence);
                            for ($j = 0; $j < count($split_span); $j++) {
                                echo "<span value='$row->id' >$split_span[$j]</span> ";
                            }
                            echo ".</p><p class='content'><br>" . baidu_translate($row->sentence) . "<hr>";
                            // echo ".</p><p class='content'><br><hr>";
                            echo "</p><br>";
                    ?>
                </div>
            </div>
            <?php } ?>
            <div>
                <button type="button" <?php if (empty($id)) echo "disabled";  ?>
                    onclick="location.href = '<?php echo 'index.php?id='.($id - 1)  ?>';"
                    class="btn btn-primary">上一条</button>
                <button type="button" <?php if (($id + 1) == $all_sum) echo "disabled";  ?>
                    onclick="location.href = '<?php echo 'index.php?id='.($id + 1)  ?>';"
                    class="btn btn-primary">下一条</button>
            </div>
        </div>
        <?php if ($all_sum == 0) { echo "<h2>未上传任何数据</h2>";}  ?>
    </center>

    <!-- Footer -->
    <div id="f_menu">
        <button type="button" class="btn btn-success" id="tagging" <?php if (empty($_COOKIE['tagging_now'])) { echo "style='display:none;'"; } ?>>标注</button>
        <button type="button" class="btn btn-success" id="restore" style="margin-bottom:30px">还原</button>
        <button type="button" class="btn btn-success" id="add_null" style="margin-bottom:100px">添加空行</button>
        <button type="button" class="btn btn-success" id="all_tagging" style="margin-bottom:70px">查看所有标注</button>
        <?php
            if (empty($id)) {
                $word_id = 1;
            }
            else {
                $word_id = $id;
            }
        ?>
        <input type="hidden" name="word_id" value="<?php echo $current_paragraph_id; ?>" id="word_id">
    </div>
    <!-- <footer class="container-fluid bg-4 text-center">
        <p>标注系统 Copyright 2019</p>
    </footer> -->
    <!-- 模态框（Modal） -->
    <!-- 查看所有标注 -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        查看标注
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="lastname" class="col-sm-3 control-label">所有标注</label>
                        
                        <div class="col-sm-9">
                            <ul>
                            <?php
                                $sql = "select * from tagging_sentence_words where user_name = '$user_name'";
                                $words_tagging = custom_query($sql);
                                foreach ($words_tagging as $row) {
                                    if (empty($row->speech_attr_select)) {
                                        continue;
                                    }    
                                ?>
                                     <li>
                                        <?php echo "Sentence: " . $row->sentence_id . "<br>" . $row->tagging_words; ?>
                                        <br>
                                        <?php echo $row->speech_attr_select . "、" . $row->verbs_classification_select . "、" .$row->one_select . "、" .$row->two_select; ?>
                                    </li>
                             <?php   } ?>
                            </ul>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- 模态框示例（Modal） -->

    <!-- 标注 -->
    <form method="post" action="" class="form-horizontal" role="form" id="form_data" onsubmit="return check_form()"
        style="margin: 20px;">
        <div class="modal fade" id="myModal_tagging" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                        </button>
                        <h4 class="modal-title" id="myModalLabel">
                            标注
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <center><span id="tagging_words"></span></center>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-sm-3 control-label">词性</label>
                            <div class="col-sm-9">
                                <select id="speech-attr-select">
                                    <option value="动词">动词</option>
                                    <option value="施动者">施动者</option>
                                    <option value="受动者">受动者</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-sm-3 control-label">动词分类</label>
                            <div class="col-sm-9">
                                <select id="verbs-classification-select">
                                    <option value="口头合作">口头合作</option>
                                    <option value="实际合作">实际合作</option>
                                    <option value="口头敌对">口头敌对</option>
                                    <option value="实际敌对">实际敌对</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-sm-3 control-label">一级分类</label>
                            <div class="col-sm-9">
                                <select id="one-select">
                                    <option value="公开声明">公开声明</option>
                                    <option value="呼吁">呼吁</option>
                                    <option value="合作意愿">合作意愿</option>
                                    <option value="要求">要求</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-sm-3 control-label">二级分类</label>
                            <div class="col-sm-9">
                                <select id="two-select">
                                    <option value="声明">声明</option>
                                    <option value="拒绝评论">拒绝评论</option>
                                    <option value="悲观评论">悲观评论</option>
                                    <option value="乐观评论">乐观评论</option>
                                    <option value="考虑政策选择">考虑政策选择</option>
                                    <option value="承认或声称负责">承认或声称负责</option>
                                    <option value="拒绝指控、否认责任">拒绝指控、否认责任</option>
                                    <option value="参与象征性活动">参与象征性活动</option>
                                    <option value="善意评论">善意评论</option>
                                    <option value="表示一致性">表示一致性</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                        </button>
                        <button type="button" class="btn btn-primary" id="submit_btn">
                            提交
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </form>
</body>

</html>