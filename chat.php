<?php
session_start();
$userId = $_SESSION["id"];
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
        @import url('https://fonts.cdnfonts.com/css/sofia-pro');

        header {
            display: flex;
            height: 10vh;
            width: 100vw;
            justify-content: center;
            align-items: center;
            position: fixed;
            background-color: #12181b;
        }
        .cara{
            width: 90%;
            height: 1px;
            background-color:#2a363b;
            position: absolute;
            bottom: 0;
        }

        .block {
            height: 11.5vh;
            width: 100%;
        }

        .block2 {
            height: 13vh;
            width: 100%;
            display: inline-block;
        }

        main {
            height: 90vh;
            width: 100vw;
        }

        .chat {
            display: flex;
            flex-direction: column; /* Stack messages vertically */
            align-items: flex-start; /* Align messages to the left side */
            overflow-y: auto; /* Add scroll if messages overflow */
        }

        .chatbox {
            height: 12vh;
            width: 100vw;
            position: absolute;
            bottom: 0;
            display: grid;
            place-items: center;
            position: fixed;
            bottom: 0;
            background-color: #12181b;
            padding-bottom: 20px;
            padding-top: 10px;
        }

        .otherUser {
            font-size: 1.5rem;
        }

        .message {
            max-width: 70%;
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.253);
            border-radius: 22px;
            padding: 10px 17px;
        }
        .message-wrapper{
            width: 100%;
            padding: 2px 20px;
            box-sizing: border-box; /* Include padding in width calculation */
        }

        .my {
            background-color: rgba(255, 98, 0, 0.5);
            float: right;
        }

        .separator {
            width: 100%;
            height: 5px;
        }

        .wrapper {
            width: 90%;
            height: 80%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.258);
            border-radius: 30px;

        }

        input {
            border: none;
            outline: none;
            padding: 2vh;
            border-radius: 0;
            color: rgb(255, 255, 255);
            width: 85%;
            background-color: transparent;
        }

        button {
            border: none;
            outline: none;
            border-radius: 0;
            width: 15%;
            background-color: transparent;
        }

        .back {
            position: absolute;
            left: 30px;
        }
        #pfp{
            height: 40px;
            width: 40px;
            margin-right: 15px;
            border-radius: 20px;
        }
    </style>
</head>

<body>
    <header>
        <span class="material-symbols-rounded back" onclick='window.location.href = "direct.php"'>
            arrow_back_ios
        </span>
        <img src="none" id="pfp">
        <div class="otherUser" id="otherUser"></div>
        <div class="cara"></div>
    </header>
    <main>
        <div class="chat" id="chat">
            
        </div>
        <div class="chatbox">
            <div class="wrapper">
                <input id="input" type="text" placeholder="Zpráva...">
                <button onclick="SendMessage()">
                    <span class="material-symbols-rounded">
                        send
                    </span>
                </button>
            </div>
        </div>
    </main>
</body>
<script>
    


    var urlString = window.location.href;
    // Create a URL object
    var url = new URL(urlString);
    // Get the search parameters from the URL
    var searchParams = url.searchParams;
    // Get specific parameter values
    var reciverId = searchParams.get("id");

    $.ajax({
            url: "api/getUsersDataById.php",
            method: "GET",
            data: {id: reciverId},
            dataType: "json",
            success: function(response){
                document.getElementById("otherUser").innerHTML = response.displayName;
                document.getElementById("pfp").src = response.pfp;
            },
            error: function(xhr, status, error){
                console.error("Chyba: " + error);
            }
        });
    
    console.log("Reciver ID: " + reciverId);
    setInterval(() => {
        getMessages();
    }, 5000);

    function getMessages(){
        $.ajax({
            url: "api/message/getMessages.php",
            method: "GET",
            data: {id: reciverId},
            dataType: "json",
            success: function(response){
                console.log(response);

                let messagesItems = '<div class="block"></div>';
                for (var i = 0; i < response.length; i++){
                    if(response[i].sender === reciverId){
                        messagesItems += '<div class="message-wrapper">';
                        messagesItems += '<div class="message">';
                        messagesItems += response[i].content;
                        messagesItems += "</div>";
                        messagesItems += "</div>";
                        if(response[i + 1]){
                            if(response[i + 1].sender !== reciverId)
                            messagesItems += '<div class="separator"></div>';
                        }
                    }
                    else{
                        messagesItems += '<div class="message-wrapper">';
                        messagesItems += '<div class="message my">';
                        messagesItems += response[i].content;
                        messagesItems += "</div>";
                        messagesItems += "</div>";
                        if(response[i + 1]){
                            if(response[i + 1].sender === reciverId)
                            messagesItems += '<div class="separator"></div>';
                        }
                    }
                }
                messagesItems += '<div class="block2" id="bottom"></div>';

                if(messagesItems !== document.getElementById("chat").innerHTML){
                    $("#chat").html(messagesItems);
                    document.getElementById("bottom").scrollIntoView();
                }
            },
            error: function(xhr, status, error){
                console.error("Chyba: " + error);
            }
        });
    }
    getMessages();

    function SendMessage() {
        var content = document.getElementById("input").value;
        $("#input").val("");

        
        var userId = <?php echo $userId ?>;
        var data = {
            sender: userId,
            reciver: parseInt(reciverId),
            content: content
        }
        //zde smichat php s js
        $.ajax({
            url: "api/message/sendMessage.php",
            method: "POST",
            data: data,
            success: function(response){
                console.log("odeslano");
                getMessages();
                
            },
            error: function(xhr, status, error){
                console.error("Chyba: " + error);
            }
        });
    }
</script>

</html>