<?php

namespace Modules\Journals\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Journals\Models\Journal;
use Modules\Journals\Models\JournalEditorialBoard;
use Modules\Usermanagement\Models\User;

class JournalDummySeeder extends Seeder
{
    /**
     * Run the database seeds for dummy journals and editorial boards.
     */
    public function run(): void
    {
        // 1. Data Dummy Jurnal (Campuran Aktif & Tidak Aktif, Berbagai Bidang Riset)
        $journalsData = [
            [
                'name'               => 'Ignite Journal of Genomics and Biotechnology',
                'slug'               => 'ignite-journal-of-genomics-and-biotechnology',
                'short_name'         => 'IJGB',
                'issn_p'             => '2715-1029',
                'issn_e'             => '2715-1037',
                'description'        => 'Jurnal ilmiah internasional bereputasi yang memuat artikel hasil penelitian di bidang genomika molekuler, bioinformatika, dan bioteknologi kesehatan.',
                'scope'              => 'Human Genomics, Medical Biotechnology, Bioinformatics, Gene Editing, Structural Biology.',
                'guidelines'         => 'Naskah harus ditulis dalam bahasa Inggris sesuai format IEEE/APA. Maksimal 8.000 kata termasuk referensi.',
                'publication_ethics' => 'IJGB menerapkan double-blind peer review dan mengacu pada standar COPE (Committee on Publication Ethics).',
                'is_active'          => true,
            ],
            [
                'name'               => 'Journal of Tropical Health and Infectious Diseases',
                'slug'               => 'journal-of-tropical-health-and-infectious-diseases',
                'short_name'         => 'JTHID',
                'issn_p'             => '2809-4412',
                'issn_e'             => '2809-4420',
                'description'        => 'Fokus pada studi epidemiologi, penyakit tropis terabaikan, transmisi vektor, dan kedokteran pencegahan di kawasan tropis.',
                'scope'              => 'Tropical Medicine, Vector-borne Diseases, Epidemiology, Global Health, Vaccine Development.',
                'guidelines'         => 'Pengiriman naskah wajib menyertakan surat persetujuan komite etika penelitian.',
                'publication_ethics' => 'Pemeriksaan plagiarisme dilakukan dengan Turnitin (maksimal similarity index 15%).',
                'is_active'          => true,
            ],
            [
                'name'               => 'Ignite Bulletin of Data Science and Artificial Intelligence',
                'slug'               => 'ignite-bulletin-of-data-science-and-ai',
                'short_name'         => 'IBDSAI',
                'issn_p'             => '2962-8811',
                'issn_e'             => '2962-882X',
                'description'        => 'Wadah publikasi riset terapan komputasi cerdas, pembelajaran mesin, dan analisis data biomedis.',
                'scope'              => 'Machine Learning in Healthcare, Medical Imaging AI, Big Data Analytics, Neural Networks.',
                'guidelines'         => 'Kode sumber dan dataset terbuka disarankan dilampirkan sebagai bahan bukti validasi.',
                'publication_ethics' => 'Penulis harus menyatakan secara jujur penggunaan alat bantu AI generative dalam pembuatan naskah.',
                'is_active'          => true,
            ],
            [
                'name'               => 'Archived Journal of Traditional Medicine and Herbal Research',
                'slug'               => 'archived-journal-of-traditional-medicine',
                'short_name'         => 'AJTMHR',
                'issn_p'             => '1907-3321',
                'issn_e'             => '2442-9910',
                'description'        => 'Jurnal pengobatan tradisional yang saat ini berstatus tidak aktif / diarsipkan.',
                'scope'              => 'Ethnobotany, Herbal Formulations, Natural Product Chemistry.',
                'guidelines'         => 'Jurnal ini sudah tidak menerima pengiriman naskah baru.',
                'publication_ethics' => 'Arsip riset tetap dipertahankan untuk referensi akademik.',
                'is_active'          => false,
            ],
            [
                'name'               => 'Indonesian Journal of Clinical Diagnostics and Pathology',
                'slug'               => 'indonesian-journal-of-clinical-diagnostics',
                'short_name'         => 'IJCDP',
                'issn_p'             => '2654-7789',
                'issn_e'             => '2654-7797',
                'description'        => 'Publikasi klinis yang menyajikan studi laboratorium medik, patologi anatomi, dan patologi klinik.',
                'scope'              => 'Molecular Pathology, Clinical Biochemistry, Hematology, Histopathology.',
                'guidelines'         => 'Menampilkan foto mikroskopi dengan resolusi minimal 300 DPI.',
                'publication_ethics' => 'Semua sampel pasien wajib di-anonimkan demi privasi medis.',
                'is_active'          => true,
            ],
        ];

        // Ambil sampel pengguna terdaftar jika ada untuk dihubungkan
        $firstUser = User::first();

        foreach ($journalsData as $jData) {
            $journal = Journal::updateOrCreate(
                ['slug' => $jData['slug']],
                $jData
            );

            // 2. Data Dummy Tim Redaksi per Jurnal dengan Berbagai Role & Status
            if ($journal->is_active) {
                $boardMembers = [
                    [
                        'user_id'     => $firstUser?->id,
                        'name'        => 'Prof. Dr. Ir. Budi Santoso, M.Sc., Ph.D.',
                        'email'       => 'budi.santoso@ignite-institute.org',
                        'affiliation' => 'Ignite Center for Genomics & Health Studies',
                        'role'        => 'Editor-in-Chief',
                        'order_no'    => 1,
                        'is_active'   => true,
                    ],
                    [
                        'user_id'     => null,
                        'name'        => 'Dr. med. Sarah Wijaya, Sp.PK',
                        'email'       => 'sarah.wijaya@med.ac.id',
                        'affiliation' => 'Department of Clinical Pathology, Faculty of Medicine',
                        'role'        => 'Managing Editor',
                        'order_no'    => 2,
                        'is_active'   => true,
                    ],
                    [
                        'user_id'     => null,
                        'name'        => 'Dr. Eng. Ahmad Riza, S.T., M.Sc.',
                        'email'       => 'ahmad.riza@lab.go.id',
                        'affiliation' => 'National Research and Innovation Agency (BRIN)',
                        'role'        => 'Section Editor',
                        'order_no'    => 3,
                        'is_active'   => true,
                    ],
                    [
                        'user_id'     => null,
                        'name'        => 'Prof. Elizabeth Montgomery, Ph.D.',
                        'email'       => 'e.montgomery@cambridge-genomics.uk',
                        'affiliation' => 'Cambridge Institute of Molecular Biology, UK',
                        'role'        => 'International Advisory Board',
                        'order_no'    => 4,
                        'is_active'   => true,
                    ],
                    [
                        'user_id'     => null,
                        'name'        => 'Dr. Hendra Gunawan (Emeritus)',
                        'email'       => 'hendra.gunawan@retired-ac.id',
                        'affiliation' => 'Department of Microbiology',
                        'role'        => 'Editorial Board Member',
                        'order_no'    => 5,
                        'is_active'   => false, // Anggota tidak aktif untuk testing status
                    ],
                ];

                foreach ($boardMembers as $mData) {
                    JournalEditorialBoard::updateOrCreate(
                        [
                            'journal_id' => $journal->id,
                            'role'       => $mData['role'],
                            'name'       => $mData['name'],
                        ],
                        $mData
                    );
                }
            }
        }
    }
}
