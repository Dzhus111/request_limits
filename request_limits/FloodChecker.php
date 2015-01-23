<?php 
    
    class FloodChecker extends RequestLimits{
        
        public function checkFloodFromIp(){
            if($this->isNewIp()){
                return true;
            }else{
                if(!$this->isLimited()){
                    if($this->isBigFloodOfRequests())$this->limitTheIp();
                    return false;
                }else{
                    if($this->isEndTimeOfLimit()) return true;
                    else{
                        if(Yii::app()->request->isAjaxRequest){
                           Yii::app()->controller->renderPartial('application.components.request_limits.views.index');
                           Yii::app()->end(); 
                        }else{
                            Yii::app()->controller->render('application.components.request_limits.views.index');
                            Yii::app()->end();
                        }
                        
                        return false;
                    }
                    return false;
                }
            }
        }
        
    }