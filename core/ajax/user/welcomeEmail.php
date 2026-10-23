<?php 
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../../system/spex_v4_func.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$UID = getSession('UID');

if (empty($UID)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
    exit;
}

$userData = FetchUserData($UID);
$WebDatar = FetchWebSettings();

if (!$userData) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

$userMail = $userData['mail'];
$userDate = $userData['register_date'];
$userWallet = $userData['balance'];
$WebsName = $WebDatar['BaseName'];

$WelcomeMail = sendWelcomeEmail($userMail, $UID, $userDate,$userWallet,$WebsName);

function sendWelcomeEmail($userMail, $UID, $userDate,$userWallet,$WebsName,) {
    $webSettings = FetchWebSettings();
    $siteName = $webSettings['BaseName'];
    $domain = $_SERVER['HTTP_HOST'];

    $SMTPHost = $webSettings['smtp_host'];
    $SMTPUser = $webSettings['smtp_user'];
    $SMTPPass = $webSettings['smtp_pass'];
    $SMTPPort = $webSettings['smtp_port'];
    
    $subject = "Welcome to $siteName - Your Account is Ready!";
    
    $message = generateWelcomeEmailTemplate($UID, $userMail, $domain, $userDate, $WebsName,$userWallet);
    
    $SendMail = new PHPMailer(true);

    $SendMail->isSMTP();
    $SendMail->Host       = $SMTPHost;
    $SendMail->SMTPAuth   = true;
    $SendMail->Username   = $SMTPUser;
    $SendMail->Password   = $SMTPPass;
    $SendMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $SendMail->Port       = $SMTPPort;

    $SendMail->setFrom('support@' . $_SERVER['HTTP_HOST'] , $siteName . ' Support'); // 
    $SendMail->addAddress($userMail);

    $SendMail->isHTML(true);
    $SendMail->Subject = $subject;
    $SendMail->Body    = $message;
    
    return $SendMail->send();
}


function generateWelcomeEmailTemplate($UID, $userMail, $domain, $userDate, $WebsName, $userWallet) {
   $Link = "https://$domain/?r=app";
   $time = date('l jS \of F Y h:i:s A');
   
   return "
   <!DOCTYPE html>
<html lang=\"en\">
<head>
   <meta charset=\"UTF-8\">
   <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
   <title>Welcome Email</title>
   <style>
       body {
           font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
           line-height: 1.6;
           color: #333;
           background-color: #f4f4f4;
           margin: 0;
           padding: 0;
       }
       .container {
           max-width: 600px;
           margin: 20px auto;
           background: #ffffff;
           border-radius: 10px;
           box-shadow: 0 0 20px rgba(0,0,0,0.1);
           overflow: hidden;
       }
       .header {
           background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
           color: white;
           padding: 30px;
           text-align: center;
       }
       .header h1 {
           margin: 0;
           font-size: 28px;
           font-weight: 300;
       }
       .content {
           padding: 40px;
       }
       .welcome-message {
           background: #f8f9fa;
           border-left: 4px solid #667eea;
           padding: 20px;
           margin: 20px 0;
           border-radius: 5px;
       }
       .highlight {
           color: #667eea;
           font-weight: bold;
       }
       .stats-box {
           background: linear-gradient(45deg, #667eea, #764ba2);
           color: white;
           padding: 20px;
           border-radius: 8px;
           text-align: center;
           margin: 20px 0;
       }
       .stats-box h3 {
           margin: 0 0 10px 0;
           font-size: 24px;
       }
       .button {
           display: inline-block;
           background: #667eea;
           color: white;
           padding: 12px 30px;
           text-decoration: none;
           border-radius: 25px;
           font-weight: bold;
           margin: 20px 0;
           transition: all 0.3s ease;
       }
       .button:hover {
           background: #764ba2;
           transform: translateY(-2px);
       }
       .footer {
           background: #333;
           color: #ccc;
           text-align: center;
           padding: 20px;
           font-size: 14px;
       }
       .social-links {
           margin: 20px 0;
       }
       .social-links a {
           display: inline-block;
           margin: 0 10px;
           color: #667eea;
           text-decoration: none;
           font-weight: bold;
       }
       .info-grid {
           display: grid;
           grid-template-columns: 1fr 1fr;
           gap: 20px;
           margin: 20px 0;
       }
       .info-card {
           background: #f8f9fa;
           padding: 15px;
           border-radius: 8px;
           border: 1px solid #e9ecef;
       }
       .info-card h4 {
           color: #667eea;
           margin: 0 0 10px 0;
       }
   </style>
</head>
<body>
   <div class=\"container\">
       <div class=\"header\">
           <h1>Welcome to $WebsName</h1>
           <p>Your journey to financial freedom starts here!</p>
       </div>
       
       <div class=\"content\">
           <div class=\"welcome-message\">
               <h2>Hello <span class=\"highlight\">User #$UID</span>!</h2>
               <p>Thank you for joining our community. We're excited to have you on board and can't wait to help you achieve your financial goals.</p>
           </div>
           
           <div class=\"info-grid\">
               <div class=\"info-card\">
                   <h4>Your Email</h4>
                   <p><strong>$userMail</strong></p>
               </div>
               <div class=\"info-card\">
                   <h4>Join Date</h4>
                   <p><strong>$userDate</strong></p>
               </div>
           </div>
           
           <div class=\"stats-box\">
               <h3>Your Wallet</h3>
               <p style=\"font-size: 16px; margin: 0; word-break: break-all;\"><strong>$$userWallet</strong></p>
               <small>Start trading and grow your portfolio!</small>
           </div>
           
           <div style=\"text-align: center;\">
               <a href=\"$Link\" class=\"button\">Start Trading Now</a>
           </div>
           
           
           <h3 style=\"color: #667eea;\">What's Next?</h3>
           <ul style=\"padding-left: 20px;\">
               <li>Complete your profile verification</li>
               <li>Make your first deposit</li>
               <li>Explore our trading tools</li>
               <li>Try our automated trading bots</li>
               <li>Start building your portfolio</li>
           </ul>
           
           <div style=\"margin-top: 30px; padding: 20px; background: #e8f4f8; border-radius: 8px;\">
               <h4 style=\"color: #0c5460; margin: 0 0 10px 0;\">Need Help?</h4>
               <p style=\"color: #0c5460; margin: 0;\">Our support team is available 24/7 to assist you. Don't hesitate to reach out if you have any questions!</p>
           </div>
       </div>
       
       <div class=\"footer\">
           <p>&copy; 2025 $WebsName. All rights reserved.</p>
           <p>You received this email because you signed up for an account on $userDate</p>
           <p><small>If you didn't create this account, please ignore this email.</small></p>
       </div>
   </div>
</body>
</html>";
}

?>