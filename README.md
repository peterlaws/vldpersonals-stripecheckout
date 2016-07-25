# vldPersonals Stripe Checkout
My Stripe Checkout Gateway for vldPersonals - It will process one-time payments, no subscriptions or saving cards and users to Stripe.

Installing is simple

1. Edit your theme's style.css and append the contents of the [style.css](./style.css) file;  

2. Download and upload [stripecards.png](./stripecasrds.png) image into your /templates/ your_template /media/ directory;  

3. I use the vldTheme theme system, which uses Bootstrap. My plugin uses the [Bootstrap3 Dialog](https://github.com/nakupanda/bootstrap3-dialog) plugin for retrieving details from Stripe, if you use bootstrap, please add  
https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css and https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js
to your themes footer.tpl file(s) after the main jquery js file;  

4. Download and upload [mod.stripe.php](./includes/modules/payments/mod.stripe.php) into your /includes/modules/payments/ directory;

5. Get the Stripe [php library](https://github.com/stripe/stripe-php), and upload it into the /includes/modules/payments/ directory where the <i>mod.stripe.php</i> file is, renaming the directory to <b>stripe</b> (important);

6. Now you are ready to activate and configure the plugin within vldPersonals admin...... Entering your 'Stripe Checkout Secret Key' and 'Stripe Checkout Publishable Key' where asked then 'Enable Stripe Checkout' to yes and your currency.... Hit submit..... and you're all set......

Note: Keep your Stripe Checkout Secret Key to yourself.


Support:
===========
I offer _limited_ support, I can:

1. Install it for you for a donation of £10 and above fee;

2. Help with any issues you have with it for a donation of £5 and above fee;  

If you would like support, please email Peter Laws at vldhelp@laws-hosting.co.uk.  

If you feel this plugin is useful, you can donate here: https://donations.laws.im/


You can ask questions in my forum - https://forums.laws.im/index.php/board,7.0.html
