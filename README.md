# nextypay-php-gateway

This php payment gateway is based for E-Commerce system of Nexty Plattform (Demo : http://45.77.248.53).  
You can register as Merchant [http://45.77.248.53/register.php](http://45.77.248.53/register.php).
Your customers should be redirected with post data [http://45.77.248.53/request.php](http://45.77.248.53/request.php).

## Table of Contents

-   [Install](#install)
-   [Cronjob](#cronjob)
-   [Register](#register)
-   [Post](#api)
-   [Contribute](#contribute)
-   [License](#license)

## Install

- php install.php

- Max scanned blocknumber init as max blocknumber on blockchain. All payments before wont be scanned.

## Cronjob

The file loader.php scanns 30(default) blocks from the next block of the max scanned block and could be stopped earlier if 
the scanning block is not created.

Bash files (loop the loader.php):
-  scan.sh (Linux System)
-  scanWin.sh (Windows system with mingw or cygwin)

Cron guides:
-  Linux: cronjob/crontab
-  Windows: https://docs.microsoft.com/en-us/previous-versions/windows/it-pro/windows-server-2008-R2-and-2008/cc748993(v=ws.11)


## Register

-  Register form with a wallet as primary key for a merchant
-  Submit
-  QR to pay with Nexty mobile apps appears. The tiny amount is created as random.
-  Wait till the gateway catchs the transaction as verify. The status of Merchant changes from 'Pending' to 'Comfirmed'.
-  Crytop Keys will be created for each merchant and be saved in database.
-  Only gateway admin has the right to the change status to 'Accepted'.

All request of merchants with status 'Pending' or 'Comfirmed' will be rejected.

## Post

The most E-commerce plattform has sample payment gateway with callback. If you want to use this Nextypay gateway, you need follow belows:  
Post params : 
-   wallet : the registered wallet, used to identify merchant to merchant
-   callbackUrl : your callback url, that listen the gateway to get payment status
-   returnUrl : redirect your customers back to your shop with this link
-   shopId : a merchant could have many shops with only one wallet. shopId should be in payment plugin setting
-   orderID : unique in the database of your shop
-   amount : value to send
-   tokenKey(coming soon) : to be sure that msg sent from the merchant

-   currency(optional) : NTY used by default if the param not included in request
-   minBlockDistance (optional) : the gateway only accepts the transactions, that has the distance to current block greater than this param
-   toWallet(optional) : the wallet used for this order, gateway use the registered wallet if this param not inlcuded in request

Procedure :
-    Request primary key = (wallet, shopId, orderId)
-    Merchant redirect customers to gateway with post data(example : http://45.77.248.53/request.php)
-    Gateway checks tokenKey and ignore if the key invalid
-    If request already exist with status 'Pending', display QR code for customers to pay with Nexty mobile apps (Android + Ios)
-    Else create new request in database with init status 'Pending'
-    On server side, when the total value of loaded transactions for this request enough, change the status of request to 'Paid'
-    For every paid request but not comfirmed, server sends msg to merchant with orderId and paidAmount
-    Once merchant reveice the msg, change the status of request to 'comfirmed', redirect customers to returnUrl

#### Table of Contents

### constructor

#### Examples

## Contributing

Contributions are welcome!

1.  Fork it.
2.  Create your feature branch: `git checkout -b my-new-feature`
3.  Commit your changes: `git commit -am 'Add some feature'`
4.  Push to the branch: `git push origin my-new-feature`
5.  Submit a pull request :D

Or contact me [a issue](https://github.com/bestboyvn87).

## License

Licensed under the MIT License.