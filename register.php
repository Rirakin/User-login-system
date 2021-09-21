<?php 
require_once "config/config.php";

$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    if(empty(trim($_POST["username"]))) 
    {
        $username_err = "Please enter a username";
    }
    else if(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['username'])))
    {
        $username_err = "Username can only contain letters, numbers and underdscores.";
    }
    else 
    {
        $sql = "SELECT id FROM users WHERE username = ?";

        if($stmt = $mysqli->prepare($sql)) 
        {
            $stmt->bind_param("s", $param_username);
            $param_username = trim($_POST['username']);

            if($stmt->execute())
            {
                $stmt->store_result();

                if($stmt->num_rows == 1)
                {
                    $username_err = "This username is already taken.";
                }
                else 
                {
                    $username = trim($_POST['username']);
                }
            }
            else 
            {
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    if(empty(trim($_POST['email'])))
    {
        $email_err = "Please enter your email.";
    }
    else
    {
        $sql = "SELECT id FROM users WHERE username = ?";
        if($stmt = $mysqli->prepare($sql))
        {
            $stmt->bind_param("s", $param_email);
            $param_email = trim($_POST['email']);

            if($stmt->execute())
            {
                $stmt->store_result();
                if($stmt->num_rows == 1)
                {
                    $email_err = "This email is already taken.";
                }
                else 
                {
                    $email = trim($_POST['email']);
                }
            }
            else 
            {
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    if(empty(trim($_POST['password'])))
    {
        $password_err = "Please enter a password.";
    }
    else if(strlen(trim($_POST['password'])) < 6)
    {
        $password_err = "Password must have atleat 6 characters.";
    }
    else 
    {
        $password = trim($_POST['password']);
    }

    if(empty(trim($_POST['confirm_password'])))
    {
        $confirm_password_err = "Please confirm password.";
    }
    else 
    {
        $confirm_password = trim($_POST['confirm_password']);
        if(empty($password_err) && ($password != $confirm_password))
        {
            $confirm_password_err = "Password did not match.";
        }
    }

    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err))
    {
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        
        if($stmt = $mysqli->prepare($sql)) 
        {
            $stmt->bind_param("sss", $param_username, $param_password, $param_email);

            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_email = $email;

            if($stmt->execute()) 
            {
                header("location: login.php");
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

    <title>Register</title>
  </head>
  <body>
    <div class="container mt-5" style="max-width: 30rem;">
        <h1>Sign up</h1>
        <p>Create an account by filling out the form</p>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" name="username" id="username" aria-describedby="helpId" value="<?php echo $username; ?>">
              <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" name="email" id="email" aria-describedby="helpId" value="<?php echo $email; ?>">
              <span class="invalid-feedback"><?php echo $email_err; ?></span>

            </div>
            <div class="form-group mt-2">
              <label for="password">Password</label>
              <input type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" name="password" id="password" aria-describedby="helpId" value="<?php echo $password; ?>">
              <span class="invalid-feedback"><?php echo $password_err; ?></span>

            </div>
            <div class="form-group mt-2">
              <label for="confirm_password">Confirm password</label>
              <input type="password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" name="confirm_password" id="confirm_password" aria-describedby="helpId" value="<?php echo $confirm_password; ?>">
              <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>

            </div>
            <button type="submit" class="btn btn-primary mt-3">Register</button>
            <button type="reset" class="btn btn-secondary mt-3">Reset</button>
            <p>Already have an account? <a href="login.php">Sign in</a></p>
        </form>

    </div>
    
  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>

 
  </body>
</html>