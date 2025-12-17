<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'NIK','Status','Kode','Nama_Sesuai_KTP','NIK_KTP','Nama_Lengkap_Sesuai_Ijazah',
        'Tempat_Lahir_Karyawan','Tanggal_Lahir_Karyawan','Umur_Karyawan','Jenis_Kelamin_Karyawan',
        'Status_Pernikahan','Golongan_Darah','Nomor_Telepon_Aktif_Karyawan','Email','Alamat_KTP',
        'RT','RW','Kelurahan_Desa','Kecamatan','Kabupaten_Kota','Provinsi','Alamat_Domisili',
        'RT_Sesuai_Domisili','RW_Sesuai_Domisili','Kelurahan_Desa_Domisili','Kecamatan_Sesuai_Domisili',
        'Kabupaten_Kota_Sesuai_Domisili','Provinsi_Sesuai_Domisili','Alamat_Lengkap'
    ];

    // relasi: satu karyawan memiliki satu data keluarga, bpjs, perusahaan, status
    public function pekerjaan()
    {
        return $this->hasMany(Pekerjaan::class, 'id_karyawan', 'id_karyawan');
    }

    public function pendidikan()
    {
        return $this->hasOne(Pendidikan::class, 'id_karyawan', 'id_karyawan');
    }

    public function kontrak()
    {
        return $this->hasOne(Kontrak::class, 'id_karyawan', 'id_karyawan');
    }

    public function keluarga()
    {
        return $this->hasOne(DataKeluarga::class, 'id_karyawan', 'id_karyawan');
    }

    public function bpjs()
    {
        return $this->hasOne(Bpjs::class, 'id_karyawan', 'id_karyawan');
    }

    public function perusahaan()
    {
        return $this->hasOne(Perusahaan::class, 'id_karyawan', 'id_karyawan');
    }

    public function status()
    {
        return $this->hasOne(StatusKaryawan::class, 'id_karyawan', 'id_karyawan');
    }
}
