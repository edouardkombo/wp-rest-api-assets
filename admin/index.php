<?php

add_action('admin_menu', 'wraa_wp_rest_api_assets_setup_menu');

function wraa_wp_rest_api_assets_setup_menu(){
    add_menu_page( 'Wp Rest Api Assets Page', 'Wp Rest Api Assets', 'manage_options', 'wp-rest-api-assets', 'wraa_launch_admin' );
}

/**
 *  Get theme assets
 */
function wraa_print_theme_scripts_styles() {

    $result = [];
    $result['permanent'] = [];
    $result['optional'] = [];

    $result['permanent']['scripts'] = [];
    $result['permanent']['styles'] = [];

    $result['optional'] = [];
    $result['optional']['url'] = [];
    $result['optional']['url']['scripts'] = [];
    $result['optional']['url']['styles'] = [];

    $theme = get_template_directory();
    $themeFunctions = $theme . "/functions.php";

    $handle = fopen($themeFunctions, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            // process the line read.
            $patterns = ["'", 'get_stylesheet_directory_uri()', ' ', './'];
            $replacePatternsBy = ['', '', '', '/'];
            $trimmedLine = trim(str_replace($patterns, $replacePatternsBy, $line));

            //Don't include wp-admin assets
            if (substr($trimmedLine, 0,9) != '/wp-admin') {
                if (substr($trimmedLine, -3) === '.js') {
                    $result['permanent']['scripts'][] =  $trimmedLine;
                }

                if (substr($trimmedLine, -4) === '.css') {
                    $result['permanent']['styles'][] =  $trimmedLine;
                }
            }
        }

        fclose($handle);
    } else {
        // error opening the file.
        die("Unable to open current theme, functions.php file!");
    }

    return $result;
}

/**
 *  Get all scripts and styles from Wordpress
 */
function wraa_print_scripts_styles() {

    //Load first the theme assets
    $result = wraa_print_theme_scripts_styles();

    // Print all loaded Scripts
    global $wp_scripts;
    foreach ( $wp_scripts->queue as $script ) {
        if ((substr($wp_scripts->registered[$script]->src, -3) === '.js') && (substr($wp_scripts->registered[$script]->src, 0, 9) !== '/wp-admin')) {
            $result['optional']['url']['scripts'][] =  $wp_scripts->registered[$script]->src;
        }
    }

    // Print all loaded Styles (CSS)
    global $wp_styles;
    foreach( $wp_styles->queue as $style ) {
        if ((substr($wp_styles->registered[$style]->src, -4) === '.css') && (substr($wp_styles->registered[$style]->src, 0, 9) !== '/wp-admin')) {
            $result['optional']['url']['styles'][] = $wp_styles->registered[ $style ]->src;
        }
    }

    wraa_save_original_scripts_and_styles($result, 'original');

    return $result;
}

/**
 * wraa_jsonpp - Pretty print JSON data
 *
 * In versions of PHP < 5.4.x, the json_encode() function does not yet provide a
 * pretty-print option. In lieu of forgoing the feature, an additional call can
 * be made to this function, passing in JSON text, and (optionally) a string to
 * be used for indentation.
 *
 * @param string $json  The JSON data, pre-encoded
 * @param string $istr  The indentation string
 *
 * @link https://github.com/ryanuber/projects/blob/master/PHP/JSON/jsonpp.php
 *
 * @return string
 */
function wraa_jsonpp($json, $istr='  ')
{
    $result = '';
    for($p=$q=$i=0; isset($json[$p]); $p++)
    {
        $json[$p] == '"' && ($p>0?$json[$p-1]:'') != '\\' && $q=!$q;
        if(!$q && strchr(" \t\n", $json[$p])){continue;}
        if(strchr('}]', $json[$p]) && !$q && $i--)
        {
            strchr('{[', $json[$p-1]) || $result .= "\n".str_repeat($istr, $i);
        }
        $result .= $json[$p];
        if(strchr(',{[', $json[$p]) && !$q)
        {
            $i += strchr('{[', $json[$p])===FALSE?0:1;
            strchr('}]', $json[$p+1]) || $result .= "\n".str_repeat($istr, $i);
        }
    }
    return $result;
}

/**
 *  Save scripts and styles content
 *
 * @param $content String
 * @param $page String
 */
function wraa_save_original_scripts_and_styles($content, $page) {

    $fileName = __DIR__ . '/../' . $page . '.json';

    $_result        = ($page === 'custom') ? $content : json_encode($content, JSON_PRETTY_PRINT) ;
    $sanitizeResult = sanitize_text_field($_result);
    $result         = wraa_jsonpp($sanitizeResult);

    $firstArray = ["\\/", "\\"];
    $secondArray = ["/", ""];
    $newResult = str_replace($firstArray, $secondArray, $result);

    file_put_contents($fileName, $newResult);
}

