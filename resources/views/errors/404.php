<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 600px;
        }
        h1 {
            font-size: 72px;
            margin: 0;
            color: #d32f2f;
        }
        p {
            font-size: 18px;
            color: #666;
            margin-top: 20px;
        }
        a {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 24px;
            background: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a:hover {
            background: #1976D2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>This Page Does Not Exist</p>
        <p>Sorry, the page you are looking for could not be found. It's just an accident that was not intentional.</p>
        <a href="<?php echo url('/'); ?>">Go to Homepage</a>
    </div>
</body>
</html>

