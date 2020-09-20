<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>简单计时器</title>
  <style type="text/css">
  input{
    border: 1px solid black;
    text-align: right;
    font-size:20px;
  }
  </style>
</head>
<body>
  <input type="text" id="text" value="0" >秒
  <br>
  <input type="button" value="开始计时" id="btn1">
  <input type="button" value="停止计时" id="btn2">
  <script type="text/javascript">
  var time = 0;
  var seconde=0;
  var btn1 = document.getElementById("btn1");
  var btn2 = document.getElementById("btn2");
  var text = document.getElementById("text");
  function getStyle(elem, prop) {
    if (window.getComputedStyle) {
      return window.getComputedStyle(elem, null)[prop];
    } else {
      return elem.currentStyle[prop];
    }
  }
  var count = 1;//设置时间戳
  btn1.addEventListener("click",showTime)
  function showTime(e) {
    if(count){
      time = setInterval(function () {
        seconde++;
        text.value = seconde;
      }, 1000)
      count =0;
    }
  }
  btn2.addEventListener("click",stopTime)
  function stopTime(){
    clearInterval(time);
    count =1;
  }
  </script>
</body>
</html>