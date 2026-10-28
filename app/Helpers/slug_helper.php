<?php

if (!function_exists('unique_slug')) {
    /**
     * Generate a unique slug for a given table/column by appending a numeric suffix
     * when the base slug already exists. Prevents DB unique-constraint 500 errors.
     *
     * @param string       $slug      Base slug (already url_title'd)
     * @param string       $table     Table name to check
     * @param string       $column    Unique column (default 'slug')
     * @param int|null     $ignoreId  Row ID to exclude (for updates)
     * @param string       $separator Separator between slug and suffix
     */
    function unique_slug(string $slug, string $table, string $column = 'slug', ?int $ignoreId = null, string $separator = '-'): string
    {
        $db = \Config\Database::connect();
        $base = $slug;
        $i = 2;

        while (true) {
            $builder = $db->table($table)->where($column, $slug);
            if ($ignoreId !== null) {
                $builder->where('id !=', $ignoreId);
            }
            $exists = $builder->countAllResults() > 0;

            if (!$exists) {
                return $slug;
            }
            $slug = $base . $separator . $i++;
        }
    }
}
