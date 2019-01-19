# Symfony3_PaypalREST_DoctrineStorage
Test PaypalREST Payum with symfony 3
This project is just for testing PaypalRest payment with Symfony 3&...4(probably not tested)
There is several 'dump()' to show you what's appening when you are executing the process or cancel the payment.

see 'services'
status and original factory had been overrided

app.payum.paypal_rest_doctrine_gateway_factory:
        class: Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder
        arguments: [AppBundle\Payum\PaypalRestDoctrine\PaypalRestDoctrineGatewayFactory]
        tags:
            - { name: payum.gateway_factory_builder, factory: paypal_rest_doctrine }

    
app.payum.action.status:
    class: AppBundle\Payum\PaypalRestDoctrine\Action\StatusAction
    public: true
    tags:
        - { name: payum.action, factory: paypal_rest_doctrine}
        
        
On the root path app_dev.php you will find two links to test paypal api calls: 
one with filesystemStorage and an other one with doctrine storage.
- Don't forget to put your id and your PaypalRest key 'sandbox'
- Don't forget the path to the sdk_config.ini in the config folder
RealBC
bcomandon@real-click.fr
