<?php
return 	[
	//应用ID,您的APPID。
	'app_id' => "2016092600598015",

	//商户私钥
	'merchant_private_key' => "MIIEowIBAAKCAQEA546mOZ4NvtCP/SXChZYXwtb8twivD5FC2/FMhjSKZcEhmx61Lupc2x9+S9UFaFsM5rJ8oDUdIdAb+UBx/wTH30fTcaDW76UXH+eAUz0wGJ5CFZWda/bFbpDfTNLWe5OK0UaCRYp2bvaN5MOryPaScO3/By3r65X4Pc8whkbF6M8J6xD6sG91pRXQMBsMJGcvlJZ4v3bqV21qVMqgRpYAO9QujiloGC7Z9VDwSJPxcHE0hehXHdVj4qGqdpg26xvv9dJXgIuIjRMyZdFmlADsbyZwBJ5mDNRa3PMQZSRLtr94Ak3rmDemgyuXsgcJ65kadXVl63WvN1aKYUtHViTqjQIDAQABAoIBAFnOv7WVEWuyZEYggQNipTCSRU2P9qWpBy9atP1QH2U7tJb/H/JkP0NVQZyTsBc7SROooiFYuEXG+zJ+xjfMWODGcMGfSf2EICXlmaWvE5pYtvS8JGQdP5GUaqbXFwyyuqJUIG5sN2buBTZ2lYfJ6h4WkFTugJPMhI0kcIODq3qRJRG8Lw+eNCSAdovjBwj5aVci8PU98yecgVh178a8VFAutKopg/SozEX6jNizk2nCoWWurT6oy47BOb9iki6p7PZ8VAEK8IjNWGzYsUbpiJ3Ibo+aKdXTK/Z+o8cmV9fsPpAR53PtsCh9AjnDL5ZN5DYNuR/nM9yitx/arZuI2xkCgYEA/oagDiVCBM41MA7jBSm1RM0QPHJ9nmz7/0nesBtMTlMtTIfL2R8O+bBpdyuSP5Tp5NmTS5BMmyP2x2Yit6idIcU+sg19syp+uFG9AgS7MOok4vi+c8dhcKtR3G95lFkE441EeXrRWWYSKRxXD2k67Ed6DXHT5nl+GnNrpKcbf5MCgYEA6OX4LXebEAD9pCzONKn/LusYLBA7n8bAsTEUgRKSMy7Hv4LEoU08JdDZ0+0piW/SiND40fhac92o9+LfJw9zjJRFybGCNID/WCfEuaYGqzsHlyhuOTc832utJSgQXnizHz2eRMEIEggopy3qYVhvW3t0CCXVO3Vx6SUwVadrAV8CgYEArkzjLsy0+TV9lvua295EFGmZxMti2ysnJxwfM9p91uV/D/muz3FFgBgfIUzlSkVgJygYhpa630MxrDt1avA9KvM4jBZRDnD7dp4DIW68AjNWL1jIk+DmCZJI7Pwp9j0/69TCH67Lzrznkt+lfxhBEa2hF7PruNlaUiLYhFkrq6ECgYBlraEAGKu1/Pi/aGBOVbl2/mR7OEsPonIV6duNzlHNzqqeTbUSxulviRLQl0X/HrbHzJQU91xmIWvXbVEHeJN12HXvbHPwADF6h0I32ugmcYsKzzfv+tG0qQnaovcih4eMKBak5bFkTfORqVYeUcGsKuWpePrzDn9Azl+fxYvN+QKBgB5PuR3vzL0C4eziJbKIpA2YhavjDFGBGvvdmpjvXz04jBq/7EMrw3yUSVEjNNOuBIwZaOK5GYk2AqEvv9DtMN9lLZCDVYdVReXjpqDGuFANuFf5sWh8zWv+a6smlL1V73VN4K6jrpsuM2NeuSgkRm/2xvFeJCTFaV3H7+h8o570",
	
	//异步通知地址
	'notify_url' => "http://39.96.169.182",
	
	//同步跳转
	'return_url' => "http://www.1810.com/index/order/returnUrl",

	//编码格式
	'charset' => "UTF-8",

	//签名方式
	'sign_type'=>"RSA2",

	//支付宝网关
	'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

	//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
	'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyk5QFdNg2iui3/crRcQVBB1MC+kkJeG0AFCTCmhfmLnbWQl5RyUyoVrJ379tQSIwZOrMl7Z6IrR0yRvowu9xnIc/cwfHeHV427AIUJ93ORozBDOhpDSHeFGmJlNLsUrvLQB5pB8mesBy7U8I5TW+xSYjsEA+IvxpmBuVUU4awEGhYWiud6JtNZCakW8Z4sOg8hhLXqdekQSiLiVPCX4e2ddiNX+PSTkCbFthiCqZorFszHnSNeqNbu+JMhHUzT3ybIwBNv7Bno0Q/nfVZ3+DKARcgBzSEncnlHgvl3eTi4BtNJPAQy0c/8f/FcmPGsAEzzZ6tThZi2rW64V6Gyp2sQIDAQAB",
];
