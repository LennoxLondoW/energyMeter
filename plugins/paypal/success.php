<?php
require_once 'config.php';

$head = $error = $body = "";
$cc = [];

// Once the transaction has been approved, we need to complete it.
if (array_key_exists('paymentId', $_GET) && array_key_exists('PayerID', $_GET)) {
  $transaction = $gateway->completePurchase(array(
    'payer_id'             => $_GET['PayerID'],
    'transactionReference' => $_GET['paymentId'],
  ));

  //checking response frompaypal
  $response = $transaction->send();
  file_put_contents("response.txt", json_encode($response));
  if ($response->isSuccessful()) {
    // The customer has successfully paid.
    // file_put_contents("response.txt",json_encode($arr_body));
    // $arr_body = json_decode(file_get_contents("response.txt"),true);
    $arr_body = $response->getData();
    $payment_id = $arr_body['id'];
    $payer_id = $arr_body['payer']['payer_info']['payer_id'];
    $payer_email = $cc[0] = $arr_body['payer']['payer_info']['email'];
    $amount = $arr_body['transactions'][0]['amount']['total'];
    $currency = PAYPAL_CURRENCY;
    $payment_status = $arr_body['state'];
    // $token = htmlspecialchars($_GET['int_token']);


    $this_user = new App();
    $this_user->activeTable = "payment_receipts";
    $this_user->comparisons = [["payment_id", " = ",  $payment_id]];
    $this_user->joiners = [''];
    $this_user->order = " BY id DESC ";
    $this_user->cols = "*";
    $this_user->limit = 1;
    $this_user->offset = 0;

    $data = $this_user->getData();
    //pass test
    $gate = false;
    //table does not exist
    if ($this_user->database_error && !$this_user->is_table($this_user->activeTable)) {
      require_once '../../app/extensions/app.migration.php';
      /*creting migrations*/
      $migration = new Migration();
      /* adjust tables*/
      $migration->tables =
        [
          $this_user->activeTable =>
          [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "payment_id varchar(255) NOT NULL UNIQUE KEY",
            "payer_id varchar(255)  NOT NULL",
            "payer_email varchar(255) NOT NULL",
            "amount double(10,2) NOT NULL",
            "currency varchar(255) NOT NULL",
            "payment_status varchar(255) NOT NULL",
            "pay_day varchar(255) NOT NULL",
            "client_id varchar(255) NOT NULL",
          ]
        ];


      if ($migration->migrate(false) === "success") {
        $gate = true;
      } else {
        $head = 'Transaction Error';
        $body = "Hello, We are sorry to inform you that something wrong happened. Please contact admin for further information.<br> <b>Admin email</b>:" . paypal_admin_email . " <br><b>Admin contacts</b>:" . paypal_admin_contacts . " .<br>  <b>Client email</b>:" . $payer_email . " <br> Your payment id is " . $payment_id . ". Thanks for connecting with us";
      }
    } elseif (count($data) === 0) {
      $gate = true;
    } else {
      $head = 'Transaction Error';
      $body = "Hello, We are sorry to inform you that our system could not validate your previous transaction. Please contact admin for further information.<br> <b>Admin email</b>:" . paypal_admin_email . " <br><b>Admin contacts</b>:" . paypal_admin_contacts . " <br> <b>Client email</b>:" . $payer_email . " <br>  Thanks for connecting with us";
    }


    if ($gate) {
      //payment approval
      //day paid
      $pay_day = date("Y/m/d H:i:s");
      //document transaction
      $this_user->insertData[] = [
        'payment_id' => $payment_id,
        'payer_id' => $payer_id,
        'payer_email' => $payer_email,
        'amount' => $amount,
        'currency' => $currency,
        'payment_status' => $payment_status,
        'pay_day' => $pay_day,
        'client_id'=> isset($_GET['client_id']) && !empty($_GET['client_id'])? preg_replace("/[^A-Za-z0-9_.@]/", "", $_GET['client_id']): 'none'
      ];

      $save = $this_user->saveData(false);

      if ($save === "success") {
        $body = "Hello, We are delighted to inform you that your transaction has been successfull. The transaction id is: " . $payment_id . ". Thank you for connecting with us! <br> <b>Admin email</b>:" . paypal_admin_email . " <br><b>Admin contacts</b>:" . paypal_admin_contacts . " <br> <b>Client email</b>:" . $payer_email . ".";
        $head = "Money Received Successfully. ";
        if (isset($_GET['client_id']) && !empty($_GET['client_id'])) {
          //document receipt of everu user if need be
          $client_id = preg_replace("/[^A-Za-z0-9_.@]/", "", $_GET['client_id']);
          $this_user->activeTable = "single_payment_receipts";
          $this_user->comparisons = [["client_id", " = ",  $client_id]];
          $this_user->joiners = [''];
          $this_user->order = " BY id DESC ";
          $this_user->cols = "*";
          $this_user->limit = 1;
          $this_user->offset = 0;
          $this_user->database_error = false;

          $data = $this_user->getData();
          //pass test
          $gate = "invalid";
          $action = "new_insert";
          //table does not exist
          if ($this_user->database_error) {
            if (!$this_user->is_table($this_user->activeTable)) {
              require_once '../../app/extensions/app.migration.php';
              /*creting migrations*/
              $migration = new Migration();
              /* adjust tables*/
              $migration->tables =
                [
                  $this_user->activeTable =>
                  [
                    "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                    "client_id varchar(255)  NOT NULL UNIQUE KEY",
                    "amount float(10,2) NOT NULL",
                  ]
                ];


              if ($migration->migrate() === "success") {
                $gate = 0;
              }
            }
          } elseif (count($data) > 0) {
            $gate = is_numeric($data[0]["amount"]) ? $data[0]["amount"] : 0;
            $action = "new_update";
          } else {
            $gate = 0;
          }
          if (is_numeric($gate)) {
            //lets store this user data
            $new_amount = $gate + $amount;
            if ($action === "new_update") {
              $this_user->update_data = [
                'amount' => $new_amount,
              ];
              $this_user->updateData();
            } else {
              //new insert
              $this_user->insertData =
                [
                  [
                    'client_id' => $client_id,
                    'amount' => $new_amount,
                  ]
                ];

              $this_user->saveData();
            }
          }
        }

        if (function_exists('paypal_callback')) {
          paypal_callback($arr_body, $client_id);
        }
      }
      // duplicate transaction 
      elseif (strstr(strtolower($save), "duplicate entry") !== false) {
        $head = 'Transaction Error';
        $body = "Hello, this transaction has already been processed. Please contact admin for further information.<br> <b>Admin email</b>:" . paypal_admin_email . " <br><b>Admin contacts</b>:" . paypal_admin_contacts . " .<br> <b>Client email</b>:" . $payer_email . " <br> Your payment id is " . $payment_id . ". Thanks for connecting with us";
      }
      //unsuccessfull insertion
      else {
        $head = 'Transaction Error';
        $body = "Hello, We are sorry to inform you that something wrong happened. Please contact admin for further information.<br> <b>Admin email</b>:" . paypal_admin_email . " <br><b>Admin contacts</b>:" . paypal_admin_contacts . " .<br> <b>Client email</b>:" . $payer_email . " <br> Your payment id is " . $payment_id . ". Thanks for connecting with us";
      }
    }
    //no duplicates and token available in the database
    else {
      $head = 'Transaction Error';
      $body = "Hello, this transaction has already been processed. Please contact admin for further information.<br> <b>Admin email</b>:" . paypal_admin_email . " <br><b>Admin contacts</b>:" . paypal_admin_contacts . " .<br> <b>Client email</b>:" . $payer_email . " <br> Your payment id is " . $payment_id . ". Thanks for connecting with us";
    }
  } else {

    //remove the thing from 
    $head = 'Transaction Error';
    $body = "Hello, We are sorry to inform you that your previous transaction could not be verified.  " . $body =  $response->getMessage();
  }
} else {
  $head = 'Transaction is declined';
  $body = "Hello, We are sorry to inform you that your previous transaction has been rejected by paypal.";
}


