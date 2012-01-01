<?php

require("directory_manip.php");

// First, take care of information that needs to be readily available for every page.

define("ROOT_PATH", $_SERVER["DOCUMENT_ROOT"]);
$reqarr = list_paths($_SERVER["REQUEST_URI"]);
$num_menus = get_menu_depth($_SERVER["REQUEST_URI"]);


// Outputs the DOCTYPE and opening html tag,
// as well as an opening head tag and some data.
// Leaves the closing head tag unspecified, so things like title can be added.

function print_init() {
   echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" ',"\n";
   echo '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',"\n";
   echo '<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">',"\n";

   echo "<head>\n";
   
   // Set the charset to ensure proper validation.
   echo '   <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />',"\n";

   // Include authorship.
   echo '   <meta name="Author" content="Terry Vaskor" />',"\n";

   // Specify the favicon for this website.
   echo '   <link rel="icon" type="image/png" href="/favicon.png" />',"\n";

   // Now include CSS information.
   echo '   <link rel="stylesheet/less" type="text/css" href="/includes/default.less" />',"\n";

   // Then, do modifications for special domains.
   echo '   <link rel="stylesheet" type="text/css" href="/includes/print.css" media="print" />',"\n";
   echo '   <link rel="stylesheet" type="text/css" href="/includes/handheld.css" media="handheld" />',"\n";

   // And then include the less parser.
   echo '   <script src="/includes/less.js" type="text/javascript"></script>',"\n";


   // Conditionally include a special line for formatting on iPod or iPhone.
   $iphone_disp = false;
   if (strpos($_SERVER['HTTP_USER_AGENT'], "iPhone") ||
      strpos($_SERVER['HTTP_USER_AGENT'], "iPod")) {
         $iphone_disp = true;
      }

   // Force use of the "handheld" style for iPhones.
   if ($iphone_disp) {
      echo '   <link rel="stylesheet" type="text/css" href="/includes/handheld.css" />',"\n";
   }

   // Do a fix for IE to try to force it to render properly.
   // This will need to be updated as the javascript program is updated and/or IE
   // is updated.
   echo '   <!-- If this is IE, run javascript code to fix its behaviour. -->',"\n";

   // NB: The ie 9 program does not appear to work properly!
//   echo "   <!--[if lt IE 9]>\n";
//   echo '   <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>',"\n";
//   echo '   <![endif]-->',"\n";
   echo '   <!--[if lt IE 8]><script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js"></script><![endif]-->',"\n";
}


// Outputs the closing head tag and the correctly modified opening body tag. 

function print_body($options = array()) {
   global $num_menus;
   $menu_num = in_array('num_menus', $options) ? $options['num_menus'] : $num_menus;

   echo "</head>\n";
   echo "<body class='menu",$menu_num,"'>\n";
}


// To be called in each source file at the close of the document.
// Most of the code deals with printing the menu; it also prints
// the footer information for the bottom of the page.

function print_stdinfo($options = array()) {
   global $reqarr, $num_menus;
   $menu_num = in_array('num_menus', $options) ? $options['num_menus'] : $num_menus;

   $menu_item_delim=',';

   # We need to maintain an array to keep an ordered list of menus.
   # However, we also want a hash for quick lookup.
   $reqhash = array();
   foreach (array_values($reqarr) as $val) {
      $value="/$val";
      $reqhash[$value] = true;
   }

   echo "<div id='stdinfo'>\n";

   if ($menu_num != 0) {
      echo "   <div id='menu' class='menu",$menu_num,"'>\n";
      echo "      <div id='home'><a href='/'>Home</a></div>\n";
      $base_subpage=true;
      foreach (array_keys($reqarr) as $keyname) {
         $menudir=constant("ROOT_PATH").'/'.$reqarr[$keyname];
         $menufile=$menudir.'/submenu.dat';

         if (is_readable($menufile)) {
            // Construct a menu based on this.
            print "      <ul>\n";

            // Each line of the data file represents a colon-delimited menu line.
            foreach (array_values(preg_split( "/\n/", file_get_contents($menufile), null, PREG_SPLIT_NO_EMPTY )) as $file_line ) {
               $menuiteminfo = split($menu_item_delim, $file_line);
               $url = $menuiteminfo[0];
               $description = $menuiteminfo[1];

               // Handle which menu items are "current" (part of the path chain)
               // Handle which menu items are "external" (links to other sites)
               // Treat the very first element of any menu list specially; it 
               // is "base" if it is not the only item in the chain.
               print '         <li';
               if (substr($url, 0, 1) != '/') {
                  print ' class="external"';
               }
               if ($reqhash[$url]) {
                  if ($base_subpage && $num_menus != 1) {
                     print ' class="base"';
                  } else {
                     print ' class="current"';
                  }
               }
               print '><a href="'.$url.'">'.$description."</a></li>\n";
               $base_subpage=false;
            }
            print "      </ul>\n";
         } elseif (is_file($menufile)) {
            echo "      <ul><li><strong class='error'>ERROR: Menu file $menufile exists but does not have read permission.</strong></li></ul>";
         } elseif (is_dir($menudir)) {
            echo "      <ul><li><strong class='error'>ERROR: Menu file $menufile not found.</strong></li></ul>";
         }
      }
      echo "   </div>\n";
   }

   echo '   <div id="footer">';
   echo '      <hr />';
   echo '';
   echo '      <div class="validation">';
   echo '         <a href="http://validator.w3.org/check?uri=referer">';
   echo '            <img src="http://www.w3.org/Icons/valid-xhtml10-blue"';
   echo '            alt="Valid XHTML 1.0 Strict" />';
   echo '         </a>';
   echo '      </div>  ';

   echo '      <p class="lastModified">';
   echo 'Last modified on ';

   $modtime = filemtime($_SERVER['SCRIPT_FILENAME']);
   print date("D, d F Y", $modtime) . '.';

   echo '</p>';
   echo "   </div>\n";
   echo "</div>\n";
   echo "</body></html>\n";
}
?>
