<?php
if (!function_exists('admin_safe')) {
    function admin_safe($value): string
    {
        if (is_null($value)) {
            return '';
        }
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('admin_format_date')) {
    function admin_format_date(?string $value, string $format = 'M d, Y'): string
    {
        if (empty($value)) {
            return '—';
        }
        $timestamp = strtotime($value);
        return $timestamp ? date($format, $timestamp) : '—';
    }
}

if (!function_exists('admin_nav_active')) {
    function admin_nav_active(string $currentPage, string $target): string
    {
        return $currentPage === $target ? ' active' : '';
    }
}

if (!function_exists('admin_time_ago')) {
    function admin_time_ago(?string $value): string
    {
        if (empty($value)) {
            return '—';
        }
        $timestamp = strtotime($value);
        if (!$timestamp) {
            return '—';
        }

        $diff = time() - $timestamp;
        if ($diff < 60) {
            return 'just now';
        }
        if ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins !== 1 ? 's' : '') . ' ago';
        }
        if ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours !== 1 ? 's' : '') . ' ago';
        }
        $days = floor($diff / 86400);
        if ($days < 30) {
            return $days . ' day' . ($days !== 1 ? 's' : '') . ' ago';
        }
        return admin_format_date($value);
    }
}

if (!function_exists('admin_table_has_column')) {
    function admin_table_has_column($db, string $table, string $column): bool
    {
        if (!($db instanceof PDO)) {
            return false;
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table) || !preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
            return false;
        }

        try {
            $query = sprintf('SHOW COLUMNS FROM `%s` LIKE :column', $table);
            $stmt = $db->prepare($query);
            $stmt->execute(['column' => $column]);
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            return false;
        }
    }
}

if (!function_exists('admin_excerpt')) {
    function admin_excerpt(?string $value, int $length = 80): string
    {
        if (empty($value)) {
            return '';
        }

        if (function_exists('mb_strimwidth')) {
            $trimmed = mb_strimwidth($value, 0, $length, '…', 'UTF-8');
        } else {
            $trimmed = substr($value, 0, $length);
            if (strlen($value) > $length) {
                $trimmed .= '…';
            }
        }

        return admin_safe($trimmed);
    }
}

if (!function_exists('admin_decode_json')) {
    function admin_decode_json(?string $value): array
    {
        if (empty($value)) {
            return [];
        }

        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }
}

if (!function_exists('admin_filter_columns')) {
    function admin_filter_columns($db, string $table, array $columns, array $fallback = []): array
    {
        $available = [];
        foreach ($columns as $column) {
            if (admin_table_has_column($db, $table, $column)) {
                $available[] = $column;
            }
        }

        if (empty($available) && !empty($fallback)) {
            foreach ($fallback as $column) {
                if (admin_table_has_column($db, $table, $column)) {
                    $available[] = $column;
                }
            }
        }

        return $available;
    }
}