if (!empty($head) && !empty($body)) {
  // lets send email
  $cc[1] = "joannwawira@gmail.com";
  $this_user->email_username = "Concerned Party";
  $this_user->email_message = $body . '<br>
                        <h3>Sender Details</h3>
                      <br>
                      <table>
                        <tbody>
                          <tr>
                            <td>Name:</td>
                            <td>' . $_SERVER['SERVER_NAME'] . '</td>
                          </tr>
                          <tr>
                            <td>Email: </td>
                            <td>' . paypal_admin_email . '</td>
                          </tr>
                          <tr>
                            <td>Contacts: </td>
                            <td>' . paypal_admin_contacts . '</td>
                          </tr>
                        </tbody>
                      </table>';
  $this_user->email_subject = $head;
  $this_user->email_to = paypal_admin_email;
  $this_user->email_cc = $cc;
  $this_user->email_attachment = false;
  // send email
  if (!$this_user->send_email()) {
    $body .= "<br> We are sorry that we are unable to send you a transactional email at the moment.";
  } else {
    $body .= "<br> We will send you a transactional email.";
  }
}

?>

<!DOCTYPE html>
<html>

<head>
  <title></title>
  <style type="text/css">
    html,
    body {
      font-family: "Arial", monospace !important;
      height: 100%;
      padding-top: 0vh;
      margin: 0px;
      line-height: 30px
    }

    .container {
      height: auto;
      width: 100%;
      justify-content: center;
      display: flex;
      padding-top: 0px;
      padding-bottom: 0px
    }

    .text {
      font-weight: bold;
      font-size: 28px;
      color: #303030;
      padding-bottom: 50px
    }

    .dud {
      color: #000000;
    }

    .white-bg {
      width: 100%;
      background: #fff;
      padding-top: 100px
    }

    .heart {
      display: block;
      width: 100%;
      text-align: center;
    }

    .swing {
      font-style: normal;
      font-size: 64px;
      color: #55ff33;
      display: block;
      width: 75px;
      height: 55px;
      margin: 20px auto
    }

    .red-green {
      color: #55ff33 !important
    }

    .red-redcheck {
      color: #f50 !important
    }

    .message {
      display: block;
      margin: 0px auto 50px auto;
      border-radius: 15px;
      max-width: 600px;
      width: 90%;
      padding: 40px;
      text-align: left;
    }

    .message .info {
      font-style: normal;
      font-size: 18px;
      color: #757575;
    }

    .message img {
      border-radius: 10px;
      margin-top: 20px;
      box-shadow: 0px 30px 30px rgba(0, 50, 0, 0.1);
      border: 1px solid #fff
    }

    .message span {
      padding: 0px 25px 0px 25px;
      text-align: center;
      display: block;
      font-family: "Arial", monospace !important;
    }

    .message span strong {
      color: #ff7700
    }

    @keyframes fadeInUp {
      0% {
        transform: translateY(100%);
        opacity: 0
      }

      20% {
        transform: translateY(90%);
      }

      20% {
        transform: translateY(10%);
      }

      100% {
        transform: translateY(0%);
        opacity: 1
      }
    }


    .starterPosition {
      animation: 3s ease-in-out 0 1 fadeInUp;
    }

    .fadeBg {
      background: #f7faf7;
    }

    @keyframes fadeInUp {
      0% {
        transform: translateY(100%);
        opacity: 0
      }

      100% {
        transform: translateY(0%);
        opacity: 1
      }
    }


    .starterPosition {
      animation: 3s ease-in-out 0 1 fadeInUp;
    }
  </style>
