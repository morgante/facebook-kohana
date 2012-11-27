
<?php defined('SYSPATH') OR die('No Direct Script Access');
 
Class Controller_Facebook extends Controller
{
    public function action_callback()
    {
		$facebook = Facebook::factory();
    }

}