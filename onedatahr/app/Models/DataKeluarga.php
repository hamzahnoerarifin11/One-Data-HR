<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DataKeluarga extends Model
{
    protected $table = 'data_keluarga';
    protected $primaryKey = 'id_keluarga';
    public $timestamps = false;
    protected $fillable = [
        'id_karyawan',
        'Nama_Ayah_Kandung','Nama_Ibu_Kandung','Nama_Lengkap_Suami_Istri','NIK_KTP_Suami_Istri','Tempat_Lahir_Suami_Istri',
        'Tanggal_Lahir_Suami_Istri','Nomor_Telepon_Suami_Istri','Pendidikan_Terakhir_Suami_Istri', 'anak',
        'Nama_Lengkap_Anak_Pertama','Tempat_Lahir_Anak_Pertama','Tanggal_Lahir_Anak_Pertama','Jenis_Kelamin_Anak_Pertama','Pendidikan_Terakhir_Anak_Pertama',
        'Nama_Lengkap_Anak_Kedua','Tempat_Lahir_Anak_Kedua','Tanggal_Lahir_Anak_Kedua','Jenis_Kelamin_Anak_Kedua','Pendidikan_Terakhir_Anak_Kedua',
        'Nama_Lengkap_Anak_Ketiga','Tempat_Lahir_Anak_Ketiga','Tanggal_Lahir_Anak_Ketiga','Jenis_Kelamin_Anak_Ketiga','Pendidikan_Terakhir_Anak_Ketiga',
        'Nama_Lengkap_Anak_Keempat','Tempat_Lahir_Anak_Keempat','Tanggal_Lahir_Anak_Keempat','Jenis_Kelamin_Anak_Keempat','Pendidikan_Terakhir_Anak_Keempat',
        'Nama_Lengkap_Anak_Kelima','Tempat_Lahir_Anak_Kelima','Tanggal_Lahir_Anak_Kelima','Jenis_Kelamin_Anak_Kelima','Pendidikan_Terakhir_Anak_Kelima',
        'Nama_Lengkap_Anak_Keenam','Tempat_Lahir_Anak_Keenam','Tanggal_Lahir_Anak_Keenam','Jenis_Kelamin_Anak_Keenam', 'Pendidikan_Terakhir_Anak_Keenam'
    ];
    protected $touches = ['karyawan'];
    protected $casts = [
        'anak' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

}
