<?php

$availableExtensions = range(2100, 9599);
$vanityLetters = [
    0 => [ 'o', '_' ],
    1 => [ 'i' ],
    2 => [ 'A', 'B', 'C' ],
    3 => [ 'D', 'E', 'F' ],
    4 => [ 'G', 'H', 'I' ],
    5 => [ 'J', 'K', 'L' ],
    6 => [ 'M', 'N', 'O' ],
    7 => [ 'P', 'Q', 'R', 'S' ],
    8 => [ 'T', 'U', 'V' ],
    9 => [ 'W', 'X', 'Y', 'Z' ]
];
$phonebookSearch = 'https://eventphone.de/guru2/phonebook?event=32C3&all=1&order=extension';

/**
 * Fetch list of all numbers and yield these back
 */
function fetchBusyExtensions($phonebookSearch) {
    $dom = new DOMDocument();
    $dom->loadHTML(file_get_contents($phonebookSearch));

    $xpath = new DOMXPath($dom);

    $elements = $xpath->query('//table/tr[position()>1]/td[1]');

    foreach ($elements as $element) {
        yield (string) $element->textContent;
    }
}

/**
 * Create vanity-numbers for number
 */
function createVanityNumbers($extension, $vanityLetters) {
    $extension = (string) $extension;

    foreach ($vanityLetters[$extension[0]] as $letterA) {
        foreach ($vanityLetters[$extension[1]] as $letterB) {
            foreach ($vanityLetters[$extension[2]] as $letterC) {
                foreach ($vanityLetters[$extension[3]] as $letterD) {
                    yield $letterA.$letterB.$letterC.$letterD;
                }
            }
        }
    }
}

// Loop through all registred extensions
foreach (fetchBusyExtensions($phonebookSearch) as $busyExtension) {
    // Search available extensions
    if (($key = array_search($busyExtension, $availableExtensions)) !== false) {
        // Unset if found in available
        unset($availableExtensions[$key]);
    }
}

//
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Available eventphone extensions with vanitynumbers for 32C3</title>
    </head>
    <body>
        <h1>Currently available eventphone extensions with vanitynumbers for 32C3</h1>
        <table>
            <thead>
                <tr><th>Number:</th><th>Vanity Numbers:</th></tr>
            </thead>
            <tbody>
                <?php foreach ($availableExtensions as $extension): ?>
                    <tr>
                        <td><?= $extension ?></td>
                        <td>
                            <?php foreach (createVanityNumbers($extension, $vanityLetters) as $vanityNumber): ?>
                                <?= $vanityNumber.' ' ?>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>