function wraa_launch_admin(){

    // First print assets, create original.json file and put asssets in
    wraa_print_scripts_styles();

    //Get post value of the textarea
    $customTextarea = filter_input(INPUT_POST, 'customTextarea');

    //If form is submitted, save content inside file
    if (!empty($customTextarea)) {
        wraa_save_original_scripts_and_styles($customTextarea, 'custom');
    }

    //Check if directory is writeable
    $isDirWritable = substr(sprintf('%o', fileperms(__DIR__ . '/../')), -4) === "0775" ? true : false;

    if (false === $isDirWritable) {
        echo "<p class=\"alert-box error\">This plugin directory should have '0775' rights to work properly !</p>";
    }

    echo "
        <style>
            .postbox {
                padding: 15px;
                width: 100%;
            }
            .textareas div {
                display: inline-block;
            }
            .alert-box {
                color:#555;
                border-radius:10px;
                font-family:Tahoma,Geneva,Arial,sans-serif;font-size:11px;
                padding:10px 10px 10px 36px;
                margin:10px;
            }
            .alert-box span {
                font-weight:bold;
                text-transform:uppercase;
            }
            .error {
                background:#ffecec;
                border:1px solid #f5aca6;
            }
            .success {
                background:#e9ffd9;
                border:1px solid #a6ca8a;
            }
            .warning {
                background:#fff8c4;
                border:1px solid #f2c779;
            }
            .notice {
                background:#e3f7fc;
                border:1px solid #8ed9f6;
            }
            span {
                font-weight: bold;
                font-style: italic;
            }
        </style>
        <div class=\"wrap\">
	        <h2 class=\"\">Wp Rest APi Assets - Administration</h2>
            <div class=\"postbox\">
                <p class=\"alert-box warning\">
                    You need Wp Rest APi v2 in order to use this plugin. If you don't have this plugin installed, please install it now <a target=\"_blank\" href=\"http://v2.wp-api.org/\">http://v2.wp-api.org/</a>.<br>
                    This plugin directory must have '0775' rights. Make sure to make it writable.<br/>
                    Also be sure that your directory has correct group permission for current linux user.
                </p><br>
                <h3>What is it for?</h3>
                <p>Request through JSON, all assets (styles and scripts) needed by active plugins and main theme.</p>
                <h3>How to use the api?</h3>
                <p class=\"alert-box notice\">
                    http://mywebsite/wp-json/wp-rest-api-assets/v2/assets <span>Get all the assets</span>.<br><br>
                    http://mywebsite/wp-json/wp-rest-api-assets/v2/assets/permanent <span>Get all permanent assets</span>.<br>
                    http://mywebsite/wp-json/wp-rest-api-assets/v2/assets/permanent/{type} <span>Replace by 'scripts' or 'styles' to fetch them</span>.<br><br>
                    http://mywebsite/wp-json/wp-rest-api-assets/v2/assets/optional <span>Get all optional assets</span>.<br>
                    http://mywebsite/wp-json/wp-rest-api-assets/v2/assets/optional/{url} <span>Get all optional assets per page url</span>.<br>
                    http://mywebsite/wp-json/wp-rest-api-assets/v2/assets/optional/{url}/{type} <span>Get all optional assets scripts or styles per page url</span>.<br>
                </p>
                <h3>How to customize the assets?</h3>
                <p>
                    The 'Original' content contains all the scripts and styles we fetched from your plugins and current theme.<br>
                    Copy the 'Original' content to the 'Custom' side and manage the scripts and css you want to show in every page of your site, by replacing 'url' with the url of each page.
                </p>
                <div class='textareas'>
                    <div>
                        <p>Original</p>
                        <textarea name=\"comment\" form=\"usrform\" disabled id='originalTextarea' cols='60%' rows='30'></textarea>
                        <p><a class='button button-primary' onclick=\"refresh();\">Refresh</a></p>
                    </div>
                    <div>
                        <p>Custom</p>
                        <form method='post'>
                            <textarea name=\"customTextarea\" id='customTextarea' cols='100%' rows='30'></textarea>
                            <p>
                                <a class='button' onclick='copy();'>Copy from original</a>
                                <input type='submit' name='Save' value='Save' class='button button-primary'>
                            </p>
                        </form>                   
                    </div>
                </div>
            </div>
        </div>
        <script>
            /**
            * Do not trust content and avoid special characters 
            */
            function htmlEntities(str) {
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }
        
        
            /**
            * Copy original content to custom
            * 
            * @returns {boolean}
            */
            function copy() {
                var originalContent = document.getElementById('originalTextarea').value;
                document.getElementById('customTextarea').value = htmlEntities(originalContent);
                return true;
            }
 
            /**
            * Get content
            * 
            * @returns {boolean}
            */
            function refresh(file) {
  
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '/wp-content/plugins/wp-rest-api-assets/'+file+'.json');
                xhr.addEventListener('readystatechange', function() {
                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                        document.getElementById(file + 'Textarea').value = htmlEntities(xhr.responseText);
                    }
                });
                xhr.send(null);
            }
            
            //Load content inside original and custom textareas at page refresh
            refresh('original');
            refresh('custom');
        </script>
        ";
}