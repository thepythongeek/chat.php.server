<?php

function send($conversationId, $sender_id, $recipient_id, $msg){
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connect = mysqli_connect("localhost", "root", "Orion1996$","swash_test");
    

    // create a new conversation if it doesnot exist yet 
    if($conversationId=='null'){
        $sql = "INSERT INTO conversation(user_id) VALUES ('$sender_id')";
       $rslt =  mysqli_query($connect, $sql);
       if ($rslt){
           // get the newly added conversation 
           $sql = "SELECT id FROM conversation where user_id='$sender_id'";
           $result = mysqli_query($connect, $sql);
           while($row = mysqli_fetch_assoc($result)){
               $conversationId = $row['id'];
             //  echo $conv_id;
            
           }
           // add these users to the conversation 
           $sql1 = "INSERT INTO user_conversation(user_id,conversation_id) VALUES ('$sender_id', '$conversationId')";
           $sql2 = "INSERT INTO user_conversation(user_id,conversation_id) VALUES ('$recipient_id', '$conversationId')";
           // execute the queries 
           mysqli_query($connect, $sql1) ;
           mysqli_query($connect, $sql2);
       }
    }
    // store the message 
    $sql = "INSERT INTO message(senderId, recipientID, isRead, body, conversationId) VALUES ('$sender_id', '$recipient_id', false, '$msg', '$conversationId')";
    $results = mysqli_query($connect, $sql);
	echo var_dump($connect);
    $response = [];
    if($results){
       
        $response['success'] = true;
        // return the conversation and all unread messages
        $sql = "SELECT * FROM message WHERE conversationId='$conversationId' AND isRead=0";
        // execute query 
        $result = mysqli_query($connect, $sql);
        $messages = [];
        while($row = mysqli_fetch_assoc($result)){
            array_push($messages, $row);
        }
        $response['message']['conversation_id'] = $conversationId;
        $response['message']['user_id'] = $recipient_id;
        $response['message']['messages'] = $messages;
    }
    else{
        $response['success'] = false;
        $response['message'] = 'failed to send message';
    }
   
    mysqli_close($connect);
    return $response;
    


}
