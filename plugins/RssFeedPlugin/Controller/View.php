<?php
/**
 * RssFeedPlugin for phplist.
 *
 * This file is a part of RssFeedPlugin.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2015 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

namespace phpList\plugin\RssFeedPlugin\Controller;

use phpList\plugin\Common\Controller;
use phpList\plugin\Common\DB;
use phpList\plugin\Common\IPopulator;
use phpList\plugin\Common\Listing;
use phpList\plugin\RssFeedPlugin;
use WebblerListing;

/**
 * This class is a subclass of the base controller class that implements the CommonPlugin_IPopulator
 * interface to show rss items.
 */
class View extends Controller implements IPopulator
{
    /*
     *    Protected methods
     */
    protected function actionDefault()
    {
        $listing = new Listing($this, $this);
        echo $listing->display();

        return;
    }

    /*
     *    Public methods
     */
    public function __construct()
    {
        parent::__construct();
        $this->dao = new RssFeedPlugin\DAO(new DB());
    }

    /*
     * Implementation of CommonPlugin_IPopulator
     */

    public function populate(WebblerListing $w, $start, $limit)
    {
        $loginId = $_SESSION['logindetails']['superuser'] ? '' : $_SESSION['logindetails']['id'];

        $w->setTitle($this->i18n->get('RSS'));

        foreach ($this->dao->feedItems($start, $limit, $loginId, false) as $row) {
            $key = $row['id'];
            $w->addElement($key, $row['url']);
            $w->addColumn($key, 'Title', $row['title']);
            $w->addColumn($key, 'Published', $row['published']);
            $w->addColumn($key, 'Content', substr($row['content'], 0, 100));
        }
    }

    public function total()
    {
        $loginId = $_SESSION['logindetails']['superuser'] ? '' : $_SESSION['logindetails']['id'];

        return $this->dao->totalFeedItems($loginId);
    }
}
