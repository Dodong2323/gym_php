<?php
include "headers.php";

class User
{
    function login($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
    
        $sql = "SELECT * FROM tbl_user WHERE user_email = :username AND user_password = :password";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":username", $json["username"], PDO::PARAM_STR);
        $stmt->bindParam(":password", $json["password"], PDO::PARAM_STR);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return json_encode([
                "user_id" => $user["user_id"],
                "user_firstname" => $user["user_firstname"],
                "user_lastname" => $user["user_lastname"],
                "user_email" => $user["user_email"],
                "Role" => $user["Role"],
                "user_phone" => $user["user_phone"],
                "user_failed_attempts" => $user["user_failed_attempts"],
                "user_Datejoined" => $user["user_Datejoined"]
            ]);
        }
        return 0;
    }

    function updateFailedAttempts($json) {
        include "connection.php";
        $json = json_decode($json, true);
    
        $user_email = $json["user_email"];
        $user_failed_attempts = $json["user_failed_attempts"];
    
        // Validate if the user_email exists in the database
        $checkSql = "SELECT user_email FROM tbl_user WHERE user_email = :user_email";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(":user_email", $user_email, PDO::PARAM_STR);
        $checkStmt->execute();
    
        if ($checkStmt->rowCount() > 0) {
            // User exists, proceed with the update
            $updateSql = "UPDATE tbl_user SET user_failed_attempts = :user_failed_attempts WHERE user_email = :user_email";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(":user_failed_attempts", $user_failed_attempts, PDO::PARAM_INT);
            $updateStmt->bindParam(":user_email", $user_email, PDO::PARAM_STR);
            $updateStmt->execute();
    
            return $updateStmt->rowCount() > 0 ? 1 : 0;
        } else {
            // User does not exist
            return json_encode(["status" => "error", "message" => "User not found"]);
        }
    }
    
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";
$user = new User();

switch ($operation) {
    case "login":
        echo $user->login($json);
        break;
    case "updateFailedAttempts":
        echo $user->updateFailedAttempts($json);
        break;
    default:
        echo "Invalid operation";
        break;
}
