<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up TaskCore</title>
    <link rel="stylesheet" href="css/sign-up.css">
</head>
<body>
    <img id="logo" src="css/images/taskcore-logo.svg" alt="TaskCore Logo">

    <h1 class="title">Sign up to TaskCore</h1>
    
    <form method="POST" action="autenticarRegistro.php">

    <div id="input-block">
        <label for="username">Username</label>
        <input id="username" name="nome" placeholder="Noobmaster69" required><br>

        <label for="email">E-mail</label>
        <input id="email" name="email" placeholder="your@email.com" type="email" required><br>

        <label for="password">Password</label>
        <input id="password" name="password" placeholder="password123" type="password" required><br>

        <button id="button-sign-up" type="submit">Sign up</button>
    </div>
    
    </form>
    
    <div id="have-account-block">
        <h1>Already have an Account? </h1>
        <a href="sign-in.php">Login</a>
    </div>
</body>
</html>