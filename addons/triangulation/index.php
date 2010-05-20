<?php
/**
 * @Author: Wilfried.Winner
 * $Revision: Triangulation v1.3
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 **/

require_once('../../init.php');
require_once(INCLUDE_PATH.'Script.php');
require_once(CLASS_PATH.'map.class.php'); // requis par ownuniverse

// Check si activ�
if (!addons::getinstance()->Get_Addons('triangulation')->CheckPerms()) DataEngine::NoPermsAndDie();
if (isset($_POST['sys1']) && isset ($_POST['dist1'])&& isset($_POST['sys2']) && isset ($_POST['dist2']) && isset($_POST['sys3']) && isset ($_POST['dist3'])) {
    $_SESSION['coord_syst1'] = gpc_esc($_POST['sys1']);
    $_SESSION['ccdistance1'] = gpc_esc($_POST['dist1']);
    $_SESSION['coord_syst2'] = gpc_esc($_POST['sys2']);
    $_SESSION['ccdistance2'] = gpc_esc($_POST['dist2']);
    $_SESSION['coord_syst3'] = gpc_esc($_POST['sys3']);
    $_SESSION['ccdistance3'] = gpc_esc($_POST['dist3']);
}
$coordvalid = 1;

$syst1= $_SESSION['coord_syst1'];
$dist1= $_SESSION['ccdistance1'];
$syst2= $_SESSION['coord_syst2'];
$dist2= $_SESSION['ccdistance2'];
$syst3= $_SESSION['coord_syst3'];
$dist3= $_SESSION['ccdistance3'];

require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = 'EU2: Addons triangulation';


$out = <<<form


<font color='red', size='3',>
<b>Attention!!</b> <br />Plus les distances en <i>[Pc]</i> s�parant les centres de communications sont grandes plus la triangulation sera bonne <i>(min 5[Pc])</i></font>
<br />
<br />

<font color=white>


<form name="settings" action="index.php" method="POST">
Pour remplir les cases ci-dessous, allez dans le jeu et dans le batiment centre de communication,<br />
Puis faites des recherches avec le nom ou la plan�te du joueur (dans le jeu dans deux ou trois syst�mes diff�rents). <br />
Remplissez ensuite le champs reserv�s ci-dessous avec le coodonn�e du syst�me dans lequel se trouve le centre de communication.<br />
Remplissez aussi le champs reserv�s ci-dessous � la distance s�parant le centre de communication � la plan�te sans son unit� <i>[Pc]<i />.
<br />
<br />
<i> Coordonn�es du syst�me du centre --------------------------- Distance en [Pc] s�parant le centre de <br />
de communications ----------------------------------------------- communication de la plan�te � trianguler</i><br /><br />
    Coord. du syst. du centre de com. 1 : <input type="text" name="sys1" value="{$_SESSION['coord_syst1']}" size="16" />
    distance sans unit� entre CC1 et la plan�te : <input type="text" name="dist1" value="{$_SESSION['ccdistance1']}" size="16" /><br />
    Coord. du syst. du centre de com. 2 : <input type="text" name='sys2' value="{$_SESSION['coord_syst2']}" size="16" />
    distance sans unit� entre CC2 et la plan�te : <input type="text" name="dist2" value="{$_SESSION['ccdistance2']}" size="16" /><br />
    Coord. du syst. du centre de com. 3 : <input type="text" name="sys3" value="{$_SESSION['coord_syst3']}" size="16" />
    distance sans unit� entre CC3 et la plan�te : <input type="text" name="dist3" value="{$_SESSION['ccdistance3']}" size="16" /><br />

    <br /> <font>L'exactitude des champ ci-dessus ne sont pas v�rifi�s, et consid�r�s syst�matiquement comme bon.</font'><br />

    <br /><font color=green size='4'><b>Triangulateur version beta </b> </font><br /><br />

    D�terminer les coordonn�es de la plan�te du joueur en cliquant sur le bouton "<b>Trianguler</b>". <br /><br />
    <input type="submit" value="Trianguler" /><br /> <br />

