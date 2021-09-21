<?php 
session_start();

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
{
    header('location: welcome.php');
    exit;
}

require_once "config/config.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    if(empty(trim($_POST['username'])))
    {
        $username_err = "Please enter username.";
    }
    else 
    {
        $username = trim($_POST["username"]);
    }

    if(empty(trim($_POST['password'])))
    {
        $password_err = "Please enter your password.";
    }
    else 
    {
        $password = trim($_POST['password']);
    }

    if(empty($username_err) && empty($password_err))
    {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
      
        if($stmt = $mysqli->prepare($sql))
        {
            $stmt->bind_param("s", $param_username);
            
            $param_username = $username;
            
            if($stmt->execute())
            {
                $stmt->store_result();
                
                if($stmt->num_rows == 1)
                {                    
                    $stmt->bind_result($id, $username, $hashed_password);
                    if($stmt->fetch())
                    {
                        if(password_verify($password, $hashed_password))
                        {
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            header("location: welcome.php");
                        } 
                        else
                        {
                            $login_err = "Invalid username or password.";
                        }
                    }
                } 
                else
                {
                    $login_err = "Invalid username or password.";
                }
            } 
            else
            {
                echo "Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }
    
    $mysqli->close();

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

    <title>Login</title>
  </head>
  <body>
    <div class="container mt-5" style="max-width: 30rem;">
        <h1>Sign in</h1>
        <p>Create an account by filling out the form</p>
        <?php 
        if(!empty($login_err))
        {
          echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" name="username" id="username" aria-describedby="helpId" value="<?php echo $username; ?>">
              <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group mt-2">
              <label for="password">Password</label>
              <input type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" name="password" id="password" aria-describedby="helpId" value="<?php echo $password; ?>">
              <span class="invalid-feedback"><?php echo $password_err; ?></span>

            </div>
            <button type="submit" class="btn btn-primary mt-3">Login</button>
            <p>Don't have an account? <a href="register.php">Sign up</a></p>
        </form>

    </div>
    
  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>

 
  </body>
</html>