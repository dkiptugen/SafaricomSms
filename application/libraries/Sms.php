<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms
	{
		protected $ci;
		protected $cfg;
		protected $apiUsername;
		protected $apiPassword;
	    public function __construct()
		    {
		        $this->ci = & get_instance();
			    $this->ci->config->load('sdp', TRUE);
			    if(ENVIRONMENT == 'testing')
			        {
			        	$this->cfg          =   $this->ci->config->item('sdp')['test'];
				        $this->apiUsername  =   'sdp';
				        $this->apiPassword  =   'sdpuser@12345';

			        }
			    else
				    {
					    $this->cfg          =   $this->ci->config->item('sdp')['prod'];
					    $this->apiUsername  =   'sdp';
					    $this->apiPassword  =   'sdpuser@12345';
				    }
		    }
		public function getAccessTokens()
			{
				try
					{
						$curl = curl_init();
						curl_setopt( $curl, CURLOPT_URL, $this->cfg->accessTokenUrl );
						curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
																				'Content-Type:application/json',
																				'X-Requested-With:XMLHttpRequest'
																			) );

						$curl_post_data =   array(
													'username' => $this->apiUsername,
													'password' => $this->apiPassword
												);

						$data_string    =   json_encode( $curl_post_data );

						curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
						curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
						curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
						curl_setopt( $curl, CURLOPT_POST, true );
						curl_setopt( $curl, CURLOPT_POSTFIELDS, $data_string );

						$curl_response = curl_exec( $curl );
						if ( $curl_response )
							{
								$results = json_decode( $curl_response );

								return $results->token;
							}
						else
							{
								return 'Curl error: ' . curl_error( $curl );
							}
					}
				catch(Exception $e)
					{
						return $e->getMessage();
					}
			}
		public function getRefreshToken($refreshToken)
			{
				try
					{
						$ch = curl_init();
						$headers    =   array(
												'Content-Type:application/json',
												'X-Requested-With:XMLHttpRequest'
											);
						$body       =   json_encode([]);
						curl_setopt( $ch, CURLOPT_URL, $this->cfg->refreshTokenUrl);
						curl_setopt( $ch, CURLOPT_HTTPHEADER,   $headers);
						curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
						curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
						curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
						curl_setopt( $ch, CURLOPT_POSTFIELDS, $body );
						curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
						$result = curl_exec($ch);
						curl_close($ch);
						if ( $result )
							{
								$results = json_decode( $result );

								return $results->token;
							}
						else
							{
								return 'Curl error: ' . curl_error( $ch );
							}
					}
				catch(Exception $e)
					{
						return $e->getMessage();
					}
			}
		public function curlRequest($url,$data)
			{
				try
					{
						$authorization = "X-Authorization: Bearer " . $this->getAccessTokens();
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
						$result = curl_exec($ch);
						curl_close($ch);
						return json_decode($result);
					}
				catch (Exception $e)
					{
						return array('error'=>$e->getMessage());
					}
			}
		public function bulkSms($new_sdp_data)
			{
				$json_data  =   array(
										"timeStamp" => time(),
										"dataSet" => [
														array(
																"userName"          =>  $new_sdp_data['userName'],
																"channel"           =>  "sms",
																"packageId"         =>  $new_sdp_data['packageId'],
																"oa"                =>  $new_sdp_data['oa'],
																"msisdn"            =>  $new_sdp_data['msisdn'],
																"message"           =>  $new_sdp_data['message'],
																"uniqueId"          =>  "2500688298721",
																"actionResponseURL" =>  $this->cfg->sendSmsCallback
															)
													]
									);
				$data       =   json_encode($json_data);
				$result     =   $this->curlRequest($this->cfg->BulkSendUrl,$data);
				if ($result)
					{
						return $result;
					}
				return FALSE;
			}
		public function sendSms($dt)
			{
				$data   =   array(
									[ "name" => "LinkId"    , 'value' => $dt['linkid']    ],
									[ 'name' => 'Msisdn'    , 'value' => $dt['phone']     ],
									[ 'name' => 'Content'   , 'value' => $dt['msg']       ],
									[ 'name' => 'OfferCode' , 'value' => $dt['offercode'] ],
					                [ 'name' => 'CpId'      , 'value' => $this->cfg->cpid ]
								);

				$json_data  =   array(
										"requestId"         => $dt['id'],
										"responseId"        => "10189519182688287792",
										"responseTimeStamp" => date('YmdHis'),
										"channel"           => "3",
										"sourceAddress"     => $this->ci->input->ip_address(),
										"operation"         => "SendSMS",
										"requestParam"      =>  array("data" => $data)

									);
				return $this->curlRequest($this->cfg->sendSmsUrl,json_encode($json_data));
			}
		public function subscription($dt)
			{
				$data   =   array(
									[ 'name' => 'OfferCode' , 'value' => $dt['offercode'] ],
									[ 'name' => 'Msisdn'    , 'value' => $dt['phone']     ],
									[ "name" => "Language"  , 'value' => $dt['linkid']    ],
									[ 'name' => 'CpId'      , 'value' => $this->cfg->cpid ]
								);
				$json_data  =   array(
										"requestId"         =>  $dt['id'],
										"requestTimeStamp"  =>  date('YmdHis'),
										"channel"           =>  "SMS",
										"operation"         =>  "ACTIVATE",
										"requestParam"      =>  array("data" => $data)
									);
				return $this->curlRequest($this->cfg->subscriptionUrl,json_encode($json_data));
			}
		public function unsubscription($dt)
			{
				$data   =   array(
									[ 'name' => 'OfferCode' , 'value' => $dt['offercode'] ],
									[ 'name' => 'Msisdn'    , 'value' => $dt['phone']     ],
									[ 'name' => 'CpId'      , 'value' => $this->cfg->cpid ]
								);
				$json_data  =   array(
										"requestId"         =>  $dt['id'],
										"requestTimeStamp"  =>  date('YmdHis'),
										"channel"           =>  "SMS",
										"sourceAddress"     =>  $this->ci->input->ip_address(),
										"operation"         =>  "DEACTIVATE",
										"requestParam"      =>  array("data" => $data)
									);
				return $this->curlRequest($this->cfg->unSubscriptionUrl,json_encode($json_data));
			}
		public function cpNotification($dt,$additionaldata = NULL)
			{
				$data   =   array(
									[ 'name' => 'OfferCode' , 'value' => $dt['offercode'] ],
									[ 'name' => 'Msisdn'    , 'value' => $dt['phone']     ],
									[ 'name' => 'Command'   , 'value' => $dt['command']   ]
								);
				$json_data  =   array(
										"requestId"         =>  $dt['id'],
										"requestTimeStamp"  =>  date('YmdHis'),
										"operation"         =>  "CP_NOTIFICATION",
										"requestParam"      =>  array("data" => $data)
									);
				if(is_array($additionaldata))
					{
						foreach($additionaldata as $key => $value)
							{
								$json_data["additionalData"][] = array( 'name' => $key , 'value' => $value );
							}
					}
			}
	}