</form>
form;
$tpl->PushOutput($out); // ajoute le texte pr�c�dant � la sortie qui sera affich�.

if ($coordvalid) {
//Debut de la triangulation
/*
Dans le centre de communication la distance en Pc s�parant deux syst�me se calcul en faisant en utilisant la formule math�matique de la distance.
Ainsi soit un syst�me A(ax,ay) et syst�me B(bx,by) la distance s�parant des syst�me est D = Arrondie{[(ax-bx)^2+(ay-by)^2]^(1/2),0} en posant
une condition sur les coordonn�es 00 = 100; Prenons pour exemple le syst�me A = 2456 ==> (ax=24, ay=56) et B = 3400 ==> (bx=34, by=00)
avec la condition sur les coordonn�es 00 �gale � 100, B = 3400 ==> (bx=34, by=100). La distance s�parant les deux syst�me vaut
D = Arrondie{[(24-34)^2+(56-100)^2]^(1/2),0} = 45[pc].
A parte de cette �quation et la mesure des distances obtenues avec au moins 2 au plus 3 centres de communitation du jeu EU2 se trouvant dans des
syst�me diff�rents nous pouvons retrouver les coordonn�es de la plan�te dont on a d�terminer la distance � l'aide des centres de communications.
*/
$systeme = array();
$flag = 3;
$Rep = 0;

//L'une des coodonnees ou distance invalide. Triangulation faite en se basant sur quatre donn�es
if (($syst1 >= 1) && ($syst1 <= 10000) && ($dist1 >= 1) && ($dist1 <= 140) &&
                                                ($syst1 >= 1) && ($syst1 <= 10000) && ($dist1 >= 1) && ($dist1 <= 140) &&
                                                ($syst3 >= 1) && ($syst3 <= 10000) && ($dist3 >= 1) && ($dist3 <= 140) )
        {
                $cmpt = 1;//Pour compter le nombre de syst�me probable
                $flag = 2;
                for ($i = 1; $i <= 10000; $i++)
                {
                        if ((calcdist($i,$syst1) == $dist1) && (calcdist($i,$syst2) == $dist2)&& (calcdist($i,$syst3) == $dist3))
                        {
                                $systeme[$cmpt++] = $i;
                                $flag = 0;
                        }
                }$Rep = 0;
        }
        else if(($syst1 <= 0) && ($syst1 >= 10000) || ($dist1 < 1) && ($dist1 > 140) &&
                                        ($syst2 >= 1) && ($syst2 <= 10000) && ($dist2 >= 1) && ($dist2 <= 140) &&
                                        ($syst3 >= 1) && ($syst3 <= 10000) && ($dist3 >= 1) && ($dist3 <= 140))
        {
                $cmpt = 1;
                $flag = 2;
                for ($i = 1; $i <= 10000; $i++)
                {
                        if ((calcdist($i,$syst2) == $dist2) && (calcdist($i,$syst3) == $dist3))
                        {
                                $systeme[$cmpt++] = $i;
                                $flag = 1;
                        }
                }$Rep = 0;

        }else if (($syst2 <= 0) && ($syst2 >= 10000) || ($dist2 < 1) && ($dist2 > 140) &&
                                                ($syst1 >= 1) && ($syst1 <= 10000) && ($dist1 >= 1) && ($dist1 <= 140) &&
                                                ($syst3 >= 1) && ($syst3 <= 10000) && ($dist3 >= 1) && ($dist3 <= 140) )
        {
                $cmpt = 1;
                $flag = 2;
                for ($i = 1; $i <= 10000; $i++)
                {
                        if ((calcdist($i,$syst1) == $dist1) && (calcdist($i,$syst3) == $dist3))
                        {
                                $systeme[$cmpt++] = $i;
                                $flag = 1;
                        }
                }$Rep = 0;

        }else if (($syst3 <= 0) && ($syst3 >= 10000) || ($dist3 < 1) && ($dist3 > 140) &&
                                                ($syst1 >= 1) && ($syst1 <= 10000) && ($dist1 >= 1) && ($dist1 <= 140) &&
                                                ($syst3 >= 1) && ($syst3 <= 10000) && ($dist3 >= 1) && ($dist3 <= 140) )
        {
                $cmpt = 1;
                $flag = 2;
                for ($i = 1; $i <= 10000; $i++)
                {
                        if ((calcdist($i,$syst1) == $dist1) && (calcdist($i,$syst2) == $dist2))
                        {
                                $systeme[$cmpt++] = $i;
                                $flag = 1;
                        }
                }$Rep = 0;
        }
         else
        {
                $tpl->PushOutput('Les donn�es rentr�es ne sont pas suffisant pour faire la triangulation.');
                $tpl->PushOutput('<br />');
                $Rep =1;
        }

if ($flag == 0 && $Rep != 1)
{
                $tpl->PushOutput('La plan�te est dans le syt�me : ');
                for($i=1; $i < $cmpt; $i++)
                {
                        $tpl->PushOutput($systeme[$i]);$tpl->PushOutput('<br />');
                        if ($i+1 < $cmpt)
                        $tpl->PushOutput(' ou <br />');
          }$Rep = 1;
}
else if ($flag == 1 && $Rep != 1)
{
                $tpl->PushOutput('La plan�te peut �tre dans le syt�me : ');
                for($i=1; $i < $cmpt; $i++)
                {
                        $tpl->PushOutput($systeme[$i]);$tpl->PushOutput('<br />');
                        if ($i < $cmpt)
                        $tpl->PushOutput(' ou <br />');
                }
                $Rep = 1;
}
else if ($flag == 2 && $Rep != 1){
                $tpl->PushOutput('Pas de solution, donn�es incorrects.');
                $tpl->PushOutput('<br />');
                $Rep = 1;
}
else if ($Rep != 1){
                $tpl->PushOutput('Les donn�es rentr�es ne sont invalides.');
                $tpl->PushOutput('<br />');
                $Rep = 1;
        }

$out;
}
else
$tpl->PushOutput('Pour la suite veuillez remplir les champs ci-dessus, ainsi que votre fiche "Production"');
$tpl->PushOutput('<br />');$tpl->PushOutput('<br />');

