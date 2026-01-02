<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KbiItemSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'KOMUNIKATIF' => [
                'Sopan dan santun dalam berkomunikasi serta menghargai perbedaan',
                'Menyampaikan informasi secara sistematis, akurat dan mudah dipahami',
                'Mudah bersinergi dan berkolaborasi baik dengan sesama tim, lintas fungsi maupun pihak eksternal'
            ],
            'UNGGUL' => [
                'Menetapkan standar kinerja tinggi dan konsisten mencapainya',
                'Selalu mencari solusi inovatif dalam bekerja',
                'Berkontribusi pada pencapaian kinerja yang lebih tinggi dibanding standar'
            ],
            'AGAMIS' => [
                'Menjalankan nilai spiritual dalam bekerja secara konsisten',
                'Mengedepankan kejujuran dan keberkahan sebagai fondasi keputusan kerja',
                'Menjaga perilaku sesuai etika dan ajaran agama'
            ],
            'TANGGUNG JAWAB' => [
                'Menyelesaikan tugas dengan tepat waktu, sesuai target perusahaan',
                'Bertanggung jawab atas dampak kualitas produk dan layanan ke pelanggan',
                'Berani mengakui kesalahan dan mengambil inisiatif perbaikan'
            ],
            'MANFAAT' => [
                'Selalu mempertimbangkan nilai tambah produk atau proses bagi pelanggan atau pengguna akhir',
                'Berkontribusi terhadap proyek atau program yang berdampak luas',
                'Menghindari aktivitas yang tidak berdampak pada efisiensi atau kualitas kerja'
            ],
            'EMPATI' => [
                'Tanggap terhadap kebutuhan rekan kerja maupun customer',
                'Bersedia membantu saat ada anggota tim atau mitra dalam kesulitan',
                'Menunjukkan kepedulian dalam komunikasi dan tindakan sehari-hari'
            ],
            'MORAL' => [
                'Memperlakukan semua orang secara adil tanpa memandang jabatan atau latar belakang',
                'Menolak segala bentuk perilaku tidak etis, manipulatif, atau diskriminatif',
                'Menjunjung tinggi integritas dalam pengambilan keputusan'
            ],
            'BELAJAR' => [
                'Proaktif mencari ilmu, teknologi, dan best practice terbaru untuk pekerjaan maupun tentang industri global',
                'Menerapkan pembelajaran dalam proyek nyata untuk peningkatan berkelanjutan',
                'Berbagi pengetahuan dan keahlian ke anggota tim'
            ],
            'AMANAH' => [
                'Menjaga kerahasiaan data, dokumen, dan keputusan strategis dalam lingkup divisi maupun perusahaan',
                'Menjalankan amanah dari atasan, pelanggan, dan mitra tanpa penyimpangan',
                'Dipercaya untuk menangani pekerjaan penting atau sensitif'
            ],
            'JUJUR' => [
                'Tidak memanipulasi data, laporan, atau hasil kerja',
                'Menyampaikan kendala yang dihadapi dengan transparan',
                'Konsisten antara ucapan dan tindakan, terutama dalam kerja tim'
            ],
            'ANTUSIAS' => [
                'Selalu terlihat bersemangat menghadapi tugas baru atau proyek lintas tim',
                'Menunjukkan energi positif meski dalam tekanan atau tantangan pekerjaan',
                'Menjadi penyemangat dan pendorong semangat tim'
            ],
        ];

        foreach ($data as $kategori => $items) {
            foreach ($items as $perilaku) {
                DB::table('kbi_items')->insert([
                    'kategori' => $kategori,
                    'perilaku' => $perilaku,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}