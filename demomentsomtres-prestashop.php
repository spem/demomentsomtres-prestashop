<?php

/*
 * Plugin Name: DeMomentSomTres Prestashop 
 * Plugin URI: http://www.demomentsomtres.com/
 * Description: Prestashop Integration based on web services
 * Version: 1.3
 * Author: Marc Queralt
 * Author URI: http://demomentsomtres.com
 * Change story
 * 1.0 - Initial version get category contents based on file cache and shortcode
 * 1.1 - Shortcode demomentsomtres-prestashop-category-desc added
 * 1.2 - Shortcode demomentsotmres-prestashop-manufacturers
 * 1.3 - Multilingual shop supported
 */

include_once 'demomentsomtres-ps-cache.php';

define('DMST_PRESTASHOP_DOMAIN', 'dmst-prestashop');

load_plugin_textdomain(DMST_PRESTASHOP_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');

// Register shortcodes
add_shortcode('demomentsomtres-prestashop-category', 'demomentsomtres_prestashop_category');
add_shortcode('demomentsomtres-prestashop-category-desc', 'demomentsomtres_prestashop_category_description');
add_shortcode('demomentsomtres-prestashop-manufacturers', 'demomentsomtres_prestashop_manufacturers');

function demomentsomtres_prestashop_category($attr) {
    if (!isset($attr['id'])):
        return '';
    else:
        $catID = $attr['id'];
    endif;
    if (!isset($attr['url'])):
        return '';
    else:
        $ps_url = $attr['url'];
    endif;
    if (!isset($attr['key'])):
        return '';
    else:
        $ps_key = $attr['key'];
    endif;
    if (!isset($attr['lang'])):
        return '';
    else:
        $lang = $attr['lang'];
    endif;
    $ws_url = $ps_url . '/api/categories/';
    $cache = new demomentsomtres_ps_cache();
    $cache->setCacheTime(3600);
    $cache->setPrestaShopURL($ps_url);
    $cache->setPrestaShopKey($ps_key);
    $xml = $cache->getPrestaShopResource('categories', $catID);
    $subCatList = '<ul id="cat' . $catID . '" class="prestashop category">';
    foreach ($xml->xpath('/prestashop/category/associations/categories/category/id') as $id):
        $xmlSub = $cache->getPrestaShopResource('categories', $id);
        $name = $xmlSub->xpath('/prestashop/category/name/language[@id="' . $lang . '"]');
        $meta = $xmlSub->xpath('/prestashop/category/meta_title/language[@id="' . $lang . '"]');
        $subCatList.='<li id="cat' . $id . '" class="prestashop category">';
        $subCatList.='<a href="' . $ps_url . '/index.php?id_category=' . $id . '&controller=category&id_lang='.$lang.'" title="' . $meta[0] . '">';
        $subCatList.=$name[0];
        $subCatList.='</a>';
        $subCatList.='</li>';
    endforeach;
    $subCatList.='</ul>';
    return $subCatList;
}

function demomentsomtres_prestashop_category_description($attr) {
    if (!isset($attr['id'])):
        return '';
    else:
        $catID = $attr['id'];
    endif;
    if (!isset($attr['url'])):
        return '';
    else:
        $ps_url = $attr['url'];
    endif;
    if (!isset($attr['key'])):
        return '';
    else:
        $ps_key = $attr['key'];
    endif;
    if (!isset($attr['lang'])):
        return '';
    else:
        $lang = $attr['lang'];
    endif;
    $ws_url = $ps_url . '/api/categories/';
    $cache = new demomentsomtres_ps_cache();
    $cache->setCacheTime(3600);
    $cache->setPrestaShopURL($ps_url);
    $cache->setPrestaShopKey($ps_key);
    $xml = $cache->getPrestaShopResource('categories', $catID);
    $descriptions = $xml->xpath('/prestashop/category/description/language[@id="' . $lang . '"]');
    $description = $descriptions[0];
    return $description;
}

function demomentsomtres_prestashop_manufacturers($attr) {
    if (!isset($attr['url'])):
        return '';
    else:
        $ps_url = $attr['url'];
    endif;
    if (!isset($attr['key'])):
        return '';
    else:
        $ps_key = $attr['key'];
    endif;
    if (!isset($attr['lang'])):
        return '';
    else:
        $lang = $attr['lang'];
    endif;
    $cache = new demomentsomtres_ps_cache();
    $cache->setCacheTime(3600);
    $cache->setPrestaShopURL($ps_url);
    $cache->setPrestaShopKey($ps_key);
    $xml = $cache->getPrestaShopResource('manufacturers');
//    $xmlImatges = $cache->getPrestaShopResource('images/manufacturers');
//    $imageTypes=$xmlImatges->xpath('/prestashop/image_types/image_type[1]/@name');
//    $imageType=$imageTypes[0];
    $manufacturers = '<ul id="prestashop_manufacturers">';
    foreach ($xml->xpath('/prestashop/manufacturers/manufacturer/@id') as $id):
        $xmlSub = $cache->getPrestaShopResource('manufacturers', $id);
        $name = $xmlSub->xpath('/prestashop/manufacturer/name');
        $active = $xmlSub->xpath('/prestashop/manufacturer/active');
        if ($active[0] == 1):
//            $imageUrl = $xmlImatges->xpath('/prestashop/images/image[@id="' . $id . '"]/@xlink:href');
//            $xmlFoto=$cache->getPrestaShopResource('images/manufacturers/'.$id.'/'.$imageType);
//            $manufacturers.= '<img src="'.$imageUrl[0].'/'.$imageType.'"/>'. $name[0] . '<br/>';
            $manufacturers.= '<li class="prestashop manufacturer">'. $name[0] . '</li>';
        endif;
    endforeach;
    $manufacturers.='</ul>';
    return $manufacturers;
}

?>