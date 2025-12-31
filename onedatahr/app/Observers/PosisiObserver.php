<?php

namespace App\Observers;

use App\Models\Posisi;

class PosisiObserver
{
    public function updated(Posisi $posisi)
    {
        // wasChanged memastikan kolom benar-benar telah tersimpan dengan nilai baru di DB
        if ($posisi->wasChanged('status')) {
            $kandidatObserver = new \App\Observers\KandidatObserver();
            // Kita panggil fungsi refresh
            $kandidatObserver->refreshPosisiProgress($posisi->id_posisi);
        }
    }
}
