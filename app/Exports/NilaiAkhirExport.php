<?php

namespace App\Exports;

use App\Models\NilaiAkhir;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class NilaiAkhirExport implements FromCollection, WithHeadings, WithColumnFormatting
{
    protected $prodi_id;
    protected $KPA_id;
    protected $TM_id;
    protected $token;

    public function __construct($prodi_id, $KPA_id, $TM_id, $token)
    {
        $this->prodi_id = $prodi_id;
        $this->KPA_id = $KPA_id;
        $this->TM_id = $TM_id;
        $this->token = $token;
    }

    public function collection()
    {
        // Get data from database
        $nilai_akhir = DB::table('nilai_mahasiswa')
            ->join('kelompok_mahasiswa', 'nilai_mahasiswa.user_id', '=', 'kelompok_mahasiswa.user_id')
            ->join('kelompok', 'kelompok_mahasiswa.kelompok_id', '=', 'kelompok.id')
            ->where('kelompok.prodi_id', $this->prodi_id)
            ->where('kelompok.KPA_id', $this->KPA_id) // Fixed this line
            ->where('kelompok.TM_id', $this->TM_id)
            ->select('nilai_mahasiswa.*', 'kelompok.nomor_kelompok')
            ->get();

        // Fetch mahasiswa data from external API
        $response = Http::withHeaders([
            'Authorization' => "Bearer $this->token"
        ])->get(env('API_URL') . "library-api/mahasiswa", [
            'limit' => 100
        ]);

        $mahasiswa = collect();
        if ($response->successful()) {
            $data = $response->json();
            $listMahasiswa = $data['data']['mahasiswa'] ?? [];
            $mahasiswa = collect($listMahasiswa)->keyBy('user_id');
        }

        // Merge mahasiswa data with nilai_akhir
        foreach ($nilai_akhir as $nilai) {
            if (isset($mahasiswa[$nilai->user_id])) {
                $nilai->nim = $mahasiswa[$nilai->user_id]['nim'];
                $nilai->nama = $mahasiswa[$nilai->user_id]['nama']; // Assuming 'nim' exists in the API response
            }
        }

        $finalData = $nilai_akhir->map(function ($nilai) {
            return [
                'kelompok' => $nilai->nomor_kelompok,
                'nim' => $nilai->nim,
                'nama' => $nilai->nama,
                'nilai_akhir' => $nilai->nilai_akhir,
            ];
        });

        return $finalData;
    }

    public function headings(): array
    {
        return [
            'Nomor Kelompok',
            'Nim',
            'Nama',
            'Nilai Akhir',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_00, // Format nilai akhir with two decimal places
        ];
    }
}
