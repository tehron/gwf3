<?php
final class Redmond_AresDwarf_I extends SR_TalkingNPC
{
	public function getName() { return 'Aron'; }
	public function onNPCTalk(SR_Player $player, $word, array $args)
	{
		$quest1 = SR_Quest::getQuest($player, 'Redmond_AresDwarf_I');
		$num = $quest1->getNeededAmount();
		$has1 = $quest1->isInQuest($player);
		$done1 = $quest1->isDone($player);
		
		if ($player->hasTemp('Redmond_AresDwarf_I_sr'))
		{
			if ($word === 'yes')
			{
				$this->rply('accept');
// 				$this->reply('Haha ok chummer... I will wait for your delivery.');
				$quest1->accept($player);
				$player->unsetTemp('Redmond_AresDwarf_I_sr');
			}
			else if ($word === 'no')
			{
				$player->unsetTemp('Redmond_AresDwarf_I_sr');
				$this->rply('laters');
// 				$this->reply('Well, if you change your mind.. Come back later.');
			}
			else
			{
				$this->rply('confirm');
// 				$this->reply('Do you accept the quest, chummer?');
			}
			return true;
		}
		
		if ($word === 'shadowrun')
		{
			if ($has1 || $done1) {
				$this->checkQuest1($player, $quest1);
			}
// 			elseif ($player->hasTemp('Redmond_AresDwarf_I_sr')) {
// 				$this->rply('confirm');
// // 				$this->reply('Do you accept the quest, chummer?');
// 			}
			else {
				$this->rply('quest1');
				$this->rply('quest2');
				$this->rply('quest3', array($num));
				$this->rply('quest4');
				$this->rply('quest5');
// 				$this->reply('You are a newbie runner, eh?');
// 				$this->reply('Chummer... Listen... We regulary get robbed by the cyberpunks.');
// 				$this->reply("The worst thing is they keep robbing even cheap things, like unstatted knives. If you can help us and bring me $num unstatted knives I would be very happy, as I plan to master the skill of knife-throwing.");
// 				$this->reply("You can remove stats from an item at the local blacksmith.");
// 				$this->reply('If you could help help us we will reward you gracefully.');
				$player->setTemp('Redmond_AresDwarf_I_sr', true);
			}
		}
		
		else
		{
			if ($has1 === true) {
				$this->checkQuest1($player, $quest1);
			}
			elseif ($word === 'yes' || $word === 'no') {
				$this->rply('default2');
// 				$this->reply('We have the finest weapons and utilities. Low prices and high damage =)');
			}
			else {
				$this->rply('default1');
// 				$this->reply("Hello my friend, are you interested in fine Ares armoury?");
			} 
		}
	}
	
	private function checkQuest1(SR_Player $player, SR_Quest $quest1)
	{
		if ($quest1->isDone($player))
		{
			return $this->rply('thx2');
// 			return $this->reply('We have enogh knives now to play with. Thanks again for your help.');
		}
		
		$have = $quest1->getAmount();
		$need = $quest1->getNeededAmount();
		$have = $quest1->giveQuesties($player, $this, 'Knife', $have, $need, true);
		
		if ($have >= $need)
		{
			$quest1->onSolve($player);
		}
		else
		{
			$this->rply('pls', array($need-$have));
// 			$this->reply(sprintf('Could you please bring me %d more knives?', $need));
		}
		
		return true;
	}
}
?>