<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-in</title>
</head>
<body>

    <form action="autenticarSignIn.php" method="POST">
        
        <div> 
            <label>
            Email: 
            </label>
            <input type="email" name="email" required> <br> </br>

            <label>
            Senha: 
            </label>
            <input type="password" name="password" required> <br> </br>

            <button type="submit">
            Entrar
            </button> <br> </br>
        </div>
    
    </form>
    
</body>
</html>