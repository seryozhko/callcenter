<?php
$settings = require_once 'settings.php';

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

$credentials = new Google_Auth_AssertionCredentials(
    $settings['google']['client_email'],
    $settings['google']['scopes'],
    file_get_contents($settings['google']['private_key'])
);
$credentials->sub = $settings['google']['credentials_sub'];
$client = new Google_Client();
$client->setAssertionCredentials($credentials);
if ($client->getAuth()->isAccessTokenExpired())
{
  $client->getAuth()->refreshTokenWithAssertion();
}
$accessToken = json_decode($client->getAccessToken())->access_token;
$serviceRequest = new DefaultServiceRequest($accessToken);
ServiceRequestFactory::setInstance($serviceRequest);
$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
$spreadsheet = $spreadsheetService->getSpreadsheetById($settings['google']['spreadsheet_id']);
$currentUpdateTime = $spreadsheet->getUpdated()->format('Y-m-d H:i');
$updated = file_get_contents($settings['global']['updated_storage']);
if($updated < $currentUpdateTime)
{
    $worksheetFeed = $spreadsheet->getWorksheets();
    $worksheet = $worksheetFeed->getByTitle('Лист1');
    $listFeed = $worksheet->getListFeed();
    $products = [];
    $sites = [];
    $cities = [];
    $operators = [];
    foreach ($listFeed->getEntries() as $entry) 
    {
        $values = $entry->getValues();
        if($values['оператор'])
        {
            $operators[] = $values['оператор'];
        }
        if($values['товар'])
        {
            $products[] = $values['товар'];
        }
        if($values['сайты'])
        {
            $sites[$values['сайты']] = [
                "url" => $values['url'],
                "mainCity" => $values['код'],
                "shipping" => [
                    "msk" => $values['msk'],
                    "spb" => $values['spb'],
                    "other" => $values['other'],
                    ],
                "mail-from" => $values['from-mail'],
                "mail-to" => $values['to-mail'],
            ];
        }
        if($values['город'])
        {
            $cities[$values['код']] = $values['город'];
        }
    }
    $data = json_encode([
                    'operators' => $operators,
                    'products' => $products, 
                    'sites' => $sites,
                    'cities' => $cities,
                ]);
    file_put_contents($settings['global']['data_storage'], $data);
    file_put_contents($settings['global']['updated_storage'], $currentUpdateTime);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <title>Prime5-Callcenter</title>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
    <!--[if lte IE 8]>
        <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/grids-responsive-old-ie-min.css">
    <![endif]-->
    <!--[if gt IE 8]><!-->
        <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/grids-responsive-min.css">
    <!--<![endif]-->
    <!--[if lte IE 8]>
        <link rel="stylesheet" href="css/marketing-old-ie.css">
    <![endif]-->
    <!--[if gt IE 8]><!-->
        <link rel="stylesheet" href="css/marketing.css">
    <!--<![endif]-->
    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <!--[if lt IE 9]>
        <script src="http://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="css/pikaday.css">
    <link rel="stylesheet" href="css/jquery.sidr.dark.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://apis.google.com/js/client:plusone.js" async defer></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="js/gl.js" ></script>
</head>
<body>
    <div id="sidr">
            <div class="content pure-u-1 is-center" id="gConnect">
                <button class="g-signin"
                    data-scope="https://www.googleapis.com/auth/userinfo.email"
                    data-clientId="636980332287-diqsbrq4odo3og8bi27gkgcu5vflgls7.apps.googleusercontent.com"
                    data-callback="onSignInCallback"
                    data-width="wide"
                    data-cookiepolicy="http://m.accesservice.ru">
                </button>        
            </div>
            <div hidden id="pForm">
                <?php require_once 'templates/productForm.html'; ?>
            </div>
    </div>
    <div style="background:#333;position:absolute;padding:0 20px;margin:20px 0">
        <h3>
            <a href="#sidr" id="form-link" style="text-decoration:none;color:#fff"><i class="fa fa-angle-double-right"></i> форма</a>
        </h3>
    </div>
    <iframe id="siteFrame" width="100%" height="98%" frameborder="0" marginheight="0" marginwidth="0" hspace="0" vspace="0"></iframe>
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.4/typeahead.bundle.min.js"></script>
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" src="js/jquery.sidr.min.js"></script>
    <script type="text/javascript" src="js/ru.js"></script>
    <script type="text/javascript" src="js/pikaday.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
</body>
</html>