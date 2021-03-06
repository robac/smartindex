<?php

class HtmlHelper {
    
    public static function createClassesList(array $classes) {
        $res = "";
        $isFirst = true;
        foreach ($classes as $class) {
            if ($class == "") {
                continue;
            }
                
            if ($isFirst) {
                $isFirst = false;
            } else {
                $res .= " ";
            }
            
            $res .= $class;
        }
        return $res;
    }
    
    public static function createIdClassesPart($id, array $classes = NULL) {
        $res = "";
        if ((!is_null($id)) && ($id != "")) {
            $res .= " id=\"".$id."\"";
        }
        
        if (!is_null($classes)) {
            $clsList = HtmlHelper::createClassesList($classes);
            if ($clsList != "") {
                $res .= " class=\"".$clsList."\"";    
            }
        }   
        
        return $res;
    }
    
    public static function createInternalLink($wikiid, $params, $text, $id = NULL, array $classes = NULL) {
        return HtmlHelper::createLink(wl($wikiid, $params), $text, $id, $classes);
    }
    
    public static function createSitemapLink ($namespace, $text, $id = NULL, array $classes = NULL) {
        return self::createInternalLink("", array("idx"=>  $namespace), $text, $id, $classes);
    }
            
    public static function createLink($link, $text, $id = NULL, array $classes = NULL) {
        return "<a href=\"".$link."\"".HtmlHelper::createIdClassesPart($id, $classes).">".$text."</a>";
    }
    
    public static function createInlineScript($script) {
        return "<script type=\"text/javascript\">/*<![CDATA[*/".$script."/*!]]>*/</script>";
    }
    
    public static function createInlineJSON($objectName, $object) {
        return 'var '.$objectName.' = '.json_encode($object).';';
    }
        
    public static function createHiddenInput($value, $id = NULL, $classes = NULL) {
        return "<input type=\"hidden\" value=\"".$value."\"".self::createIdClassesPart($id, $classes).">";
    }
}