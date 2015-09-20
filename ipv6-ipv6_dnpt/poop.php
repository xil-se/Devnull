<?php


function icmpChecksum($data)
{
    // Add a 0 to the end of the data, if it's an "odd length"
    if (strlen($data)%2)
        $data .= "\x00";

    // Let PHP do all the dirty work
    $bit = unpack('n*', $data);
    $sum = array_sum($bit);

    // Stolen from: Khaless [at] bigpond [dot] com
    // The code from the original ping program:
    //    sum = (sum >> 16) + (sum & 0xffff);    /* add hi 16 to low 16 */
    //    sum += (sum >> 16);            /* add carry */
    // which also works fine, but it seems to me that
    // Khaless will work on large data.
    while ($sum>>16)
        $sum = ($sum >> 16) + ($sum & 0xffff);

    return pack('n*', ~$sum);
}

function dnpt($ext_ip, $ext_prefix, $int_prefix)
{
    // This is not a complete solution!!!!!!!!!!!!!!!!!!!!!!!!!!
    $ext_prefix = str_replace(":", "", $ext_prefix);
    $int_prefix = str_replace(":", "", $int_prefix);

    // hehe
    $sauce = hexdec(split(":", $ext_ip)[4]);

    $ext_c = icmpChecksum(hex2bin($ext_prefix));
    $int_c = icmpChecksum(hex2bin($int_prefix));

    print_r(unpack('n', $int_c));
    $diff = unpack('n', $ext_c)[1] - unpack('n', $int_c)[1] ;
    if ($diff < 0) $diff = 0xffff + $diff;
    $diff = $sauce - $diff;

    print(bin2hex($ext_c));
    print("\n");
    print(bin2hex($int_c));
    print("\n");
    print(dechex($diff));
    print("\n");

    $out = split(":", $ext_ip);
    $out[4] = dechex($diff);
    $out = join($out, ":");

    return $out;
}


print("The internal address is ". 
        dnpt("2a01:07c8:aab9:0030:8000:0000:0000:0001",
             "2a01:07c8:aab9:0030", 
             "fd42:0454:e661:0000"));
print("\n");

print("The internal address is ".
        dnpt("2a01:07c8:aab9:0030:9999:1234:0000:0003",
             "2a01:07c8:aab9:0030",
             "fd42:0454:e661:0000"));

print("\n");
