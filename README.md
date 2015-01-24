# request_limits
Yii component that can Rate Limit of Requests from single IP

Overview
With this extension you can protect any page on your site from the frequent requests from the same IP in a short time. For example, bots can often send requests to your site, in the login form, registration form or send spam and so on.

Using this extension, you can set the time between requests and set time of limit access from IP. Limited ip will redirected to the error page.

Requirements 
Yii 1.1 or above

Usage 
It is easy to use.

 - place the extension folder request_limits in protected/components
 - create table in your database like this:
 
CREATE TABLE IF NOT EXISTS `users_ip` 
( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_ip` varchar(50) NOT NULL,
`time` bigint(20) NOT NULL,
`status` varchar(20) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

 - import the extension directory in your config file main.php

'import'=>array(
        'application.models.*',
        'application.components.*',
        'application.controllers.*',
        'application.components.request_limits.*'
    ),
	
Installation is ready. Now to use the extension, in the place where you are going to use it write the following code there:

$checkIp=new FloodChecker(); // creating an instance of the  FloodChecker class
$checkIp->checkFloodFromIp(); // calling a function that is will do all work
That's all. This code you can paste any place in views and controllers. The user or bot IPs who was limited will be redirected to an error page. You can change error view in request_limits/views/index.php

The default time between 2 requests from the same IP equals 2000 milliseconds. The default time of limit access for IP equals 900000 milliseconds (15 minutes). 
You can set your values. For example:

$checkIp=new FloodChecker();
$checkIp->setSafeTime(1000); // time between 2 requests
$checkIp->setLimitTime(600000); // time of limit access for IP
$checkIp->checkFloodFromIp();

The function checkFloodFromIp() return true if IP is not limited and false if IP is limited. So you can use this function in the operators. like this:

$checkIp=new FloodChecker();
if($checkIp->checkFloodFromIp()){
   //to do something
}