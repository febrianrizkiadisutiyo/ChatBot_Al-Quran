<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Verse;
use Telegram\Bot\Api;
use App\Models\Verses;
use App\Models\Chapter;
use App\Models\Chapters;
use App\Models\word_verses;
use Illuminate\Http\Request;
use App\Models\word_translations;
use Illuminate\Support\Facades\DB;
use App\Models\verses_translations;
use App\Http\Controllers\Controller;
use App\Models\WordVerse;
use Illuminate\Support\Facades\Storage;

class TeleBotController extends Controller
{

    protected $telegram;

    //start
    const QSTART_GUIDE = "<strong>START ALQURAN</strong>".PHP_EOL
    ."Selamat Datang Di QuranType Pencarian Tafsir dan Latin Alquran"
    .PHP_EOL.PHP_EOL
    ."Menu-Menu Yang Tersedia"
    .PHP_EOL
    ."/start Start "
    .PHP_EOL
    ."/qn Quran by Number"
    .PHP_EOL
    ."/qv Quran by View (Latin dan Tafsir)"
    .PHP_EOL
    ."/qvwbw Quran View Word by Word (Ayat, Latin Perkata)"
    .PHP_EOL
    ."/qtaf Quran Tafsir"
    .PHP_EOL
    ."/qstaf Quran SearchTafsir"
    .PHP_EOL
    ."/qslatin Quran Search Transliteration (Latin)";

    //fitur per-ayat
    const QN_GUIDE = "<strong>QN</strong>".PHP_EOL
    ."Menu ini untuk menampilkan Surat dan Ayat Al-Quran."
    .PHP_EOL.PHP_EOL
    ."Cara menggunakan:"
    .PHP_EOL
    ."<code>Silahkan  mengetik Surat dan Ayat yang dicari."
    .PHP_EOL
    ."/qn [surat]:[ayat]</code>"
    .PHP_EOL.PHP_EOL
    ."Contoh:"
    .PHP_EOL
    ."<code>/qn 1:1</code>"
    .PHP_EOL
    ."<code>/qn 1:1-3</code>";

    const QV_GUIDE = "<strong>QV</strong>".PHP_EOL
    ."Menu ini untuk menampilkan salah satu Ayat Al-Quran beserta informasi lengkap (latin dan tafsir)."
    .PHP_EOL.PHP_EOL
    ."Cara menggunakan:"
    .PHP_EOL
    ."<code>Silahkan  mengetik Surat dan Ayat yang dicari."
    .PHP_EOL
    ."/qv [surat]:[ayat]</code>"
    .PHP_EOL.PHP_EOL
    ."Contoh:"
    .PHP_EOL
    ."<code>/qv 1:1</code>";

    const QVWORD_GUIDE = "<strong>QV</strong>".PHP_EOL
    ."Menu ini untuk menampilkan Ayat Al-Quran Perkata beserta informasi latin"
    .PHP_EOL.PHP_EOL
    ."Cara menggunakan:"
    .PHP_EOL
    ."<code>Silahkan  mengetik Surat, Ayat, dan Kata Keberapa yang dicari."
    .PHP_EOL
    ."/qvwbw_[surat]_[ayat]_[Nomor Kata]</code>"
    .PHP_EOL.PHP_EOL
    ."Contoh:"
    .PHP_EOL
    ."<code>/qvwbw_1_1_1</code>";
    const QTAF_GUIDE = "<strong>QTAF</strong>".PHP_EOL
    ."Menu ini untuk menampilkan Tafsir berdasarkan Ayat Al-Quran dari beberapa sumber Tafsir.
    Daftar Tafsir :
    1. Long (Panjang)- Kemenag
    2. Short (Pendek)- Kemenag
    3. Quraish - Muhammad Quraish Shihab
    4. Jalalayn - Jalaluddin al-Mahalini dan Jalaluddin as-Suyuthi
    5. Tafsir Ibn Kathir - Hafiz Ibn Kathir - Indonesia
    6. Tafsir Ibn Kathir - Hafiz Ibn Kathir - English"
    .PHP_EOL.PHP_EOL
    ."Cara menggunakan:"
    .PHP_EOL
    ."<code>Silahkan  mengetik Surat dan Ayat yang dicari."
    .PHP_EOL
    ."/qtaf_[tafsir]_[surat]_[ayat]</code>"
    .PHP_EOL.PHP_EOL
    ."Contoh:"
    .PHP_EOL
    ."<code>/qtaf_1_1_1</code>";

