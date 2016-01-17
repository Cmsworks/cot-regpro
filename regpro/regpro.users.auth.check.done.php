<?php

/**
 * [BEGIN_COT_EXT]
 * Hooks=users.auth.check.done
 * [END_COT_EXT]
 */

defined('COT_CODE') or die('Wrong URL.');

if($cfg['plugin']['regpro']['protime'] > 0)
{
	require_once cot_langfile('regpro', 'plug');

	$urr = $db->query("SELECT * FROM $db_users WHERE user_id=".$ruserid)->fetch();

	if($urr['user_logcount'] == 1)
	{
		$upro = cot_getuserpro($ruserid);
		$initialtime = ($upro > $sys['now']) ? $upro : $sys['now'];
		$rproexpire = $initialtime + $cfg['plugin']['regpro']['protime']*24*60*60;

		if($db->update($db_users,  array('user_pro' => (int)$rproexpire), "user_id=".(int)$ruserid))
		{
			cot_mail($urr['user_email'], $L['regpro_mail_subject'], sprintf($L['regpro_mail_body'], $urr['user_name']));
			cot_log("Pro for register");
			/* === Hook === */
	                foreach (cot_getextplugins('regpro.done') as $pl)
	                {
		            include $pl;
	                }
	                /* ===== */
		}
	}
}
