# Magento 2 PagaLu payment gateway


[**Magento 2 Create Payment Method**]

Based on (https://www.mageplaza.com/magento-2-create-payment-method/) proves that store admin has rights to generate as many payment methods as they need when your store is based on Magento 2 platform, an great era of ecommerce architecture. Depending on the customer's requirement, you probably plug it in your list of the existing payment method. The additional payment methods surely bring the diversity of customer choice when they proceed to checkout on your site. On the other's hands, multiple payment method is the great strategy to reach out the global marketplace.

Payment Gateway integration in Magento 2 stores. After launching the new payment methods, you will find and configure it according the path `Admin panel > Stores > Settings > Configuration > Sales > Payment Methods`. There, admin possibly assigns a payment method to specific shipping method, this means they will work in pairs when enabling.

### To Use the PLUGIN:
Obtain Pagalu Token first for your environment by reaching out to team [at] robobo [dot] org


### Installation instructions:

    The plugin can be installed from the master branch on the following location (github): https://github.com/roboboinc/pagalu-magento
    One of the simplest form of setting up the current version is to:
        git clone the repository inside {magento_root_directory}/app/code/magento/
        Rename the directory to PagaLuPaymentGateway
        Run: bin/magento setup:upgrade
    The plugin should now be installed and you can move onto the admin page of your magento shop and enter the PagaLu Token provided by going to:
        Stores > Configuration > Sales > Payment Methods
        PagaLu Gateway should show under Other Payment Methods
        Ensure it is set Enabled (Yes)
        Enter the PagaLu API Key  Provided
        Ensure that Debug is set to Yes (the API Key provided only works on Sandbox environment)
        Save Config

With these steps, it should be possible to go and start testing making purchases. 

Developed and tested against Magento ver. 2.3.3

## To UPGRADE the plugin
1 - cd into: {magento_root_directory}/app/code/magento/PagaLuPaymentGateway
2 - git pull origin master
3 - Run bin/magento setup:upgrade
    3.1 - example: we used a VPS, and via ssh, we run `../../../../bin/magento setup:upgrade` from the directory we are in, or alternatively: `{magento_root_directory}/bin/magento setup:upgrade`
4 - Test in the front-end

## Step 1: Create payment method module
TO BE UPDATED...

