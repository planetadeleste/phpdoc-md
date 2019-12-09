<?php
namespace PHPDocMD;

use PHPDocMD\MarkdownHelpers as MD;
use PHPDocMD\HTMLHelpers as HTML;

/**
 * HTML elements allowed in github: https://github.com/jch/html-pipeline/blob/master/lib/html/pipeline/sanitization_filter.rb
 * h1 h2 h3 h4 h5 h6 h7 h8 br b i strong em a pre code img tt
 * div ins del sup sub p ol ul table thead tbody tfoot blockquote
 * dl dt dd kbd q samp var hr ruby rt rp li tr td th s strike summary
 * details caption figure figcaption
 * abbr bdo cite dfn mark small span time wbr
 */
?>

# <?= ($deprecated ? '/!\ Deprecated /!\ ': '') 
    . ($abstract ? 'asbtract ' : '') 
    . $namespace . ' \ ' . $shortClass 
?>

<?= $description ?>

<?= $longDescription ?>

<?php if ($extends) { ?>
Extends: <?= implode(', ', array_map(Generator::class.'::classLink', $extends)) ?>
<?php } ?>


<?php if ($implements) { ?>
Implements: <?= implode(', ', array_map(Generator::class.'::classLink', $implements)) ?>
<?php } ?>

<?php
if ($constants) {
    echo "## Constants\n";

    foreach (Generator::indexByDefiner($constants) as $definer => $constants) {
        echo $definer != $className ? "    Defined by: $definer\n\n" : '';
        
        foreach ($constants as $constant) {
            echo '    '.$constant['signature']."\n";
        }
    }
}
?>

<?php 
/** /
if ($properties) {
    echo "## Properties\n";

    foreach (Generator::indexByDefiner($properties) as $definer => $properties) {
        echo $definer != $className 
            ? "\n    Defined by: ".Generator::classLink($definer)."\n" 
            : '';
        
        foreach ($properties as $property) {
            echo '    '.$property['signature']."\n";
            echo (trim($property['description']) ? $property['description']."\n" : '');
        }
    }
}
/**/

?>

<?php 

if ($methods) {
    echo "## Methods\n";

    foreach (Generator::indexByDefiner($methods) as $definer => $methods) {
        echo $definer != $className 
            ? "\n### Defined by: ".Generator::classLink($definer)."\n" 
            : '';
        
        foreach ($methods as $method) {
            // echo '#### <code>'.$method['signature'].'</code>'
            echo '#### - '.MD::anchor("<code style=\"background-color: white; color: inherit;\">{$method['signature']}</code>")
                // ."{#".MD::anchorId($method['signature'])."}"
                .($method['deprecated'] ? ' /!\ Deprecated /!\ ' : '')
                ."\n";
            
            $fullDescription = [];
            
            if ($method['description']) {
                $fullDescription[] = $method['description'];
            }
                
            if ($method['arguments']) {
                $argumentsDescription = ["Parameters:"];
                foreach ($method['arguments'] as $argument) {
                    // $argumentsDescription[] = ' &#43; '
                    $argumentsDescription[] = ' &#x25FE; '
                        .($argument['type'] ? Generator::classLink($argument['type']) : 'mixed')
                        .' '.$argument['name']
                        .( ! empty($argument['description']) ? ': '.$argument['description'] : '')
                        ;
                }
                $fullDescription[] = implode("<br>", $argumentsDescription);
            }
            
            # todo use statement
            if ($method['returnDescription']) {
                $fullDescription[] = "Returns a ".HTML::classLink($method['returnType'])
                    .': '.$method['returnDescription']
                    ;
            }
            
            if ($fullDescription) {
                echo "<blockquote><pre><code>".implode("<br><br>", $fullDescription)."</code></pre></blockquote>\n\n\n";
            }
            // echo "<a href='rtyu'>tatata</a></code></pre></blockquote>\n\n\n";
            // echo "    lalal\n\n\n";
            // echo " > lalal\n\n\n";
            // echo " > lalal\n\n\n";
        }
    }
}