</head>

<body class="fadeBg">

  <div class="white-bg">

    <div class="heart starterPosition enderPosition">
      <span class="swing">
        <em class="green-check">&#33;</em>
    </div>

    <div class="container starterPosition">
      <div class="text"></div>
    </div>
  </div>

  <div class="message starterPosition">
    <div class="info">
      <img class="starterPosition" src="p.png" width="100%" />
      <br><br>
      <span class="starterPosition">
        <?php echo $body;  ?>
      </span>
    </div>
  </div>


  <script>
    // ——————————————————————————————————————————————————
    // TextScramble
    // ——————————————————————————————————————————————————

    class TextScramble {
      constructor(el) {
        this.el = el
        this.chars = '!<>-_\\/[]{}—=+*^?#________'
        this.update = this.update.bind(this)
      }
      setText(newText) {
        const oldText = this.el.innerText
        const length = Math.max(oldText.length, newText.length)
        const promise = new Promise((resolve) => this.resolve = resolve)
        this.queue = []
        for (let i = 0; i < length; i++) {
          const from = oldText[i] || ''
          const to = newText[i] || ''
          const start = Math.floor(Math.random() * 40)
          const end = start + Math.floor(Math.random() * 40)
          this.queue.push({
            from,
            to,
            start,
            end
          })
        }
        cancelAnimationFrame(this.frameRequest)
        this.frame = 0
        this.update()
        return promise
      }
      update() {
        let output = ''
        let complete = 0
        for (let i = 0, n = this.queue.length; i < n; i++) {
          let {
            from,
            to,
            start,
            end,
            char
          } = this.queue[i]
          if (this.frame >= end) {
            complete++
            output += to
          } else if (this.frame >= start) {
            if (!char || Math.random() < 0.28) {
              char = this.randomChar()
              this.queue[i].char = char
            }
            output += `<span class="dud">${char}</span>`
          } else {
            output += from
          }
        }
        this.el.innerHTML = output
        if (complete === this.queue.length) {
          this.resolve()
        } else {
          this.frameRequest = requestAnimationFrame(this.update)
          this.frame++
        }
      }
      randomChar() {
        return this.chars[Math.floor(Math.random() * this.chars.length)]
      }
    }

    // ——————————————————————————————————————————————————
    // Example
    // ——————————————————————————————————————————————————

    const phrases = [
      '<?php echo $head; ?>',
    ]

    const el = document.querySelector('.text')
    const fx = new TextScramble(el)

    let counter = 0
    const next = () => {
      fx.setText(phrases[counter]).then(() => {
        setTimeout(next, 200000)
      })
      counter = (counter + 1) % phrases.length
    }

    next();
  </script>
</body>

</html>