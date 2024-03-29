<?php
// +---------------------------------------------------------------------------+
// | Seagull 1.0                                                               |
// +---------------------------------------------------------------------------+
// | AnchorTOC.php                                                             |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006 Demian Turner                                          |
// |                                                                           |
// | Author: Demian Turner <demian@phpkitchen.com>                             |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This library is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU Library General Public               |
// | License as published by the Free Software Foundation; either              |
// | version 2 of the License, or (at your option) any later version.          |
// |                                                                           |
// | This library is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU         |
// | Library General Public License for more details.                          |
// |                                                                           |
// | You should have received a copy of the GNU Library General Public         |
// | License along with this library; if not, write to the Free                |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
// |                                                                           |
// +---------------------------------------------------------------------------+
//
require_once SGL_CORE_DIR . '/Category.php';

/**
 * Menu_AnchorTOC class
 *
 * Creates a list of anchor links, like a Table of Contents
 *
 * @package navigation
 * @author  Demian Turner <demian@phpkitchen.com>
 * @version 0.1
 * @access  public
 * @since   PHP 4.1
 */
class Menu_AnchorTOC extends SGL_Category
{
    var $module = 'navigation';

    function __construct($options, $conf)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->conf = $conf;
    }

    function render($id = 0)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $result = $this->getChildren($id);
        $listString = '';
        $listString .= '<ul>';
        for ($x = 0; $x < count($result); $x++) {
            //  only generate link if node if leaf
            if ($this->isBranch($result[$x]['item_category_id'])) {
                $listString .= '<li>' . $result[$x]['label'] . "\n";
            } else {
                $link = str_replace(' ', '_', $result[$x]['label']);
                $listString .= "<li><a href='#" . $link . "'>" . $result[$x]['label'] . "</a>\n";
            }
            // if branch then recurse
            if ($this->isBranch($result[$x]['item_category_id'])) {
                $listString .= $this->render($result[$x]['item_category_id']);
            }
        }
        $listString .=  '</ul>';
        return $listString;
    }
}
?>