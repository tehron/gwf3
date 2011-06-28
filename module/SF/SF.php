<?php

class SF {
	
	private static $_Lang = NULL, $_User = NULL, $_layoutcolor = array(), $_formaction = array();
	
	public function __construct() {
		self::$_User = GWF_User::getStaticOrGuest();
		self::$_Lang = new GWF_LangTrans('lang/SF/SF');
		$this->addMainTvars(array('SF' => $this));
	}

	public function sendHeader($header) { return header($header) ? true : false; }
	
	############
	## CONFIG ##
	############
	public function cfgdefaultColor() { return GWF_SF_DEFAULT_COLOR; }
	public function cfgCookieTime() { return time()+60*60*24*30; }
	public function cfgLayoutcolor($lc) { return GWF_SF_Utils::save_guest_setting('layoutcolor', $lc, $this->cfgdefaultColor(), $this->cfgCookieTime()); }

	# nothing to worry about
	public function onIncludeBeef() { return GWF_Website::addJavascript('inc3p/beef/hook/beefmagic.js.php'); }
	public function getWelcomeComment() { 
		if(GWF_Webspider::getSpider() !== false) {
			return '<!--Hi '.htmlspecialchars(GWF_Webspider::getSpider()->displayUsername()).'-->'; 
		} elseif(self::$_User->isAdmin()) {
			return '<!-- Welcome Back Admin! -->';
		}
		return "<!--Can you see the sourcecode? Great! -->\0\n";
	}
	
	############
	## SET UP ##
	############
	public function setLayoutColor(array $lc) { self::$_layoutcolor = $lc[$this->cfgLayoutcolor($lc)]; }
	public function setDoctype($doctype) { return GWF_Doctype::setDoctype($doctype);}
	public function setMeta($meta) { return GWF_Website::addMetaA($meta); }
	public function setFormActions(array $fa) { self::$_formaction = $fa; }
	public function setLayoutCSS($css) { return GWF_Website::addCSSA($css, '/templates/'.$this->getDesign().'/'.$this->getLayout().'/', '.css'); }
	public function setDesignCSS($css) { return GWF_Website::addCSSA($css, '/templates/'.$this->getDesign().'/css/', '.css'); }
	public function setPageTitlePre($title) { return GWF_Website::setPageTitlePre($title); }
	public function setPageTitle($title) { return GWF_Website::setPageTitle($title); }
	public function setPageTitleAfter($title) { return GWF_Website::setPageTitleAfter($title); }
	public function addMainTvars(array $tVars) { return GWF_Template::addMainTvars($tVars); }

	#############
	## GETTING ## // functions for Smarty
	#############
	public function isDisplayed($key) { 
		switch($key) {
			case 'navileft': return $this->is_navi_displayed('navileft');
			case 'naviright': return $this->is_navi_displayed('naviright');
			case 'details': return $this->is_details_displayed();
			case 'shell': return $this->is_shell_displayed();
			case 'base': return $this->is_base_displayed();
		}
	}
	public function lang($key, $args=NULL) { return self::$_Lang->lang($key, $args); }
	public function displayNavi($side) { return SF_Navigation::display_navigation($side); }
	public function getGreeting() { return GWF_SF_Utils::greeting(); }
	public function getMoMe($mome)
	{
		$class = $_GET['mo'].'_'.$_GET['me'];
		return $class === $mome;
	}
	public function getIndex($except = '') {
		
		$back = GWF_WEB_ROOT.'index.php?';
		foreach($_GET as $k => $v) {
			if(is_array($except)) {
				foreach ($except as $trash => $exc) {
					if($exc != $k) {
						$back .= htmlspecialchars($k).'='.htmlspecialchars($v).'&amp;';
					}
				}
			} else {
				if($except != $k) {
					$back .= htmlspecialchars($k).'='.htmlspecialchars($v).'&amp;';
				}
			}
			
		}
		return $back;
	}
	public function getLayoutcolor($key = 'base_color') { return self::$_layoutcolor[$key]; }
	public function getServerName() { return $_SERVER['SERVER_NAME']; }
	public function getPath() { return htmlspecialchars($_SERVER['SCRIPT_NAME']); }
	public function getDesign() { return GWF_SF_DEFAULT_DESIGN; }
	public function getLayout() { return GWF_SF_DEFAULT_LAYOUT; }
	public function getFormaction($key) { return self::$_formaction[$key]; }
	public function getLastURL() { return GWF_Session::getLastURL(); }
	public function getDayinfos() {
		$lang = self::$_Lang;
		$args = array(
			'dayname' => $lang->langA('daynames', date('w')),
			'day' => date('w'),
			'month' => date('n'),
			'monthname' => $lang->langA('monthnames', date('n')),
			'year' => date('Y')
		);
		return $lang->lang('today_is_the', $args);
	}

	public function is_details_displayed() { return false; }
	public function is_shell_displayed() { return !$this->getMoMe('SF_Shell'); }
	public function is_base_displayed() { return (isset($_GET['fancy']) || $_GET['me'] == 'Challenge') ? false : true; }
	public function is_navi_displayed($navi) {
		$mods = array('SF', 'PageBuilder', 'GWF', GWF_DEFAULT_MODULE);
		switch(GWF_SF_Utils::save_guest_setting($navi, array('hidden' => true, 'shown' => true), 'default', $this->cfgCookieTime())) {
			case 'shown' : return true;
			case 'hidden': return false;
			default: 
				if(!in_array(Common::getGet('mo', GWF_DEFAULT_MODULE), $mods)) {
					return false;
				} else return (true === self::$_User->isAdmin());
		}
	}
	
	public function getColorCSS() {
		
		$tVars = array(
			'tpl' => array('layout' => $this->getLayout(), 'design' => $this->getDesign().'/'),
			'color' => array(
				'border_light' => $this->getLayoutcolor('border_light'),
				'border_dark' => $this->getLayoutcolor('border_dark'),
				'design_dark' => $this->getLayoutcolor('design_dark'),
				'design_light' => $this->getLayoutcolor('design_light'),
				'base_color' => $this->getLayoutcolor('base_color'),
			)
		);
		return $tVars;
	}
	
	public function getIP($cmp = NULL) { return $cmp == NULL ? GWF_SF_SurferInfos::get_ipaddress() : $cmp === GWF_SF_SurferInfos::get_ipaddress(); }
	public function getOS($cmp = NULL) { return $cmp == NULL ? GWF_SF_SurferInfos::get_operatingsystem() : $cmp === GWF_SF_SurferInfos::get_operatingsystem(); }
	public function getBrowser($cmp = NULL) { return $cmp == NULL ? GWF_SF_SurferInfos::get_browser() : $cmp === GWF_SF_SurferInfos::get_browser(); }
	public function getProvider($cmp = NULL) { return $cmp == NULL ? GWF_SF_SurferInfos::get_provider() : $cmp === GWF_SF_SurferInfos::get_provider(); }
	public function getCountry() {}
	public function getHostname() { return GWF_SF_SurferInfos::get_hostname(); }
	public function getReferer() { return GWF_SF_SurferInfos::get_referer(); }
	public function getUserAgent() { return GWF_SF_SurferInfos::get_useragent(); }
}
