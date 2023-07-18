<?php
class DriverBackTogetherService{
	public function insertTogetherComment($initiator_id,$together_id,$lng,$lat){
        DriverBackTogether::model()->insertTogetherComment($initiator_id,$together_id,$lng,$lat);
    }
	public function findTogether($user_id){
        DriverBackTogether::model()->findTogether($user_id);
    }
}
