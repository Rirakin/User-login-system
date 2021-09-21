<?php 

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) 
{
    header("location: login.php");
    exit;
}

require_once "config/config.php";

$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    if(empty(trim($_POST["new_password"])))
    {
        $new_password_err = "Please enter the new password.";
    }
    else if(strlen(trim($_POST["new_password"])) < 6)
    {
        $new_password_err = "Password must have atleast 6 characters.";
    }
    else 
    {
        $new_password = trim($_POST["new_password"]);
    }

    if(empty(trim($_POST["confirm_password"])))
    {
        $confirm_password_err = "Please confirm the password.";
    }
    else 
    {
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)) 
        {
            $confirm_password_err = "Password did not match.";
        }
    }

    if(empty($new_password_err) && empty($confirm_password_err)) 
    {
        $sql = "UPDATE users SET password = ? WHERE id = ?";

        if($stmt = $mysqli -> prepare($sql))
        {
            $stmt -> bind_param("si", $param_password, $param_id);

            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];

            if($stmt -> execute()) 
            {
                session_destroy();
                header("location: login.php");
                exit();
            }
            else 
            {
                echo "Something went wrong. Please try again later.";
            }

            $stmt -> close();
        }
    }
    $mysqli -> close();
}
?> 

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">

    <title>Reset Password</title>
  </head>
  <body>
    <div class="container mt-5" style="max-width: 30rem;">
        <h1>Reset password</h1>
        <p>Please fill out this form to reset your password</p>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
            <div class="form-group">
              <label for="new-password">New password</label>
              <input type="password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" name="new_password" id="new-password" aria-describedby="helpId" value="<?php echo $new_password; ?>">
              <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group mt-2">
              <label for="confirm-password">Confirm password</label>
              <input type="password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" name="confirm_password" id="confirm-password" aria-describedby="helpId">
              <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>

            </div>
            <button type="submit" class="btn btn-primary mt-3">Submit</button>
            <a class="btn btn-primary mt-3" href="welcome.php" role="button">Cancel</a>
        </form>

    </div>
    
  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>

 
  </body>
</html>