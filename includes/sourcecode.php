<?php
  // Function that just includes the proper stylesheet.
  function print_source_head_include() {
     echo '<link rel="stylesheet" type="text/css" href="/includes/sourcecode.css" />',"\n";
  }

  // TODO: This function should assign classes to code spans.
  // This will enable colourization of the code in the included CSS.
  function print_source_code($srcpath) {
     if (is_file($srcpath)) {
        echo htmlspecialchars(file_get_contents($srcpath), ENT_QUOTES);
     }
     else {
        $path_parts = pathinfo($srcpath);
        $directory = $path_parts['dirname'];
        $filename = $path_parts['basename'];

        if (!is_dir($directory)) {
           echo '<strong class="error">ERROR: The directory ',$directory,' does not exist!</strong>';
        } else {
           echo '<strong class="error">ERROR: The file ',$filename,' does not exist in the directory ',$directory,'!</strong>';
        }
     }
  }

  function include_src($full_path = __FILE__) {
     $path_parts = pathinfo($full_path);

     // What we care about is the path to this point,
     // and the filename without the .php extension.
     $dirpath = $path_parts['dirname'];
     $file = $path_parts['filename'];

     // So the full path to the source will be...
     $path_to_src = $dirpath . '/src/' . $file;

     // Debug
//     echo "<p>dirpath: $dirpath; file: $file; path_to_src: $path_to_src</p>\n";

     echo '<div class="source_code">',"\n";
     echo '   <div class="source_header">',"\n";
     echo "      $file (<a href='src/$file'>source</a>)\n";
     echo "   </div>\n";
     echo '   <div class="source_body">',"\n";
     echo "<pre>";
     print_source_code($path_to_src);
     echo "</pre>\n";
     echo "   </div>\n";
     echo "</div>\n";
  }
?>