//------------------------------------------------------------------------------
function carre($nbre) {
return $nbre*$nbre;
}

function calcdist($sys1,$sys2) {
//Pour avoir l'abscisse des coordonn�es du syst�me 1 [syst1 = 2456 ax = 2456%100]
//Pour avoir l'ordon�e des coordonn�es du syst�me 1 [syst1 = 2456 ay = (2456-2456%100)/100]
        list($syst1y, $syst1x)=map::ss2xy($sys1);
        list($syst2y, $syst2x)=map::ss2xy($sys2);
        return round(round(round(sqrt(carre($syst1x-$syst2x)+carre($syst1y-$syst2y)),2),1));
}

//------------------------------------------------
// Un petit menu perso pour l'addons
$menu = array(
    'carte' => array('%ROOT_URL%Carte.php','%IMAGES_URL%Btn-Carte.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
    'prod' => array('%ROOT_URL%ownuniverse.php','%IMAGES_URL%Btn-Production.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
    'triangulation' => array('%ADDONS_URL%triangulation/index.php','%IMAGES_URL%Btn-triangulation.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()));

$tpl->DoOutput($menu,true); // stoppe toute execution du script et transmet les sorties html/xml/...
// les deux 'true' �tant
// 1- Inclusion du menu (html, sans effet sur xml/img)
// 2- Inclusion de l'entete de base (html, sans effet sur xml/img)


