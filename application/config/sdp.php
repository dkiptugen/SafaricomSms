<?php
$config['test'] =  (object)array(
									'accessTokenUrl'        =>   'https://dtsvc.safaricom.com:8480/api/auth/login',
								    'refreshTokenUrl'       =>   'https://dtsvc.safaricom.com:8480/api/auth/RefreshToken',
			                        'subscriptionUrl'       =>   'https://dtsvc.safaricom.com:8480/api/public/SDP/activate',
			                        'unSubscriptionUrl'     =>   'https://dtsvc.safaricom.com:8480/api/public/SDP/deactivate',
								    'sendSmsUrl'            =>   'https://dtsvc.safaricom.com:8480/api/public/SDP/sendSMSRequest',
								    'BulkSendUrl'           =>   'https://dtsvc.safaricom.com:8480/api/public/CMS/bulksms',
									'cpid'                  =>    001
								);

$config['prod'] =   (object)array(
									'accessTokenUrl'        =>   'https://dsvc.safaricom.com:9480/api/auth/login',
									'refreshTokenUrl'       =>   'https://dsvc.safaricom.com:9480/api/auth/RefreshToken',
									'subscriptionUrl'       =>   'https://dsvc.safaricom.com:9480/api/public/SDP/activate',
									'unSubscriptionUrl'     =>   'https://dsvc.safaricom.com:9480/api/public/SDP/deactivate',
									'sendSmsUrl'            =>   'https://dsvc.safaricom.com9480/api/public/SDP/sendSMSRequest',
									'BulkSendUrl'           =>   'https://dsvc.safaricom.com:9480/api/public/CMS/bulksms',
									'cpid'                  =>    002
								);
