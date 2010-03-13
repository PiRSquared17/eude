<?php
/**
 * $Author$
 * $Revision$
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 **/
if (!SCRIPT_IN) die('Need by included');


class tpl_menu {
/// déclaration de base...
    static private $instance;

    protected $out='';
    protected $left=0;

    public function __construct() {
    }

    /**
     * Définit le menu par défaut, pour ajouter des entrée voir la partie addons.
     * @return array menu
     */
    static function DefaultMenu() {

// 'menu_unique_id' => array('file/http','btn-img','btn_width','eval(some_php_for_axx)', $array_for_sub_menu_item),
// $array_for_sub_menu_item = array('file/http','btn-img','eval(some_php_for_axx)')
        return array(
        'carto' => array('%ROOT_URL%index.php','%IMAGES_URL%btn-cartographie.png',180,'DataEngine::CheckPerms(CXX_CARTOGRAPHIE)', array(
        array('%ROOT_URL%index.php','%IMAGES_URL%Btn-Tableau.png','DataEngine::CheckPerms(CXX_CARTOGRAPHIE)'),
        array('%ROOT_URL%Carte.php','%IMAGES_URL%Btn-Carte.png','DataEngine::CheckPerms(CXX_CARTE)'),
        ),
        ),
        'perso' => array('%ROOT_URL%Mafiche.php','%IMAGES_URL%Btn-Mafiche.png',125,'DataEngine::CheckPerms(CXX_PERSO)', array(
        array('%ROOT_URL%Mafiche.php','%IMAGES_URL%Btn-Mafiche.png','DataEngine::CheckPerms(CXX_PERSO)'),
        array('%ROOT_URL%Recherche.php','%IMAGES_URL%Btn-Recherche.png','DataEngine::CheckPerms(CXX_PERSO_RESEARCH)'),
        array('%ROOT_URL%ownuniverse.php','%IMAGES_URL%Btn-Production.png','DataEngine::CheckPerms(CXX_PERSO_OWNUNIVERSE)'),
        ),
        ),
        'addon' => array('', '%IMAGES_URL%btn-addon.png',180, 'addons::getinstance()->IncludeAddonMenu()', array() ),
        'admin' => array('%ROOT_URL%Membres.php','%IMAGES_URL%Btn-Membres.png',180,'DataEngine::CheckPerms(CXX_MEMBRES_HIERARCHIE)', array(
        array('%ROOT_URL%Membres.php','%IMAGES_URL%btn-hierarchie.png','DataEngine::CheckPerms(CXX_MEMBRES_HIERARCHIE)'),
        array('%ROOT_URL%editmembres.php','%IMAGES_URL%btn-editions.png','DataEngine::CheckPerms(CXX_MEMBRES_EDIT)'),
        array('%ROOT_URL%stats.php','%IMAGES_URL%btn-statistiques.png','DataEngine::CheckPerms(CXX_MEMBRES_STATS)'),
        array('%ROOT_URL%EAdmin.php','%IMAGES_URL%Btn-Admin.png','DataEngine::CheckPerms(CXX_MEMBRES_ADMIN)'),
        ),
        ),
        'forum' => array(Config::GetForumLink(),'%IMAGES_URL%Btn-Forum.png',125,'Config::GetForumLink() != ""', null),
        'logout' => array('%ROOT_URL%logout.php','%IMAGES_URL%btn-logout.png',180,'DataEngine::CheckPerms(AXX_GUEST)', null),
        );
    }

    static public function Gen_Menu($menu) {
        return self::getinstance()->_Gen_Menu($menu);
    }

    protected function _Gen_Menu($menu) {
        $this->left=5;
        $this->out = <<<HEADER
<div id="menu" style="z-index:4; font-size:10px; width:98%; height:40px; background-image:url('%IMAGES_URL%Fond-Menu.png'); background-repeat: repeat-x; position:absolute; top:0px; margin-left:auto; margin-right:auto;">&nbsp;</div>

HEADER;
        foreach($menu as $menu_id => $main_menu) {
            if (!eval("return {$main_menu[3]};")) continue; // pas d'autorisation sur ce menu.
            $submenu = false;

            if (is_array($main_menu[4])) {
                $sub_items=array();
                foreach($main_menu[4] as $sub_menu) {
                    if (!eval("return {$sub_menu[2]};")) continue; // pas d'autorisation sur ce sous_menu.
                    $sub_items[] = $this->sub_menu_item($sub_menu[0],$sub_menu[1]);
                }
                if (count($sub_items)>0) {
                    $submenu = true;
                    $this->sub_menu($menu_id, $main_menu[2], $sub_items);
                }
            }
            $this->main_menu($menu_id, $main_menu[0], $main_menu[1], $main_menu[2], $submenu);
        }
        return $this->out.'<br/><br/>';
    }

    protected function main_menu($id, $url, $img, $width, $submenu=true) {
        $sm = ($submenu) ? ' OnMouseOver="montre2(\''.$id.'\');" OnMouseOut="cache2(\''.$id.'\');"': '';
        $link = ''; $link2 = '';
        if ($url) {
            $link = (stristr($url,"http") === false) ? "<a href='$url'>": "<a href='$url' target='_blank'>";
            $link2 = '</a>';
        }
        $l = ($this->left).'px'; $w=($width).'px';

        $this->out .= <<<EOF
<div id="mm_$id" style="z-index:7; left:$l; width:$w; top:5px; position:absolute;"$sm>
    <center>$link<img src="$img" />{$link2}</center>
</div>
EOF;
        $this->left += $width+5;
    }

    protected function sub_menu($id, $width, $content) {
        $content = implode("<br/>\n", $content);
        $left = ($this->left-5).'px'; $width=($width+10).'px';
        $this->out .= <<<EOF
<div id="sm_$id"  onmouseover="montre2('$id');" onmouseout="cache2('$id');" style="z-index:10; font-size:10px; top:30px; left:{$left}; width:$width; background-color: black; visibility:hidden; position:absolute; text-align:center">$content</div>

EOF;
    }

    protected function sub_menu_item($url, $img) {
        if ($url != "")
            return "<a href=\"$url\"><img src=\"$img\"></a>";
        else
            return "<img src=\"$img\">";
    }

    /**
     * @return tpl_menu
     */
    static public function getinstance() {
        if ( ! self::$instance )
            return new self();
        else
            return self::$instance;
    }

}