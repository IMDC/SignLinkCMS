<?php

/* database connection */
define('DB_USER', 'testcodeuser');
define('DB_PASSWORD', 'cltu53R');
define('DB_PORT', '3306');              // 3306
define('DB_HOST', 'localhost');         //localhost
define('DB_NAME', 'signlinkcms_testcode');

/* uploads directory */
define('UPLOAD_DIR', 'uploads/');

/* width of column */
define('BLOCK_WIDTH', '145');

/* salt */
define('CMSSALT','8d%18:7dao#".]109a0djf1l86<14');

/* salt length */
define('SALT_LENGTH', 9);

/* define the set file paths to ffmpeg
   probably best to define the path from the site's root directory */
define('FFMPEG_PATH', 'ffmpeg/ffmpeg');
// define the name of the image file to overlay on top of video thumbnails
// needs to be located inside the IMAGE_FOLDER define, set in the index.php file
define('PLAYOVERLAY_IMAGE', 'play_btn.png');
?>