    const QTAFWEB_GUIDE = "".PHP_EOL
    ."<a href='https://6ae6-114-125-38-90.ap.ngrok.io/tafsir'>Silahkan Klik</a>"
    .PHP_EOL.PHP_EOL;

    const QSTAF_GUIDE = "<strong>QS</strong>".PHP_EOL
    ."Menu ini untuk mencari Tafsir Al-Quran berdasarkan Keyword."
    .PHP_EOL.PHP_EOL
    ."Cara menggunakan:"
    .PHP_EOL
    ."<code>Silahkan  mengetik Keyword yang dicari."
    .PHP_EOL
    ."/qstaf [keyword]</code>"
    .PHP_EOL.PHP_EOL
    ."Contoh:"
    .PHP_EOL
    ."<code>/qstaf kebahagiaan</code>";

    const QSLATIN_GUIDE = "<strong>QS</strong>".PHP_EOL
    ."Menu ini untuk mencari Latin Al-Quran berdasarkan Keyword."
    .PHP_EOL.PHP_EOL
    ."Cara menggunakan:"
    .PHP_EOL
    ."<code>Silahkan  mengetik Keyword yang dicari."
    .PHP_EOL
    ."/qslatin [keyword]</code>"
    .PHP_EOL.PHP_EOL
    ."Contoh:"
    .PHP_EOL
    ."<code>/qslatin bis'mi</code>";

