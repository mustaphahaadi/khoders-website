<?php
/**
 * KHODERS WORLD File Upload Handler
 * 
 * Handles secure file uploads for images and media
 * Validates file types, sizes, and generates secure filenames
 * 
 * Usage:
 *   $uploader = new FileUploader('images', 2 * 1024 * 1024); // 2MB limit
 *   $result = $uploader->upload($_FILES['image']);
 *   if ($result['success']) {
 *       $imagePath = $result['path'];
 *   }
 */

class FileUploader {
    private $uploadDir;
    private $maxFileSize;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    
    /**
     * Initialize uploader
     * 
     * @param string $subdir - Subdirectory in uploads folder (e.g., 'images', 'courses', 'team')
     * @param int $maxFileSize - Maximum file size in bytes (default 5MB)
     */
    public function __construct($subdir = 'uploads', $maxFileSize = 5 * 1024 * 1024) {
        // Ensure uploads directory exists
        $baseDir = realpath(__DIR__ . '/../public/uploads') ?: __DIR__ . '/../public/uploads';
        $sanitizedSubdir = preg_replace('/[^a-z0-9_-]/', '', strtolower($subdir));
        $this->uploadDir = $baseDir . DIRECTORY_SEPARATOR . $sanitizedSubdir;
        $this->maxFileSize = $maxFileSize;
        
        // Create directories if they don't exist
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Handle file upload
     * 
     * @param array $fileInput - $_FILES['input_name']
     * @param bool $deleteOld - Delete old file if path provided (optional)
     * @return array ['success' => bool, 'path' => string, 'error' => string]
     */
    public function upload($fileInput, $deleteOld = null) {
        $result = [
            'success' => false,
            'path' => '',
            'error' => ''
        ];
        
        // Validate file input
        if (empty($fileInput) || !isset($fileInput['tmp_name'])) {
            $result['error'] = 'No file provided';
            return $result;
        }
        
        if ($fileInput['error'] !== UPLOAD_ERR_OK) {
            $result['error'] = $this->getUploadError($fileInput['error']);
            return $result;
        }
        
        // Validate file size
        if ($fileInput['size'] > $this->maxFileSize) {
            $result['error'] = 'File size exceeds limit (' . $this->formatFileSize($this->maxFileSize) . ')';
            return $result;
        }
        
        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileInput['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedTypes)) {
            $result['error'] = 'File type not allowed. Allowed types: ' . implode(', ', $this->allowedExtensions);
            return $result;
        }
        
        // Validate extension
        $ext = strtolower(pathinfo($fileInput['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            $result['error'] = 'File extension not allowed';
            return $result;
        }
        
        // Generate secure filename
        $filename = $this->generateSecureFilename($fileInput['name']);
        $filePath = $this->uploadDir . DIRECTORY_SEPARATOR . $filename;
        
        // Validate final path is within upload directory
        $realUploadDir = realpath($this->uploadDir);
        $realFilePath = realpath(dirname($filePath)) . DIRECTORY_SEPARATOR . basename($filePath);
        if (!$realUploadDir || strpos($realFilePath, $realUploadDir) !== 0) {
            $result['error'] = 'Invalid upload path';
            return $result;
        }
        
        $publicPath = '/public/uploads/' . basename($this->uploadDir) . '/' . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($fileInput['tmp_name'], $filePath)) {
            // Delete old file if provided
            if (!empty($deleteOld)) {
                $oldPath = realpath(__DIR__ . '/..' . $deleteOld);
                $uploadsBase = realpath(__DIR__ . '/../public/uploads');
                if ($oldPath && $uploadsBase && strpos($oldPath, $uploadsBase) === 0 && file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            
            $result['success'] = true;
            $result['path'] = $publicPath;
        } else {
            $result['error'] = 'Failed to save file to server';
        }
        
        return $result;
    }
    
    /**
     * Delete uploaded file
     */
    public function delete($filePath) {
        if (empty($filePath)) {
            return false;
        }
        
        $fullPath = realpath(__DIR__ . '/..' . $filePath);
        $uploadsBase = realpath(__DIR__ . '/../public/uploads');
        
        if ($fullPath && $uploadsBase && strpos($fullPath, $uploadsBase) === 0 && file_exists($fullPath)) {
            return @unlink($fullPath);
        }
        
        return false;
    }
    
    /**
     * Generate secure filename
     */
    private function generateSecureFilename($originalName) {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Sanitize filename
        $name = preg_replace('/[^a-z0-9_-]/', '-', strtolower($name));
        $name = preg_replace('/-+/', '-', $name);
        $name = trim($name, '-');
        
        // Add timestamp and random string for uniqueness
        $timestamp = time();
        $random = bin2hex(random_bytes(4));
        
        return "{$name}-{$timestamp}-{$random}.{$ext}";
    }
    
    /**
     * Get upload error message
     */
    private function getUploadError($code) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File upload incomplete',
            UPLOAD_ERR_NO_FILE => 'No file provided',
            UPLOAD_ERR_NO_TMP_DIR => 'Temporary directory missing',
            UPLOAD_ERR_CANT_WRITE => 'Cannot write to disk',
            UPLOAD_ERR_EXTENSION => 'File upload blocked by extension'
        ];
        
        return $errors[$code] ?? 'Unknown upload error';
    }
    
    /**
     * Format bytes to human-readable size
     */
    private function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Set allowed file types
     */
    public function setAllowedTypes($types) {
        $this->allowedTypes = $types;
    }
    
    /**
     * Set maximum file size
     */
    public function setMaxFileSize($maxSize) {
        $this->maxFileSize = $maxSize;
    }
}
?>
