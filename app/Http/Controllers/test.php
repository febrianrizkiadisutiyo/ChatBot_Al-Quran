<?php

namespace App\Http\Controllers;

use App\Models\Chapters;
use App\Models\Verses;
use App\Models\word_translations;
use App\Models\word_verses;
use App\Models\verses_translations;
use Faker\Provider\Lorem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class test extends Controller
{
    public function testAyat()
    {
//        $ayat = Verses::find(1);
//        $find = Chapters::where('id', 1)->first();
//        $find = Verses::where('number', 2)->first();
        //            $sql2 = "SELECT word_translations.text,  verses.number , verses.id_chapter , chapters.name, chapters.arabic_name
//            FROM word_translations
//            INNER JOIN word_verses ON word_verses.id = word_translations.id_word_verse
//            INNER JOIN verses ON verses.id = word_verses.id_verse
//            INNER JOIN chapters ON chapters.id = verses.id_chapter
//            WHERE word_translations.text LIKE '%$q2%'";
        
        

         // dd($find);
//        $find = verses_translations::query()->where('id_translation', 33)->where('id_verse', 6230)->first();
//        dd($find);
//        $find = Verses::query()->where('number', 4)->where('id_chapter', 1)->first();

        // $surah = Chapters::where('id', 1)->first();
        // $ayat = Verses::where('id_chapter', $surah->id)->where('number', 1)->first();
        // $word = word_verses::where('id_verse', $ayat->id)->where('number', 2)->first();
        // $word2 = word_translations::where('id_word_verse', $word->id)->where('id_language', 1)->first();

//        $countWord = word_translations::where('text', 'LIKE', '%'. 'wahai orang' .'%')->count();
//        $translate = word_translations::where('text', 'LIKE', '%'. 'wahai orang' .'%')->first();
//
//        dd($translate);

        // $word = word_verses::where('id', $translate->id_word_verse)->first();

        // $ayat = Verses::where('id', $word->id_verse)->first();

        // $find = verses_translations::where('id_translation', 33)->where('id_verse', $ayat->id)->first();

        // SELECT * FROM `word_verses` WHERE `transliteration` LIKE '%bis%';
        // SELECT * FROM `word_verses` WHERE MATCH (transliteration) against ('bis');

        // $translate = word_translations::where('text', 'LIKE', '%'. 'wahai orang' .'%')->first();
        // $chat = 'nikah';


        // $wVerse = word_translations::where('text', 'LIKE', '%' . $chat . '%')->get();

        /**
        * rupanya yang di tangkat oleh $word berbasis array 
        **/
        // $arr = [];
        // for ($i=0; $i < sizeof($wVerse); $i++) { 
        //         // code...
                // $word = word_verses::where('id', $wVerse->id_word_verse)->get();
        //         array_push($arr, $word);
        // }

        // dd($arr);
        // $ayat = Verses::where('id', $word->id_verse)->get();
        // $chapter = Chapters::where('id', $ayat->id_chapter)->get();
        // $chat = '/qstrans wahai orang-orang yang beriman';
        // $cari = substr($chat, 8);
        // $find = DB::table('verse_translations')
        //                 ->join('verses', 'verse_translations.id_verse', '=', 'verses.id' )
        //                 ->join('chapters', 'verses.id_chapter', '=', 'chapters.id')
        //                 ->where('verse_translations.text', 'LIKE', '%'.$cari.'%')
        //                 ->where('verse_translations.id_translation', '=', 33)
        //                 ->get(['verses.number', 'chapters.number_chapter', 'chapters.name']);

        // $find = verses_translations::where('text', 'LIKE', '%'.$chat.'%')->where('id_translation', 33)->get();
        // $ayat = Verses::where('id', )
        // $surah = Chapters::where('id', 1)->first();
        // $ayat = Verses::query()->where('id_chapter', 1)->count();
                    // $countSurah = Chapters::where('id', 1)->get('verse_count');


        // $str = "/qvwbw 1:1:1";
        // $jumlah = explode(" ",$str);

        // $find = DB::table('word_translations')
        //                 ->join('word_verses', 'word_translations.id_word_verse', '=', 'word_verses.id' )
        //                 ->join('verses', 'word_verses.id_verse', '=', 'verses.id')
        //                 ->join('chapters', 'verses.id_chapter', '=', 'chapters.id')
        //                 ->where('word_translations.text', 'LIKE', '%sapi%')
        //                 ->where('word_translations.id_language', '=', 13)
        //                 ->get(['word_verses.number as numberWord', 'verses.number as numberVerses', 'chapters.number_chapter', 'chapters.name']);
        // dd(sizeof($find));
        $textArabic = "Hellow";
        $countWordVerses = word_verses::with(['verse', 'verse.chapter'])->orWhere('text_uthmani', 'LIKE', $textArabic)->orWhere(function ($query) use ($textArabic) {
                            $query->orWhere('text_uthmani_simple', 'LIKE', $textArabic)
                                ->orWhere('text_imlaei', 'LIKE', $textArabic)
                                ->orWhere('text_imlaei_simple', 'LIKE', $textArabic)
                                ->orWhere('text_indopak', 'LIKE', $textArabic);
                        })->count();
        $countAll = word_translations::with(['word_verses', 'word_verses.chapters'])
        // $countAll = DB::table('word_translations')
        //                 ->join('word_verses', 'word_translations.id_word_verse', '=', 'word_verses.id' )
        //                 ->join('verses', 'word_verses.id_verse', '=', 'verses.id')
        //                 ->join('chapters', 'verses.id_chapter', '=', 'chapters.id')
        //                 ->where('word_translations.text', 'LIKE', '%'.$chat[1].'%')
        //                 ->where('word_translations.id_language', '=', 13)
        //                 ->get(['word_verses.number as numberWord', 'verses.number as numberVerses', 'chapters.number_chapter', 'chapters.name'])->count();

        return view('test', [
                'test' => 
        ]);
    }
}
