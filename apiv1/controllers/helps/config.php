<?php
// Config.php
/**
 * @About:      API Interface
 * @File:       Config.php
 * @Date:       febrero-2025
 * @Version:    1.1
 * @Developer:  Hosmmer Eduardo
 *spip_connect_db('localhost','','lacasa12_api2024','6@ol8XJ1iI5v','lacasa12_db_apisv1','mysql', 'api','','utf8');
 **/

 
class Config {
    const DB_HOST = 'localhost';
    const DB_NAME = 'lacasa12_db_apisv1';
    const DB_USER = 'lacasa12_api2024';
    const DB_PASSWORD = '6@ol8XJ1iI5v';
    public static $url_api = 'https://www.lacasadelbarbero.com.co/api2025/ecrire/?exec=apis&bonjour=oui';
} 
?>