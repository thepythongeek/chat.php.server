<?php
namespace MyApp;
include ('chat/get_conversations.php');
include ('chat/send_message.php');

use Exception;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
//include 'connection/connect.php';


class Socket implements MessageComponentInterface{
    
    public function __construct(){
        $this->clients = new \SplObjectStorage;
        $this->users = [];
        
    }

    function onOpen(ConnectionInterface $conn){
        // store the new connection 
        $this->clients->attach($conn);
        echo "NEW connection! ({$conn->resourceId})\n";
    }

    function onMessage(ConnectionInterface $from, $msg)
    {
        // create custom events 'connect' and 'sendMessage' and 'register'
        // 'connect' event specifies we should grab all conversations for 
        // this client
        // json format is "{'event':'connect', 'user_id':''}"
    
        $data =  json_decode($msg, true);
        if($data['event'] == 'connect'){
           // echo var_dump($data);
            $user_id = $data['user_id'];
            // grab all conversations for this user 
            // and send to this user
    
            $response = getAllConversationsFor($user_id);
            $response['event'] = 'connect';
//echo var_dump($response);
           $from->send(json_encode($response));

        }
        // 'sendMessage' event specifies when to send conversations new or refreshed to these 
        // users 
        // {'event':'sendMessage', 'message': message parameters}
        if($data['event']=='sendMessage'){
            
         
            $dat = $data['message'];
            $conversationId = $dat['conversationId'];
	//    echo var_dump($dat);
            $response = send($conversationId, $dat['senderId'], $dat['recipientID'],$dat['body']);
           $response['event'] = "sendMessage";
            // send this conversation only to recipient if they are online
           
            if(isset($this->users[$dat['recipientID']])){
                
                
                $this->users[$dat['recipientID']]->send(json_encode($response));
            }
    
          $this->users[$dat['senderId']]->send(json_encode($response));
            
        
        }
        // 'register' event allows to trace the resource id for this user
        // json format {'event':'register', 'user_id':XXX}
        if($data['event'] == 'register'){
    
            foreach($this->clients as $client){
                if($from->resourceId == $client->resourceId){
                    $this->users[$data['user_id']] = $client;
                  // array_push($this->users, [$data["user_id"]=>$client->resourceId]);
        
                    break;
                }
            }
        }
        // 'prize' event allows to send prize winner details
        // 'format' is {'event': 'prize', 'message': {'winner_id':XXXX, 'name':XXXX}} 
        if($data['event'] == 'prize'){
            foreach($this->clients as $client){
		$response = array();
		$response['event']='prize';
		$response["message"] = $data['message'];
                $client->send(json_encode($response));
            }
        }
        
    }

    function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

    }

    function onError(ConnectionInterface $conn, Exception $e)
    {
        
    }
    
        
    
}
