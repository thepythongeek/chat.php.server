<?php



function getAllConversationsFor($user_id){
    // this functions returns all conversations for
    // this user
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$connect = mysqli_connect("localhost", "root", "Orion1996$","swash_test");
    
    //$user_id = $_POST['user_id'];
    // the query
    $sql = "SELECT DISTINCT c.user_id, u.name AS username, v.name AS username2, c.conversation_id,conversation.user_id AS user2, conversation.createdAt
     FROM user u JOIN user_conversation c ON u.id=c.user_id 
     JOIN conversation ON c.conversation_id=conversation.id
      JOIN user v ON conversation.user_id=v.id 
      WHERE conversation.user_id = '$user_id' OR c.user_id = '$user_id' GROUP BY c.user_id,conversation.user_id, u.name, v.name, c.conversation_id, conversation.createdAt HAVING c.user_id<>conversation.user_id;";
    // execute query
    $result = mysqli_query($connect, $sql);
    // ready array for reponse 
    $response = [];
    if($result){
        $response['message'] = [];
        $response['success'] = true;
        // now get recent message for this conversation
        while($row = mysqli_fetch_assoc($result)){
            // run a query to get all messages for this conversation but only the recent one 

            // grab the conversation id
            $id = $row['conversation_id'];

            $sql = "SELECT * FROM message WHERE conversationId='$id' ORDER BY createdAt DESC LIMIT 1";
        
          
            
            //$sql = "SELECT * FROM message WHERE conversationId='$id' and ORDER BY createdAt DESC";
            // execute query 
            $msg_result = mysqli_query($connect, $sql);
            while($messages = mysqli_fetch_assoc($msg_result)){
                $row['messages'] = $messages;
            
            }
            
            array_push($response['message'], $row);
        }
        
   
    }
    else{
        $response['success'] = false;
        $response['message'] = 'something went wrong';
        
    }
    mysqli_close($connect);
    return  $response;
}


