class action{
    public function check_requirements($requirements){
        if(is_array($requirements)){
        $errors = [];
            foreach($requirements as $requirement){
                $result = $requirement();
                if($result != true){
                    $errors.add($result);
                }
            }
        }
    }
}