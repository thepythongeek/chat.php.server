<?php 

$connect = mysqli_connect("localhost", "root", "Orion1996$","swash_test");

function updateMessage(){
    global $connect;
    $messageId = $_POST['id'];
    // run the sql 
    $sql = "UPDATE message SET isRead=1 WHERE id='$messageId'";
    $result = mysqli_query($connect, $sql);
    // the array to hold the response 
    $response = [];
    if($result){
        $response['success'] = true;
        $response['message'] = 'success';
    }
    else{
        $response['success'] = false;
        $response['message'] = 'failure';
    }
    echo json_encode($response);
    mysqli_close($connect);
}
updateMessage();
