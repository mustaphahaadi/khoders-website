<?php
/**
 * Input Validation Helper
 */
class Validator {
    public static function phone($phone) {
        $clean = preg_replace('/[^0-9+]/', '', $phone);
        return preg_match('/^\+?233\d{9}$/', $clean) || preg_match('/^0\d{9}$/', $clean);
    }
    
    public static function studentId($id) {
        return preg_match('/^[A-Z0-9]{4,20}$/i', str_replace(['-', ' '], '', $id));
    }
    
    public static function url($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    public static function slug($slug) {
        return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug);
    }
    
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function required($value) {
        return !empty(trim($value));
    }
    
    public static function minLength($value, $min) {
        return mb_strlen(trim($value)) >= $min;
    }
    
    public static function maxLength($value, $max) {
        return mb_strlen(trim($value)) <= $max;
    }
}
