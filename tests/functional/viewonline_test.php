<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

/**
* @group functional
*/
class phpbb_functional_viewonline_test extends phpbb_functional_test_case
{
	
	protected function get_forum_name_by_topic_id($topic_id)
	{
		$db = $this->get_db();

		// Forum info
		$sql =  'SELECT f.forum_name
			FROM ' . FORUMS_TABLE . ' f,' . TOPICS_TABLE . ' t
			WHERE t.forum_id = f.forum_id
				AND t.topic_id = ' . (int) $topic_id;
		$result = $db->sql_query($sql);
		$forum_name = $db->sql_fetchfield('forum_name');
		$db->sql_freeresult($result, 1800); // cache for 30 minutes

		return $forum_name;
	}

	protected function get_forum_name_by_forum_id($forum_id)
	{
		$db = $this->get_db();

		// Forum info
		$sql =  'SELECT forum_name
			FROM ' . FORUMS_TABLE . ' 
			WHERE forum_id = ' . (int) $topic_id;
		$result = $db->sql_query($sql);
		$forum_name = $db->sql_fetchfield('forum_name');
		$db->sql_freeresult($result, 1800); // cache for 30 minutes

		return $forum_name;
	}

	public function test_viewonline_reply_mode()
	{
		$this->create_user('viewonline-test-user');
		$this->logout();
		$this->login('viewonline-test-user');
		$crawler = self::request('GET', 'posting.php?mode=reply&t=1&sid=' . $this->sid);
		$this->assertContainsLang('POST_REPLY', $crawler->text());

		// Log in as another user
		$this->logout();
		$this->login();
		$crawler = self::request('GET', 'viewonline.php?sid=' . $this->sid);
		$this->assertContainsLang('FORUM_LOCATION', $crawler->text());

		// Make sure posting reply page is in the list
		$this->assertStringContainsString('viewonline-test-user', $crawler->text());
		$this->assertStringContainsString($this->lang('REPLYING_MESSAGE', $this->get_forum_name_by_topic_id(1)), $crawler->text());
	}

	public function test_viewonline_posting_mode()
	{
		$this->logout();
		$this->login('viewonline-test-user');
		$crawler = self::request('GET', 'posting.php?mode=post&f=1&sid=' . $this->sid);
		$this->assertContainsLang('POST_TOPIC', $crawler->text());

		// Log in as another user
		$this->logout();
		$this->login();
		$crawler = self::request('GET', 'viewonline.php?sid=' . $this->sid);

		// Make sure posting message page is in the list
		$this->assertStringContainsString('viewonline-test-user', $crawler->text());
		$this->assertStringContainsString($this->lang('POSTING_MESSAGE', $this->get_forum_name_by_forum_id(2)), $crawler->text());
	}

	public function test_viewonline_reading_topic()
	{
		$this->logout();
		$this->login('viewonline-test-user');
		$crawler = self::request('GET', 'viewtopic.php?t=1&sid=' . $this->sid);

		// Log in as another user
		$this->logout();
		$this->login();
		$crawler = self::request('GET', 'viewonline.php?sid=' . $this->sid);

		// Make sure the page is in the list
		$this->assertStringContainsString('viewonline-test-user', $crawler->text());
		$this->assertStringContainsString($this->lang('READING_TOPIC', $this->get_forum_name_by_topic_id(1)), $crawler->text());
	}

	public function test_viewonline_reading_forum()
	{
		$this->logout();
		$this->login('viewonline-test-user');
		$crawler = self::request('GET', 'viewforum.php?f=2&sid=' . $this->sid);

		// Log in as another user
		$this->logout();
		$this->login();
		$crawler = self::request('GET', 'viewonline.php?sid=' . $this->sid);

		// Make sure the page is in the list
		$this->assertStringContainsString('viewonline-test-user', $crawler->text());
		$this->assertStringContainsString($this->lang('READING_FORUM', $this->get_forum_name_by_forum_id(2)), $crawler->text());
	}
}
