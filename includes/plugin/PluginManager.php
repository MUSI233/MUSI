<?php
/*
 本代码由 PHP代码加密工具 Xend [企业版](Build 5.05.63) 创建
 创建时间 2021-02-26 13:33:29
 严禁反编译、逆向等任何形式的侵权行为，违者将追究法律责任
*/

namespace plugin;if(!defined("IIIIIJ"))define("IIIIIJ","IIIIII");$GLOBALS[IIIIIJ]=explode("|J|0|z", "H*|J|0|z494A494A49");if(!defined(pack($GLOBALS[IIIIIJ][00],$GLOBALS[IIIIIJ][0x1])))define(pack($GLOBALS[IIIIIJ][00],$GLOBALS[IIIIIJ][0x1]), ord(69));if(!defined("IIIIJJ"))define("IIIIJJ","IIIIJI");$GLOBALS[IIIIJJ]=explode("|=|h|Z", "H*|=|h|Z494A49494A49|=|h|Z69735F6172726179|=|h|Z494A4949494A|=|h|Z636F756E74|=|h|Z494A49494949|=|h|Z6D6574686F645F657869737473|=|h|Z494A4A494A4A|=|h|Z696E5F6172726179|=|h|Z49494A4A4A|=|h|Z66696C655F657869737473");$GLOBALS[pack($GLOBALS[IIIIJJ][0],$GLOBALS[IIIIJJ][1])]=pack($GLOBALS[IIIIJJ][0],$GLOBALS[IIIIJJ][2]);$GLOBALS[pack($GLOBALS[IIIIJJ][0],$GLOBALS[IIIIJJ][3])]=pack($GLOBALS[IIIIJJ][0],$GLOBALS[IIIIJJ][04]);$GLOBALS[pack($GLOBALS[IIIIJJ][0],$GLOBALS[IIIIJJ][0x5])]=pack($GLOBALS[IIIIJJ][0],$GLOBALS[IIIIJJ][0x6]);$GLOBALS[pack($GLOBALS[IIIIJJ][0],$GLOBALS[IIIIJJ][07])]=pack($GLOBALS[IIIIJJ][0],$GLOBALS[IIIIJJ][0x8]);$GLOBALS[pack($GLOBALS[IIIIJJ][0],$GLOBALS[IIIIJJ][011])]=pack($GLOBALS[IIIIJJ][0],$GLOBALS[IIIIJJ][012]);class PluginManager{private $_listeners=[];public function __construct($plugins){if(!defined("IIJJI"))define("IIJJI","IIJIJ");$GLOBALS[IIJJI]=explode("|J|^|z", "H*|J|^|z706C7567696E732F|J|^|z6469726563746F7279|J|^|z616374696F6E2E706870|J|^|z49494A4A4A|J|^|z706C7567696E2E706870|J|^|z6D6F64656C2E706870");if(!empty($plugins)){foreach($plugins as $plugin){$p_path=ROOT . pack($GLOBALS[IIJJI][0],$GLOBALS[IIJJI][0x1]) . $plugin[pack($GLOBALS[IIJJI][0],$GLOBALS[IIJJI][2])]. DS;$file=$p_path . pack($GLOBALS[IIJJI][0],$GLOBALS[IIJJI][3]);if($GLOBALS[pack($GLOBALS[IIJJI][0],$GLOBALS[IIJJI][4])]($file)){include_once $file;}$file=$p_path . pack($GLOBALS[IIJJI][0],$GLOBALS[IIJJI][5]);if($GLOBALS[pack($GLOBALS[IIJJI][0],$GLOBALS[IIJJI][4])]($file)){include_once $file;}$file=$p_path . pack($GLOBALS[IIJJI][0],$GLOBALS[IIJJI][06]);if($GLOBALS[pack($GLOBALS[IIJJI][0],$GLOBALS[IIJJI][4])]($file)){include_once $file;}}}}public function destroy_register($IIJII,$IIIJJ=''){if(empty($IIIJJ)){if(isset($this->_listeners[$IIJII])){unset($this->_listeners[$IIJII]);return true;}}if(isset($this->_listeners[$IIJII][$IIIJJ])){unset($this->_listeners[$IIJII][$IIIJJ]);return true;}return false;}public function register($IJJJJI,$IJJJIJ,$IJJJII){if(!defined("IJJIJI"))define("IJJIJI","IJJIIJ");$GLOBALS[IJJIJI]=explode("|a|O|7", "H*|a|O|7494A4A494A4A");$IIIII=get_class($IJJJIJ);if(!$IIIII){return false;}$IJJJJJ=get_class_methods($IJJJIJ);if(!$GLOBALS[pack($GLOBALS[IJJIJI][0],$GLOBALS[IJJIJI][01])]($IJJJII,$IJJJJJ)){return false;}$this->_listeners[$IJJJJI][$IIIII]=[$IJJJIJ,$IJJJII];return true;}public function triggerAll($IJIJII,$IJIIJJ=[]){if(!defined("IIJJJJ"))define("IIJJJJ","IIJJJI");$GLOBALS[IIJJJJ]=explode("|p|;|`", "H*|p|;|`494A49494A49|p|;|`494A4949494A|p|;|`494A49494949");$IJJIII=[];if(isset($this->_listeners[$IJIJII])&&$GLOBALS[pack($GLOBALS[IIJJJJ][00],$GLOBALS[IIJJJJ][1])]($this->_listeners[$IJIJII])&&$GLOBALS[pack($GLOBALS[IIJJJJ][00],$GLOBALS[IIJJJJ][02])]($this->_listeners[$IJIJII])>(E_CORE_WARNING*78-2496)){foreach($this->_listeners[$IJIJII]as $IJIJJJ){$IJIJJI=$IJIJJJ[(E_CORE_WARNING*78-2496)];$IJIJIJ=$IJIJJJ[(0-1951+E_CORE_WARNING*61)];if($GLOBALS[pack($GLOBALS[IIJJJJ][00],$GLOBALS[IIJJJJ][03])]($IJIJJI,$IJIJIJ)){$IJJIII[get_class($IJIJJI)]=$IJIJJI->$IJIJIJ($IJIIJJ);}}}return $IJJIII;}public function trigger($IIJIII,$IIIJJJ,$IIIJJI=[]){if(!defined("IIIJIJ"))define("IIIJIJ","IIIJII");$GLOBALS[IIIJIJ]=explode("|=|G|X", "H*|=|G|X494A49494A49|=|G|X494A4949494A|=|G|X494A49494949");$IIJJIJ=[];if(isset($this->_listeners[$IIIJJJ])&&$GLOBALS[pack($GLOBALS[IIIJIJ][0],$GLOBALS[IIIJIJ][1])]($this->_listeners[$IIIJJJ])&&$GLOBALS[pack($GLOBALS[IIIJIJ][0],$GLOBALS[IIIJIJ][2])]($this->_listeners[$IIIJJJ])>((E_CORE_WARNING*47-1504)-576+E_CORE_WARNING*18)){foreach($this->_listeners[$IIIJJJ]as $IIJJII=>$IIJIJJ){$IIJIJI=$IIJIJJ[((E_CORE_WARNING*47-1504)-576+E_CORE_WARNING*18)];if($IIJIII!=$IIJJII)continue 1;$IIJIIJ=$IIJIJJ[(E_CORE_WARNING*77-2463)];if($GLOBALS[pack($GLOBALS[IIIJIJ][0],$GLOBALS[IIIJIJ][0x3])]($IIJIJI,$IIJIIJ)){$IIJJIJ=$IIJIJI->$IIJIIJ($IIIJJI);break 1;}}}return $IIJJIJ;}}
?>