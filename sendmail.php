<?php
$settings = require_once 'settings.php';
if(isset($_POST))
{
    $siteName = $_POST['siteName'];

    // $text = "namestore[VALUE]:".$siteName.
    // "[NEXTPAIR]num[VALUE]: 0".
    // "[NEXTPAIR]date[VALUE]: ".date('Y-m-d H:i:s').
    // "[NEXTPAIR]Mail[VALUE]:".
    // "[NEXTPAIR]tel[VALUE]:".$_POST['phoneNumber'].
    // "[NEXTPAIR]adress[VALUE]:".$_POST['orderAddress'].
    // "[NEXTPAIR]comment[VALUE]:".$_POST['logistComment'].
    // "[NEXTPAIR]namehom[VALUE]:".$_POST['clientName'].
    // "[NEXTPAIR] 1".
    //     "[NEXTPRODUCT]name[VALUEP]:Apple iPhone 4 16Gb white".
    //         "[NEXTPAIRP]Kol[VALUEP]:1".
    //         "[NEXTPAIRP]price[VALUEP]:7890 руб.".
    //         "[NEXTPAIRP]".
    // "[NEXTPRODUCTEND]"
    // "Сумма 7890 руб. Курьером до двери в пределах МКАД 390 руб. Итого 8280 руб.";

    $text = "Оператор: ".$_POST['operatorId']."<br>".
            "Имя: ".$_POST['clientName']."<br>".
            "Город: ".$_POST['clientCity']."<br>".
            "Телефон: ".$_POST['phoneNumber']."<br>".
            "Дата доставки: ".$_POST['orderDate']."<br>".
            "Время доставки: ".$_POST['orderTime']."<br>".
            "Адрес доставки: ".$_POST['orderAddress']."<br>".
            "Стоимость доставки: ".$_POST['shippingCost']."<br>".
            "Комментарий для логиста: ".$_POST['logistComment']."<br>";

    $productArr = $_POST['products'];
    $products = "Товары: <br>";
    foreach ($productArr as $value)
    {
        $products .= $value."руб <br>";
    }
    $text .= $products;

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'manager@tradecompany.su';
    $mail->Password = 'ue3KpTBD';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->From = $_POST['mail-from'];
    $mail->FromName = 'Внешний оператор';
    $mail->addAddress($_POST['mail-to']);
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = $siteName.' заказ от внешнего оператора';
    $mail->Body    = $text;
    // // // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    if(!$mail->send())
    {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
    else
    {
        echo 'ok';
    }
}