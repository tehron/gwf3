<?php
final class Slaytags_Admin extends GWF_Method
{
	public function getUserGroups() { return array(GWF_Group::ADMIN, GWF_Group::STAFF); }
	
	public function execute(GWF_Module $module)
	{
		if (isset($_POST['recalc']))
		{
			$this->onRecalcTags($module).$this->templateAdmin($module);
		}
		
		return $this->templateAdmin($module);
	}
	
	private function templateAdmin(Module_Slaytags $module)
	{
		$form_actions = $this->formActions($module);
		$tVars = array(
			'form_actions' => $form_actions->templateY('Actions'),
		);
		return $module->template('admin.tpl', $tVars);
	}
	
	private function formActions(Module_Slaytags $module)
	{
		$data = array(
			'recalc' => array(GWF_Form::SUBMIT, 'RecalcTags'),
		);
		return new GWF_Form($this, $data);
	}
	
	private function onRecalcTags(Module_Slaytags $module)
	{
		$form_actions = $this->formActions($module);
		if (false !== ($error = $form_actions->validate($module)))
		{
			return $error;
		}
		
		$table = GDO::table('Slay_Song');
		if (false === ($result = $table->select('*', '', 'ss_id ASC', NULL)))
		{
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		while (false !== ($song = $table->fetch($result, GDO::ARRAY_O)))
		{
			$song instanceof Slay_Song;
			if (false === $song->computeTags())
			{
				die('OOOOOOPS!!!!!!');
			}
		}
		
		$table->free($result);
		
		return $module->message('rehashed!');
	}
}
?>