<?php
session_start();
if(!isset($_SESSION["username"])){
  header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lunar Chat</title>
  <link rel="manifest" href="site.webmanifest" />
  <link rel="stylesheet" href="global.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <style>
    .material-symbols-rounded {
      font-variation-settings:
        'FILL' 1,
        'wght' 400,
        'GRAD' 0,
        'opsz' 48
    }
  </style>
  <style>
    .back {
      position: absolute;
      left: 30px;
      margin-top: 1vh;
    }
    .chats {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .chat-item {
      width: 85%;
      height: 90px;
      background-color: #2a2e35;
      padding: 15px;
      display: flex;
      align-items: center;
      border-radius: 10px;
      margin-bottom: 10px;
    }

    .pfp {
      height: 60px;
      width: 60px;
      border-radius: 30px;
    }

    .name {
      margin-left: 20px;
      font-size: 1.3rem;
      font-weight: 300;
    }

    h1 {
      text-align: center;
    }
    h2{
      font-size: 1.4rem;
      text-align: center;
      font-weight: 300;
    }
  </style>
</head>

<body>
  <span class="material-symbols-rounded back" onclick='window.location.href = "feed.html"'>
    arrow_back_ios
</span>
  <h1>DIRECT</h1>
  <div class="chats" id="chats">
    <i><h2>Načítání...</h2></i>

  </div>
</body>
<script>
  $(document).ready(() => {
    $.ajax({
      url: "api/getFollowerList.php",
      method: "GET",
      dataType: "json",

      success: function (response) {
        console.log("Received JSON:", response);
        let followerList = "";
        for (var i = 0; i < response.length; i++) {
          followerList += "<div class='chat-item' onclick='goToChat(" + response[i].id + ")'>";
          followerList += '<img class="pfp" src="' + response[i].pfp +'"></img>';
          followerList += '<div class="name">' + response[i].username + '</div>';
          followerList += "</div>";
        }

        $("#chats").html(followerList);
      },

      error: function (xhr, status, error) {
        console.log("Error status:", status);
        console.log("Error response:", xhr.responseText);
        console.log("Error:", error);
      }
    });
  });

  function goToChat(id){
    window.location.href = "chat.php?id=" + id;
  }
</script>

</html>