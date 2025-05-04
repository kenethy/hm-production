<?php

namespace App\Http\Controllers;

class BlogController extends Controller
{
    public function index()
    {
        // In a real application, you would fetch blog posts from the database
        $posts = [
            [
                'id' => 1,
                'title' => 'Tips Merawat Mobil di Musim Hujan',
                'excerpt' => 'Musim hujan bisa menjadi tantangan tersendiri untuk perawatan mobil. Berikut beberapa tips untuk menjaga mobil Anda tetap prima di musim hujan.',
                'date' => '2023-11-15',
                'image' => 'blog-1.jpg',
            ],
            [
                'id' => 2,
                'title' => 'Mengenal Jenis-jenis Oli Mobil',
                'excerpt' => 'Oli merupakan komponen penting dalam perawatan mobil. Ketahui jenis-jenis oli mobil dan perbedaannya untuk memilih yang tepat untuk kendaraan Anda.',
                'date' => '2023-10-20',
                'image' => 'blog-2.jpg',
            ],
            [
                'id' => 3,
                'title' => 'Cara Mengenali Kerusakan pada Sistem Rem',
                'excerpt' => 'Sistem rem yang baik sangat penting untuk keselamatan berkendara. Pelajari cara mengenali tanda-tanda kerusakan pada sistem rem mobil Anda.',
                'date' => '2023-09-05',
                'image' => 'blog-3.jpg',
            ],
        ];
        
        return view('pages.blog.index', [
            'title' => 'Blog',
            'posts' => $posts
        ]);
    }
    
    public function show($id)
    {
        // In a real application, you would fetch the specific blog post from the database
        $posts = [
            1 => [
                'id' => 1,
                'title' => 'Tips Merawat Mobil di Musim Hujan',
                'content' => '<p>Musim hujan bisa menjadi tantangan tersendiri untuk perawatan mobil. Berikut beberapa tips untuk menjaga mobil Anda tetap prima di musim hujan.</p>
                <h3>1. Periksa Wiper Secara Berkala</h3>
                <p>Wiper yang berfungsi dengan baik sangat penting untuk visibilitas saat hujan. Periksa karet wiper dan ganti jika sudah aus atau tidak bekerja optimal.</p>
                <h3>2. Pastikan Sistem Drainase Mobil Bersih</h3>
                <p>Sistem drainase yang tersumbat dapat menyebabkan air menggenang dan berpotensi merusak komponen mobil. Bersihkan saluran air di sekitar kap mesin dan bagian bawah kaca depan.</p>
                <h3>3. Periksa Kondisi Ban</h3>
                <p>Ban dengan alur yang baik sangat penting untuk mencegah aquaplaning saat hujan. Pastikan tekanan ban sesuai rekomendasi pabrikan.</p>
                <h3>4. Jaga Kebersihan Eksterior</h3>
                <p>Cuci mobil secara teratur untuk menghilangkan kotoran dan residu yang bisa merusak cat, terutama setelah mobil terkena air hujan yang mengandung asam.</p>
                <h3>5. Perhatikan Sistem Kelistrikan</h3>
                <p>Periksa semua lampu dan sistem kelistrikan mobil untuk memastikan semuanya berfungsi dengan baik, terutama saat visibilitas rendah karena hujan.</p>',
                'date' => '2023-11-15',
                'image' => 'blog-1.jpg',
            ],
            2 => [
                'id' => 2,
                'title' => 'Mengenal Jenis-jenis Oli Mobil',
                'content' => '<p>Oli merupakan komponen penting dalam perawatan mobil. Ketahui jenis-jenis oli mobil dan perbedaannya untuk memilih yang tepat untuk kendaraan Anda.</p>
                <h3>1. Oli Mineral</h3>
                <p>Oli mineral adalah jenis oli paling dasar yang terbuat dari minyak bumi yang dimurnikan. Oli ini biasanya lebih murah tetapi memerlukan penggantian lebih sering.</p>
                <h3>2. Oli Semi-Sintetis</h3>
                <p>Oli semi-sintetis adalah campuran antara oli mineral dan sintetis. Memberikan perlindungan lebih baik daripada oli mineral dengan harga yang lebih terjangkau dibanding oli sintetis penuh.</p>
                <h3>3. Oli Sintetis Penuh</h3>
                <p>Oli sintetis penuh dibuat dari bahan kimia buatan yang dirancang khusus untuk memberikan performa optimal. Oli ini menawarkan perlindungan terbaik dan interval penggantian yang lebih lama.</p>
                <h3>4. Oli High Mileage</h3>
                <p>Oli high mileage dirancang khusus untuk mobil dengan jarak tempuh tinggi (biasanya di atas 100.000 km). Mengandung aditif untuk membantu mengurangi kebocoran dan konsumsi oli pada mesin tua.</p>
                <h3>5. Memilih Oli yang Tepat</h3>
                <p>Selalu ikuti rekomendasi pabrikan untuk jenis dan viskositas oli. Faktor seperti usia mobil, kondisi mesin, dan pola penggunaan juga perlu dipertimbangkan dalam memilih oli.</p>',
                'date' => '2023-10-20',
                'image' => 'blog-2.jpg',
            ],
            3 => [
                'id' => 3,
                'title' => 'Cara Mengenali Kerusakan pada Sistem Rem',
                'content' => '<p>Sistem rem yang baik sangat penting untuk keselamatan berkendara. Pelajari cara mengenali tanda-tanda kerusakan pada sistem rem mobil Anda.</p>
                <h3>1. Suara Berdecit atau Menggeram</h3>
                <p>Suara berdecit saat mengerem bisa menandakan kampas rem yang aus. Suara menggeram biasanya menunjukkan masalah pada piringan rem.</p>
                <h3>2. Pedal Rem Terasa Lembek</h3>
                <p>Jika pedal rem terasa lembek atau turun ke lantai saat ditekan, ini bisa menandakan kebocoran pada sistem hidrolik rem atau masalah pada master silinder.</p>
                <h3>3. Mobil Tertarik ke Satu Sisi Saat Mengerem</h3>
                <p>Jika mobil tertarik ke kanan atau kiri saat Anda mengerem, ini bisa menandakan kampas rem yang aus tidak merata atau kaliper rem yang macet.</p>
                <h3>4. Getaran Saat Mengerem</h3>
                <p>Getaran pada pedal rem atau setir saat mengerem biasanya menandakan piringan rem yang tidak rata atau bergelombang.</p>
                <h3>5. Lampu Indikator Rem Menyala</h3>
                <p>Jika lampu indikator rem di dashboard menyala, segera periksa sistem rem Anda. Ini bisa menandakan berbagai masalah, dari kampas rem yang aus hingga masalah pada sistem ABS.</p>',
                'date' => '2023-09-05',
                'image' => 'blog-3.jpg',
            ],
        ];
        
        if (!isset($posts[$id])) {
            abort(404);
        }
        
        return view('pages.blog.show', [
            'title' => $posts[$id]['title'],
            'post' => $posts[$id]
        ]);
    }
}
