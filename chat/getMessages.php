<?php 

function getMessages(){
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connect = mysqli_connect("localhost", "root", "Orion1996$","swash_test");

    $conversationId = $_POST['id'];
    // run the sql 
    $sql = "SELECT * FROM message WHERE id='$conversationId'";
    $result = mysqli_query($connect, $sql);
    // the array to hold the response 
    $response = [];
    if($result){
        $response['success'] = true;
        $response['message'] = [];
        while($row = mysqli_fetch_assoc($result)){
            array_push($response['message'], $row);
        }
        
    }
    else{
        $response['success'] = false;
        $response['message'] = 'failure';
    }
    echo json_encode($response);
    mysqli_close($connect);
}
updateMessage();
