<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('getjabatan')) {
    function getjabatan(string $pekerjaan, string $Jabatan): array
    {
        $result = DB::selectOne(
            "SHOW COLUMNS FROM {$pekerjaan} WHERE Field = '{$Jabatan}'"
        );

        if (!$result) {
            return [];
        }

        preg_match("/^enum\((.*)\)$/", $result->Type, $matches);

        return collect(explode(',', $matches[1]))
            ->map(fn ($value) => trim($value, "'"))
            ->toArray();
    }
}
if (!function_exists('getdepartement')) {
function getdepartement(string $pekerjaan, string $Departement): array
    {
        $result = DB::selectOne(
            "SHOW COLUMNS FROM {$pekerjaan} WHERE Field = '{$Departement}'"
        );

        if (!$result) {
            return [];
        }

        preg_match("/^enum\((.*)\)$/", $result->Type, $matches);

        return collect(explode(',', $matches[1]))
            ->map(fn ($value) => trim($value, "'"))
            ->toArray();
    }
}
if (!function_exists('getdivisi')) {
    function getdivisi(string $pekerjaan, string $Divisi): array
    {
        $result = DB::selectOne(
            "SHOW COLUMNS FROM {$pekerjaan} WHERE Field = '{$Divisi}'"
        );

        if (!$result) {
            return [];
        }

        preg_match("/^enum\((.*)\)$/", $result->Type, $matches);

        return collect(explode(',', $matches[1]))
            ->map(fn ($value) => trim($value, "'"))
            ->toArray();
    }
}
if (!function_exists('getunit')) {
     function getunit(string $pekerjaan, string $Unit): array
    {
        $result = DB::selectOne(
            "SHOW COLUMNS FROM {$pekerjaan} WHERE Field = '{$Unit}'"
        );

        if (!$result) {
            return [];
        }

        preg_match("/^enum\((.*)\)$/", $result->Type, $matches);

        return collect(explode(',', $matches[1]))
            ->map(fn ($value) => trim($value, "'"))
            ->toArray();
    }
}
if (!function_exists('getlokasikerja')) {
    function getlokasikerja(string $pekerjaan, string $Lokasi_Kerja): array
    {
        $result = DB::selectOne(
            "SHOW COLUMNS FROM {$pekerjaan} WHERE Field = '{$Lokasi_Kerja}'"
        );

        if (!$result) {
            return [];
        }

        preg_match("/^enum\((.*)\)$/", $result->Type, $matches);

        return collect(explode(',', $matches[1]))
            ->map(fn ($value) => trim($value, "'"))
            ->toArray();
    }
if (!function_exists('getperusahaan')) {
    function getperusahaan(string $perusahaan, string $Perusahaan): array
    {
        $result = DB::selectOne(
            "SHOW COLUMNS FROM {$perusahaan} WHERE Field = '{$Perusahaan}'"
        );

        if (!$result) {
            return [];
        }

        preg_match("/^enum\((.*)\)$/", $result->Type, $matches);

        return collect(explode(',', $matches[1]))
            ->map(fn ($value) => trim($value, "'"))
            ->toArray();
    }
}
if (!function_exists('getpendidikan')) {
    function getpendidikan(string $pendidikan, string $Pendidikan_Terakhir): array
    {
        $result = DB::selectOne(
            "SHOW COLUMNS FROM {$pendidikan} WHERE Field = '{$Pendidikan_Terakhir}'"
        );

        if (!$result) {
            return [];
        }

        preg_match("/^enum\((.*)\)$/", $result->Type, $matches);

        return collect(explode(',', $matches[1]))
            ->map(fn ($value) => trim($value, "'"))
            ->toArray();
    }
}
}
