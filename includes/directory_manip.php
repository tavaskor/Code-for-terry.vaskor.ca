<?php

// TODO: Examine PHP references; should these array functions return them?
function get_path_elements($mainURL) {
   return preg_split( "/\//", $mainURL, null, PREG_SPLIT_NO_EMPTY );
}

function list_paths($mainURL) {
   $reqarr = get_path_elements($mainURL);

   // Modify each so they form a cumulative URI chain
   $req_count = count($reqarr);
   for ($i = 1; $i < $req_count; $i++) {
      $reqarr[$i] = $reqarr[$i-1].'/'.$reqarr[$i];
   }
   return $reqarr;
}

function get_menu_depth($mainURL) {
   $num_menus = count(get_path_elements($mainURL));
   $isdir_lastreqelem = ( $mainURL[strlen($mainURL)-1] == '/' );

   if (! $isdir_lastreqelem) {
         $num_menus--;
   }

   return $num_menus;
}

?>
