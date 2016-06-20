<?php
/*
php300系统类
系统类已继承云类库,其它类直接继承system_class或model里的m_class即可
以下为系统扩展
本文件内容不可删除
*/
class system_class extends php300_class{
	
	public function __construct(){
		if(C('autoconnect')){
			DB() -> open(C('DB'));
		}
	}
	
	public function display($names='index'){	//模板渲染
		$tmp_names = $names.$GLOBALS['PHP300_CON']['TMP_TAIL'];
		if(!$GLOBALS['TMP']->templateExists($tmp_names)){
			$this->get_error_page('找不到'.$tmp_names.'模板页');
		}
		$GLOBALS['TMP']->display($names.$GLOBALS['PHP300_CON']['TMP_TAIL']);
	}
	
	public function get_vars($vername=''){	//获取指定模板全部变量
		return $GLOBALS['TMP']->get_template_vars($vername);
	}
	
	public function get_error_page($error_txt = '未知错误',$tmp_names = 'php300_tmp/php300_error'){	//调用错误页
		if($GLOBALS['PHP300_CON']['LOGS']){
			$this->record_logs($error_txt);
		}
		$this->del_tmp();
		$this->set_var('error_txt',$error_txt);
		$this->display($tmp_names);
		exit();
	}
	
	public function set_var($varname='',$val=''){	//设置模板变量
		$GLOBALS['TMP']->assign($varname,$val);
	}
	
	public function del_vars(){		//清除全部变量赋值
		$GLOBALS['TMP']->clearAllAssign(); 
	}
	
	public function del_var($varname=''){	//清除指定变量赋值,参数可以是个string也可以是个array
		$GLOBALS['TMP']->clearAssign($varname);
	}
	
	public function del_tmp($tmpname=''){	//清除缓存文件,参数为空的话则清除全部缓存文件
		if($tmpname!=''){
			//$this->del_cache($tmpname);
			$tmpname = $tmpname.$GLOBALS['PHP300_CON']['TMP_TAIL'];
		}else{
			unset($tmpname);
		}
		$GLOBALS['TMP']->clearCompiledTemplate($tmpname);
	}
	
	public function del_cache($tmpname=''){	//清空缓存,非缓存文件
		if($tmpname!=''){
			$GLOBALS['TMP']->cache->clear($tmpname.$GLOBALS['PHP300_CON']['TMP_TAIL']);
		}else{
			$GLOBALS['TMP']->cache->clearAll();
		}
	}
	
	public function com_tmp($force = true){	//编译全部模板文件,参数为true的话则只编译修改过的文件,为false的话则强制编译全部文件
		$GLOBALS['TMP']->compileAllTemplates($GLOBALS['PHP300_CON']['TMP_TAIL'],$force);
	}
	
	public function	fetch($names='index'){
		$tmp_names = $names.$GLOBALS['PHP300_CON']['TMP_TAIL'];
		if(!$GLOBALS['TMP']->templateExists($tmp_names)){
			$this->get_error_page('找不到'.$tmp_names.'模板页');
		}
		return $GLOBALS['TMP']->fetch($names.$GLOBALS['PHP300_CON']['TMP_TAIL']);
	}
	
	public function __call($method, $args) {
		$error = "貌似找不到<b>".$method."</b>方法哟~!";
		$this->get_error_page($error);
		return false;
 	}
 	
 	public function php300_error_handler($errno, $errstr, $errfile, $errline ) {
		if($errno<8){
				$error_txt = "错误级别：$errno<br />错误信息：$errstr<br />错误文件：$errfile<br />错误行数：$errline";
				$GLOBALS['system']->get_error_page($error_txt);
		}
	}
	
	public function record_logs($errortxt){
		if($errortxt!=''){
			$errortxt = str_replace('<br />','，',$errortxt)." --- 生成时间：".date('20y-m-d H:i:s',time())."\r\n\r\n";
			error_log($errortxt,3,'./logs/error_logs.log');
		}
	}
}