    public function __construct() {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    public function sendMessage($id) {
        return $this->telegram->sendMessage([
            'chat_id' => $id,
            'text' => 'Assalamualaikum'
        ]);
    }
    public function messages() {
        return $this->telegram->getUpdates();
    }

    public function setWebhook(){

        $url = 'https://5c65-140-213-148-203.ngrok-free.app';

        $this->telegram->setWebhook([
            'url' => $url.'/api/telegram/webhook/'.env('TELEGRAM_BOT_TOKEN')
        ]);
        return ['message' => 'webhook ready'];
    }

    public function webhook(Request $request) {

        try {
            $userId = $request['message'] ['from'] ['id'];

            $input = $request['message']['text'];
    
            $chat = explode(" ", $input);
            $chat2 = explode("_", $input);
            // $menu = $chat[0];
            // $menu2 = $chat2[0];
            
            //menu qn
            if ($chat[0] == "/qn") {
                if (sizeof($chat) <= 1) {
                    $message = self::QN_GUIDE;
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => $message
                    ]);
                } elseif (sizeof($chat) == 2) {
                    $verse_key = $chat[1];
                    //pisah surah dan ayat
                    $pecah = explode(":", $verse_key);
                    $idChapter = $pecah[0];
                    $verseNumber = $pecah[1];
                    if ($idChapter <= 114 && $idChapter >= 1) {
                        //pecah ayat
                        $pecahVerseNumber = explode("-", $verseNumber);
                        if (sizeof($pecahVerseNumber) == 1) {
                            $firstVerses = $pecahVerseNumber[0];
                            //menghitung banyak ayat di dalam surah
                            $count = Verse::where('id_chapter', $idChapter)->count();
                            if ($firstVerses <= $count) {
                                $chapters = Chapter::query()->where('id', $idChapter)->firstOrFail();
                                $verses = Verse::query()->where('id_chapter', $idChapter)->where('number', $firstVerses)->get();
                                $messageOut = self::getQNAnswere($verses, $chapters);
                                $this->telegram->sendMessage([
                                    'parse_mode'=>'HTML',
                                    'chat_id'=> $userId,
                                    'text'=> $messageOut
                                ]);
                            } else {
                                $this->telegram->sendMessage([
                                    'parse_mode'=>'HTML',
                                    'chat_id'=> $userId,
                                    'text'=> "Jumlah Ayat pada Surah ini hanya $count"
                                ]);
                            }

                        //menampilkan lebih dari 1 ayat
                        } elseif (sizeof($pecahVerseNumber)==2){
                            
                            $firstVerses = $pecahVerseNumber[0];
                            $lastverses = $pecahVerseNumber[1];
                            $versesBetween = Verse::query()->where('id_chapter', $idChapter)
                            ->whereBetween('number', [$firstVerses,$lastverses])->get();
                            $chapters = Chapter::query()->where('id', $idChapter)->firstOrFail();
                            //ambil text dari fungsi
                            // $messageOut = self::getQNAnswere($versesBetween, $chapters);
                            // $this->telegram->sendMessage([
                            //     'parse_mode'=>'HTML',
                            //     'chat_id' =>$userId,
                            //     'text' => $messageOut
                            // ]);
                            $messageOut = self::getQNAnswere($versesBetween, $chapters);
                            $splitVerse = str_split($messageOut, 4090);
                            foreach ($splitVerse as $value){
                                try {
                                    $this->telegram->sendMessage([
                                        'parse_mode'=>'HTML',
                                        'chat_id' => $userId,
                                        'text' => htmlspecialchars($value)
                                    ]);
                                } catch (\Exception $e) {
                                    $this->telegram->sendMessage([
                                        'parse_mode'=>'HTML',
                                        'chat_id' => $userId,
                                        'text' => "Error bagian qn"
                                    ]);
                                }
                            }
                            
                        }else{
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' =>$userId,
                                'text' => 'Perintah yang Anda Masukkan Salah'
                            ]); 
                        }
                       
                    } else {
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' =>$userId,
                            'text' => 'Jumlah Surah Pada Alquran hanya 114'
                        ]);
                    }
                }
                //menu qv
            } elseif ($chat[0] == "/qv") {
                if (sizeof($chat) <= 1) {
                    $message = self::QV_GUIDE;
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => $message
                    ]);
                }elseif (sizeof($chat) == 2) {
                    // dapat kan nomor surat dan nomor ayat
                 $noChapAndNoVers = explode(":", $chat[1]);
                 $idChap = $noChapAndNoVers[0];
                 $numVer = $noChapAndNoVers[1];
                 if ($idChap <= 114 && $numVer >= 1){
                    $pecahVerseNumber = explode("-", $numVer);
                    $firstVerses = $pecahVerseNumber[0];
                    $count = Verse::where('id_chapter', $idChap)->count();
                    if (sizeof($noChapAndNoVers)!=2) {
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => "perintah yang anda masukkan salah"
                        ]);
                    }else{
                        if ($firstVerses <= $count){
                            // ini isi chapter dan ayat
                            $chapters = Chapter::where('id', $idChap)->first();
                            $verses = Verse::query()->where('id_chapter', $idChap)->where('number', $firstVerses)->first();
                            $messageOut = self::getQVAnswer($verses, $chapters);
                            $this->telegram->sendMessage([
                                    'parse_mode'=>'HTML',
                                    'chat_id' => $userId,
                                    'text' => $messageOut
                                ]);
                        } else{
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id'=> $userId,
                                'text'=> "Jumlah Ayat pada Surah ini hanya $count"
                            ]);
                        }
                    }
                    
                     
                 } else  {
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' =>$userId,
                        'text' => 'Jumlah Surah Pada Alquran hanya 114'
                    ]);
                }
                
                //  // dapat kan nomor surat dan nomor ayat
                //  $noChapAndNoVers = explode(":", $chat[1]);
                //  if (sizeof($noChapAndNoVers)!=2) {
                //     //  throw new  \Exception("Format yang anda masukkan salah.", -1);
                //     $this->telegram->sendMessage([
                //         'parse_mode'=>'HTML',
                //         'chat_id' => $userId,
                //         'text' => "perintah yang anda masukkan salah"
                //     ]);
                //  }else {
                //       // ini isi chapter dan ayat
                //  $chapters = Chapter::where('id', $noChapAndNoVers[0])->first();
                //  $verses = Verse::query()->where('id_chapter', $chapters->id)->where('number', $noChapAndNoVers[1])->first();
                //  $messageOut = self::getQVAnswer($verses, $chapters);
                //  $this->telegram->sendMessage([
                //          'parse_mode'=>'HTML',
                //          'chat_id' => $userId,
                //          'text' => $messageOut
                //      ]);
                //  }
               
                } else {
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' =>$userId,
                        'text' => 'Jumlah Surah Pada Alquran hanya 114'
                    ]);
                }
    
            //menu qv untuk pencarian latin dan qn
            }elseif ($chat2[0] == "/qv") {
                if (sizeof($chat2) <= 1) {
                    $message = self::QV_GUIDE;
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => $message
                    ]);
                }else {
   
                 $chapters = Chapter::where('id', $chat2[1])->first();
                 $verses = Verse::query()->where('id_chapter', $chapters->id)->where('number', $chat2[2])->first();
                 $messageOut = self::getQVAnswer($verses, $chapters);
                 $this->telegram->sendMessage([
                         'parse_mode'=>'HTML',
                         'chat_id' => $userId,
                         'text' => $messageOut
                     ]);
                }
            }elseif ($chat2[0] == "/qvwbw"){
               if (sizeof($chat2) <= 1) {
                $message = self::QVWORD_GUIDE;
                $this->telegram->sendMessage([
                    'parse_mode'=>'HTML',
                    'chat_id' => $userId,
                    'text' => $message
                ]);
               } else {
                $chapters = Chapter::where('id', $chat2[1])->first();
                $verses = Verse::query()->where('id_chapter', $chapters->id)->where('number', $chat2[2])->first();
                $word_verses = WordVerse::query()->where('id_verse', $verses->id)->where('number', $chat2[3])->first();
                $messageOut =self::getWord($word_verses, $verses, $chapters);
                $this->telegram->sendMessage([
                    'parse_mode'=>'HTML',
                    'chat_id' => $userId,
                    'text' => $messageOut,
                ]);
               }
            } elseif($chat2[0] == "/qtaf"){
                if (sizeof($chat2) <= 1) {
                    $messageOut = self::QTAF_GUIDE;
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => $messageOut,
                        ]);
                } else {
                    $tafsir = DB::table('verse_tafsirs')
                                ->join('verses', 'verse_tafsirs.id_verse', '=', 'verses.id')
                                ->join('chapters','verses.id_chapter', '=', 'chapters.id')
                                ->join('tafsirs', 'verse_tafsirs.id_tafsir', '=', 'tafsirs.id')
                                ->where('tafsirs.id',$chat2[1])
                                ->where('verses.id_chapter',$chat2[2])
                                ->where('verses.number',$chat2[3])
                                ->get(['chapters.name as nameChapter','chapters.number_chapter','verses.number',
                                'verses.text_uthmani','verse_tafsirs.text','tafsirs.name','tafsirs.author_name',
                                'tafsirs.language_name']);
                        $messageOut = self::getTaf($tafsir);

                        $split = str_split($messageOut, 4090);
                      
                        foreach ($split as $item) {
                            try {
                                //code...
                                $this->telegram->sendMessage([
                                    'parse_mode'=>'HTML',
                                    'chat_id' => $userId,
                                    'text' => $item
                                ]);
                            } catch (\Exception $e) {
                                //throw $th;
                                $this->telegram->sendMessage([
                                    'parse_mode'=>'HTML',
                                    'chat_id' => $userId,
                                    'text' => report($e)
                                ]);
                            }                           
                        }
                }
    
            }elseif($chat[0]== "/qstaf"){
                $messageOut = self::QSTAF_GUIDE;
                if(sizeof($chat)<= 1){
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut
                    ]);
                }else{
                    $cari = substr($input, 7);
                                $caritaf = DB::table('verse_tafsirs')
                                ->join('verses','verse_tafsirs.id_verse', '=', 'verses.id')
                                ->join('chapters','verses.id_chapter', '=', 'chapters.id')
                                ->join('tafsirs', 'verse_tafsirs.id_tafsir', '=', 'tafsirs.id')
                                ->where('verse_tafsirs.text','LIKE','%'.$cari.'%')
                                ->get(['verse_tafsirs.id_tafsir','chapters.name','chapters.number_chapter','verses.number']);
                    
                                $countTaf = DB::table('verse_tafsirs')
                                ->join('verses','verse_tafsirs.id_verse', '=', 'verses.id')
                                ->join('chapters','verses.id_chapter', '=', 'chapters.id')
                                ->join('tafsirs', 'verse_tafsirs.id_tafsir', '=', 'tafsirs.id')
                                ->where('verse_tafsirs.text','LIKE','%'.$cari.'%')->get()
                                ->count();

                    if($countTaf == 0){
                        $messageOut = 'Terdapat <strong>'.$countTaf.' tafsir ditemukan</strong>, silahkan coba dengan kata kunci lain.';
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => $messageOut
                        ]);
                    }elseif($countTaf <= 2828){
                        try {
                            $messageOut = 'Terdapat <strong>'.$countTaf.' tafsir ditemukan</strong>, silahkan pilih salah satu :';
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => $messageOut
                            ]);
                            if ($countTaf >= 130){
                                $length = 0;
                                while ($countTaf >= 130) {
                                    $countTaf -= 130;
                                    $length++;
                                }
                                for ($i = 0; $i <= $length; $i++){
                                    $skip = $i * 130;
                                    $caritaf = DB::table('verse_tafsirs')
                                        ->join('verses','verse_tafsirs.id_verse', '=', 'verses.id')
                                        ->join('chapters','verses.id_chapter', '=', 'chapters.id')
                                        ->join('tafsirs', 'verse_tafsirs.id_tafsir', '=', 'tafsirs.id')
                                        ->where('verse_tafsirs.text','LIKE','%'.$cari.'%')
                                        ->skip($skip)->take(130)
                                        ->get(['verse_tafsirs.id_tafsir','chapters.name','chapters.number_chapter','verses.number']);
                                   
                                        $messageTaf = self::getCariTaf($caritaf);
                                            $this->telegram->sendMessage([
                                                'parse_mode'=>'HTML',
                                                'chat_id' => $userId,
                                                'text' => $messageTaf
                                            ]);
                                }  
                                
                            }else {       
                                $caritaf = DB::table('verse_tafsirs')
                                ->join('verses','verse_tafsirs.id_verse', '=', 'verses.id')
                                ->join('chapters','verses.id_chapter', '=', 'chapters.id')
                                ->join('tafsirs', 'verse_tafsirs.id_tafsir', '=', 'tafsirs.id')
                                ->where('verse_tafsirs.text','LIKE','%'.$cari.'%')
                                ->get(['verse_tafsirs.id_tafsir','chapters.name','chapters.number_chapter','verses.number']);                             
                                $messageTaf = self::getCariTaf($caritaf);
                                $this->telegram->sendMessage([
                                    'parse_mode'=>'HTML',
                                    'chat_id' => $userId,
                                    'text' => $messageTaf
                                ]);
                            }
                        }catch (Exception $e) {
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => error_log($e)
                            ]);
                        }  
                    
                    }else {
                            $messageOut = 'Terdapat <strong>'.$countTaf.' tafsir ditemukan</strong>, silahkan pilih salah satu :';
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => $messageOut
                            ]);
                            if ($countTaf >= 130){
                                $length = 0;
                                while ($countTaf >= 130) {
                                    $countTaf -= 130;
                                    $length++;
                                }
                                for ($i = 0; $i <= 9; $i++){
                                    $skip = $i * 130;
                                    $caritaf = DB::table('verse_tafsirs')
                                        ->join('verses','verse_tafsirs.id_verse', '=', 'verses.id')
                                        ->join('chapters','verses.id_chapter', '=', 'chapters.id')
                                        ->join('tafsirs', 'verse_tafsirs.id_tafsir', '=', 'tafsirs.id')
                                        ->where('verse_tafsirs.text','LIKE','%'.$cari.'%')
                                        ->skip($skip)->take(130)
                                        ->get(['verse_tafsirs.id_tafsir','chapters.name','chapters.number_chapter','verses.number']);
                                   
                                        $messageTaf = self::getCariTaf($caritaf);
                                            $this->telegram->sendMessage([
                                                'parse_mode'=>'HTML',
                                                'chat_id' => $userId,
                                                'text' => $messageTaf
                                            ]);
                                }  
                            }
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => "Hasil Pencarian anda terlalu panjang, sehingga hanya akan ditampilkan tafsir sebanyak 1.300 tafsir"
                            ]);
                            // $caritaf = DB::table('verse_tafsirs')
                            //     ->join('verses','verse_tafsirs.id_verse', '=', 'verses.id')
                            //     ->join('chapters','verses.id_chapter', '=', 'chapters.id')
                            //     ->join('tafsirs', 'verse_tafsirs.id_tafsir', '=', 'tafsirs.id')
                            //     ->where('verse_tafsirs.text','LIKE','%'.$cari.'%')
                            //     ->get(['verse_tafsirs.id_tafsir','chapters.name','chapters.number_chapter','verses.number']);                             
                            //     $messageTaf = self::getCariTaf($caritaf);
                            //     $this->telegram->sendMessage([
                            //         'parse_mode'=>'HTML',
                            //         'chat_id' => $userId,
                            //         'text' => $messageTaf
                            // ]);
                    }
                   
                    
                }
                
            }elseif($chat[0]== "/qslatin"){
                $messageOut = self::QSLATIN_GUIDE;
                if(sizeof($chat)<= 1){
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut
                    ]);
                }else{
                    $cari = substr($input, 9);
                    
                    $cariLatin =DB::table('verses')
                                ->join('chapters','verses.id_chapter','=','chapters.id')
                                ->where('verses.transliteration','LIKE','%'.$cari.'%')
                                ->get();

                    $countLatin = DB::table('verses')
                            ->join('chapters','verses.id_chapter','=','chapters.id')
                            ->where('verses.transliteration','LIKE','%'.$cari.'%')
                            ->get()
                            ->count();
                    // $countCari = Verse::where('transliteration','LIKE','%'.$cariLatin.'%')->get()->count();
                    if ($countLatin == 0) {
                        $messageOut = 'Terdapat <strong>' .$countLatin. ' ayat ditemukan</strong>, silahkan coba dengan kata kunci lain.';
                        $this->telegram->sendMessage([
                            'parse_mode' => 'HTML',
                            'chat_id' => $userId,
                            'text' => $messageOut,
                        ]);
                    } elseif($countLatin <= 2828) {
                        $messageOut = 'Terdapat <strong>' .$countLatin. ' ayat ditemukan</strong>, silahkan coba dengan kata kunci lain.';
                        $this->telegram->sendMessage([
                            'parse_mode' => 'HTML',
                            'chat_id' => $userId,
                            'text' => $messageOut
                        ]);
                        if ($countLatin >= 130){
                            $length = 0;
                            while ($countLatin >= 130) {
                                $countLatin -= 130;
                                $length++;
                            }
                            for ($i = 0; $i <= $length; $i++) {
                                $skip = $i * 130;
                                $cariLatin =DB::table('verses')
                                ->join('chapters','verses.id_chapter','=','chapters.id')
                                ->where('verses.transliteration','LIKE','%'.$cari.'%')
                                ->skip($skip)->take(130)
                                ->get();

                                $messageTaf = self::getCariLatin($cariLatin);
                                            $this->telegram->sendMessage([
                                                'parse_mode'=>'HTML',
                                                'chat_id' => $userId,
                                                'text' => $messageTaf
                                            ]);
                            }
                        } else {
                            
                            $cariLatin =DB::table('verses')
                                ->join('chapters','verses.id_chapter','=','chapters.id')
                                ->where('verses.transliteration','LIKE','%'.$cari.'%')
                                ->get();
                                
                                $messageTaf = self::getCariLatin($cariLatin);
                                            $this->telegram->sendMessage([
                                                'parse_mode'=>'HTML',
                                                'chat_id' => $userId,
                                                'text' => $messageTaf
                                            ]);
                        }
                    } else {
                        $messageOut = 'Terdapat <strong>' .$countLatin. ' ayat ditemukan</strong>, silahkan coba dengan kata kunci lain.';
                        $this->telegram->sendMessage([
                            'parse_mode' => 'HTML',
                            'chat_id' => $userId,
                            'text' => $messageOut
                        ]);
                        if ($countLatin >= 130){
                            $length = 0;
                            while ($countLatin >= 130) {
                                $countLatin -= 130;
                                $length++;
                            }
                            for ($i = 0; $i <= 9; $i++) {
                                $skip = $i * 130;
                                $cariLatin =DB::table('verses')
                                ->join('chapters','verses.id_chapter','=','chapters.id')
                                ->where('verses.transliteration','LIKE','%'.$cari.'%')
                                ->skip($skip)->take(130)
                                ->get();

                                $messageTaf = self::getCariLatin($cariLatin);
                                            $this->telegram->sendMessage([
                                                'parse_mode'=>'HTML',
                                                'chat_id' => $userId,
                                                'text' => $messageTaf
                                            ]);
                            }
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => "Hasil Pencarian anda terlalu panjang, sehingga hanya akan ditampilkan hasil sebanyak 1.300"
                            ]);
                        } else {
                            
                            $cariLatin =DB::table('verses')
                                ->join('chapters','verses.id_chapter','=','chapters.id')
                                ->where('verses.transliteration','LIKE','%'.$cari.'%')
                                ->get();
                                
                                $messageTaf = self::getCariLatin($cariLatin);
                                            $this->telegram->sendMessage([
                                                'parse_mode'=>'HTML',
                                                'chat_id' => $userId,
                                                'text' => $messageTaf
                                            ]);
                        }
                    }
                    
                }
            }elseif($chat[0] == "/start"){
                $messageOut = self::QSTART_GUIDE;
                $this->telegram->sendMessage([
                    'parse_mode'=>'HTML',
                    'chat_id' => $userId,
                    'text' => $messageOut
                ]);
            } else {
                $this->telegram->sendMessage([
                    'chat_id' => $userId,
                    'text' => "Menu Tidak Tersedia"
                ]);
            }     
        } catch (\Exception $e) {
            $this->telegram->sendMessage([
                'chat_id' => $userId,
                'text' => "Sintax Error"
            ]);
        }
        Storage::put('logs.txt', json_encode($request->all(), JSON_PRETTY_PRINT));
    }
    public static function arabicNumber($number){
        $western_arabic = array('0','1','2','3','4','5','6','7','8','9');
        $eastern_arabic = array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');

        return str_replace($western_arabic, $eastern_arabic, strval($number));
       
    }
    public static function indonesiaNumber($number){
        $western_arabic = array('0','1','2','3','4','5','6','7','8','9');
        $eastern_arabic = array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');

        return str_replace($eastern_arabic, $western_arabic, strval($number)); 
    }
    
    public function getQNAnswere($versesBetween, $chapters){
        $message = '';
        foreach ($versesBetween as $item){
            $message = $message
            .PHP_EOL.
            $item->text_uthmani.' ( '.self::arabicNumber($item->number).' )'
            .PHP_EOL.PHP_EOL.
            '{/qv_'.$item->id_chapter.'_'.$item->number.'}';
        }
        return 
        '<strong> ('.self::arabicNumber($chapters->number_chapter).' )'.$chapters->arabic_name.'</strong>'.
        PHP_EOL.
        $message
        .PHP_EOL;
         
    }

    public function getQVAnswer( Verse $verses, $chapters){
        $ayatMsg= $verses->text_uthmani .' ( '.self::arabicNumber($verses->number).' )';
        $ayatLatin = $verses->transliteration .' ( '.self::indonesiaNumber($verses->number).' )';
        // $tafsirMsg ='';
        // foreach($tafsir as $taf){
        //     $tafsirMsg = $tafsirMsg.'['.$taf->name.' {/qtaf_'.$taf->id.'}] ';
        // }
        return '<strong> ('.self::arabicNumber($chapters->number_chapter).' )'.$chapters->arabic_name.'</strong>'
        .PHP_EOL.
        '<strong>'. $chapters->name. ' (QS '.$chapters->id.':'.$verses->number.')'.'</strong>'
        .PHP_EOL.
        PHP_EOL.
        $ayatMsg
        .PHP_EOL.
        PHP_EOL.
        $ayatLatin
        .PHP_EOL.
        '<code>📚 Lihat Tafsir  : </code>'. '/qtaf_1_'.$verses->id_chapter.'_'.$verses->number
        .PHP_EOL.
        '<code>📚 Daftar Tafsir Yang Tersedia  : </code>'. '{/qtaf}'
        ;
        // .PHP_EOL.
        // $tafsirMsg.''
           
    }

    public function getWord( WordVerse $word_verses, Verse $verses, $chapters){
        $ayatMsg= $verses->text_uthmani .' ( '.self::arabicNumber($verses->number).' )';
        $wordMsg= $word_verses->text_uthmani .' ( '.self::arabicNumber($word_verses->number).' )';
        $wordLatin = $word_verses->transliteration .' ( '.self::indonesiaNumber($word_verses->number).' )';
        return 
        '<strong> ('.self::arabicNumber($chapters->number_chapter).' )'.$chapters->arabic_name.'</strong>'
        .PHP_EOL.
        '<strong>'. $chapters->name. ' (QS '.$chapters->id.':'.$verses->number.')'.'</strong>'
        .PHP_EOL.
        $ayatMsg
        .PHP_EOL.
        PHP_EOL.
        $wordMsg
        .PHP_EOL.
        PHP_EOL.
        $wordLatin;
        // $wordMsg = $word_verses->text_uthmani.' ( '.self::arabicNumber($word_verses->number).' )';
        // $wordLatin = $word_verses->transliteration .' ( '.self::indonesiaNumber($word_verses->number).' )';
        // return '<strong> ('.self::arabicNumber($word_verses->number_chapter).' )'.$word_verses->arabic_name.'</strong>'
        // .PHP_EOL.
        // '<strong>'. $word_verses->name. ' (QS '.$word_verses->id.':'.$word_verses->number.')'.'</strong>'
        // .PHP_EOL.
        // PHP_EOL.
        // $wordMsg
        // .PHP_EOL.
        // PHP_EOL.
        // $wordLatin;
    }

    public function getTaf($tafsir){
        $message = '';
        foreach ($tafsir as $taf){
            $message = $message
                .PHP_EOL.PHP_EOL.
                '<strong>'.'Tafsir '.strip_tags($taf->nameChapter). ' (QS '.$taf->number_chapter.':'.$taf->number.')'.'</strong>'
                .PHP_EOL.PHP_EOL.
                strip_tags($taf->text_uthmani) .' ( '.self::arabicNumber($taf->number).' )'
                .PHP_EOL.PHP_EOL.
                // "<strong>Tafsir : </strong>".
                // PHP_EOL.
                // preg_replace("/[^a-zA-Z0-9]/", " ", strip_tags($taf->text))
                strip_tags($taf->text)
                .PHP_EOL.PHP_EOL.
                "<strong>Kitab Tafsir : </strong>".$taf->name. ",". $taf->author_name
                .PHP_EOL.
                "<strong>Bahasa : </strong>".$taf->language_name;
        }
        return $message;
    }
    public function getCariTaf($caritaf){
        $number = 1;
        $message = '';
        foreach ($caritaf as $cari){
            $message = $message
            .PHP_EOL.
            // $number.').'.strip_tags($cari->name).' '.'-'.' '.'/qtaf_'.$cari->id_tafsir.'_'.$cari->number_chapter.'_'.$cari->number;
           '/qtaf_'.$cari->id_tafsir.'_'.$cari->number_chapter.'_'.$cari->number;
            
            // $number++;
        }
        return $message;
    }

    public function getCariLatin($cariLatin){
        $message = '';
        foreach ($cariLatin as $cari){
            $message = $message
            .PHP_EOL.
            // $number.').'.strip_tags($cari->name).' '.'-'.' '.'/qtaf_'.$cari->id_tafsir.'_'.$cari->number_chapter.'_'.$cari->number;
           '/qv_'.$cari->number_chapter.'_'.$cari->number;
            
            // $number++;
        }
        return $message;
    }

}
