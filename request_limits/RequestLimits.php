<?php 
 
    class RequestLimits{
        
        private $open = 'open';
        private $limited = 'limited';
        private $ip=null;
        private $userIpData;
        private $limitTime=900000; // in milliseconds
        private $safeTime=2000; // in milliseconds
        private $previousRequestTime;
      
        
        public function getIp(){
            $this->ip=$_SERVER["REMOTE_ADDR"];
            return $this->ip;
        }
        
        public function setLimitTime($time){
            $this->limitTime=$time;
        }
        
        public function setSafeTime($time){
            $this->safeTime=$time;
        }
        
        public function isNewIp(){
            $this->userIpData=Yii::app()->db->createCommand()
                                ->select('*')
                                ->from('users_ip')
                                ->where('user_ip=:_ip', array(':_ip'=>$this->getIp()))
                                ->queryRow();
            
            if($this->userIpData==null)
            {   
                $insert=Yii::app()->db->createCommand()
                        ->insert('users_ip',array(
                                                    'user_ip'=>$this->getIp(),
                                                    'time'=>round(microtime(true)*1000),
                                                    'status'=>$this->open
                                                    ));
                return true;
            }else{
                if(!$this->isLimited())
                {   
                    $this->previousRequestTime=$this->userIpData['time'];
                    $update=Yii::app()->db->createCommand()
                            ->update('users_ip',array('time'=>round(microtime(true)*1000)),'user_ip=:_ip',
                            array(':_ip'=>$this->getIp()));
                }
                return false;
            }
            
        }
        
        public function isLimited(){
            if($this->userIpData['status']==$this->limited) return true;
            if($this->userIpData['status']==$this->open) return false;
        }
        
        public function isBigFloodOfRequests(){
            $this->userIpData=Yii::app()->db->createCommand()
                                ->select('time')
                                ->from('users_ip')
                                ->where('user_ip=:_ip', array(':_ip'=>$this->getIp()))
                                ->queryRow();
            $timeBetweenTwoRequests=$this->userIpData['time'] - $this->previousRequestTime;
            if($timeBetweenTwoRequests<$this->safeTime) return true;
            else return false;
        }
        
        public function isEndTimeOfLimit(){
            if($this->isLimited()){
                $leftTime=round(microtime(true)*1000) - $this->userIpData['time'];
                if($leftTime>$this->limitTime){
                    $update=Yii::app()->db->createCommand()
                            ->update('users_ip',array('time'=>round(microtime(true)*1000), 'status'=>$this->open),'user_ip=:_ip',
                            array(':_ip'=>$this->getIp()));
                    return true;
                }else{
                    return false;
                }
            }
        }
        
        public function limitTheIp(){
            $update=Yii::app()->db->createCommand()
                            ->update('users_ip',array('status'=>$this->limited),'user_ip=:_ip',
                            array(':_ip'=>$this->getIp()));
            if(Yii::app()->request->isAjaxRequest){
                
               Yii::app()->controller->renderPartial('application.components.request_limits.views.index');
               Yii::app()->end();
            }else{
                Yii::app()->controller->render('application.components.request_limits.views.index');
                Yii::app()->end();
            }
            
            
        }
    } 