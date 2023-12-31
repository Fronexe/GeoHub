<?php 
 require('../functions/functions.inc.php');
 require("../partials/regex.inc.php");
 echo "<script> const passwordRegex = ".$passwordReg."</script>";
 echo "<script> const emailRegex = ".$emailReg."</script>";
 echo "<script> const usernameRegex = ".$usernameReg."</script>";
 echo "<script> const fullnameRegex = ".$nameReg."</script>";
if (isset($_POST['submit'])) {
    

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $fullname = $_POST['fullname'];

    if(!preg_match($emailReg, $email)){
        $_SESSION['error'] = "Please make sure that the entered email is valid";
        header("Location: /ITCS333-Project/auth/signup.php?email=".$email."&username=".$username."&fullname=".$fullname);   
    }
    else if(!preg_match($usernameReg, $username)){
        $_SESSION['error'] = "Please make sure that Username is 4 to 20 characters long, only contains alphabet letters and 0 to 9 numerics";
        header("Location: /ITCS333-Project/auth/signup.php?email=".$email."&username=".$username."&fullname=".$fullname);   
    }
    else if(!preg_match($passwordReg, $password) && !preg_match($passwordReg, $password2)){
        $_SESSION['error'] = "Please make sure that the entered password has one special character, one small letter, one capital letter and at least 8 characters long";
        header("Location: /ITCS333-Project/auth/signup.php?email=".$email."&username=".$username."&fullname=".$fullname);   
    }
    else if(!preg_match($nameReg, $fullname) ){
        $_SESSION['error'] = "Please make sure that the entered full name is entered properly";
        header("Location: /ITCS333-Project/auth/signup.php?email=".$email."&username=".$username."&fullname=".$fullname);   
    }
    else{
        if($password != $password2){
            $_SESSION['error'] = "Passwords do not match";
            header("Location: /ITCS333-Project/auth/signup.php?email=".$email."&username=".$username."&fullname=".$fullname);   
        }else{
            
            $usernameQuery = "SELECT * FROM users WHERE BINARY username = '$username'";
            $emailQuery = "SELECT * FROM users WHERE BINARY email = '$email'";
            
            $usernameResult = ($db->query($usernameQuery)->rowCount());
            $emailResult = ($db->query($emailQuery)->rowCount());
            
            //Cecks if username or email already exist, else it inserts the values into the database
            if($usernameResult>0){
                $_SESSION['error'] = "Username already exists";
                header("Location: /ITCS333-Project/auth/signup.php?email=".$email."&username=".$username."&fullname=".$fullname);   
            }
            else if($emailResult>0){
                $_SESSION['error'] = "Email already exists";
                header("Location: /ITCS333-Project/auth/signup.php?email=".$email."&username=".$username."&fullname=".$fullname);   
            }
            else{

                $hash = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, email, fName, hash, pcode) 
                        VALUES('$username', '$email', '$fullname', '$hash', 0)";
                $result = $db->query($sql);

                $_SESSION["userId"] = $db->lastInsertId();
                $_SESSION["username"] = $username;

                if(isset($_POST['rememberMe']) && $_POST['rememberMe'] == 'rememberMe'){
                    $data = $_SESSION["userId"].'#'.$_SESSION["username"];
                    $data = base64_encode($data);
                    setcookie("session", $data,time() + 604800, '/', '', true, true);
                }
                
                if(!isset($_COOKIE["redirect"])){
                    $_SESSION['success'] = "Sign Up Successful, check your account for email verification!";
                    header("Location: /ITCS333-Project/quiz/quizzesDisplay.php");
                }else{
                    header("Location: ".$_COOKIE["redirect"]);
                    setcookie ("redirect", $redirectUrl, time() - 3600,'/');
                }
            }
        }
    }

}
?>