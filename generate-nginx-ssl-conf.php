<?php
/* Simple php script to generate /var/www/site/conf/nginx/ssl.conf wordops */
$params = [
    'domain:'
];

$opts = getopt('', $params);

if ( array_key_exists('domain', $opts) ) {
    
    $site_domain = $opts['domain'];

    $OUTPUT = "";

    $OUTPUT .= "\tlisten 443 ssl http2;" . PHP_EOL;

    $OUTPUT .= "\tssl_certificate /var/www/$site_domain/conf/certs/fullchain.pem;" . PHP_EOL;
    $OUTPUT .= "\tssl_certificate_key /var/www/$site_domain/conf/certs/privkey.pem;" . PHP_EOL;
    $OUTPUT .= "\tssl_prefer_server_ciphers on;" . PHP_EOL;
    $OUTPUT .= "\n";
    $OUTPUT .= "\tssl_buffer_size 8k;" . PHP_EOL;
    $OUTPUT .= "\n";
    $OUTPUT .= "\tresolver 8.8.8.8;" . PHP_EOL;
    $OUTPUT .= "\n";
    $OUTPUT .= "\tsubs_filter_types mime-type *;" . PHP_EOL;
    $OUTPUT .= "\tsubs_filter 'http://\$host' 'https://\$host';" . PHP_EOL;
    $OUTPUT .= "\tsubs_filter 'http://fonts' 'https://fonts';" . PHP_EOL;
    $OUTPUT .= "\tsubs_filter 'http://maps' 'https://maps';" . PHP_EOL;

    file_put_contents("ssl-$site_domain.conf", $OUTPUT);

    echo "Arquivo ssl-$site_domain.conf gerado com sucesso\nVocê deve movê-lo para /var/www/$site_domain/conf/nginx\n\n";

    $OUTPUT = "";
    $OUTPUT .= "\tserver {" . PHP_EOL;
    $OUTPUT .= "\t\tlisten 80;" . PHP_EOL;
    $OUTPUT .= "\t\tlisten [::]:80;" . PHP_EOL;
    $OUTPUT .= "\t\tserver_name $site_domain;" . PHP_EOL;
    $OUTPUT .= "\n" . PHP_EOL;
    $OUTPUT .= "\t\tlocation /.well-known/acme-challenge/ {" . PHP_EOL;
    $OUTPUT .= "\t\t\tallow all;" . PHP_EOL;
    $OUTPUT .= "\t\t\troot /var/www/$site_domain/htdocs/;" . PHP_EOL;
    $OUTPUT .= "\t\t\ttry_files \$uri =404;" . PHP_EOL;
    $OUTPUT .= "\t\t}" . PHP_EOL;
    $OUTPUT .= "\n" . PHP_EOL;
    $OUTPUT .= "\t\tlocation / {" . PHP_EOL;
    $OUTPUT .= "\t\t\treturn 301 https://$site_domain\$request_uri;" . PHP_EOL;
    $OUTPUT .= "\t\t}" . PHP_EOL;
    $OUTPUT .= "\t}" . PHP_EOL;

    file_put_contents("force-ssl-$site_domain.conf", $OUTPUT);

    echo "Arquivo force-ssl-$site_domain.conf gerado com sucesso!\nVocê deve movê-lo para /etc/nginx/conf.d";

} else {
    echo "O dominio não foi definido!" . PHP_EOL;
}
