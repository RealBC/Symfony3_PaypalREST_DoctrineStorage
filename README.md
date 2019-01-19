<<<<<<< HEAD
test_payum
==========

A Symfony project created on November 23, 2018, 8:56 am.
=======
# Symfony3_PaypalREST_DoctrineStorage

Test PaypalREST Payum with symfony 3

This project is just for testing PaypalRest Symfony 3 and PayumBundle


There is several 'dump()' to show you what's appening when you are executing the process or cancel the payment.

see 'services'
'CaptureAction' 'StatusAction' 'factory' had been overrided for Doctrine Storage

        
On the root path app_dev.php you will find two links to test paypal api calls: 
one with filesystemStorage and an other one with doctrine storage.

- Don't the tiny cmd: php bin/console doctrine:schema:update
- Don't forget to put your id and your PaypalRest key 'sandbox'
- Don't forget the path to the sdk_config.ini in the config folder

have a good day !

RealBC
bcomandon@real-click.fr
>>>>>>> 95b0c7b8aa996c20ab8507e3f72fccb4376c5fb9
