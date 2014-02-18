<?php
class gmapViewGmp extends viewGmp {
    public static $gmapApiUrl = "https://maps.googleapis.com/maps/api/js?&sensor=false&language=";
    public static $_mapsData;
    
    public function getAllMaps(){
      
    }
    public function showAllMaps($data,$fromAjax=false){
        $this->assign('mapsArr',$data);  
        $this->assign('fromAjax',$fromAjax);  
        
        frameGmp::_()->addStyle('gmapCss',$this->getModule()->getModPath().'css/map.css');
        return parent::getContent('allMapsContent');
        
   }
   public function addMapData($params){
        if(empty(self::$_mapsData)){
            self::$_mapsData = array();
        }
        self::$_mapsData[] =$params;
   }
    public function drawMap($params){
            $mapObj = frameGmp::_()->getModule('gmap')->getModel()->getMapById($params['id']);
            $shortCodeHtmlParams = array('width','height','align');
            foreach($shortCodeHtmlParams as $code){
                if(isset($params[$code])){
                    $mapObj['html_options'][$code]= $params[$code];
                }
            }
            $shortCodeMapParams = array('map_display_mode','language','type','zoom','enable_zoom',
                                        'enable_mouse_zoom');
            foreach($shortCodeMapParams as $code){
                if(isset($params[$code])){
                    $mapObj['params'][$code]= $params[$code];
                }
            }            
            if($mapObj['params']['map_display_mode']=='popup'){
                frameGmp::_()->addScript('bpopup',GMP_JS_PATH."/bpopup.js");      
            }
            frameGmp::_()->addScript("google_maps_api_".$mapObj['params']['language'], self::$gmapApiUrl.$mapObj['params']['language']);
            frameGmp::_()->addStyle("map_params", $this->getModule()->getModPath().'css/map.css');
            $this->addMapData($mapObj);
			
			$indoWindowSize  = utilsGmp::unserialize(frameGmp::_()->getModule("options")->getModel("options")->get("infowindow_size"));
			$this->assign("indoWindowSize",$indoWindowSize);
			
            $this->assign('currentMap', $mapObj);
			return parent::getContent('mapPreview');
    }
    public function addNewMapData(){
            $maps  = $this->getModel()->getAllMaps(true);
            
            $marker_opts=  frameGmp::_()->getModule('marker')->getModel()->constructMarkerOptions(); 
            $map_opts = $this->getModel()->constructMapOptions();
           
            $this->assign('mapArr'      , $maps);
            $this->assign('marker_opts' , $marker_opts);
            $this->assign('map_opts'    , $map_opts );
            
            return parent::getContent('newMapData');
    }
    public function editMaps(){
            return parent::getContent('editMaps');            
    }
    public function addMapDataToJs(){
        frameGmp::_()->addJSVar('map.options', 'gmpAllMapsInfo', self::$_mapsData);
    }
}